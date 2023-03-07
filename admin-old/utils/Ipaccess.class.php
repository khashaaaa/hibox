<?php

class Ipaccess extends GeneralUtil
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'index'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = 'ipaccess/'; //- путь к шаблону
    protected $tpl;
   
    public function defaultAction()
    {
        global $otapilib;

        try{
            $this->checkAuth();

            //Получаем все скидки
            $otapilib->setErrorsAsExceptionsOn();
            $sid = Session::get('sid');			
			
            $ipaccessconfig = $otapilib->GetInstanceOptionsInfo($sid);
			//print_R($ipaccessconfig);
            $this->tpl->assign('AllowedIPs', $ipaccessconfig['AllowedIPs']);
			$this->tpl->assign('accessip_on', $ipaccessconfig['IsIPCheckUsed']);		
						
			
        }
        catch(NotFoundException $e){
        }
        catch(ServiceException $e){
            Session::setError($e->getMessage(), $e->getErrorCode());
        }
        catch(Exception $e){
            Session::setError($e->getMessage(), $e->getCode());
        }
        print $this->fetchTemplate();
    }
	
//=======================================  Работа со списками ============================================
    public function AddCurIpAction()
    {
        global $otapilib;

        try{
            $this->checkAuth();
            $otapilib->setErrorsAsExceptionsOn();
            $sid = Session::get('sid');
            $xml = $this->generateAddXML($_SERVER['SERVER_ADDR']);			
            $otapilib->UpdateInstanceOptions($sid, $xml);
        }
        catch(ServiceException $e){
            Session::setError($e->getMessage(), $e->getErrorCode());
        }
        catch(Exception $e){
            Session::setError($e->getMessage(), $e->getCode());
        }
        header('Location: index.php?cmd=ipaccess');
    }
	
	
	public function AddIpAction()
    {
        global $otapilib;
        try{
            $this->checkAuth();
            $otapilib->setErrorsAsExceptionsOn();
            $sid = Session::get('sid');
            $xml = $this->generateAddXML($_POST['ipnew']);			
            $otapilib->UpdateInstanceOptions($sid, $xml);
        }
        catch(ServiceException $e){
            Session::setError($e->getMessage(), $e->getErrorCode());
        }
        catch(Exception $e){
            Session::setError($e->getMessage(), $e->getCode());
        }
        header('Location: index.php?cmd=ipaccess');
    }

	public function SwitchOnIpAction()
    {
        global $otapilib;
        try{
            $this->checkAuth();
            $otapilib->setErrorsAsExceptionsOn();
            $sid = Session::get('sid');
            $xml = $this->generateSwitchXML('true');			
            $otapilib->UpdateInstanceOptions($sid, $xml);
        }
        catch(ServiceException $e){
            Session::setError($e->getMessage(), $e->getErrorCode());
        }
        catch(Exception $e){
            Session::setError($e->getMessage(), $e->getCode());
        }
        header('Location: index.php?cmd=ipaccess');
    }
	
	public function SwitchOffIpAction()
    {
        global $otapilib;
        try{
            $this->checkAuth();
            $otapilib->setErrorsAsExceptionsOn();
            $sid = Session::get('sid');
            $xml = $this->generateSwitchXML('false');
            $otapilib->UpdateInstanceOptions($sid, $xml);
        }
        catch(ServiceException $e){
            Session::setError($e->getMessage(), $e->getErrorCode());
        }
        catch(Exception $e){
            Session::setError($e->getMessage(), $e->getCode());
        }

        header('Location: index.php?cmd=ipaccess');
    }
	

    public function DelIpAction()
    {
		global $otapilib;
        try{
            $this->checkAuth();
            $otapilib->setErrorsAsExceptionsOn();
            $sid = Session::get('sid');
            $xml = $this->generateDelXML($_GET['ip']);						
            $otapilib->UpdateInstanceOptions($sid, $xml);
        }
        catch(ServiceException $e){
            Session::setError($e->getMessage(), $e->getErrorCode());
        }
        catch(Exception $e){
            Session::setError($e->getMessage(), $e->getCode());
        }
        header('Location: index.php?cmd=ipaccess');       
    }

    
  
   
	
    /**
     * Генерирование xml для добавления группы скидок
     * @param $data     Передаваемые параметры
     * @return string   Сгенерированная xml строка
     */
    private function generateAddXML($data){	
		global $otapilib;
		$sid = Session::get('sid');			
		$ipaccessconfig = $otapilib->GetInstanceOptionsInfo($sid);		
		$xml='<InstanceOptionsData><AllowedIPs>';
		foreach ($ipaccessconfig['AllowedIPs'] as $item) {
			if ($item!=htmlspecialchars($data))		
				$xml.='<string>'.$item.'</string>';
			
		}
		$xml.='<string>'.htmlspecialchars($data).'</string>';
		$xml.='</AllowedIPs></InstanceOptionsData>';		
		return $xml;
    }
	
	/**
     * Генерирование xml для добавления группы скидок
     * @param $data     Передаваемые параметры
     * @return string   Сгенерированная xml строка
     */
    private function generateDelXML($data){	
		global $otapilib;
		$sid = Session::get('sid');			
		$ipaccessconfig = $otapilib->GetInstanceOptionsInfo($sid);		
		$xml='<InstanceOptionsData><AllowedIPs>';
		foreach ($ipaccessconfig['AllowedIPs'] as $item) {
			if ($item!=htmlspecialchars($data))		
				$xml.='<string>'.$item.'</string>';			
		}
		$xml.='</AllowedIPs></InstanceOptionsData>';		
		return $xml;
    }
	
	/**
     * Генерирование xml для добавления группы скидок
     * @param $data     Передаваемые параметры
     * @return string   Сгенерированная xml строка
     */
    private function generateSwitchXML($data){	
		global $otapilib;
		$sid = Session::get('sid');			
		$ipaccessconfig = $otapilib->GetInstanceOptionsInfo($sid);	
		$xml='<InstanceOptionsData><IsIPCheckUsed>'.htmlspecialchars($data).'</IsIPCheckUsed><AllowedIPs>';
		foreach ($ipaccessconfig['AllowedIPs'] as $item) {				
				$xml.='<string>'.$item.'</string>';			
		}		
		$xml.='</AllowedIPs></InstanceOptionsData>';				
        return $xml;
    }
	

}