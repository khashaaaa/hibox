<?php

class ItemWithReview extends GenerateBlock {

	protected $_cache = false; //- кэшируем или нет.
	protected $_life_time = 3600; //- время на которое будем кешировать
	protected $_template = 'reviews'; //- шаблон, на основе которого будем собирать блок
	protected $_template_path = '/main/';

	/**
     * @var UrlWrapper
     */
    private $baseUrl;

    private $reviewsProvider;

	public function __construct() {
		parent::__construct(true);

		$this->baseUrl = new UrlWrapper();
        $this->baseUrl->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
        $this->reviewsProvider = new ReviewsProvider();
	}

	private function clearUrl(){
		unset($_GET['rating']);
		unset($_GET['cost']);
		unset($_GET['count']);
		unset($_GET['filters']);
		unset($_GET['clear']);
		unset($_GET['script_name']);
		unset($_GET['ignorefilters']);
	}

	private function checkClear(){
		if (isset($_GET['clear'])) {
			if(in_array('Seo2', General::$enabledFeatures)){
				$url = $_SERVER['REQUEST_URI'];
				header('Location: ' . current(explode('?', $url)));
				die;
			}

			$url = $_SERVER['REQUEST_URI'];
			$currentQuery = $_SERVER['QUERY_STRING'];
			$this->clearUrl();
			$newQuery = http_build_query($_GET);
			header('Location: ' . str_replace($currentQuery, $newQuery, $url));
			die;
		}
	}

	protected function setVars() {

		$this->checkClear();

		$cid = $this->request->getValueSafe('cid');
        $searchMethod = $this->request->getValueSafe('SearchMethod');

		if($this->request->post('sort_by')) {
			$this->baseUrl->DeleteKey('sort_by')->Add('sort_by', $this->request->post('sort_by'));
		}

        if($this->request->post('per_page')) {
        	$this->baseUrl->DeleteKey('per_page')->Add('per_page', $this->request->post('per_page'))
                ->DeleteKey('from')->Add('from', 0);
        }

		if (! empty($_POST)) {
			header('Location: ' . $this->baseUrl->Get());
		}

		// Постараничный вывод
        $perPage = General::getNumConfigValue('comments_catalog_perpage', 16);
		if ($this->request->get('per_page')) {
            $perPage = $this->request->get('per_page');
		}
		$from = 0;
		if ($this->request->get('from')) {
			$from = $this->request->get('from');
		}

        $subcats = array();
        $products = array();
        $searchParams = array();
        $availableSearchMethodList = array();
        $activeSearchMethod = new OtapiProviderSearchMethodInfo(null);
        $totalCount = 0;

        try {
            $searchController = new SearchController();
            $searchParams = $searchController->bindSearchParams($this->request);

            $sid = User::getObject()->getSid();
            $lang = Session::getActiveLang();

            $xmlParams = '';
            $xmlParams = new SimpleXMLElement('<SearchItemsParameters></SearchItemsParameters>');
            $xmlParams->Module = SearchController::MODULE_REVIEWS;
            if (! empty($searchParams['Provider'])) {
                $xmlParams->Provider = $searchParams['Provider'];
            }
            if (! empty($searchParams['SearchMethod'])) {
                $xmlParams->SearchMethod = $searchParams['SearchMethod'];
            }
            if (! empty($searchParams['cid'])) {
                $xmlParams->CategoryId = $searchParams['cid'];
            }
            $xmlParams = $xmlParams->asXML();

            // поиск товаров
            /** @var OtapiBatchItemSearchResultAnswer $searchResult */
            OTAPILib2::BatchSearchItemsFrame(
                $lang,
                $sid,
                $xmlParams,
                $from,
                $perPage,
                'AvailableSearchMethods',
                $searchResult
            );
            OTAPILib2::makeRequests();

            // для удобства разбираем ответ сервисов по переменным
            $searchResult = $searchResult->GetResult();
            /** @var OtapiBatchItemSearchResult $searchResult */
            $totalCount = $searchResult->GetItems()->GetItems()->GetTotalCount();

            foreach ($searchResult->GetItems()->GetItems()->GetContent()->GetItem() as $value) {
                $products[] = Product::getObject($value->GetId(), $value, array(
                    'category' => $searchResult->GetCategory(),
                ));
            }

            foreach ($searchResult->GetItems()->GetCategories()->GetContent()->GetItem() as $value) {
                if (!$value->IsFiltered() && !$value->IsHidden()) {
                    $subcats[] = array(
                        'id' => $value->GetId(),
                        'name' => $value->GetName(),
                    );
                }
            }

            $activeSearchMethod = $searchResult->GetItems()->GetSearchMethod();
            if ($searchResult->GetAvailableSearchMethods()) {
                foreach ($searchResult->GetAvailableSearchMethods()->GetContent()->GetItem() as $value) {
                    $availableSearchMethodList[] = $value;

                    // определяем активный способ поиска
                    if (
                        $searchResult->GetItems()->GetProvider() == $value->GetProvider() &&
                        $searchResult->GetItems()->GetSearchMethod() == $value->GetSearchMethod()
                    ) {
                        $activeSearchMethod = $value;
                    }
                }
            }
        } catch (ServiceException $e) {
            Session::setError($e->getMessage());
        }

		$GLOBALS['rootpath'] = array();

		$this->tpl->assign('subCats', $subcats);
		$this->tpl->assign('products', $products);
		$this->tpl->assign('totalCount', $totalCount);
		$this->tpl->assign('count', $totalCount);
		$this->tpl->assign('from', $from);
		$this->tpl->assign('cid', $cid);
		$this->tpl->assign('perPage', $perPage);
        $this->tpl->assign('searchParams', $searchParams);
        $this->tpl->assign('availableSearchMethodList', $availableSearchMethodList);
        $this->tpl->assign('activeSearchMethod', $activeSearchMethod);

		$this->tpl->assign('baseUrl', $this->baseUrl);
	}
}
