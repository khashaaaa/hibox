<?php

class Calculator {

    function defaultAction(){
        global $otapilib;

        if (!Login::auth()){
            include(TPL_DIR.'login.php');
            return false;
        }

        $cms = new CMS();
        if (!$cms->Check()){
            include(TPL_DIR . 'db_connection_fail.php');
            die;
        }

        $cms->checkTable('countries');
        $cms->checkTable('delivery');
        $cms->checkTable('countries_for_delivery');

        $delivery = $cms->GetDelivery();
        $countries = $cms->GetCountries();
		$countries_for_delivery = array();
        $items = $cms->GetCountriesByDelivery();
		foreach ($items as $item) {
			$countries_for_delivery[$item['country_id']] = $item['is_active'];
		}

        $current_lang = $this->setActiveLang();
        $translations = $cms->getTranslations('', $current_lang);
        include(TPL_DIR . 'calculator/calculator.php');

    }

    private function setActiveLang(){
        if(@$_GET['lang'])
            $_SESSION['translate_lang'] = @$_GET['lang'];
        if(!@$_SESSION['translate_lang']){
            $_SESSION['translate_lang'] = 'en';
        }
        return $_SESSION['translate_lang'];
    }

    public function add_deliveryAction()
    {
        if (Login::auth()) {
            global $otapilib;
            $sid = $_SESSION['sid'];
            $userid = @$_GET['id'];

            $result = $otapilib->GetWebUISettings($sid);
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }

            include(TPL_DIR . 'calculator/delivery_edit.php');
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

            $cms = new CMS();
            $status = $cms->Check();

            if ($status)
            {
                $cms->AddOrUpdateDelivery();
            }
            header('Location:index.php?cmd=calculator#tabs-2');

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
        $cms = new CMS();
        $status = $cms->Check();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($status && $id)
        {
            $delivery = $cms->GetDelivery();

        } else {
            include(TPL_DIR . 'calcalator.php');
            die;
        }

        include(TPL_DIR . 'calculator/delivery_edit.php');
    }

    public function add_countryAction()
    {
        if (Login::auth()) {
            global $otapilib;
            $sid = $_SESSION['sid'];
            $userid = @$_GET['id'];

            $result = $otapilib->GetWebUISettings($sid);
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }

            include(TPL_DIR . 'calculator/country_edit.php');
        } else {
            include(TPL_DIR . 'login.php');
        }
    }

    public function country_saveAction()
    {
        if (Login::auth()) {
            global $otapilib;
            $sid = $_SESSION['sid'];

            $result = $otapilib->GetWebUISettings($sid);
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }

            $cms = new CMS();
            $status = $cms->Check();

            if ($status)
            {
                $cms->AddOrUpdateCountry();
            }
            header('Location:index.php?cmd=calculator#tabs-3');

        } else {
            include(TPL_DIR . 'login.php');
        }
    }

    public function country_editAction()
    {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired');
            die;
        }
        $cms = new CMS();
        $status = $cms->Check();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($status && $id)
        {
            $countries = $cms->GetCountries();
        } else {
            include(TPL_DIR . 'calcalator.php');
            die;
        }

        include(TPL_DIR . 'calculator/country_edit.php');
    }

    public function deleteAction()
    {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired');
            die;
        }
        $cms = new CMS();
        $status = $cms->Check();

        $table_name = isset($_GET['tbl']) ? trim($_GET['tbl']) : NULL;
        $id = isset($_GET['id']) ? (int) $_GET['id'] : NULL;
		try {
			$result = $cms->DeleteRow($table_name, $id);
			if ($table_name=='delivery')
				$cms->DeleteDelivery($id);
		} catch (Exception $e) {
			echo $e->getMessage();
		}
        if ($result) echo 'Ok';
        die;
    }

    public function getCountryByDeliveryAction()
    {
        $cms = new CMS();
        $status = $cms->Check();

		$retVal = array();
		try {
			$data = $cms->GetCountriesByDelivery();
			$retVal = array('success'=>true,'data'=>$data);
		} catch (Exception $e) {
			$retVal = array('success'=>false,'message'=>$e->getMessage());
		}
        print json_encode($retVal);
		die;
    }

	public function setCountryByDeliveryAction()
	{
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired');
            die;
        }

        $cms = new CMS();
        $status = $cms->Check();

		$retVal = array();
		try {
			$data = $cms->SetCountriesByDelivery();
			$retVal = array('success'=>true);
		} catch (Exception $e) {
			$retVal = array('success'=>false,'message'=>$e->getMessage());
		}
        print json_encode($retVal);
		die;
	}

    public function delivery_for_country_editAction()
    {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired');
            die;
        }
        $cms = new CMS();
        if ($cms->Check()) {

            $data = $cms->GetDataByCountryAndDelivery();
            include(TPL_DIR . 'calculator/countries_for_delivery_edit.php');
        }
    }

    public function delivery_for_country_saveAction()
    {
        if (Login::auth()) {
            global $otapilib;
            $sid = $_SESSION['sid'];

            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }

            $cms = new CMS();
            $status = $cms->Check();

            if ($status)
            {
                $cms->UpdateParamsForDelivery();
            }
            header('Location:index.php?cmd=calculator');

        } else {
            include(TPL_DIR . 'login.php');
        }
    }
    
    public function set_custom_calculatorAction()
    {
        if (Login::auth()) {
            global $otapilib;
            $sid = $_SESSION['sid'];

            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }

            $cms = new CMS();
            $status = $cms->Check();

            if ($status)
            {
                $cms->ClearCalculator();
                $cms->SetCustomCalculator();
            }

        } else {
            include(TPL_DIR . 'login.php');
        }
    }

}
