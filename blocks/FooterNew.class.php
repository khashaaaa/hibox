<?php

class FooterNew extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'footernew'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/main/';

    public function __construct()
    {
        if (isset($_GET['print']) && $_GET['print'] == 'Y') {
            $this->_template='footerprint';
        }
        parent::__construct(true);
    }

    private function _setMenu(){
        $cms = new CMS();
    
        $menu = false;
        if ($cms->Check()) {
			$cRep = new ContentRepository($cms);
            $menu = $cms->getBlock('bottom_menu_' . $_SESSION['active_lang']);
            if($menu){
                $menu_full = json_decode($menu);
                $menu_full = CMS::removeNotAvailableMenuItems($menu_full);
                $menu = array();
                foreach($menu_full as $m){
                    $isContentPage = is_numeric($m);
                    $menu[] = $isContentPage ? $cRep->GetPageByID($m) : array('alias' => $m, 'title' => Lang::get($m));
                }
            }
        }
    
        $this->tpl->assign('menu', $menu);
    }
    
    protected function setVars()
    {
        if (!isset($_GET['print'])||$_GET['print']!='Y'){
            $this->_setMenu();
        }
    }
}

?>