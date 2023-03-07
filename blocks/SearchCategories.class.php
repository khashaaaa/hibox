<?php

class SearchCategories extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'searchcategories'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/main/';
    protected $_hash = '';

	public function __construct()
    {
        parent::__construct(true);
        if($this->_cache)
            $this->tpl->_caching_id = Session::get('active_lang');
        $this->otapilib->setErrorsAsExceptionsOn();
    }
	
    protected function setVars()
    {
        if (isset($GLOBALS['searchcats_ajax'])){
            $searchCategories = $this->getSearchCategories();
            $this->tpl->assign('searchcats', $searchCategories);
		}
        else{
            $this->tpl->assign('searchcats_ajax', true);
        }
    }

    private function getSearchCategories(){
        if($this->fileMysqlMemoryCache->Exists('search_categories')){
            $searchCategories = $this->getSearchCategoriesFromCache();
        } else{
            $searchCategories = $this->getSearchCategoriesFromOTAPI();
        }
        return $searchCategories;
    }

    private function getSearchCategoriesFromCache(){
        return unserialize($this->fileMysqlMemoryCache->GetCacheEl('search_categories'));
    }

    private function getSearchCategoriesFromOTAPI(){
        try{
            $searchCategories = $this->otapilib->GetSearchCategoryInfoList();
			$searchCategories = $this->PrepareSearchCategoriesToCache($searchCategories);			

            $this->fileMysqlMemoryCache->AddCacheEl('search_categories', 21600, serialize($searchCategories));
        }
        catch(ServiceException $e){
            $searchCategories = array();
            $message = $e->getErrorMessage();
            if (OTBase::isTest() && Session::get('sid')) {
               $message = $e->getErrorCode().': '.$message;
            }
            show_error($message);
            $this->_cache = false;
        }
        return $searchCategories;
    }
	
	private function PrepareSearchCategoriesToCache($tmp){
        foreach ($tmp as &$value) {			
			foreach ($value as &$value2) {				   
				$value2 = (string)$value2; 		
			}    		
		}		
        return $tmp;
    }
}

?>