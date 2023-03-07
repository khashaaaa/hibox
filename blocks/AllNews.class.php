<?php

class AllNews extends GenerateBlock {

    protected $_cache = CFG_CACHED; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'allnews'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/main/';

    public function __construct() {
        parent::__construct(true);
    }

    protected function setVars() {
        $cms = new CMS();
        $status = $cms->Check();
        if ($status) {
            $allNews = array();
            if(@$_GET['p'] == 'allnews') {
                $allNews = $cms->GetAllNews();
            }
            $GLOBALS['pagetitle'] = Lang::get('news');
            $this->tpl->assign('allNews', $allNews);
        }
        $this->tpl->assign('status', $status);
    }

}

?>
