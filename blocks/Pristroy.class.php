<?php

class Pristroy extends GenerateBlock
{
    protected $defaultAction = 'list';

    public function __construct()
    {
        if (! CMS::IsFeatureEnabled('FleaMarket')) {
            header('Location: /');
            die();
        }

        parent::__construct();

        $this->pristroy = new PristroyRepository($this->cms);
        $this->item = new ItemInfoRepository($this->cms);
    }

    protected function itemAction()
    {
        $id = (int)RequestWrapper::get('id');
        $item = $this->pristroy->getProduct($id);

        if (! empty($item) && $this->pristroy->isProductApproved($item)) {

            if (RequestWrapper::post('review')) {
                $name = Session::getUserData('username');
                $review = RequestWrapper::post('review');
                $this->item->saveComment($review['text'], $review['item_cid'], $name, $id);
                header('Location: ' . RequestWrapper::env('REQUEST_URI') . '&tab=paymship');
            }
            if (! empty($item['fullinfo']['Item'])) {
                $iteminfo = $item['fullinfo']['Item'];
            } else {
                $iteminfo = array();
                $iteminfo['noTaoData'] = 1;
                
            }
            $iteminfo['pristroy'] = $item;

            $sellerItems = $this->pristroy->getListByUserId($item['user_id'], $item['id']);            
            $this->tpl->assign('iteminfo', $iteminfo);
            $this->tpl->assign('sellerItems', $sellerItems);
        } else {
            $items = $this->pristroy->getList(0, 16);
            $this->tpl->assign('items', !empty($items['data']) ? $items['data'] : array());
            $this->tpl->assign('notfound_title', Lang::get('Pristroy_item_not_found'));
            $this->_template = 'notfound';
        }
    }

    protected function sellerAction()
    {
        $id = (int)RequestWrapper::get('id');
        $items = $this->pristroy->getListByUserId($id);
        $this->tpl->assign('items', $items);
        if (! empty($items)) {
            $this->tpl->assign('seller', $items[0]['user_login']);
        } else {
            $items = $this->pristroy->getList(0, 16);
            $this->tpl->assign('items', !empty($items['data']) ? $items['data'] : array());
            $this->tpl->assign('notfound_title', Lang::get('Pristroy_seller_not_found'));
            $this->_template = 'notfound';
        }
    }

    protected function listAction($request)
    {
        $perpage = 20;
        if ($this->request->getValue('per_page')) {
            $perpage = $request->getValue('per_page', 16);
        } elseif (General::getNumConfigValue('pristroy_catalog_perpage')) {
            $perpage = General::getNumConfigValue('pristroy_catalog_perpage');
        }
        $page = $request->getValue('page', 0);
        $from = ($page > 1) ? ($page - 1) * $perpage : 0;
        $sort = $request->getValue('sort_by', 'Default');
        
        $list = $this->pristroy->getList($from, $perpage, PristroyRepository::STATUS_APPROVED, true, $sort);
        $count = $list['rows'];
        $this->tpl->assign('list', $list['data']);
        $this->tpl->assign('totalcount', count($list['data']));
        $this->tpl->assign('count', $count);
        $this->tpl->assign('perpage', $perpage);
        $this->tpl->assign('from', $from );
        $this->tpl->assign('sortBy', $sort );
        $this->tpl->assign('paginator', new Paginator($count, $page, $perpage));
        
        $availableSorts = array(array('OrderBy' => 'Default', 'DisplayName' => Lang::get('Pristroy_default_sort')));
        $availableSorts[] = array('OrderBy' => 'Price:Asc', 'DisplayName' => Lang::get('Pristroy_price_asc_sort'));
        $availableSorts[] = array('OrderBy' => 'Price:Desc', 'DisplayName' => Lang::get('Pristroy_price_desc_sort'));
        $availableSorts[] = array('OrderBy' => 'Latest:Asc', 'DisplayName' => Lang::get('Pristroy_update_sort'));
        $this->tpl->assign('availableSorts', $availableSorts);
        
        $this->baseUrl = new UrlWrapper();
        $this->baseUrl->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
        if($this->request->getValue('sort_by')) {
            $this->baseUrl->DeleteKey('sort_by')->Add('sort_by', $this->request->getValue('sort_by'));
        }
        if($this->request->getValue('per_page')) {
            $this->baseUrl->DeleteKey('per_page')->Add('per_page', $this->request->getValue('per_page'));
        }
        if ($this->request->getMethod() == 'POST') {
            $this->request->LocationRedirect($this->baseUrl->Get());
        }
    }

}

