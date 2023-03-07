<?php

class SearchPropNew extends GenerateBlock
{
    protected $_cache = false;
    protected $_life_time = 3600;
    protected $_template = 'searchpropnew';
    protected $_template_path = '/main/';

    private $searchProperties;
    private $searchPropertiesLogic;

    public function setSearchProperties($searchProperties, $logic){
        $this->searchProperties = $searchProperties;
        $this->searchPropertiesLogic = $logic;
    }

    protected function setVars()
    {
        if (isset($_GET['filters'][20000])) {
            $activeBrandFilters = $_GET['filters'][20000];
        } else {
            $activeBrandFilters = false;
        }
        $this->tpl->assign('searchprops', $this->searchProperties);
        $this->tpl->assign('logic', $this->searchPropertiesLogic);
        $this->tpl->assign('activeBrandFilters', $activeBrandFilters);
        
    }

    public function setBaseUrl($url){
        $this->tpl->assign('baseUrl', clone $url);
    }
    
    public function setClearUrl($url){
        $this->tpl->assign('clearUrl', clone $url);
    }

    public static function getCacheId(){
        $categoryId = RequestWrapper::getValueSafe('cid');
        $provider = RequestWrapper::getValueSafe('Provider');
        $searchMethod = RequestWrapper::getValueSafe('SearchMethod');

        $cacheId = $categoryId;
        if($provider){
            $cacheId .= '_'.$provider;
        }
        if($searchMethod){
            $cacheId .= '_'.$searchMethod;
        }

        return $cacheId;
    }
}

?>