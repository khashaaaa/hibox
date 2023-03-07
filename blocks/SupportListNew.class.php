<?php

class SupportListNew extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'supportlistnew'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/main/';
    private $userData;

    public function __construct()
    {
        parent::__construct(true);
        $this->userData = new UserData();
    }

    protected function setVars()
    {
        $sid = Session::getUserOrGuestSession();        
		if (Session::get(Session::getHttpHost() . 'isMayAuthenticated')) {
            //Считаем что авторизованы
            $allUserNoteData = $this->otapilib->BatchGetUserData($sid, 'UserStatus,Note');                
            if ($allUserNoteData['Status']['IsSessionExpired'] == 'false') {
                Session::set(Session::getHttpHost() . 'isMayAuthenticated', true);                
            } else {
                Session::clearUserData();                
            }
            $allNote = $allUserNoteData['Note'];
        } else {
            //Неавторизованы или неизвестно (все равно вызовем просто GetNote :) )
            $allNote = $this->otapilib->GetNote($sid);	
        }

        $url = UrlGenerator::generateContentUrl('supportlist');

        if($this->request->valueExists('clear')) {
            $isdeleted = $this->otapilib->ClearNote($sid);
            if ($isdeleted) {
                $allNote = array();
            }
        }

        if (isset($_POST['per_page'])) {
            if(isset($_GET['per_page'])) {
                $url = str_replace('&per_page=' . $_GET['per_page'], '', $url);
                $url = str_replace('?per_page=' . $_GET['per_page'], '', $url);
            } 
            if (in_array('Seo2', General::$enabledFeatures)) {
                $url .= '?per_page=' . $_POST['per_page'];
            } else {
                $url .= '&per_page=' . $_POST['per_page'];
            }
            if(isset($_GET['page'])) {
                $url = str_replace('page=' . $_GET['page'], '', $url);
            }
            header('Location: ' . $url);
        }
		
        if ($allNote === false) show_error($this->otapilib->error_message);
			
        // Постараничный вывод
        if (! empty($_GET['per_page'])) {
            $perpage = $_GET['per_page'];
        } else {
            $perpage = (isset($_SESSION['perpage']) && !empty($_SESSION['perpage'])) ? $_SESSION['perpage'] : General::getConfigValue('default_perpage', 20);
        }
        if (empty($page)) {
            $page = (int)$this->request->get('page', 1);
        }
        $from = ($page - 1) * $perpage;

        $crumbs = array(
            array('title' => Lang::get('home'), 'url' => UrlGenerator::getHomeUrl()),
            array('title' => Lang::get('favourite'), 'url' => '/?p=favourite'),
        );
        CrumbsController::setCrumbs($crumbs);
        $this->tpl->assign('list', $allNote);
        $this->tpl->assign('from', $from);
        $this->tpl->assign('perpage', $perpage);
        $this->tpl->assign('pageUrl', $url);
        $this->tpl->assign('crumbs', $crumbs);
        $this->tpl->assign('paginatorFav', new Paginator(count($allNote), $page, $perpage));
    }

    public function addAction($request)
    {
        $this->otapilib->setErrorsAsExceptionsOn();

        try {
            $sessionId = Session::getUserOrGuestSession();
            $id = $request->getValue('id');
            $quantity = $request->getValue('quantity');
            $configurationId = $request->getValue('configurationId');
            $promoId = $request->getValue('promoId');
            $externalDeliveryId = $request->getValue('externalDeliveryId');

            $res = $this->otapilib->AddItemToNote($sessionId, $id, $quantity, null, $configurationId, $promoId,
                null, null, null, null, null, null,
                null, null, null, '', $externalDeliveryId
            );

            $items = $this->otapilib->GetNote(Session::getUserOrGuestSession());
            $this->userData->ClearUserDataCache();

            print json_encode(array('Success'=>'Ok', 'Count' => count($items), 'itemId' => $res));
        } catch(ServiceException $e){
            header('HTTP/1.1 500 ' . $e->getErrorCode());
            die($e->getMessage());
        } catch(Exception $e){
            header('HTTP/1.1 500 ' . $e->getCode());
            die($e->getMessage());
        }
    }
    
    public function deleteGroupOrItemAction($request)
    {
        $sid = Session::getUserOrGuestSession();
        $language = Session::getActiveLang();
        if ($this->request->valueExists('del')) {            
            $isdeleted = $this->otapilib->RemoveItemFromNote($sid, $this->request->get('del'));
            
        }
        if($this->request->valueExists('delGroup')){
            $items = $this->request->get('ids');
            $isdeleted = OTAPILib2::RemoveItemsFromNote($language, $sid, $items, $answer);
        }
        $this->userData->ClearUserDataCache();
        if (in_array('Seo2', General::$enabledFeatures)) {
            $url = '/supportlist?per_page=' . $this->request->get('per_page');
        } else {
            $url = '/?p=supportlist&per_page=' . $this->request->get('per_page');
        }
        self::LocationRedirect($url);
    }

    static function removeItem($otapilib, $id)
    {       
        $otapilib->RemoveItemFromNote(Session::getUserOrGuestSession(), $id);
        $userData = new UserData();
        $userData->ClearUserDataCache();
    }

    static function editItemQuantity($otapilib, $id, $quantity)
    {
        $sid = Session::getUserOrGuestSession();
        $res = $otapilib->EditNoteItemQuantity($sid, $id, $quantity);
        if ($res) {
            print json_encode($otapilib->GetNote($sid));
        }
    }

    static function editItemComment($otapilib, $id, $comment)
    {
        // удалить файл кеша корзины и избранного
        $fileMysqlMemoryCache = new FileAndMysqlMemoryCache(new CMS());
        $fileMysqlMemoryCache->DelCacheEl('BatchGetUserData:' . Session::getUserOrGuestSession());

        $fields = '<Fields><FieldInfo Name="Comment" Value="' . $comment . '"/></Fields>';

        return $otapilib->EditNoteItemFields(Session::getUserOrGuestSession(), $id, $fields);
    }

    static function moveToBasket($ids)
    {
        global $otapilib;
        $sid = Session::getUserOrGuestSession();
        $otapilib->MoveItemFromNoteToBasket($sid, $ids);
        $userData = new UserData();
        $userData->ClearUserDataCache();
        if (RequestWrapper::isAjax()) {
            print json_encode(array('Success'=>'Ok'));
            die();
        } else {
            self::LocationRedirect($_SERVER['HTTP_REFERER']);
        }
    }

    static function moveItemsToBasket($items)
    {
        global $otapilib;
        $sid = Session::getUserOrGuestSession();
        $lang = Session::getActiveLang();
        OTAPILib2::MoveItemsFromNoteToBasket($lang, $sid, $items, $answer);
        OTAPILib2::makeRequests();
        $userData = new UserData();
        $userData->ClearUserDataCache();
        if (RequestWrapper::isAjax()) {
            print json_encode(array('Success'=>'Ok'));
            die();
        } else {
            self::LocationRedirect($_SERVER['HTTP_REFERER']);
        }
    }

	public static function LocationRedirect($url) 
	{		
        header("Location: $url");
        die();
    }
}
