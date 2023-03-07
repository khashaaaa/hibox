<?php

class VendorNew extends GenerateBlock {

    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'itemlistnew'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/main/';
    protected $_hash = '';

    public function __construct() {	
		
        $this->_hash = trim(htmlspecialchars(stripslashes(strip_tags($_GET['id']))));		
        parent::__construct(true);
    }

    private function xmlParams(){
        $xmlParams = new SimpleXMLElement('<SearchItemsParameters></SearchItemsParameters>');
        if (RequestWrapper::getValueSafe('search'))
            $xmlParams->addChild('ItemTitle', RequestWrapper::getValueSafe('search'));
        if (RequestWrapper::getValueSafe('cid'))
            $xmlParams->addChild('CategoryId', RequestWrapper::getValueSafe('cid'));
        $xmlParams->addChild('VendorId', RequestWrapper::getValueSafe('id'));
        $xmlParams->addChild('LanguageOfQuery', 'ru');
        if (@$_GET['cost']['from'])
            $xmlParams->addChild('MinPrice', @$_GET['cost']['from']);
        if (@$_GET['cost']['to'])
            $xmlParams->addChild('MaxPrice', @$_GET['cost']['to']);
        if (@$_GET['count']['from'])
            $xmlParams->addChild('MinQuantity', @$_GET['count']['from']);
        if (@$_GET['count']['to'])
            $xmlParams->addChild('MaxQuantity', @$_GET['count']['to']);
        if (@$_GET['rating']['from'])
            $xmlParams->addChild('MinVendorRating', @$_GET['rating']['from']);
        if (@$_GET['rating']['to'])
            $xmlParams->addChild('MaxVendorRating', @$_GET['rating']['to']);
        
		if (RequestWrapper::getValueSafe('brand'))
            $xmlParams->addChild('BrandPropertyValueId', RequestWrapper::getValueSafe('brand'));
        $xmlParams->addChild('OrderBy', @$_GET['sort_by']?$_GET['sort_by']:'popularity:desc');
        if (@$_GET['IsOriginal'])
            $xmlParams->addChild('IsOriginal', @$_GET['IsOriginal']);
        if (@$_GET['tmall'])
            $xmlParams->addChild('IsTmall', @$_GET['tmall']);
		if (@$_GET['filters']['StuffStatus'])
			$xmlParams->addChild('StuffStatus', $_GET['filters']['StuffStatus']);
        $searchParams = str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
        return $searchParams;
    }

    protected function setVars() {
		
        // Запрашиваем информацию о товарах в категории и передаем её в шаблон
        if (isset($_GET['clear'])) {
            Session::clear('filter');
            header('Location: ' . str_replace('&clear', '', $_SERVER['REQUEST_URI']));
            die;
        }

        $url = $_SERVER['REQUEST_URI'];
        
        if (isset($_POST['sort_by'])) {
            $url = str_replace('&sort_by=' . $_GET['sort_by'], '', $url);
            $url .= '&sort_by=' . $_POST['sort_by'];
            $_GET['sort_by'] = $_POST['sort_by'];
        }
        if (isset($_POST['per_page'])) {
            $url = str_replace('&per_page=' . @$_GET['per_page'], '', $url);
            $url .= '&per_page=' . $_POST['per_page'];
            
            $_GET['per_page'] = $_POST['per_page'];
        }
        if (!empty($_POST))
            header('Location: ' . $url);

		if (!isset($_GET['sort_by']) && isset($_SESSION['sort_by']) && !empty($_SESSION['sort_by'])) {
			$_GET['sort_by'] = $_SESSION['sort_by'];
        } else {
		    Session::set('sort_by', @$_GET['sort_by']);
        }

        // Постараничный вывод
        $from = isset($_GET['from']) ? $_GET['from'] : 0;
		
        /*
        $perpage = (isset($_SESSION['perpage']) && !empty($_SESSION['perpage'])) ? $_SESSION['perpage'] : 16;
        if (isset($_GET['per_page']))
            $perpage = $_GET['per_page'];
        */
        
        $perPage = $this->getAndAssignPerPageItemCount();
        Session::set('perpage', $perPage);
        global $otapilib;

        $searchParams = $this->xmlParams();
        
        $foundAll = $otapilib->BatchSearchItemsFrame(session_id(), $searchParams, $from, $perPage, 'SubCategories,Vendor');
        
        $GLOBALS['searchprop'] = @$foundAll['SearchProperties'];
        
        $itemlist = $foundAll['Items']['Items'];
        $categorylist = $foundAll['Items']['Categories'];
        $GLOBALS['rootpath'] = $foundAll['RootPath'];
		$tmall = (isset($_GET['tmall']) && ($_GET['tmall'] == 'true')) ? $_GET['tmall'] : '';

        $from = isset($_GET['from']) ? $_GET['from'] : 0;
        $url = str_replace('&from=' . $from, '', $url);

        if (isset($_POST['sort_by'])) {
            $url = str_replace('&sort_by=' . @$_GET['sort_by'], '', $url);
            $url .= '&sort_by=' . $_POST['sort_by'];
        }
        if (isset($_POST['per_page'])) {
            $url = str_replace('&per_page=' . $perPage, '', $url);
            $url .= '&per_page=' . $perPage;
        }
		$url = str_replace('&tmall=' . $tmall, '', $url);

        $GLOBALS['url'] = $url
                . '&cost[from]=' . @$_GET['cost']['from']
                . '&cost[to]=' . @$_GET['cost']['to']
                . '&count[from]=' . @$_GET['count']['from']
                . '&count[to]=' . @$_GET['count']['to']
                . '&rating[from]=' . @$_GET['rating']['from']
                . '&rating[to]=' . @$_GET['rating']['to']
        ;
        $GLOBALS['cats'] = @$categorylist;

        //if ($found === false)
            //show_error($otapilib->error_message);

        $vid = RequestWrapper::getValueSafe('id'); 
        
        $SP = new SearchPropNew();
        $searchProp = $SP->Generate();
        
        $this->tpl->assign('SearchProp',$searchProp);
        $this->tpl->assign('vendorInfo', $foundAll['Vendor']);
        $this->tpl->assign('itemlist', $itemlist['data']);
        $this->tpl->assign('totalcount', isset($itemlist['totalcount']) ? $itemlist['totalcount'] : 0);
        if (isset($itemlist['totalcount'])) {
            $this->tpl->assign('count', $itemlist['totalcount'] > 10000 ? 10000 : $itemlist['totalcount']);
        } else {
            $this->tpl->assign('count', 0);
        }
        $this->tpl->assign('cid', $vid);
		$this->tpl->assign('tmall', $tmall);
        $this->tpl->assign('from', $from);
        $this->tpl->assign('perpage', $perPage);
        $this->tpl->assign('pageUrl', $url.'&');
        $baseUrl = new UrlWrapper();
        $baseUrl->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
        $this->tpl->assign('baseUrl', $baseUrl);
    }

    private function getAndAssignPerPageItemCount(){
        $default_perpage = General::getConfigValue('default_perpage') ? General::getConfigValue('default_perpage') : 20;
        $perpage = $this->request->getValue('per_page') ? $this->request->getValue('per_page') : $default_perpage;
        $this->tpl->assign('perpage', $perpage);
        return $perpage;
    }
}

?>