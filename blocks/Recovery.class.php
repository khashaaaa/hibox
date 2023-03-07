<?php

class Recovery extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'recovery'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/users/';
    
    private $_errors;

    public function __construct()
    {
        parent::__construct(true);
        $this->errors = array(
            'AccountIsBanned' => Lang::get('account_is_banned'),
            'LoginFailed' => Lang::get('login_failed')
        );
    }

    private function recover($userid){
        try {
            $res = $this->otapilib->RequestPasswordRecovery($userid);
            return array(true, $res['ConfirmationCode'], $res['Email']);
        } catch (Exception $e) {
            Session::setError($e->getMessage(), $e->getCode());
            return array(false, $e->getMessage());
        }
    }
    
    
    
    protected function setVars()
    {
        $this->otapilib->setErrorsAsExceptionsOn();
        try {
            if(isset($_POST['recovery'])){
                $res = $this->recover($_POST['userid']);
                $this->tpl->assign('show_recovery', '1');
                if ($res[0]) {
                    $this->tpl->assign('success_recovery', Lang::get('recovery_sent'));
                } else {
                    Session::setError($res[1]);                
                }
                return ;
            }
            if(isset($_GET['code'])){
                $this->tpl->assign('code', $_GET['code']);
                $this->_template = 'changepassword';
                return ;
            }
            if(isset($_POST['complete'])){
                if (empty($_POST['newPassword']) || ($_POST['newPassword'] != $_POST['newPasswordConfirm'])) {
                    throw new Exception(Lang::get('Password_empty_or_not_valid'));
                }
                $sid = $this->otapilib->ConfirmNewPassword($_POST['code'], $_POST['newPassword']);
                Users::AutoLogin($sid);
                RequestWrapper::LocationRedirect(UrlGenerator::generatePrivateOfficeUrl());
                return;
            }
            if (Session::getUserData()) {
                $this->request->LocationRedirect(UrlGenerator::generatePrivateOfficeUrl());
            }
        } catch (Exception $e) {
            Session::setError($e->getMessage(), $e->getCode());
        }
    }
}

?>