<?php
OTBase::import('system.lib.referral_system.lib.*');

class UserReferal extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'referal_system'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/referral_system/';
    private   $isAdmin = false;

    public function __construct()
    {
        $this->otapilib = new OTAPIlib();
        parent::__construct(true);
    }

    protected function setVars()
    {	
        $this->otapilib->setErrorsAsExceptionsOn();
        if(!Session::getUserData()){
            Users::Logout();
            header('Location: /?p=login');
            return ;
        }	
		
        $sid = Session::getUserSession();
        try{
            $user = $this->otapilib->GetUserInfo($sid);
        } catch(ServiceException $e){
            Session::setError($e->getMessage());
            return;
        }

        $RefData = new ReferralUserManager($this->cms, new SupportRepository($this->cms));

        try {
            $RefUser = $RefData->GetById($user['id']);
        } catch(NotFoundException $e){
            // Добавим пользователя в реферальную систему, если еще не добавлен
            $RefData->Add($user['login'], $user['id'], 0);
        }

        try {
            if (!isset($RefUser)) {
                $RefUser = $RefData->GetById($user['id']);
            }
            $this->assignData($RefData, $RefUser);
        } catch(NotFoundException $e){
            return false;
        }
    }

    private function assignData($RefData, $RefUser)
    {
        $RefCats = new ReferralCategoryManager($this->cms);
        $ReferralOrderManager = New ReferralOrderManager($this->cms);

        //получаем всех пользователей
        $UsersFromRef = $RefData->GetUsersByParentId($RefUser->GetId());
        //получаем всех заказоы пользователей
        if (is_array($UsersFromRef)) {
            $RefSys = array();
            foreach ($UsersFromRef as &$user) {
                $RefOrders = $ReferralOrderManager->GetOrdersByUserId($user['user_id']);
                $user['orders'] = isset($user['orders']) ? $user['orders'] : array();
                if ($RefOrders)
                    $user['orders'] = array_merge($user['orders'], $ReferralOrderManager->GetOrdersByUserId($user['user_id']));
            }
            $RefSys['login'] = $RefUser->GetLogin();
            $RefSys['userId'] = $RefUser->GetId();
            $RefSys['balance'] = $RefUser->GetBalance();
            $RefSys['category'] = $RefCats->GetById($RefUser->GetCategory());
            $link = base64_encode($RefSys['login'] . "|" . $RefSys['userId']);

            $this->tpl->assign('RefSys', $RefSys);
            $this->tpl->assign('link', $link);
            $this->tpl->assign('UsersFromRef', $UsersFromRef);

        } else {
            $this->tpl->assign('nofoundusers', true);
        }
    }

}

?>