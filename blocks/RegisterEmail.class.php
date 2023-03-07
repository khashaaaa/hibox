<?php

class RegisterEmail extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'register_email'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/users/';

    public function __construct()
    {
        parent::__construct(true);
    }

    protected function setVars()
    {
        $args = func_get_args();
        $this->tpl->assign('username', $args[0][0]);
        $this->tpl->assign('password', $args[0][1]);
    }
}
