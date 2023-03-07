<?php

class Email extends GenerateBlock {

    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = ''; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/notify/';
    protected $_data = array();

    public function __construct() {
        parent::__construct(true);
    }

    public function setTemplate($tpl){
        $this->_template = $tpl;
    }

    public function setData($data){
        $this->_data = $data;
    }

    protected function setVars() {
        foreach($this->_data as $k=>$v){
            $this->tpl->assign($k, $v);
        }
    }

}

?>