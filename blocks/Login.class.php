<?php

class Login extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'login'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/users/';

    protected $max_failed_logins = 5; //- количество неудачных логинов, после которых показываем капчу

    public function __construct()
    {
        parent::__construct(true);
        $this->otapilib->setErrorsAsExceptionsOn();
    }

    private function processLogin()
    {
        if (!$this->request->getValue('username'))
            throw new Exception(Lang::get('not_entered_login'));

        if (!$this->request->getValue('password'))
            throw new Exception(Lang::get('not_entered_password'));

        return Users::Login($this->request->getAll());
    }

    private function checkCaptcha()
    {
        $captchaPath = dirname(dirname(__FILE__)).'/lib/securimage/securimage.php';
        require_once $captchaPath;

        $secureImage = new Securimage();
        return $secureImage->check($this->request->getValue('ct_captcha'));
    }

    protected function setVars()
    {
        $redirectUrl = General::getConfigValue('auth_to_private_office') ? 
                UrlGenerator::generateContentUrl('privateoffice') : UrlGenerator::getHomeUrl();
        if (isset($_SERVER['HTTP_REFERER'])) {
            // если станица вызывается не с сайта
            if (stristr($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME']) === false) {
                if (!Session::get('login_from')) {
                    Session::set('login_from', $redirectUrl);
                }
            } elseif( // если вызов не со страницы авторизации
                stristr($_SERVER['HTTP_REFERER'], '/login') === false &&
                stristr($_SERVER['HTTP_REFERER'], 'p=login') === false
            ) {
                Session::set('login_from', $_SERVER['HTTP_REFERER']);
            }
        }
        if ($this->request->valueExists('referer')) {
            Session::set('login_from', UrlGenerator::generateContentUrl($this->request->getValue('referer')));
        }

        // авторизация через соц. сети
        $this->tpl->assign('emailRequiredForm', false);
        if (CMS::IsFeatureEnabled('ExternalAuthentication')) {
            $this->externalAuthentication();
        }

        if ( $this->request->valueExists('code')) {
            try {
                $this->otapilib->ConfirmPasswordRecovery($this->request->getValue('code'));
                $this->tpl->assign('successRecovery', Lang::get('recovery_data_to_email'));
            } catch(Exception $e) {
                Session::setError(Lang::get('recovery_password_expired'));
            }
            return true;
        }

        // если пользователь уже авторизован
        if (Session::getUserData()) {
            if ($this->request->valueExists('referer')) {
                $this->request->LocationRedirect(UrlGenerator::generateContentUrl($this->request->getValue('referer')));
            }

            Session::set('login_from', Session::get('login_from') ? Session::get('login_from') : $redirectUrl);
            $this->request->LocationRedirect(Session::get('login_from'));
        }

        if (Session::getErrorCode() == 'incorrect_code' || Session::get('failed_logins') > $this->max_failed_logins) {
            $this->tpl->assign('captcha', true);
        }

        if ($this->request->getMethod() == 'POST') {
            if ($this->request->valueExists('ct_captcha') && !$this->checkCaptcha()) {
                Session::setError(Lang::get('incorrect_code'), 'incorrect_code');
                $this->request->LocationRedirect(UrlGenerator::generateContentUrl('login'));
            }

            if ($this->request->valueExists('recovery')) {
                try {
                    $this->tpl->assign('show_recovery', '1');
                    $this->otapilib->RequestPasswordRecovery($this->request->getValue('userid'));
                    Session::set('success_recovery', Lang::get('recovery_sent'));
                } catch(ServiceException $e) {
                    General::sessionExpiredHandle(false);
                    Session::set('error_recovery', Lang::get('user_not_exist'));
                }
                $this->request->LocationRedirect(UrlGenerator::generateContentUrl('login'));
            }

            try {
                $this->processLogin();
                Session::set('failed_logins', 0);
                Session::set('login_from', Session::get('login_from') ? Session::get('login_from') : $redirectUrl);
                $this->request->LocationRedirect(Session::get('login_from'));
            } catch(ServiceException $e) {
                Session::setError($e->getErrorMessage(), $e->getErrorCode());
                Session::set('failed_logins', ((int)Session::get('failed_logins')) + 1);
                $this->request->LocationRedirect(UrlGenerator::generateContentUrl('login'));
            } catch(Exception $e) {
                Session::setError($e->getMessage(), $e->getCode());
                Session::set('failed_logins', ((int)Session::get('failed_logins')) + 1);
                $this->request->LocationRedirect(UrlGenerator::generateContentUrl('login'));
            }
        }
    }

    public function externalAuthentication()
    {
        try {
            // авторизация в соц. сети
            if ($this->request->valueExists('authSystem')) {
                $this->authSystemAction(); // редирект на соц. сеть
            }

            // авторизация в коробке под sessionId
            if ($this->request->valueExists('sessionId')) {
                $this->authBySessionId();
            }

            // отобразить ошибку от сервиса
            if ($this->request->valueExists('error') && $this->request->getValue('error') == 'email_required') {
                // сервис запросил email - подменяем форму логина
                $this->assignEmailRequiredForm();
            } elseif ($this->request->valueExists('error')) {
                throw new Exception($this->request->getValue('error'));
            }
        } catch(ServiceException $e) {
            Session::setError($e->getErrorMessage(), $e->getErrorCode());
        } catch(Exception $e) {
            Session::setError($e->getMessage(), $e->getCode());
        }

        return false;
    }

    private function authSystemAction()
    {
        $authSystemId = $this->request->getValue('authSystem');
        $returnUrl = UrlGenerator::generateContentUrl('login', true);

        $answer = null;
        OTAPILib2::GetExternalAuthenticationInfo(Session::getActiveLang(), User::getObject()->getSid(), $authSystemId, $returnUrl, $answer);
        OTAPILib2::makeRequests();

        if ($answer && $answer->GetResult() && $answer->GetResult()->GetRedirectUrl()) {
            $url = $answer->GetResult()->GetRedirectUrl();
            $this->request->LocationRedirect($url);
        }

        throw new Exception(Lang::get('Service_page_something_wrong_text'));
    }

    private function authBySessionId()
    {
        $sid = $this->request->getValue('sessionId');

        // проверяем корректность sid
        $answer = null;
        OTAPILib2::GetUserStatusInfo(Session::getActiveLang(), $sid, $answer);
        OTAPILib2::makeRequests();
        // логиним покупателя
        Users::AutoLogin($sid);
    }

    private function assignEmailRequiredForm()
    {
        $contextId = $this->request->getValue('contextId');
        $email = $this->request->getValue('email');
        $password = $this->request->getValue('password', '');

        $this->tpl->assign('emailRequiredForm', true);
        $this->tpl->assign('getPassword', false);
        $this->tpl->assign('contextId', $contextId);
        $this->tpl->assign('email', $email);

        // если ввели email
        if ($email) {
            try {
                OTAPILib2::ConfirmExternalAuthentication(Session::getActiveLang(), $contextId, $email, $password, $answer);
                OTAPILib2::makeRequests();

                $sid = $answer->GetSessionId()->GetValue();
                $this->request->set('sessionId', $sid);
                $this->authBySessionId();
            } catch(ServiceException $e) {
                // если надо запросить пароль
                if ($e->getErrorCode() == 'ValidationError' && $e->getSubErrorCode() == 'PasswordRequiredForFinishExternalAuthentication') {
                    $this->tpl->assign('getPassword', true);
                }
                Session::setError($e->getErrorMessage(), $e->getErrorCode());
            } catch(Exception $e) {
                Session::setError($e->getMessage(), $e->getCode());
            }
        }
        // если ввели пароль - показываем это поле
        if ($password) {
            $this->tpl->assign('getPassword', true);
        }
    }
}
