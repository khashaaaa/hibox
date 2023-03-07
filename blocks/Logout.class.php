<?php

class Logout extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = ''; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '';

    public function __construct()
    {
        parent::__construct(true);
    }

    protected function setVars()
    {
        $this->fileMysqlMemoryCache->DelCacheEl('BatchGetUserData:'.Session::getUserOrGuestSession());
        Users::Logout();
        header('Location: ' . UrlGenerator::getHomeUrl());
        die();
    }
}

?>