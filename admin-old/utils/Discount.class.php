<?php

class Discount extends GeneralUtil
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'index'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = 'discount/'; //- путь к шаблону
    protected $tpl;
   
    public function defaultAction()
    {
        global $otapilib;

        try{
            $this->checkAuth();

            //Получаем все скидки
            $otapilib->setErrorsAsExceptionsOn();
            $sid = Session::get('sid');
            $otapilib->setUseAdminLangOn();
            $otapilib->setUseAdminLangOff();
            $discounts = $otapilib->GetDiscountGroupList($sid);
            $this->tpl->assign('discounts', $discounts);
			$this->tpl->assign('ssid', $sid);
			//Получаем кол-во страницы
			$perpage = 15; // Задаем вручную тут
			if (isset($_GET['page']))  { 
				$from = $perpage*($_GET['page']-1);				
			}  else {				
				$from = 0;
			}
						
            if(!isset($_GET['d_group']))
                throw new NotFoundException();			
            $discount_users = $otapilib->GetUsersOfDiscountGroup($sid, $_GET['d_group'], $from, $perpage);
			
            $this->tpl->assign('discount_users', $discount_users);
			$this->tpl->assign('perpage', $perpage);
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
    public function AddDiscountAction()
    {
        global $otapilib;

        try{
            $this->checkAuth();
            $otapilib->setErrorsAsExceptionsOn();
            $sid = Session::get('sid');
            $xml = $this->generateAddDiscountXML($_POST);	
			//echo $xml;
            //Новый список скидок
            $otapilib->AddDiscountGroupToInstance($sid, $xml);
        }
        catch(ServiceException $e){
            Session::setError($e->getMessage(), $e->getErrorCode());
        }
        catch(Exception $e){
            Session::setError($e->getMessage(), $e->getCode());
        }

        header('Location: index.php?cmd=discount');
    }

    public function DelDiscountAction()
    {
		global $otapilib;
       			
		try{	
		    $this->checkAuth();
			$otapilib->setErrorsAsExceptionsOn();				
			//Удаляем список скидок	
			$sid = Session::get('sid');				
    		$otapilib->RemoveDiscountGroupFromInstance($sid,$_GET['d_group']);				
    			
		}			
		catch(ServiceException $e){
    		Session::setError($e->getMessage(), $e->getErrorCode());
		}
		catch(Exception $e){
   			Session::setError($e->getMessage(), $e->getCode());
		}
		
		header('Location: ?cmd=discount');        
    }

    public function UpdateDiscountAction()
    {
					
		global $otapilib;
       			
		try{
			$this->checkAuth();
			$otapilib->setErrorsAsExceptionsOn();
			$sid = Session::get('sid');			
			//Изменяем список скидок									
    		$xml = $this->generateUpdateDiscountXML($_POST); 				
    		$otapilib->UpdateDiscountGroup($sid, $_POST['id'],$xml);
		}
		catch(ServiceException $e){
    		Session::setError($e->getMessage(), $e->getErrorCode());
		}
		catch(Exception $e){
   			Session::setError($e->getMessage(), $e->getCode());
		}
			
		header('Location: ?cmd=discount');
        
    }
	
//=======================================  Работа со юзерами ============================================	

    public function DelUserFromDiscountAction()
    {
		
        global $otapilib;       			
		try{			
			$this->checkAuth();
			$otapilib->setErrorsAsExceptionsOn();
			//Удалаяем юзера из списка	
		 	$sid = Session::get('sid');			
    		$otapilib->RemoveUserFromDiscountGroup($sid,$_GET['d_group'],$_GET['user_id']);
		}			
		catch(ServiceException $e){
    		Session::setError($e->getMessage(), $e->getErrorCode());
		}
		catch(Exception $e){
   			Session::setError($e->getMessage(), $e->getCode());
		}
		
		header('Location: ?cmd=discount&d_group='.$_GET['d_group']);
        
    }


    public function AddUserToDiscountAction()
    {		
        global $otapilib;       			
		try{			
			$this->checkAuth();
			$otapilib->setErrorsAsExceptionsOn();
			//Делаем юзеру приятно	
		 	$sid = Session::get('sid');				
    		$otapilib->AddUserToDiscountGroup($sid,$_GET['d_group'],$_POST['userid']);
			
		}			
		catch(ServiceException $e){
    		Session::setError($e->getMessage(), $e->getErrorCode());
		}
		catch(Exception $e){
   			Session::setError($e->getMessage(), $e->getCode());
		}
		
	    header('Location: ?cmd=discount&d_group='.$_GET['d_group']);
    }
	
	public function ReplaceUserAction()
    {		
        global $otapilib;       			
		try{			
			$this->checkAuth();
			$otapilib->setErrorsAsExceptionsOn();
			//Делаем юзеру приятно	
		 	$sid = Session::get('sid');				
    		$otapilib->AddUserToDiscountGroup($sid,$_POST['to_group'],$_POST['id_prelace']);
			
		}			
		catch(ServiceException $e){
    		Session::setError($e->getMessage(), $e->getErrorCode());
		}
		catch(Exception $e){
   			Session::setError($e->getMessage(), $e->getCode());
		}		
	    header('Location: ?cmd=discount&d_group='.$_POST['to_group']);
    }
	
	
	function GetUserAction()
    {
        global $otapilib;       			
		try{			
			$this->checkAuth();
			$otapilib->setErrorsAsExceptionsOn();
			//Удалаяем юзера из списка	
		 	$sid = Session::get('sid');
			//$users = $otapilib->FindBaseUserInfoListFrame($sid, $filters, $from, $perpage);
			$filters = str_replace('<?xml version="1.0"?>', '', $this->_generateFilters());		
			
			$users = $otapilib->FindBaseUserInfoListFrame($sid, $filters,0, 50); 
			$key = false;
			$usersShort = array();	
			
			foreach ($users['content'] as $k => $user) {
				if ($user['Login'] == @htmlspecialchars($_POST['nme'])) {
					$key = $k;
					$tmp['Id'] = $user['Id'];
				    $tmp['Login'] = $user['Login'];
				    $usersShort[] = $tmp;
					break;
				}
			}
								
			
			foreach ($users['content'] as $k => $user) {
				if ($k == $key) {
					continue;
				}
				if ($k == 20) {
					break;
				}
				$usersShort[] = $user;
			}
			$usrs="<ul id=\"scr\" >";
        	foreach ($usersShort as $item) {
            	$usrs.="<li onclick=\"SetUser('{$item['Id']}','{$item['Login']}')\"><a onclick=\"SetUser('{$item['Id']}','{$item['Login']}')\">".$item['Login']."</a></li>";
        	}
        	$usrs.="</ul>";
        	print $usrs;
			
		}			
		catch(ServiceException $e){
    		Session::setError($e->getMessage(), $e->getErrorCode());
		}
		catch(Exception $e){
   			Session::setError($e->getMessage(), $e->getCode());
		}
		die();
		
		
		
    }
	
    /**
     * Генерирование xml для добавления группы скидок
     * @param $data     Передаваемые параметры
     * @return string   Сгенерированная xml строка
     */
    private function generateAddDiscountXML($data){		
		//$data['Name'] = iconv('cp1251', 'UTF-8', $data['Name']);
		//$data['Description'] = iconv('cp1251', 'UTF-8', $data['Description']);
		//$data['Percent'] = iconv('cp1251', 'UTF-8', $data['Percent']);
		//$data['PurchaseVolume'] = iconv('cp1251', 'UTF-8', $data['PurchaseVolume']);	
		
		
        $xmlParams = new SimpleXMLElement('<DiscountGroupAddData></DiscountGroupAddData>');
        $xmlParams->addChild('Name', htmlspecialchars($data['Name']));
        $xmlParams->addChild('Description', htmlspecialchars($data['Description']));

        $el = $xmlParams->addChild('Discount');
        $el->addChild('Percent', htmlspecialchars($data['Percent']));

        $el = $xmlParams->addChild('DiscountIdentificationParametr');
        $el->addChild('PurchaseVolume', htmlspecialchars($data['PurchaseVolume']));

        return $xmlParams->asXML();
    }
	
	private function generateUpdateDiscountXML($data){
        $xmlParams = new SimpleXMLElement('<DiscountGroupUpdateData></DiscountGroupUpdateData>');
        $xmlParams->addChild('Name', htmlspecialchars($data['Name']));
        $xmlParams->addChild('Description', htmlspecialchars($data['Description']));

        $el = $xmlParams->addChild('Discount');
        $el->addChild('Percent', htmlspecialchars($data['Percent']));

        $el = $xmlParams->addChild('DiscountIdentificationParametr');
        $el->addChild('PurchaseVolume', htmlspecialchars($data['PurchaseVolume']));

        return $xmlParams->asXML();
    }
	
	private function _generateFilters()
    {
        $xmlParams = new SimpleXMLElement('<UserFilterParameters></UserFilterParameters>');
        $xmlParams->addChild('Login', @htmlspecialchars($_POST['nme']));        
        return str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
    }

}