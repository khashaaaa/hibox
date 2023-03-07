<?php

class AllCats extends GenerateBlock {

    protected $_cache = true; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'allcats'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/main/';

    public function __construct() {
        parent::__construct(true);
        $this->tpl->_caching_id = Session::get('active_lang').@$_SERVER['HTTP_HOST'];
    }

    protected function setVars() {
        // Запрашивается список брен
        global $otapilib;

        $otapilib->curl_timeout = 600;
        $cats = $otapilib->GetTwoLevelRootCategoryInfoList();
        if(CMS::IsFeatureEnabled('Seo2')){
			try {
                $this->prepareCategoriesAliases($cats);
			} catch (DBException $e) {
                Session::setError($e->getMessage(), 'DBError');
			}
        }

        $treeCats = array();
        if ($cats) foreach($cats as $c) {
            if ($c['IsHidden'] == 'true') { 
                continue; 
            }
            if (! $c['ParentId']) {
                $treeCats[$c['Id']] = array_merge($c, array('children' => array()));
            } else {
                $treeCats[$c['ParentId']]['children'][] = $c;
            }
        }
        
        $this->tpl->assign('treeCats', $treeCats);
    }

    private function prepareCategoriesAliases(&$categories){
        $SeoCatsRepository = new SeoCategoryRepository(new CMS());
        if(is_array($categories))
            foreach($categories as &$c){
                $c['alias'] = $SeoCatsRepository->getCategoryAlias($c['Id'], $c['Name']);
            }
    }

}
