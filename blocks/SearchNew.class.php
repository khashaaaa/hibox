<?php

class SearchNew extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'itemlistnew'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/main/';

    public function __construct()
    {

        parent::__construct(true);
    }

    public static function getArrayValueByKeys($array, $keys){
        $tmp = $array;
        foreach($keys->request as $k){
            $tmp = $tmp[(string)$k];
        }
        return $tmp;
    }

    public static function getXmlParameter($requestType, $requestKeys, $xmlKey, &$xmlElement){
        switch($requestType){
            case 'get':
                $value = @self::getArrayValueByKeys($_GET, $requestKeys);
                if($value)
                    $xmlElement->addChild($xmlKey, $value);
                break;
            case 'post_or_get':
                $valuePost = @self::getArrayValueByKeys($_POST, $requestKeys);
                $valueGet = @self::getArrayValueByKeys($_GET, $requestKeys);
                if($valuePost)
                    $xmlElement->addChild($xmlKey, $valuePost);
                elseif($valueGet)
                    $xmlElement->addChild($xmlKey, $valueGet);
                break;
        }
    }

    private function generateSearchParameters(){
        $xmlParams = new SimpleXMLElement('<SearchItemsParameters></SearchItemsParameters>');
        $xmlParams->addChild('CategoryMode', 'External');

        $xmlSearchConfig = simplexml_load_file(CFG_APP_ROOT.'/config/request2xml.search.xml');
        foreach($xmlSearchConfig->parameter as $c){
            self::getXmlParameter((string)$c['request_type'],$c->children(),(string)$c['name'],$xmlParams);
        }

        if (isset($_GET['filters'])) {
            foreach ($_GET['filters'] as $pid => $vid) {
                if($pid!='StuffStatus' || !$vid) continue;
                $xmlParams->addChild('StuffStatus', $vid);
            }
        }
        return $xmlParams->asXML();
    }

    protected function setVars()
    {
        global $otapilib;

        // Запрашиваем информацию о товарах в категории и передаем её в шаблон
        $search = RequestWrapper::getValueSafe('search');
        $search = str_replace(array('>', '<'), '', $search);
        $_GET['search'] = $search;
        if (isset($_GET['clear']))
        {
            $url = $_SERVER['REQUEST_URI'];
            if (strpos($url, '&cost') !== false) $url = substr($url, 0, strpos($url, '&cost'));
            $url = str_replace(array('&clear=', '&clear'), '', $url);
            header('Location: '.$url);
            die;
        }
        $url = $_SERVER['REQUEST_URI'];
        $from = isset($_GET['from']) ? $_GET['from'] : 0;
        $tmall = (isset($_GET['tmall']) && ($_GET['tmall']=='true')) ? $_GET['tmall'] : '';
        $url = str_replace('&from='.$from, '', $url);
        $url = str_replace('&tmall='.$tmall, '', $url);

        if (!isset($_POST['sort_by'])) {
            if (isset($_SESSION['sort_by']) && !empty($_SESSION['sort_by']))
                $_POST['sort_by'] = $_SESSION['sort_by'];
        } else {
            Session::set('sort_by', $_POST['sort_by']);
        }

        if (isset($_POST['sort_by']))
        {
            $url = str_replace('&sort_by='.@$_POST['sort_by'], '', $url);
            $url .= '&sort_by='.$_POST['sort_by'];
        }

        if (isset($_POST['per_page']))
        {
            $url = str_replace('&per_page='.@$_GET['per_page'], '', $url);
            $url .= '&per_page='.$_POST['per_page'];
        }

        if(isset($_GET['brand']) && !empty($_GET['brand']) && !preg_match('/\&brand/',$url))
            $url .= '&brand='.RequestWrapper::getValueSafe('brand');

        $GLOBALS['url'] = $url
            .'&cost[from]='.@$_GET['cost']['from']
            .'&cost[to]='.@$_GET['cost']['to']
            .'&count[from]='.@$_GET['count']['from']
            .'&count[to]='.@$_GET['count']['to']
            .'&rating[from]='.@$_GET['rating']['from']
            .'&rating[to]='.@$_GET['rating']['to']
                ;
        // Постараничный вывод
        $perpage = (isset($_SESSION['perpage']) && !empty($_SESSION['perpage'])) ? $_SESSION['perpage'] : 16;
        if (isset($_GET['per_page'])) $perpage = $_GET['per_page'];
        if (isset($_POST['per_page'])) $perpage = $_POST['per_page'];
        Session::set('perpage', $perpage);

        $searchParams = str_replace('<?xml version="1.0"?>','',$this->generateSearchParameters());

        $blockList = 'SubCategories,SearchProperties,Vendor,RootPath';
        if(RequestWrapper::getValueSafe('brand')){
            $blockList .= ',Brand';
        }
        $foundAll = $otapilib->BatchSearchItemsFrame(session_id(), $searchParams, $from, $perpage, $blockList);
        if(RequestWrapper::getValueSafe('brand')){
            $GLOBALS['brandinfo'] = $foundAll['Brand'];
        }

        $itemlist = $foundAll['Items']['Items'];
        $categorylist = $foundAll['Items']['Categories'];
        $GLOBALS['rootpath'] = $foundAll['RootPath'];

        /*if (count(@$itemlist['data']) == 1)
        {
            header('Location: /?p=item&id='.$itemlist['data'][0]['id']);
        }*/

        if(count(@$categorylist) > 1){
            $GLOBALS['cats'] = @$categorylist;
            $this->tpl->assign('subcats',@$categorylist);
        }

        $categoriesResult = $otapilib->FindHintCategoryInfoList($search);
        $categories = array();

        if (! is_array($categoriesResult)) {
            $categoriesResult = array();
        }

        $categoriesResult = $this->checkFilteredCategory($categoriesResult);

        foreach ($categoriesResult as $item) {
            $urlParams = array('p=subcategory', 'cid='.$item['id']);
            if ($item['isvirtual'] === 'true'){
                $urlParams[] = 'virt';
            }
            $link = '/?' . implode('&', $urlParams);
            $categories[$link] = $item['name'];
        }
        $GLOBALS['searchprop'] = @$foundAll['SearchProperties'];

        $SP = new SearchPropNew();
        $searchProp = $SP->Generate();

        $this->tpl->assign('SearchProp',$searchProp);
        $this->tpl->assign('hintcats',$categoriesResult);
        $this->tpl->assign('itemlist', @$itemlist['data']);
        $this->tpl->assign('totalcount', $itemlist['totalcount']);
        $this->tpl->assign('count', $itemlist['totalcount']>10000 ? 10000 : $itemlist['totalcount']);
        $this->tpl->assign('search', $search);
        $this->tpl->assign('from', $from);
        $this->tpl->assign('tmall', $tmall);
        $this->tpl->assign('perpage', $perpage);
        $this->tpl->assign('pageurl', $url);
        $this->tpl->assign('emptymessage', Lang::get('No_goods_found'));
    }

    /**
    * Функция исключает из массива подходящих категорий те,
    * которые указаны в ограничениях в админ. панели
    */
    private function checkFilteredCategory ($categories) {
        $filteredCategory = array();

        foreach ($categories as $category) {
            if (isset($category['isfiltered']) && $category['isfiltered'] == 'true') {
                continue;
            }
            $filteredCategory[] = $category;
        }

        return $filteredCategory;
    }
}

?>