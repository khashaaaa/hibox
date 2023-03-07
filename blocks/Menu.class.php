<?php

class Menu extends GenerateBlock
{
    protected $_cache = false;
    protected $_life_time = 600;
    protected $_template = 'menu';
    protected $_template_path = '/menu/';
    protected $_hash = '';

    public function __construct()
    {
        parent::__construct(true);
    }

    protected function setVars()
    {
        global $otapilib;
		if(!Session::isAuthenticated()){
            Users::Logout();
			$this->tpl->assign('notAuth', true);
            return ;
        }

		$available_amount = floor(@$GLOBALS['accountinfo']['availableamount']);
		$this->tpl->assign('deposit', $available_amount);
		
        $SupportRepository = new SupportRepository(new CMS()); 
		
        if (isset($GLOBALS['$otapilib->GetUserInfo'])) {
            $userinfo = $GLOBALS['$otapilib->GetUserInfo'];
        } else {
            $userinfo = $otapilib->GetUserInfo(Session::getUserSession());
            $GLOBALS['$otapilib->GetUserInfo'] = $userinfo;
        }

		if (!empty($userinfo['Id']))
			$unreadCount = $SupportRepository->getTicketMessagesCount(false, 'Out', 0, $userinfo['Id']);
		else 
            $unreadCount=0;
		$newTicketAnswers = $unreadCount>0&&defined ('ADVANCED_SUPPORT_INTERFACE')?' ('.$unreadCount.')':'';
		$this->tpl->assign('newTicketAnswers', $newTicketAnswers);        
        $this->tpl->assign('unreadCount', $unreadCount);       

        //$this->tpl->assign('rootcats', $rootcats);
        if (isset($GLOBALS['catpath']))
        {
            $catpath = array_slice($GLOBALS['catpath'], 0, 1);
            //print_r($catpath);
            if (isset($catpath[0]))
            {
                $this->tpl->assign('cid', @$catpath[0]['id']);
            }
        }
    }
}

?>