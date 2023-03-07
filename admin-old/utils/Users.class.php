<?php

class Users extends GeneralUtil
{
    public $error = '';

       

    static function sendEmail2($params) {
		General::mail_utf8(
			$params['email'],
			CFG_SITE_NAME,
			str_replace(':8080', '', 'noreply@'.preg_replace('/^www\./','',$_SERVER['HTTP_HOST'])),
			$params['subject'],
			$params['message']
		);        
    }


    function defaultAction()
    {
        if (Login::auth()) {
            global $otapilib;
            $sid = Session::get('sid');

            $filters = str_replace('<?xml version="1.0"?>', '', $this->_generateFilters());

            $perpage = RequestWrapper::get('ps') ? RequestWrapper::get('ps') : 10;
            $a_page = isset($_SESSION['admin_user_page']) ? $_SESSION['admin_user_page'] : 1;
            $from = RequestWrapper::get('page') ? RequestWrapper::get('page') : $a_page;
            $from = ($from > 1) ? ($from-1) * $perpage : 0;

            if ($filters === false) {
                $users = array();
            } else {
                $users = $otapilib->FindBaseUserInfoListFrame($sid, $filters, $from, $perpage);
                if (defined('CFG_PROTECT_USERS_EMAIL') && CFG_PROTECT_USERS_EMAIL) {
                    $usersNew = $users;
                    $usersNew['content'] = $usersNew['Content'] = array();
                    foreach ($users['Content'] as $user) {
                        if ($user['Login'] == Session::getUserData('username')) {
                            $usersNew['Content'][] = $user;
                        }
                    }
                    $usersNew['content'] = $usersNew['Content'];
                    $users = $usersNew;
                }
            }
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }
            $pageurl = $this->_getPageURL();

            include(TPL_DIR . 'users/users.php');
        } else {
            include(TPL_DIR . 'login.php');
        }
    }
	
	function exportusersAction()
    {
        if (Login::auth()) {
			$fileName='users';
            global $otapilib;
            $sid = Session::get('sid');
			
			$xmlParams = new SimpleXMLElement('<UserFilterParameters></UserFilterParameters>');
            if (defined('CFG_PROTECT_USERS_EMAIL') && CFG_PROTECT_USERS_EMAIL) {
                $xmlParams->Login = Session::getUserData('username');
            }
            $filters = str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
			
			$users_all = $otapilib->FindBaseUserInfoListFrame($sid, $filters, RequestWrapper::get('from'), 100);
			if (RequestWrapper::get('from')!=0) {
				$xml = simplexml_load_file('../cache/'.$fileName.'.xml');
			} else {
				//if (file_exists('../cache/'.$fileName.'.xml')) unlink('../cache/'.$fileName.'.xml');				
				$xml = new SimpleXMLElement('<translations/>');
			}
			
				foreach($users_all['Content'] as $t){
					$el = $xml->addChild('key', htmlspecialchars($t['login']));
					$el->addAttribute('email', htmlspecialchars($t['email']));
					$el->addAttribute('id', htmlspecialchars($t['id']));
				}
					
			$dom = dom_import_simplexml($xml)->ownerDocument;
			$dom->formatOutput = true;
			file_put_contents('../cache/'.$fileName.'.xml',$dom->saveXML());
			die();
			
        } else {
            include(TPL_DIR . 'login.php');
        }
    }
	
	function exportusersfileAction()
    {
        if (Login::auth()) {
          
			$fileName='users';
			
			$file = '../cache/'.$fileName.'.xml';
			header ("Content-Type: application/octet-stream");
			header ("Accept-Ranges: bytes");
			header ("Content-Length: ".filesize($file));
			header ("Content-Disposition: attachment; filename=".$fileName.".xml");
			readfile($file);
			//unlink($file);
			die();
        } else {
            include(TPL_DIR . 'login.php');
        }
    }
	
    function userinfoAction()
    {
        if (Login::auth()) {
            global $otapilib;
            $sid = Session::get('sid');
            $userid = RequestWrapper::get('id');
            $error = '';
            //$result = $otapilib->GetWebUISettings($sid);

            $xml = '<OrderSearchParameters><UserIdDelimitedList>'.(int)$userid.'</UserIdDelimitedList></OrderSearchParameters>';

            if (CFG_MULTI_CURL)
            {
                // Инициализируем
                $otapilib->InitMulti();
                $user = $otapilib->GetUserInfoForOperator($sid, $userid);
                $user_account = $otapilib->GetAccountInfoForOperator($sid, $userid);
                $all_orders = $otapilib->SearchOrders($sid, $xml,0,1000);
                //$all_orders = $otapilib->GetSalesOrdersListForOperator($sid, '');
                $moneyhistory = $otapilib->GetStatementForOperator($sid, $userid, '', '');
                $status_list = $otapilib->GetOrderStatusList($sid);
                $profiles = $otapilib->GetUserProfileInfoListForOperator($sid, $userid);
                $countries = InstanceProvider::getObject()->getDeliveryCountryInfoList();
                $countries = $otapilib->GetDeliveryCountryInfoList($countries->asXML());
                // Делаем запросы
                $otapilib->MultiDo();
                $user = $otapilib->GetUserInfoForOperator($sid, $userid);
                if ($otapilib->error_message == 'SessionExpired') {
                    header('Location: index.php?expired');
                    die;
                }
                if (!$user) $error = $otapilib->error_message;
                $user_account = $otapilib->GetAccountInfoForOperator($sid, $userid);
                if (!$user_account) $error .= $otapilib->error_message;
                // Получение заказов пользователя.
                $all_orders = $otapilib->SearchOrders($sid, $xml,0,1000);
                if (!$all_orders) $error .= $otapilib->error_message;
                $moneyhistory = $otapilib->GetStatementForOperator($sid, $userid, '', '');

                $status_list = $otapilib->GetOrderStatusList($sid);
                if (!$status_list) $error .= $otapilib->error_message;
                
                $profiles = $otapilib->GetUserProfileInfoListForOperator($sid, $userid);
                if (!$profiles) $error .= $otapilib->error_message;

                $countries = InstanceProvider::getObject()->getDeliveryCountryInfoList();
                $countries = $otapilib->GetDeliveryCountryInfoList($countries->asXML());
                if (!$countries) $error .= $otapilib->error_message;
                
                // Сбрасываем
                $otapilib->StopMulti();
            } else {
                $user = $otapilib->GetUserInfoForOperator($sid, $userid);
                if ($otapilib->error_message == 'SessionExpired') {
                    header('Location: index.php?expired');
                    die;
                }
                if (!$user) $error = $otapilib->error_message;
                $user_account = $otapilib->GetAccountInfoForOperator($sid, $userid);
                if (!$user_account) $error .= $otapilib->error_message;
                // Получение заказов пользователя.
                $all_orders = $otapilib->SearchOrders($sid, $xml,0,1000);
                if (!$all_orders) $error .= $otapilib->error_message;
                $moneyhistory = $otapilib->GetStatementForOperator($sid, $userid, '', '');

                $status_list = $otapilib->GetOrderStatusList($sid);
                if (!$status_list) $error .= $otapilib->error_message;
                
                $profiles = $otapilib->GetUserProfileInfoListForOperator($sid, $userid);
                if (!$profiles) $error .= $otapilib->error_message;

                $countries = InstanceProvider::getObject()->getDeliveryCountryInfoList();
                $countries = $otapilib->GetDeliveryCountryInfoList($countries->asXML());
                if (!$countries) $error .= $otapilib->error_message;
            }

            $all_orders = $all_orders['Content'];
			$usedLanguages = $this->getActiveLanguages();
			
			
            $user_orders = array();
            if ($all_orders) {
                foreach ($all_orders as $order) {
                    if($order['custid'] == (int)$user['id']) {
                        $user_orders[] = $order;
                    }
                }
            }

            include(TPL_DIR . 'users/userinfo.php');
        } else {
            include(TPL_DIR . 'login.php');
        }
    }
    public function savePasswordAction()
    {
        global $otapilib;
        $sid = @Session::get('sid');
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired');
            die;
        }
        $cms = new CMS();
        $status = $cms->Check();
        if ($status)
        {
            if (!RequestWrapper::post('Password') || strlen(trim(RequestWrapper::post('Password')))<6){
                echo LangAdmin::get('password_min_length_6');
                die();
            }
//          $otapilib->setErrorsAsExceptionsOn();
            try {
                $user = $otapilib->GetUserInfoForOperator($sid, intval(RequestWrapper::post('Id')));
                foreach ($user as $key=>$field){
                    $_POST[$key] = $field;
                }
                $fields = str_replace('<?xml version="1.0"?>', '', $this->_generateUserFields());
                $result = $otapilib->EditUser($sid, $fields);
                if ($result){
                    $newPass = trim(RequestWrapper::post('Password'));
                    if (file_exists(TPLCUSTOM_DIR.'users/recovery_email.php'))
                        include(TPLCUSTOM_ABSOLUTE_PATH.'users/recovery_email.php');
                    else
                        include(TPL_ABSOLUTE_PATH.'users/recovery_email.php');
                    $params['message'] = $message;
                    $params['email'] = $user['Email'];
                    $params['subject'] = LangAdmin::get('pass_recovery');
                    $this->sendEmail2($params);
                }
            } catch (Exception $e) {
                echo LangAdmin::get('savePassError');
                die();
            }

            echo 'Ok';
        }
        die;
    }

    function authAction()
    {
        global $otapilib;
        $sid = Session::get('sid');
        $login = RequestWrapper::post('login');
        @define('NO_DEBUG', true);

        $auth = $otapilib->AuthenticateAsUser($sid, $login);

        if ($otapilib->error_message == 'SessionExpired') {
            print 'RELOGIN';
            die;
        }
        if (!$auth) {
            print $otapilib->error_message;
        } else {
            $userinfo = array(
                'sid' => $auth,
                'username' => $login,
                'IsAuthenticated' => true
            );
            Session::setUserData($userinfo);
            print 'Ok';
        }
    }

    public function activateAction()
    {
        global $otapilib;
        $sid = Session::get('sid');
        $userId = RequestWrapper::get('userId');
        @define('NO_DEBUG', true);

        if ($otapilib->error_message == 'SessionExpired') {
            print 'RELOGIN';
            die;
        }

        $user = $otapilib->GetUserInfoForOperator($sid, $userId);
        $code = RequestWrapper::post('EmailConfirmationCode');
        if (empty($user)) {
            print 'USER_NOT_FOUND';
            die;
        }
        if (isset($user['IsEmailVerified']) && 'false' !== $user['IsEmailVerified']) {
            print 'ALREADY_VERIFIED';
            die;
        }
        if (empty($user['EmailConfirmationCode']) || $user['EmailConfirmationCode'] != $code) {
            print 'INCORRECT_CODE';
            die;
        }

        $result = $otapilib->ConfirmEmail(User::getObject()->getSid(), $code);
        if (empty($result)) {
            print $otapilib->error_message;
            die;
        }

        print 'Ok';
    }

    function usercreateAction()
    {
        if (Login::auth()) {
            global $otapilib;
            $sid = Session::get('sid');
            $userid = RequestWrapper::get('id');

            $result = $otapilib->GetWebUISettings($sid);
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }

            include(TPL_DIR . 'users/usercreate.php');
        } else {
            include(TPL_DIR . 'login.php');
        }
    }
    
    function deleteDeliveryProfileAction() {
        global $otapilib;
        $sid = Session::get('sid');
        $userId = RequestWrapper::get('userId');
        $profileId = RequestWrapper::get('profileId');
        @define('NO_DEBUG', true);

        if ($otapilib->error_message == 'SessionExpired') {
            print 'RELOGIN';
            die;
        }
        
        $result = $otapilib->DeleteUserProfileForOperator($sid, $userId, $profileId);
        if (empty($result)) {
            print $otapilib->error_message;
            die;
        }

        print 'Ok';
    }
    
    function editDeliveryProfileAction() {
        global $otapilib;
        $sid = Session::get('sid');
        @define('NO_DEBUG', true);

        if ($otapilib->error_message == 'SessionExpired') {
            print json_encode(array('success' => false, 'message' => 'RELOGIN'));
            die;
        }
        
        $profile = $_POST['Profile'];
        $is_new = isset($profile['Id']) ? false : true;
        
        $xmlData = $this->_generateDeliveryProfile($profile, $is_new);

        if ($is_new) {
            $result = $otapilib->CreateUserProfileForOperator($sid, $profile['UserId'], $xmlData);    
        } else {
            $result = $otapilib->UpdateUserProfileForOperator($sid, $profile['UserId'], $profile['Id'], $xmlData);    
        }
        
        if (empty($result)) {
            print json_encode(array('success' => false, 'message' => $otapilib->error_message));
            die;
        }
        
        print json_encode(array('success' => true));
    }

    function usereditAction()
    {
        if (Login::auth()) {
            global $otapilib;
            $error = '';
            $sid = Session::get('sid');
            $userid = RequestWrapper::get('id');

            $result = $otapilib->GetWebUISettings($sid);
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }

            $user = $otapilib->GetUserInfoForOperator($sid, $userid);
            if (!$user) $error = $otapilib->error_message;
            
            $profiles = $otapilib->GetUserProfileInfoListForOperator($sid, $userid);
            if (!$profiles) $error .= $otapilib->error_message;

            $countries = InstanceProvider::getObject()->getDeliveryCountryInfoList();
            $countries = $otapilib->GetDeliveryCountryInfoList($countries->asXML());

            include(TPL_DIR . 'users/useredit.php');
        } else {
            include(TPL_DIR . 'login.php');
        }
    }

    /**
     * @param RequestWrapper $request
     */
    function saveuserAction($request)
    {
        if (Login::auth()) {
            global $otapilib;
            $sid = Session::get('sid');

            $otapilib->setErrorsAsExceptionsOn();
            $error = false;
            try {
                $fields = str_replace('<?xml version="1.0"?>', '', $this->_generateUserFields());

                if (RequestWrapper::post('Id')) {
                    $userid = RequestWrapper::post('Id');
                    $result = $otapilib->EditUser($sid, $fields);
                    Plugins::invokeEvent('onEditUser', array('user' => $_POST));
                } else {
                    $userid = $otapilib->AddUser($sid, $fields);
                    if ($userid !== false) {
                        Plugins::invokeEvent('onAddUser', array('user' => $_POST, 'newUserId' => $userid));
                    }
                }
                if (! $result) $error = $otapilib->error_message;

            } catch (ServiceException $e) {
                $error = $e->getMessage();
            } catch (Exception $e) {
                $error = $e->getMessage();
            }

            if ($error == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }
            if ($error) {
                Session::setError($error);
            } 

            if ($userid) {
                header('Location:index.php?sid=&cmd=users&do=useredit&id=' . $userid);
            } else {
                header('Location:index.php?sid=&cmd=users&do=usercreate');
            }
            
        } else {
            include(TPL_DIR . 'login.php');
        }
    }

    function deleteAction()
    {
        global $otapilib;
        @define('NO_DEBUG', true);
        $sid = Session::get('sid');
        $userid = RequestWrapper::get('id');

        $r = $otapilib->DeleteUser($sid, $userid);

        if (!$r) print $otapilib->error_message;
        else print 'Ok';

        die;
    }

    function userlockAction()
    {
        global $otapilib;
        @define('NO_DEBUG', true);
        $sid = Session::get('sid');
        $userid = RequestWrapper::get('id');

        $r = $otapilib->SetUserBan($sid, $userid, 'true');
        if (!$r) {
            print $otapilib->error_message;
        }
        else print 'Ok';

        die;
    }

    function userunlockAction()
    {
        global $otapilib;
        @define('NO_DEBUG', true);
        $sid = Session::get('sid');
        $userid = RequestWrapper::get('id');

        $r = $otapilib->SetUserBan($sid, $userid, 'false');
        if (!$r) {
            print $otapilib->error_message;
        }
        else print 'Ok';

        die;
    }

    function accountinfoAction()
    {
        if (Login::auth()) {
            global $otapilib;
            $sid = Session::get('sid');
            $userid = RequestWrapper::get('id');
            $error = '';

            $result = $otapilib->GetWebUISettings($sid);
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }

            $user = $otapilib->GetUserInfoForOperator($sid, $userid);
            if (!$user) $error = $otapilib->error_message;

            $user_account = $otapilib->GetAccountInfoForOperator($sid, $userid);
            if (!$user_account) $error .= $otapilib->error_message;

            include(TPL_DIR . 'users/accountinfo.php');
        } else {
            include(TPL_DIR . 'login.php');
        }
    }

    /**
     * @param RequestWrapper $request
     */
    function accountactionAction($request)
    {
       if (Login::auth()) {
            global $otapilib;
            $sid = Session::get('sid');
            $userid = RequestWrapper::get('id');
            $userLogin = RequestWrapper::get('login');
            $error = '';

            $result = $otapilib->GetWebUISettings($sid);
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }

            $r = $otapilib->PostTransaction($sid, $userid, RequestWrapper::post('summa'), RequestWrapper::post('comment'), RequestWrapper::post('isdebit'), date('Y-m-d h-i-s'));
            if (!$r) $error = $otapilib->error_message;

            // del cache AccountInfo for user
            $userData = new UserData();
            $userData->ClearAccountInfoCacheByLogin($userLogin);

            $user_account = $otapilib->GetAccountInfoForOperator($sid, $userid);
            if (!$user_account) $error .= $otapilib->error_message;

           $request->RedirectToReferrer();
        } else {
            include(TPL_DIR . 'login.php');
        }
    }

    private function _generateDeliveryProfile($fields, $new = true){
        if ($new) {
           $xml = new SimpleXMLElement('<UserProfileCreateData></UserProfileCreateData>'); 
        } else {
            $xml = new SimpleXMLElement('<UserProfileUpdateData></UserProfileUpdateData>');
        }
        
        $xml->addChild('FirstName', htmlspecialchars($fields['FirstName']));
        $xml->addChild('LastName', htmlspecialchars($fields['LastName']));
        $xml->addChild('MiddleName', htmlspecialchars($fields['MiddleName']));
        $xml->addChild('CountryCode', htmlspecialchars($fields['CountryCode']));
        $xml->addChild('City', htmlspecialchars($fields['City']));
	$xml->addChild('Address', htmlspecialchars($fields['Address']));
        
        $xml->addChild('Phone', htmlspecialchars($fields['Phone']));
        $xml->addChild('PostalCode', htmlspecialchars($fields['PostalCode']) ? $fields['PostalCode'] : '000000');
        $xml->addChild('Region', htmlspecialchars($fields['Region']));
        if (in_array('PassportData', General::$enabledFeatures)) { 
            $xml->addChild('PassportNumber', htmlspecialchars($fields['PassportNumber']));
            $xml->addChild('RegistrationAddress', htmlspecialchars($fields['RegistrationAddress']));
        }
        return $xml->asXML();
    }
    
    private function _generateFilters()
    {
        $xmlParams = new SimpleXMLElement('<UserFilterParameters></UserFilterParameters>');
        if (RequestWrapper::get('login')) $xmlParams->addChild('Login', @htmlspecialchars(RequestWrapper::get('login')));
        if (RequestWrapper::get('email')) $xmlParams->addChild('Email', RequestWrapper::get('email'));
        if (RequestWrapper::get('lastname')) $xmlParams->addChild('LastName', RequestWrapper::get('lastname'));
        if (RequestWrapper::get('isactive')) $xmlParams->addChild('IsActive', RequestWrapper::get('isactive'));
        if (RequestWrapper::get('phone')) $xmlParams->addChild('Phone', @htmlspecialchars(RequestWrapper::get('phone')));

        if (defined('CFG_PROTECT_USERS_EMAIL') && CFG_PROTECT_USERS_EMAIL && Session::getUserData('username') == null){
            return false;
        } elseif (defined('CFG_PROTECT_USERS_EMAIL') && CFG_PROTECT_USERS_EMAIL) {
            $xmlParams->Login = htmlspecialchars(Session::getUserData('username'));
        }

        if (RequestWrapper::post('sort_by'))
            $xmlParams->addChild('OrderBy', RequestWrapper::post('sort_by'));
        elseif (RequestWrapper::get('sort_by'))
            $xmlParams->addChild('OrderBy', RequestWrapper::get('sort_by'));

        return str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
    }

    private function _generateUserFields()
    {
        $xmlParams = new SimpleXMLElement('<UserUpdateData></UserUpdateData>');
        if (RequestWrapper::post('Id')) $xmlParams->addChild('Id', @htmlspecialchars(RequestWrapper::post('Id')));
        if (RequestWrapper::post('Email')) $xmlParams->addChild('Email', RequestWrapper::post('Email'));
        if (RequestWrapper::post('Login')) $xmlParams->addChild('Login', RequestWrapper::post('Login'));
        if (RequestWrapper::post('Password')) $xmlParams->addChild('Password', trim(RequestWrapper::post('Password')));

        if (RequestWrapper::post('IsActive')) $xmlParams->addChild('IsActive', 'true');
        else $xmlParams->addChild('IsActive', 'false');

        if (RequestWrapper::post('FirstName')) $xmlParams->addChild('FirstName', RequestWrapper::post('FirstName'));
        if (RequestWrapper::post('LastName')) $xmlParams->addChild('LastName', RequestWrapper::post('LastName'));
        if (RequestWrapper::post('MiddleName')) $xmlParams->addChild('MiddleName', RequestWrapper::post('MiddleName'));
        if (RequestWrapper::post('Sex')) $xmlParams->addChild('Sex', RequestWrapper::post('Sex'));

        //if (RequestWrapper::post('Country')) $xmlParams->addChild('Country', htmlspecialchars(RequestWrapper::post('Country')));
        if (RequestWrapper::post('DeliveryCountryCode')) $xmlParams->addChild('DeliveryCountryCode', htmlspecialchars(RequestWrapper::post('DeliveryCountryCode')));
        if (RequestWrapper::post('DeliveryCountryCode')) $xmlParams->addChild('CountryCode', htmlspecialchars(RequestWrapper::post('DeliveryCountryCode')));
        if (RequestWrapper::post('City')) $xmlParams->addChild('City', htmlspecialchars(RequestWrapper::post('City')));
        if (RequestWrapper::post('Address')) $xmlParams->addChild('Address', htmlspecialchars(RequestWrapper::post('Address')));
        if (RequestWrapper::post('Phone')) $xmlParams->addChild('Phone', RequestWrapper::post('Phone'));
        if (RequestWrapper::post('PostalCode')) $xmlParams->addChild('PostalCode', RequestWrapper::post('PostalCode'));
        if (RequestWrapper::post('Region')) $xmlParams->addChild('Region', RequestWrapper::post('Region'));
        if (RequestWrapper::post('Skype')) $xmlParams->addChild('Skype', RequestWrapper::post('Skype'));

        if (RequestWrapper::post('RecipientFirstName')) $xmlParams->addChild('RecipientFirstName', RequestWrapper::post('RecipientFirstName'));
        if (RequestWrapper::post('RecipientLastName')) $xmlParams->addChild('RecipientLastName', RequestWrapper::post('RecipientLastName'));
        if (RequestWrapper::post('RecipientMiddleName')) $xmlParams->addChild('RecipientMiddleName', RequestWrapper::post('RecipientMiddleName'));

        return str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
    }

    private function _getPageURL()
    {
        $pageurl = 'index.php?';

        $params = explode('&', $_SERVER['QUERY_STRING']);

        foreach ($params as $param) {
            @list($key, $value) = explode('=', $param);
            if (in_array($key, array('error', 'success', 'do', 'id'))) continue;

            $pageurl .= "&$key=$value";
        }

        return $pageurl;
    }

    function recoverpasswordAction()
    {
        if (Login::auth()) {
            global $otapilib;
            $sid = Session::get('sid');
            $result = $otapilib->GetWebUISettings($sid);
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }
            $uid = intval(RequestWrapper::get('id'));
            $user = $otapilib->GetUserInfoForOperator($sid, $uid);
            $res = $this->recover($user['login']);
            if($res[0]){
                if (CFG_BUYINCHINA){
                    $newPass = $otapilib->ConfirmPasswordRecovery($res[1]);
                } else {
                    $code = $_SERVER['HTTP_HOST'].'/index.php?p=login&code='.$res[1];
                }
                if (file_exists(TPLCUSTOM_DIR.'users/recovery_email.php'))
                    include(TPLCUSTOM_ABSOLUTE_PATH.'users/recovery_email.php');
                else
                    include(TPL_ABSOLUTE_PATH.'users/recovery_email.php');
                $params['message'] = isset($message) ? $message : '';
                $params['email'] = $res[2];
                $params['subject'] = LangAdmin::get('pass_recovery');
                $this->sendEmail2($params);
            }

            header('Location: index.php?cmd=users&do=userinfo&id='.$uid);
            echo '<script>document.location.href="index.php?cmd=users&do=userinfo&id='.$uid.'";</script>';
            exit;
        } else {
            include(TPL_DIR . 'login.php');
        }
    }
    private function recover($userid){
        global $otapilib;
        $res = $otapilib->RequestPasswordRecovery($userid);
        if(!$res)
            return array(false, Lang::get('user_not_exist'));
        return array(true, $res['ConfirmationCode'], $res['Email']);
    }

}
