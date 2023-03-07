<?php

class Content extends GenerateBlock
{
    protected $_cache = CFG_CACHED; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = ''; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/content/';
    protected $_hash = '';

    public function __construct()
    {
        $this->_hash = $_GET['mode'];
        parent::__construct(true);
    }

    protected function setVars()
    {
        switch(@$_GET['mode']){
            case 'reg_success':
                $this->_template = 'reg_success';
                break;
            case 'user_agreement':
                $this->_template = 'user_agreement';
                break;
            case 'confirm_email_fail':
                $this->_template = 'confirm_email_fail';
                break;
            case 'need_confirm_email':
                $this->_template = 'need_confirm_email';
                break;
            case 'success_register':
                $this->_template = 'success_register';
                $this->tpl->assign('username', @$_SESSION['username']);
                $this->tpl->assign('email', @$_SESSION['email']);
                break;
        }
    }
}

?>