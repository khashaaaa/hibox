<?php

class MoneyInfo extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'moneyinfo'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/privateoffice/';
    private $currencyData = array();

    protected $request;
	protected $sid;

    const MONTH = 2592000; // 30*24*3600
    const COUNT_DATE_ARRAY = 3;

    const WRONG_STRING_FORMAT_ERROR = 'can not be converted to type date';
   
    public function __construct()
    {
        parent::__construct(true);
		$this->request = new RequestWrapper();		
		
    }

    protected function setVars()
    {
        $currency_list = InstanceProvider::getObject()->getCurrencyInstanceList();
        $this->currencyData = $this->otapilib->GetCurrencyInstanceList($currency_list->asXML());
        if ($this->currencyData) {
            $this->currencyData = $this->currencyData['Internal'];
        }

		$this->otapilib->setErrorsAsExceptionsOn();
        if(!Session::getUserData()){
            Users::Logout();
            header('Location: /?p=login');
            return ;
        }    
		$this->sid = Session::getUserSession();     
        $result = Plugins::onRenderMoneyInfo($this->sid);
        if($result){
            if(is_array($result))
                foreach($result as $k=>$v){
                    $this->tpl->assign($k, $v);
                }
            return ;
        }
        $defaultDate = array(
            'from'  => date('d.m.Y', time() - self::MONTH),
            'to'    => date('d.m.Y', time())
        );
        if ((! $this->request->valueExists('fromdate')) or 
                (! trim($this->request->getValue('fromdate')))) {
            $fromdate = $defaultDate['from'];
        } else {
			$fromdate = $this->request->getValue('fromdate');
		}
        if ((! $this->request->valueExists('todate')) or 
                (! trim($this->request->getValue('todate')))) {
            $todate = $defaultDate['to'];
        } else {
			$todate = $this->request->getValue('todate');
		}
        $fromdate = $this->_checkDate($fromdate, $defaultDate['from']);
        $todate = $this->_checkDate($todate, $defaultDate['to']);

        $moneyhistory = array();

        if (CFG_MULTI_CURL)
        {
            // С мультипотоками
            // Инициализируем
            $this->otapilib->InitMulti();
            if (isset($GLOBALS['$otapilib->GetUserInfo']))
            {
                $userinfo = $GLOBALS['$otapilib->GetUserInfo'];
            } else {
                $userinfo = $this->otapilib->GetUserInfo($this->sid);
            }
            $accountinfo = $this->otapilib->GetAccountInfo($this->sid);
            $moneyhistory = $this->otapilib->GetStatement($this->sid, $fromdate, $todate);
            // Делаем запросы
            $this->otapilib->MultiDo();
			try{
            	if (isset($GLOBALS['$otapilib->GetUserInfo']))
            	{
                	$userinfo = $GLOBALS['$otapilib->GetUserInfo'];
            	} else {
                	$userinfo = $this->otapilib->GetUserInfo($this->sid);
                	$GLOBALS['$otapilib->GetUserInfo'] = $userinfo;
            	}
            	$accountinfo = $this->otapilib->GetAccountInfo($this->sid);
            	$moneyhistory = $this->otapilib->GetStatement($this->sid, $fromdate, $todate);
			}
			catch(ServiceException $e){
				$errorMessage = $e->getMessage();
                if (strpos($errorMessage, self::WRONG_STRING_FORMAT_ERROR) !== false) {
                    $errorMessage = Lang::get('wrong_string_format_error'); 
                }
                Session::setError($errorMessage);
			}
            // Сбрасываем
            $this->otapilib->StopMulti();
        } else {
            // По старому
			try{
            	if (isset($GLOBALS['$otapilib->GetUserInfo']))
            	{
                	$userinfo = $GLOBALS['$otapilib->GetUserInfo'];
            	} else {
                	$userinfo = $this->otapilib->GetUserInfo($this->sid);
                	$GLOBALS['$otapilib->GetUserInfo'] = $userinfo;
            	}
            	$accountinfo = $this->otapilib->GetAccountInfo($this->sid);
            	$moneyhistory = $this->otapilib->GetStatement($this->sid, $fromdate, $todate);
			}
			catch(ServiceException $e){
                $errorMessage = $e->getMessage();
                if (strpos($errorMessage, self::WRONG_STRING_FORMAT_ERROR) !== false) {
                    $errorMessage = Lang::get('wrong_string_format_error'); 
                }
                Session::setError($errorMessage);
			}
        }

        $this->tpl->assign('todate', $todate);
        $this->tpl->assign('fromdate', $fromdate);
        $this->tpl->assign('userinfo', $userinfo);
        $this->tpl->assign('accountinfo', $accountinfo);
        $this->tpl->assign('moneyhistory', $moneyhistory);
    }	
	
	
    private function _checkDate($date, $dateDefault)
    {
        $dateArray = explode('.', $date);
        if (count($dateArray) != self::COUNT_DATE_ARRAY) {
            return $dateDefault;
        }
        return $date;
    }
}

?>