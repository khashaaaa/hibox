<?php

class Profile extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'profile'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/users/';

    const DEFAULT_USER_PROFILE_COUNT = 3; 

    public function __construct()
    {
        parent::__construct(true);
    }

    private function xmlParams($fields){
        $xml = new SimpleXMLElement('<UserUpdateData></UserUpdateData>');
        $xml->addChild('Email', htmlspecialchars($fields['Email']));
        $xml->addChild('FirstName', htmlspecialchars($fields['FirstName']));
        $xml->addChild('LastName', htmlspecialchars($fields['LastName']));
        if (InstanceProvider::getObject()->GetProfileFieldState('MiddleName') !== "Disabled") {
            $xml->addChild('MiddleName', htmlspecialchars($fields['MiddleName']));
        }
        $xml->addChild('Skype', htmlspecialchars($fields['Skype']));
        $xml->addChild('Sex', htmlspecialchars($fields['Sex']));
        $xml->addChild('Phone', htmlspecialchars($fields['Phone']));

        return $xml->asXML();
    }

    public function xmlParamsDeliveryProfile($fields, $new = true){
        if ($new) {
            $xml = new SimpleXMLElement('<UserProfileCreateData></UserProfileCreateData>');
        } else {
            $xml = new SimpleXMLElement('<UserProfileUpdateData></UserProfileUpdateData>');
        }

        $xml->addChild('FirstName', htmlspecialchars($fields['FirstName']));
        $xml->addChild('LastName', htmlspecialchars($fields['LastName']));
        $xml->addChild('EnableValidation', true);
        if (isset($fields['MiddleName'])) {
            $xml->addChild('MiddleName', htmlspecialchars($fields['MiddleName']));
        }
        if (isset($fields['ExternalDeliveryId'])) {
            $xml->addChild('ExternalDeliveryId', htmlspecialchars($fields['ExternalDeliveryId']));
        } else {
            $xml->addChild('ExternalDeliveryId', '');
        }
        if (isset($fields['PickupPointCode'])) {
            $xml->addChild('PickupPointCode', htmlspecialchars($fields['PickupPointCode']));
        } else {
            $xml->addChild('PickupPointCode', '');
        }
        if (isset($fields['CountryCode'])) {
            $xml->addChild('CountryCode', htmlspecialchars($fields['CountryCode']));
        }
        if (isset($fields['CityCode'])) {
            $xml->addChild('CityCode', htmlspecialchars($fields['CityCode']));
        }
        $xml->addChild('City', htmlspecialchars($fields['City']));

        $xml->addChild('Address', htmlspecialchars($fields['Address']));

        $xml->addChild('Phone', htmlspecialchars($fields['Phone']));
        $xml->addChild('PostalCode', $fields['PostalCode'] ? htmlspecialchars($fields['PostalCode']) : '000000');
        $xml->addChild('Region', htmlspecialchars($fields['Region']));

        if (in_array('PassportData', General::$enabledFeatures)) {
            if (isset($fields['PassportNumber'])) $xml->addChild('PassportNumber', htmlspecialchars($fields['PassportNumber']));
            if (isset($fields['RegistrationAddress'])) $xml->addChild('RegistrationAddress', htmlspecialchars($fields['RegistrationAddress']));
        }
        if (isset($fields['PassportIssueDate']) && !empty($fields['PassportIssueDate'])) {
            $xml->addChild('PassportIssueDate', htmlspecialchars($fields['PassportIssueDate']));
        }

        $xml->addChild('INN', htmlspecialchars($fields['INN']));

        return $xml->asXML();
    }

    private function validateFields($fields){
        if(!$fields['Phone'])
            return array(false, Lang::get('not_entered_required_data'));
        if(!filter_var($fields['Email'], FILTER_VALIDATE_EMAIL))
            return array(false, Lang::get('incorrect_email'));
        return false;
    }


    private function validateFieldsForDelivery($fields){

        $fields = $fields['Profile'];

        if(!$fields['FirstName'])
            return array(false, Lang::get('not_entered_required_data'));
        if(!$fields['LastName'])
            return array(false, Lang::get('not_entered_required_data'));
        //if(!$fields['MiddleName'])
        //    return array(false, Lang::get('not_entered_required_data'));
        if(!$fields['City'])
            return array(false, Lang::get('not_entered_required_data'));
        if(!$fields['Address'])
            return array(false, Lang::get('not_entered_required_data'));
        if (!$fields['PostalCode'] && InstanceProvider::getObject()->GetProfileFieldState('PostalCode') !== "Disabled") {
            return array(false, Lang::get('not_entered_required_data'));
        }

        return false;
    }

    private function save($fields){
        global $otapilib;
        $otapilib->setErrorsAsExceptionsOn();
        try{
            $error = $this->validateFields($fields);
            if($error) return $error;

            $xmlParams = str_replace('<?xml version="1.0"?>', '', $this->xmlParams($fields));
            $sid = Session::getUserSession();

            $reg = $otapilib->UpdateUser($sid, $xmlParams);
            return array(true, Lang::get('data_updated_successfully'));
        }
        catch(ServiceException $e){
            Session::setError($e->getMessage(), $e->getErrorCode());
            return array(false, $e->getMessage());
        }
        catch(Exception $e){
            Session::setError($e->getMessage(), $e->getCode());
        }
    }

    public function saveDeliveryProfile($fields){
        global $otapilib;
        $otapilib->setErrorsAsExceptionsOn();

        try {

            $xmlParams = str_replace('<?xml version="1.0"?>', '', $this->xmlParamsDeliveryProfile($fields['Profile'], false));
            $sid = Session::getUserSession();

            $r = $otapilib->UpdateUserProfile($sid, $fields['Profile']['Id'], $xmlParams);

            return array(true, Lang::get('data_updated_successfully'));
        } catch(ServiceException $e) {
            Session::setError($e->getMessage(), $e->getErrorCode());
            return array(false, $e->getMessage());
        } catch(Exception $e){
            Session::setError($e->getMessage(), $e->getCode());
            return array(false, $e->getMessage());
        }
    }

    private function changePassword($fields) {
        global $otapilib;

        if($fields['newPassword'] != $fields['newPasswordConfirm']) {
            return array(false, Lang::get('confirm_pass_mismatch'));
        }

        if(strlen($fields['newPassword']) < 6) {
            return array(false, Lang::get('min_pass_length'));
        }
        
        $sid = Session::getUserSession();
        $this->otapilib->setErrorsAsExceptionsOn();

        try {
            $this->otapilib->ChangePassword($sid, $fields['currentPassword'], $fields['newPassword']);
        }
        catch(ServiceException $e) {
            return array(false, $e->getErrorMessage());
        }
        catch(Exception $e){
            return array(false, $e->getMessage());
        }

        return array(true, '');
    }

    private function changeEmail($fields) {

        if (! $fields['newEmail']) {
            return [false, Lang::get('email_incorrect')];
        }

        try {
            $sid = Session::getUserSession();

            $this->otapilib->setErrorsAsExceptionsOn();
            $this->otapilib->ChangeEmail($sid, $fields['password'], $fields['newEmail']);
        } catch (ServiceException $e) {
            return array(false, $e->getErrorMessage());
        } catch (Exception $e){
             return array(false, $e->getMessage());
        }

        return array(true, '');
    }

    private function changePhone($fields)
    {
        try {
            if (! $fields['newPhone'] || empty($fields['newPhone'])) {
                throw new Exception(Lang::get('Phone_has_to_be_filled'));
            }

            $lang = Session::getActiveLang();
            $sid = User::getObject()->getSid();

            $this->otapilib->setErrorsAsExceptionsOn();
            OTAPILib2::ChangePhone($lang, $sid, $fields['password'], $fields['newPhone'], $answer);
            OTAPILib2::makeRequests();

            $this->sendAjaxResponse([
                'isPhoneVerificationUsed' => $answer->GetEmailConfirmationInfo()->IsPhoneVerificationUsed(),
                'redirectUrl' => '/?p=profile&success_phone'
            ]);
        } catch (Exception $e) {
            $this->sendAjaxResponse(['error' => $e->getMessage()]);
        }
    }

    private function confirmPhone($fields) {
        try {
            if (! $fields['confirmation_code'] || empty($fields['confirmation_code'])) {
                throw new Exception(Lang::get('Code_has_to_be_filled'));
            }

            $lang = Session::getActiveLang();
            $sid = User::getObject()->getSid();
            $code = $fields['confirmation_code'];

            OTAPILib2::ConfirmPhone($lang, $sid, $code, $answer);
            OTAPILib2::makeRequests();

            $this->sendAjaxResponse([
                'redirectUrl' => '/?p=profile&success_phone'
            ]);
        } catch (Exception $e) {
            $this->sendAjaxResponse(['error' => $e->getMessage()]);
        }
    }

    public function addNewDeliveryProfile($params) {
        global $otapilib;

        $otapilib->setErrorsAsExceptionsOn();

        try {
            $sid = Session::getUserSession();
            $xmlParams = str_replace('<?xml version="1.0"?>', '', $this->xmlParamsDeliveryProfile($_POST['Profile']));
            $otapilib->CreateUserProfile($sid, $xmlParams);
            header('Location: /?p=profile&mode=delivery&success');
        } catch(ServiceException $e) {
            Session::setError($e->getMessage(), $e->getErrorCode());
            header('Location: /?p=profile&new_profile&error=' . $e->getMessage());
        } catch(Exception $e){
            Session::setError($e->getMessage(), $e->getErrorCode());
            header('Location: /?p=profile&new_profile&error=' . $e->getMessage());
        }
    }
    
    protected function setVars()
    {
        global $otapilib;

        if (! Session::isAuthenticated()) {
            header('Location: ' . UrlGenerator::toRoute('login'));
            die();
        }

        $error = '';
        $sid = Session::getUserSession();
        $userData = $otapilib->GetUserInfo($sid);

        $instanceOptionsInfo = $this->otapilib->GetInstanceOptionsInfo(Session::get('sid'));
        $IsEmailConfirmationUsed = false;

        if(isset($_GET['success_email']))
            $IsEmailConfirmationUsed = $instanceOptionsInfo['IsEmailConfirmationUsed'];

        $this->tpl->assign('IsEmailConfirmationUsed', $IsEmailConfirmationUsed);

        $countries = InstanceProvider::getObject()->getDeliveryCountryInfoList();
        $countries = $this->otapilib->GetDeliveryCountryInfoList($countries->asXML());
        $this->tpl->assign('countries', $countries);

        $options = $otapilib->GetCommonInstanceOptionsInfo();
        $this->tpl->assign('options', $options);

        /** добавление нового профиля доставки */
        if(isset($_GET['new_profile']) && $_POST) {
            $this->addNewDeliveryProfile($_POST);
            return;
        }

        $profiles = $otapilib->GetUserProfileInfoList($sid);
        $this->tpl->assign('profiles', $profiles);

        if(isset($_GET['success_password']))
            $this->tpl->assign('success', Lang::get('password_successfully_changed'));
        if(isset($_GET['success_update']))
            $this->tpl->assign('success', Lang::get('data_updated_successfully'));

        $new_profile = isset($_GET['new_profile']) ? true : false;
        $this->tpl->assign('new_profile', $new_profile);

        if ($new_profile) {
            $this->tpl->assign('error', isset($_GET['error']) ? $_GET['error'] : false);
            $this->_template = 'new_profile';
            return;
        }
        if (isset($_GET['mode']) && $_GET['mode'] == 'delivery') {
            if (! isset($options['userprofile']) or ! $options['userprofile']) {
                $options['userprofile'] = self::DEFAULT_USER_PROFILE_COUNT;
            }
            $this->tpl->assign('commonSettings', $options);
            $this->tpl->assign('error', isset($_GET['error']) ? $_GET['error'] : false);
            $this->_template = 'delivery';
            return;
        }
        foreach($userData as $k=>$v){
            $this->tpl->assign($k, $v);
        }

        if(@$_POST['change_email']) {
            list($success, $error) = $this->changeEmail($_POST);

            if (! $success) {
                $this->tpl->assign('error_email', $error);
            } else {
                header('Location: /?p=profile&success_email');
            }
        } elseif (@$_POST['change_phone']) {
            $this->changePhone($_POST);
        } elseif (@$_POST['confirm_phone']) {
            $this->confirmPhone($_POST);
        } elseif (@$_POST['change_password']) {
            list($success, $error) = $this->changePassword($_POST);

            if (! $success) {
                $this->tpl->assign('error_password', $error);
            } else {
                header('Location: /?p=profile&success_password');
            }
        } elseif ($_POST) {
            $delivery = false;
            if (isset($_POST['save_delivery_profile'])) {
                /** Редактирование/сохранение профиля доставки */
                list($success, $error) = $this->saveDeliveryProfile($_POST);
                $delivery = true;
            } else {
                list($success, $error) = $this->save($_POST);
            }

            if (!$success) {
                foreach ($userData as $k => $v) {
                    if (isset($_POST[$k])) $this->tpl->assign($k, $_POST[$k]);
                }
                $this->tpl->assign('error', $error);
            } else {
                if ($delivery) {
                    header('Location: /?p=profile&mode=delivery&success_update');
                } else {
                    header('Location: /?p=profile&success_update');
                }
            }
        }
    }

    public function DeleteDeliveryProfileAction($request){
        global $otapilib;
        $id = $request->getValue('id');
        $result = array("success" => true);

        $otapilib->setErrorsAsExceptionsOn();

        try{
            $r = $otapilib->DeleteUserProfile(Session::getUserSession(), $id);
            echo json_encode($result);
        } catch(ServiceException $e){
            Session::setError($e->getMessage(), 'account_delete_error');
            echo json_encode(array('success' => false, 'message' => $e->getMessage()));
        } catch(Exception $e){
            Session::setError($e->getMessage(), 'account_delete_error');
            echo json_encode(array('success' => false, 'message' => $e->getMessage()));
        }
    }

    /**
     * @param RequestWrapper $request
     */
    public function deleteAccountAction($request)
    {
        $this->otapilib->setErrorsAsExceptionsOn();
        try{
            OTBase::import('system.lib.Validation.*');
            OTBase::import('system.lib.Validation.Rules.*');
            $V = new Validator(array(
                'password' => $request->getValue('password')
            ));
            $V->addRule(new NotEmptyString(), 'password', Lang::get('Password_has_to_be_filled'));
            if (! $V->validate()) {
                $errors = $V->getErrors();
                $error = $errors[0];
                throw new Exception($error->message);
            }

            $this->otapilib->DeleteAccount(Session::getUserSession(), $request->getValue('password'));
            Users::Logout();
            $request->LocationRedirect('/');
        }
        catch(ServiceException $e){
            Session::setError($e->getErrorMessage(), 'account_delete_error');
            $request->RedirectToReferrer();
        }
        catch(Exception $e){
            Session::setError($e->getMessage(), 'account_delete_error');
            $request->RedirectToReferrer();
        }
    }
}

?>