<?php

class Post extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'postnew'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/main/';

    public function __construct()
    {
		
        parent::__construct(true);
    }
    
    protected function setVars()
    {
		
        $cms = new CMS();
        $status = $cms->Check();
        if ($status)
        {   
            $digestclass = new DigestRepository($cms);
            $post = array();
            if(@$_GET['p'] == 'post' && RequestWrapper::getValueSafe('id')){
                $post = $digestclass->GetPostById((int)RequestWrapper::getValueSafe('id'));				
            }
            
            if ($post) {            
                $GLOBALS['pagetitle'] = empty($post['pagetitle']) ? Lang::get('post') . ' - ' . $post['title'] : $post['pagetitle'];
                $GLOBALS['seo_keywords'] = empty($post['seo_keywords']) ? '' : $post['seo_keywords'];
                $GLOBALS['seo_description'] = empty($post['seo_description']) ? '' : $post['seo_description'];
                $GLOBALS['CrumbNamePost']['title'] = $post['title'];
                $GLOBALS['CrumbNamePost']['category_id'] = $post['category_id'];
            }
            $this->tpl->assign('post', $post);
        } else {
            //
	   
        }
        $this->tpl->assign('status', $status);
    }
}

?>
