<?php

class PayForm extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'pay'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/privateoffice/';
	
	protected $request;	

   
    public function __construct()
    {
        parent::__construct(true);
		$this->request = new RequestWrapper();		
		
    }

    protected function setVars()
    {        
		$this->otapilib->setErrorsAsExceptionsOn();
        if(!Session::getUserData()){
            Users::Logout();
            header('Location: /?p=login');
            return ;			
			
        }
		if($this->request->valueExists('deposit')){
            $GLOBALS['title'] = Lang::get('payments');
        }	
        $Pay = new Pay();
        $P = $Pay->Generate();
        $this->tpl->assign('Pay', $P);
        
    }	
	
	
}

?>