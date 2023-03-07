<?php

class Delivery extends GeneralUtil {

    protected $_cache = false;
    protected $_life_time = 3600;
    protected $_template = 'delivery';
    protected $_template_path = 'delivery/';

    public $error = '';

    function defaultAction() {
        global $otapilib;
        $this->checkAuth();
        $sid = $_SESSION['sid'];
        
        $delivery = $otapilib->GetExternalDeliveryTypeList($sid);

        $rates = array();
        if ($delivery) {
            $rates = $otapilib->GetExternalDeliveryRateList($sid);
        }
        
        $countries = $otapilib->GetCountryInfoList();

        $all_countries = array();
        foreach ($countries as $country) {
            $all_countries[$country['id']] = $country['name'];
        }

        $this->tpl->assign('delivery', $delivery);
        $this->tpl->assign('all_countries', $all_countries);
        $this->tpl->assign('countries', $countries);
        $this->tpl->assign('rates', $rates);
        $this->tpl->assign('error', isset($error)?$error:'');

        print $this->fetchTemplate();
    }
    
    
    public function add_deliveryAction()
    {
        if (Login::auth()) {
            global $otapilib;
            $sid = $_SESSION['sid'];

            $result = $otapilib->GetWebUISettings($sid);
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }
            if (!$result) Session::setError($otapilib->error_message, $otapilib->error_code);

            $currency_list = $otapilib->GetCurrencyList(Session::get('sid'));
            if (!$currency_list) Session::setError($otapilib->error_message, $otapilib->error_code);

            $integrationTypes = $otapilib->GetDeliveryServiceSystemInfoList();

            include(TPL_DIR . 'delivery/delivery_edit.php');
        } else {
            include(TPL_DIR . 'login.php');
        }
    }
    
    
    public function delivery_saveAction()
    {
        if (Login::auth()) {
            global $otapilib;
            $sid = $_SESSION['sid'];

            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }
            //Конструктор сохранение======================================
        	if (isset($_GET['construcor'])) {
				$_formula = self::GenerateFormula($_POST);			
			} else {
				$_formula = $_POST['formula'];
			}
			//echo $_formula;
			//============================================================
            if (isset($_POST['id']) && $_POST['id']) {
                $xml = '<ExternalDeliveryType>';
                $xml.= '<Id>' . $_POST['id'] . '</Id>';
                $xml.= '<Name>' . $_POST['name'] . '</Name>';
                $xml.= '<Description>' . htmlspecialchars($_POST['description']) . '</Description>';
                $xml.= '<Formula>' . htmlspecialchars($_formula) . '</Formula>';
                $xml.= '<CurrencyCode>' . $_POST['currencycode'] . '</CurrencyCode>';
                if (isset($_POST['order']) && ((int)$_POST['order'] >= 0)) {
                    $xml.= '<Order>' . (int)$_POST['order'] . '</Order>';
                }
                if ($_POST['integration_type'] != $_POST['current_integration_type']) {
                    $xml.= '<IntegrationType>' . $_POST['integration_type'] . '</IntegrationType>';
                }
                $xml.= '</ExternalDeliveryType>';

                $r = $otapilib->EditExternalDeliveryType($sid, $xml);
                
                if (!$r) {
                    Session::setError($otapilib->error_message, $otapilib->error_code);
                    header('Location:index.php?cmd=delivery#tabs-2&error=' . $otapilib->error_message);
                    die();
                }
                //echo $otapilib->error_message; die;
            } else {
                $xml = '<ExternalDeliveryType>';
                $xml.= '<Name>' . $_POST['name'] . '</Name>';
                $xml.= '<Description>' . htmlspecialchars($_POST['description']) . '</Description>';
                $xml.= '<Formula>' . htmlspecialchars($_formula) . '</Formula>';
                $xml.= '<CurrencyCode>' . $_POST['currencycode'] . '</CurrencyCode>';
                if (isset($_POST['order']) && ((int)$_POST['order'] >= 0)) {
                    $xml.= '<Order>' . (int)$_POST['order'] . '</Order>';
                }
                $xml.= '<IntegrationType>' . $_POST['integration_type'] . '</IntegrationType>';
                $xml.= '</ExternalDeliveryType>';

                $r = $otapilib->CreateExternalDeliveryType($sid, $xml);
                if (!$r) {
                    Session::setError($otapilib->error_message, $otapilib->error_code);
                    header('Location:index.php?cmd=delivery#tabs-2&error=' . $otapilib->error_message);
                    die();
                }
            }

            header('Location:index.php?cmd=delivery#tabs-2');

        } else {
            include(TPL_DIR . 'login.php');
        }
    }
    
    
    public function delivery_editAction()
    {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired');
            die;
        }

        $id = isset($_GET['id']) ? $_GET['id'] : 0;

        if ($id) {
            $delivery = $otapilib->GetExternalDeliveryType($sid, $id);
            if (!$delivery) Session::setError($otapilib->error_message, $otapilib->error_code);
            $currency_list = $otapilib->GetCurrencyList(Session::get('sid'));
            if (!$currency_list) Session::setError($otapilib->error_message, $otapilib->error_code);
		
        } else {
            include(TPL_DIR . 'delivery/delivery.html');
            die;
        }
		//Конструктор вывод======================================
        if (isset($_GET['construcor'])) {
			if (isset($delivery))  $parsed_formula = self::ParseFormula($delivery['formula']);			
		}
		//=======================================================
        $countries = $otapilib->GetCountryInfoList();
        
        $all_countries = array();
        foreach ($countries as $country) {
            $all_countries[$country['id']] = $country['name'];
        }

        $integrationTypes = $otapilib->GetDeliveryServiceSystemInfoList();

        include(TPL_DIR . 'delivery/delivery_edit.php');
    }
    
    
    function delivery_deleteAction () {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired');
            die;
        }

        $id = isset($_GET['delivery_id']) ? $_GET['delivery_id'] : 0;

        if ($id) {
            $r = $otapilib->RemoveExternalDeliveryType($sid, $id);
            if (!$r) {
                print $otapilib->error_message;
            } else {
                print 'OK';
            }
        } else {
            print 'Wrong rate_id';
        }
        die;
    }
    
    function rate_editAction () {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired');
            die;
        }

        $id = isset($_GET['rate_id']) ? $_GET['rate_id'] : 0;

        if ($id) {
            $rate = $otapilib->GetExternalDeliveryRate($sid, $id);
            $delivery = $otapilib->GetExternalDeliveryType($sid, $rate['externaldeliverytypeid']);
        } else {
            $delivery = $otapilib->GetExternalDeliveryType($sid, $_GET['delivery_id']);
        }

        $countries = $otapilib->GetCountryInfoList();
        
        $all_countries = array();
        foreach ($countries as $country) {
            $all_countries[$country['id']] = $country['name'];
        }
        
        include(TPL_DIR . 'delivery/rate_edit.php');
    }
    
    public function rate_saveAction()
    {
        if (Login::auth()) {
            global $otapilib;
            $sid = $_SESSION['sid'];

            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }

            if (isset($_POST['id']) && $_POST['id']) {
                $xml = '<ExternalDeliveryRate>';
                $xml.= '<Id>' . $_POST['id'] . '</Id>';
                $xml.= '<ExternalDeliveryTypeId>' . $_POST['externaldeliverytypeid'] . '</ExternalDeliveryTypeId>';
                $xml.= '<CountryCode>' . $_POST['countrycode'] . '</CountryCode>';
                $xml.= '<Start>' . str_replace(',', '.', $_POST['start']) . '</Start>';
                $xml.= '<Step>' . str_replace(',', '.', $_POST['step']) . '</Step>';
                $isenabled = (isset($_POST['isenabled'])) ? 1 : 0;
                $xml.= '<IsEnabled>' . $isenabled . '</IsEnabled>';
                $xml.= '</ExternalDeliveryRate>';

                $r = $otapilib->EditExternalDeliveryRate($sid, $xml);
                if (!$r) Session::setError($otapilib->error_message, $otapilib->error_code);
            } else {
                $xml = '<ExternalDeliveryRate>';
                $xml.= '<ExternalDeliveryTypeId>' . $_POST['externaldeliverytypeid'] . '</ExternalDeliveryTypeId>';
                $xml.= '<CountryCode>' . $_POST['countrycode'] . '</CountryCode>';
                $xml.= '<Start>' . str_replace(',', '.', $_POST['start']) . '</Start>';
                $xml.= '<Step>' . str_replace(',', '.', $_POST['step']) . '</Step>';
                $isenabled = (isset($_POST['isenabled'])) ? 1 : 0;
                $xml.= '<IsEnabled>' . $isenabled . '</IsEnabled>';
                $xml.= '</ExternalDeliveryRate>';

                $r = $otapilib->CreateExternalDeliveryRate($sid, $xml);
                if (!$r) Session::setError($otapilib->error_message, $otapilib->error_code);
            }
            //echo '$xml = ' . $xml;
            //die;
            header('Location:index.php?cmd=delivery#tabs-1');

        } else {
            include(TPL_DIR . 'login.php');
        }
    }
    
    function rate_deleteAction () {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired');
            die;
        }

        $id = isset($_GET['rate_id']) ? $_GET['rate_id'] : 0;

        if ($id) {
            $r = $otapilib->RemoveExternalDeliveryRate($sid, $id);
            if (!$r) {
                print $otapilib->error_message;
            } else {
                print 'OK';
            }
        } else {
            print 'Wrong rate_id';
        }
        die;
    }
	
	
	public function ParseFormula($formula)
    {
		//Парсим формулу			
		$formula="_".$formula."_"; //Так как не всегда видит первый и последний символ			
		$mass = array();			
        //$weight < 2 ? 0 : ($start + (ceil ($weight * 10) - 1) * $step)		
		if (strpos($formula, "?")) { //Если есть зависимость от веса
			if ((strpos($formula, "&")) or (strpos($formula, "|"))) {				
				//($weight > 20) && ($weight <= 1)) ? 0 : ($start + (ceil ($weight * 10) - 1) * $step)
				//Если формула задана не так увы (((((( и так много проверок
				preg_match_all("/weight >(.*)\)/isU", $formula, $weight1, PREG_PATTERN_ORDER);
				preg_match_all("/weight <=(.*)\)/isU", $formula, $weight2, PREG_PATTERN_ORDER);
				@$mass['max_weight'] = str_replace(' ', '', $weight1[1][0]);
				@$mass['min_weight'] = str_replace(' ', '', $weight2[1][0]);
			} else {
				//$weight < 2 				
				if (strpos($formula, "<")) {
					preg_match_all("/weight <(.*)\?/isU", $formula, $weight1, PREG_PATTERN_ORDER);			
					@$mass['max_weight'] = str_replace(' ', '', $weight1[1][0]);
					@$mass['min_weight'] = '';
				} else {
					preg_match_all("/weight >(.*)\?/isU", $formula, $weight1, PREG_PATTERN_ORDER);			
					@$mass['max_weight'] = '';
					@$mass['min_weight'] = str_replace(' ', '', $weight1[1][0]);
				}
			
			}
			//Выевялем ошибки
			if  (($mass['max_weight']=='') and ($mass['min_weight']==''))   {
				@$mass['errorparse']='1';
			}	
			
	    } else {
			@$mass['max_weight'] = '';
			@$mass['min_weight'] = '';
		}
		
		
		//Шаг по весу		
		preg_match_all("/weight \* (.*)\)/isU", $formula, $step_weight, PREG_PATTERN_ORDER);
		if (isset($step_weight[1][0]))										
			$mass['step_weight'] = @$step_weight[1][0]/100;
		 
		preg_match_all("/weight \/ (.*)\)/isU", $formula, $step_weight, PREG_PATTERN_ORDER);
		if (@$step_weight[1][0]<>'')										
			$mass['step_weight'] = @$step_weight[1][0];		
		//Минимальная цена доставки		
		if (strpos($formula, htmlspecialchars('$start'))) 
			$mass['min_price_delivery'] = 'checked';
										
		//Округляем иль нет		
		if (strpos($formula, "ceil")) 		
			$mass['rounding'] = 'checked';
										
		//Цена шага по весу	
		if (strpos($formula, htmlspecialchars('$step'))) 
			$mass['step_price'] = 'checked';				
		
		//Подгатавливаем массив на выход к публике - убираем символу способные попасть в массив при разборе		
		foreach ($mass as &$value) {
			$value = str_replace(' ', '', $value);
    		$value = str_replace('_', '', $value);
			$value = str_replace(')', '', $value);
			$value = str_replace('(', '', $value);			
		}
		
		
		
		return $mass;
    }
	
    public function GenerateFormula($params)
    {	
		//Генерируем
		$end_formula = '';
		$weight = htmlspecialchars('$weight');
		$start = htmlspecialchars('$start');
		$step = htmlspecialchars('$step');
		//Если ограничения веса заданы
		if (($params['max_weight']!='') or ($params['min_weight']!='')) {
			if ($params['max_weight']=='')  $params['max_weight']=999;
			if ($params['min_weight']=='')  $params['min_weight']=0;
			$end_formula.="(".$weight." > ".$params['max_weight'].") || (".$weight." <= ".$params['min_weight'].") ? 0 : ";
		}
		//($weight > 20) && ($weight <= 1)) ? 0 : $start + (ceil ($weight * 10) - 1) * $step
		if (!empty($params['min_price_delivery'])) 
			$end_formula.=$start;
		if (!empty($params['step_price'])) {
			$end_formula.= ' + ( ';
			if (!empty($params['rounding'])) 
	    		$end_formula.= ' ceil '; 			
			$end_formula.= '( '.$weight.' '; 		
			if (!empty($params['step_weight'])) 
	    		$end_formula.= '/ '.$params['step_weight'];
			$end_formula.= ' )'; 
			if (!empty($params['min_price_delivery'])) 
	    		$end_formula.= ' - 1';	
			$end_formula.= ' )';
			if (!empty($params['step_price'])) 
				$end_formula.=" * ".$step;				
		}
        return $end_formula;
    }
}