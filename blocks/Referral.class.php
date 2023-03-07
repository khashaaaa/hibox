<?php

class Referral extends GenerateBlock {

	protected $_cache = false; //- кэшируем или нет.
	protected $_life_time = 3600; //- время на которое будем кешировать
	protected $_template = 'index'; //- шаблон, на основе которого будем собирать блок
	protected $_template_path = '/referral/';
    /**
     * @var CMS
     */
    protected $cms;

	public function __construct() {
		parent::__construct(true);
        $this->cms = new CMS();
	}

	protected function setVars() {
        global $otapilib;
        if(!Session::getUserData())
            header('Location: ' . UrlGenerator::getHomeUrl());

		$user_login = Session::getUserDataByKey('username');
		$cms = new CMS();
		$status = $cms->Check();
		$statuses = array(
			'-'
			, Lang::get('Participant')
			, Lang::get('Leader')
			, Lang::get('Boss')
			, Lang::get('President')
		);


		$cms->checkTable('site_referrals');
		$user = $cms->GetUserByLogin($user_login);

		$this->tpl->assign('user', $user);
        $this->tpl->assign('statuses', $statuses);
        $this->tpl->assign('messages', $this->getMessages());
	}

    public function getMessages(){
        global $otapilib;

        $this->cms->Check();
        $this->cms->checkTable('site_referrals_messages');
        $user = $otapilib->GetUserInfo(Session::getUserSession());
        $q = $this->cms->query('SELECT * FROM site_support WHERE direction = "Out" AND categoryid="REFERRAL" AND userid = "'.$user['Id'].'"
            ORDER BY added DESC');

        $result = array();
        while($result[] = mysqli_fetch_assoc($q)){}
        return $result;
    }
}
