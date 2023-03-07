<?php

OTBase::import('system.lib.referral_system.ReferalSystem');
OTBase::import('system.lib.referral_system.lib.*');

class Register extends GenerateBlock
{
    protected $_cache = false;
    protected $_life_time = 3600;
    protected $_template = 'register';
    protected $_template_path = '/users/';

    protected $cms;

    const PASSWORD_MIN_LENGHT = 6;

    public function __construct()
    {
        parent::__construct(true);
        $this->otapilib->setErrorsAsExceptionsOn();
        $this->cms = new CMS();
    }

    private function xmlParams($fields)
    {
        $xml = new SimpleXMLElement('<UserRegistrationData></UserRegistrationData>');
        $xml->addChild('Email', htmlspecialchars($fields['email']));
        $xml->addChild('Password', htmlspecialchars($fields['password']));
        $xml->addChild('Login', htmlspecialchars($fields['username']));

        return $xml->asXML();
    }

    private function validateFields($fields)
    {
        if (! $fields['username']) {
            throw new Exception(Lang::get('not_entered_login'));
        }
        if (! isset($fields['agree'])) {
            throw new Exception(Lang::get('not_agree_with_user_agreement'));
        }
        if (! $fields['email']) {
            throw new Exception(Lang::get('not_entered_email'));
        }
        if (! filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception(Lang::get('incorrect_email'));
        }
        if (! $fields['password']) {
            throw new Exception(Lang::get('not_entered_password'));
        }
        if (strlen($fields['password']) < self::PASSWORD_MIN_LENGHT) {
            throw new Exception(Lang::get('pass_min_len'));
        }
    }

    private function processReg($fields)
    {
        $this->otapilib->setErrorsAsExceptionsOn();
        try {
            $this->validateFields($fields);
            OTCaptcha::validate($fields);
            $xmlParams = str_replace('<?xml version="1.0"?>', '', $this->xmlParams($fields));
            $result = $this->otapilib->RegisterUser(User::getObject()->getSid(), $xmlParams);
            return array(true, $result, Lang::get('reg_success'), '');
        }
        catch (ServiceException $e) {

            if ((string)$e->getErrorCode() == 'AlreadyExists') {
                if (strpos((string)$e->getErrorMessage(), 'Login') !== false) {
                    Session::setError(Lang::get('login_username').' '.$fields['username'].' '.Lang::get('is_used_already'));
                    return array(false, array(), '', '');
                } elseif (strpos($e->getErrorMessage(), 'Email') !== false) {
                    Session::setError(Lang::get('Email').' '.$fields['email'].' '.Lang::get('is_used_already'));
                    return array(false, array(), '', '');
                } else {
                    Session::setError($e->getErrorMessage());
                    return array(false, array(), $e->getErrorMessage(), '');
                }
            } else {
                Session::setError($e->getMessage());
                return array(false, array(), $e->getMessage(), '');
            }
        }
        catch (Exception $e) {
            Session::setError($e->getMessage(), $e->getCode());
            return array(false, array(), $e->getMessage(), '');
        }
    }

    protected function setVars()
    {
        if (RequestWrapper::getValueSafe('activation')) {
            try {
                $result = $this->otapilib->ConfirmEmail(User::getObject()->getSid(), RequestWrapper::getValueSafe('activation'));
                $sid = (string)$result->SessionId->Value;

                if (!empty($sid)) {
                    // Залогиним пользователя
                    $this->authBySessionId($sid);
                    header('Location: /?p=content&mode=reg_success');
                } else {
                    header('Location: /?p=login&reg_success=1');
                }
            } catch (ServiceException $e) {
                Session::setError($e->getErrorMessage(), $e->getErrorCode());
                header('Location: /?p=content&mode=confirm_email_fail');
            }
        }
        if (Session::isAuthenticated()) {
            $this->request->LocationRedirect(UrlGenerator::generatePrivateOfficeUrl());
        }
        
        if ($_POST) {
            list($success, $registerResult, $error, $errorcaptcha) = $this->processReg($_POST);
            if (! $success) {
                $this->tpl->assign('username', RequestWrapper::post('username'));
                $this->tpl->assign('email', RequestWrapper::post('email'));
                $this->tpl->assign('errorcaptcha', $errorcaptcha);
            } else {
                $userInfo = array(
                    'id' => $registerResult['UserId'],
                    'username' => RequestWrapper::post('username'),
                    'email' => RequestWrapper::post('email'),
                    'parent' => RequestWrapper::post('parent'),
                    'parent_id' => RequestWrapper::post('parent_id')
                );
                // Подписка и реферальная система
                $this->_afterRegister($userInfo);

                // отправка писем регистрации/подтверждения и редирект
                if ($registerResult['IsEmailVerificationUsed'] == 'true') {
                    header('Location: /?p=content&mode=need_confirm_email');
                } else {
                    if (!empty($registerResult['SessionId'])) {
                        // Залогиним пользователя
                        $this->authBySessionId($registerResult['SessionId']);
                    }
                    header('Location: /?p=content&mode=reg_success');
                }
            }
        }
	    $cRep = new ContentRepository($this->cms);
        $page = $cRep->GetPageByAlias('terms_of_use');
        $userAgreement = ($page) ? $page['text'] : Lang::get('empty_page_msg');
        $this->tpl->assign('userAgreement', $userAgreement);

        // оставлено для старых кастомных шаблонов, рекапчей теперь занимается класс OTCaptcha
        $this->tpl->assign('hideCaptcha', (defined('CFG_NO_CAPTCHA') || OTBase::isTest()));
    }

    private function authBySessionId($sid)
    {
        // проверяем корректность sid
        $answer = null;
        OTAPILib2::GetUserStatusInfo(Session::getActiveLang(), $sid, $answer);
        OTAPILib2::makeRequests();
        // логиним покупателя
        Users::AutoLogin($sid);
    }

    private function _afterRegister($userInfo)
    {
        if (CMS::IsFeatureEnabled('Newsletter')) {
            Subscribe::SetSubscribe($userInfo['email'], $userInfo['username'], $userInfo['id']);
        }

        if (! empty($userInfo['id'])) {
            if(CMS::IsFeatureEnabled('ReferralProgram')){
                ReferalSystem::onUserRegister($userInfo);
            }
            Plugins::invokeEvent('onUserRegister', array('userInfo' => $userInfo));
        }
    }

}
