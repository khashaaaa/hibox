<?php

class News extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'news'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/main/';

    public function __construct()
    {
		
        parent::__construct(true);
    }
    
    protected function setVars()
    {
        $cms = new CMS();
        $status = $cms->Check();
        if ($status) {
            $alias = SCRIPT_NAME;
            
            $news = array();
            if (@$_GET['p'] == 'news' && RequestWrapper::getValueSafe('id')) {
                $news = $cms->GetNewsById((int)RequestWrapper::getValueSafe('id'));
            }
            
            $GLOBALS['pagetitle'] = Lang::get('news') . ' - ' . $news['title'];
            $this->tpl->assign('news', $news);
            
            $text = $news['text'];
            $this->tpl->assign('text', $text);
        }
        $this->tpl->assign('status', $status);
    }
}

?>
