<?php

class AuthenticationController extends GeneralContoller
{
    const PASSWORD_MIN_LENGTH = 6;
    const MAX_FAILED_LOGINS = 5; //- количество неудачных логинов, после которых показываем капчу

    public function loginAction()
    {
        CrumbsController::setCrumbs([
            ['title' => Lang::get('home'), 'url' => UrlGenerator::getHomeUrl()],
            ['title' => Lang::get('private_office_login')]
        ]);
        General::$_page['title_h1'] = Lang::get('private_office_login');

        $error = null;
        $login = $this->request->getValue('username', '');
        $password = $this->request->getValue('password', '');
        $remember = $this->request->getValue('remember');

		
		if (!$this->request->isPost() && isset($_SERVER['HTTP_REFERER']) && stristr($_SERVER['HTTP_REFERER'], 'q=login') === false && stristr($_SERVER['HTTP_REFERER'], '/login') === false && stristr($_SERVER['HTTP_REFERER'], 'p=login') === false) {
			Session::set('login_from', $_SERVER['HTTP_REFERER']);
		}


        if (OTCaptcha::isCaptchaUnavailable()) {
            $captcha = false;
        } elseif (Session::get('failed_logins') >= $this::MAX_FAILED_LOGINS) {
            $captcha = true;
        } else {
            $captcha = false;
        }

        try {        
            // если пользователь уже авторизован
            if ($this->getUser()->isAuthenticated()) {
                return $this->redirect($this->getRedirectUrl());
            }

            // подтвереждение почты
            if ($this->request->valueExists('code')) {
                $request = new OtapiPasswordRecoveryConfirmationResultInfoAnswer(null);
                OTAPILib2::ConfirmPasswordRecovery($this->request->getValue('code'), $request);
                OTAPILib2::makeRequests();
                return $this->redirect(UrlGenerator::toRoute('register_success'));
            }

            // авторизация в коробке под sessionId
            if ($this->request->valueExists('sessionId')) {
                $this->authBySessionId($this->request->getValue('sessionId'));
                return $this->redirect($this->getRedirectUrl());
            }

            // авторизация через соц. сети
            if (CMS::IsFeatureEnabled('ExternalAuthentication')) {
                // авторизация в соц. сети
                if ($this->request->valueExists('authSystem')) {
                    return $this->redirectToAuthSystem(); // редирект на соц. сеть
                }

                // отобразить ошибку от сервиса
                if ($this->request->valueExists('error') && $this->request->getValue('error') == 'email_required') {
                    // сервис запросил email - подменяем форму логина
                    return $this->renderLoginCompletionForm();
                } elseif ($this->request->valueExists('error')) {
                    throw new Exception($this->request->getValue('error'));
                }
            }

            if ($this->request->isPost()) {
                if ($captcha) {
                    OTCaptcha::validate(RequestWrapper::allPost());
                }

                $user = array(
                    'username' => $login,
                    'password' => $password,
                    'remember' => $remember
                );
                
                Users::Login($user);

                Session::set('failed_logins', 0);
                
                return $this->redirect($this->getRedirectUrl());
            }
        } catch (Exception $e) {
            Session::set('failed_logins', ((int)Session::get('failed_logins') + 1));
            if (RequestWrapper::isAjax()) {
                return $this->respondAjaxError($e);
            }
            $error = $this->errorHandler->getExceptionAsArray($e);
            $error = $error['message'];
        }

        return $this->render('controllers/authentication/login', [
            'error' => $error,
            'captcha' => $captcha,
        ]);
    }

	private function getRedirectUrl()
	{
		$redirectUrl =  UrlGenerator::getHomeUrl();
		if (General::getConfigValue('auth_to_private_office')) {
			$redirectUrl = UrlGenerator::toRoute('privateoffice');
		}

		$referer = $this->request->getReferrer();
		if ($referer) {
			// если станица вызывается не с сайта
			if (stristr($referer, $_SERVER['SERVER_NAME']) === false) {
				if (!Session::get('login_from')) {
					Session::set('login_from', $redirectUrl);
				}
			} elseif ( // если вызов не со страницы авторизации
				stristr($referer, '/login') === false &&
				stristr($referer, 'p=login') === false &&
				stristr($referer, 'q=login') === false &&
				!Session::get('login_from')
			) {
				Session::set('login_from', $referer);
			}
		}

		if ($this->request->valueExists('referer')) {
			Session::set('login_from', UrlGenerator::toRoute($this->request->getValue('referer')));
		}

		if (Session::get('login_from') &&  stristr(Session::get('login_from'), 'login') === false) {
			$redirectUrl = Session::get('login_from');
		}

		if (Session::get('login_from_new') &&  stristr(Session::get('login_from_new'), 'login') === false) {
			$redirectUrl = Session::get('login_from_new');
		}

		return $redirectUrl;
	}

	private function redirectToAuthSystem()
	{
		$authSystemId = $this->request->getValue('authSystem');
		$returnUrl = UrlGenerator::toRoute('login', [], true);
		$referer = $this->request->getReferrer();

		if ($referer) {
			if (stristr($referer, 'p=login') === false) {
				Session::set('login_from_new', $referer);
			}
		}

		$answer = null;
		OTAPILib2::GetExternalAuthenticationInfo(Session::getActiveLang(), User::getObject()->getSid(), $authSystemId, $returnUrl, $answer);
		OTAPILib2::makeRequests();

		if ($answer && $answer->GetResult() && $answer->GetResult()->GetRedirectUrl()) {
			$url = $answer->GetResult()->GetRedirectUrl();
			return $this->redirect($url);
		}

		throw new Exception(Lang::get('Service_page_something_wrong_text'));
	}

    private function renderLoginCompletionForm()
    {
        $error = null;
        $contextId = $this->request->getValue('contextId');
        $email = $this->request->getValue('email');
        $password = $this->request->getValue('password', '');

        if ($email) {
            try {
                $answer = new OtapiSessionIdAnswer(null);
                OTAPILib2::ConfirmExternalAuthentication(Session::getActiveLang(), $contextId, $email, $password, $answer);
                OTAPILib2::makeRequests();

                $sessionId = $answer->GetSessionId()->GetValue();
                $this->authBySessionId($sessionId);
                return $this->redirect($this->getRedirectUrl());
            } catch(ServiceException $e) {
                // если надо запросить пароль
                if ($e->getErrorCode() == 'ValidationError' && $e->getSubErrorCode() == 'PasswordRequiredForFinishExternalAuthentication') {
                    $password = true;
                }
                $error = $this->errorHandler->getExceptionAsArray($e);
                $error = $error['message'];
            } catch(Exception $e) {
                $error = $this->errorHandler->getExceptionAsArray($e);
                $error = $error['message'];
            }
        }

        return $this->render('controllers/authentication/login-completion', [
            'error' => $error,
            'contextId' => $contextId,
            'email' => $email,
            'password' => $password,
        ]);
    }

    public function recoveryAction()
    {
        CrumbsController::setCrumbs([
            ['title' => Lang::get('home'), 'url' => UrlGenerator::getHomeUrl()],
            ['title' => Lang::get('pass_recovery')]
        ]);
        General::$_page['title_h1'] = Lang::get('pass_recovery');

        $error = null;
        $login = $this->request->getValue('username', '');
        $success = $this->request->getValue('recoverySuccess') ? Lang::get('recovery_sent') : '';

        try {
            // если пользователь уже авторизован
            if ($this->getUser()->isAuthenticated()) {
                return $this->redirect(UrlGenerator::toRoute('privateoffice'));
            }

            // ввод нового пароля
            if ($this->request->valueExists('code')) {
                return $this->renderRecoveryConfirmPassword();
            }

            // отправить письмо с ссылкой на восстановление пароля
            if ($this->request->isPost()) {
                $request = new OtapiPasswordRecoveryRequestResultInfoAnswer(null);
                OTAPILib2::RequestPasswordRecovery($login, $request);
                OTAPILib2::makeRequests();
                if (RequestWrapper::isAjax()) {
                    return $this->sendAjaxResponse(['message' => Lang::get('recovery_sent')]);
                }
                return $this->redirect(UrlGenerator::toRoute('recovery', ['recoverySuccess' => 1]));
            }
        } catch (Exception $e) {
            if (RequestWrapper::isAjax()) {
                return $this->respondAjaxError($e);
            }
            $error = $this->errorHandler->getExceptionAsArray($e);
            $error = $error['message'];
        }

        return $this->render('controllers/authentication/recovery', [
            'error' => $error,
            'login' => $login,
            'success' => $success,
        ]);
    }

    private function renderRecoveryConfirmPassword()
    {
        $error = null;
        $recoveryCode = $this->request->getValue('code');
        $newPassword = $this->request->getValue('newPassword');
        $newPasswordConfirm = $this->request->getValue('newPasswordConfirm');

        try {
            if ($this->request->isPost()) {
                if (empty($newPassword) || ($newPassword != $newPasswordConfirm)) {
                    throw new Exception(Lang::get('Password_empty_or_not_valid'));
                }

                $request = new OtapiSessionIdAnswer(null);
                OTAPILib2::ConfirmNewPassword(Session::getActiveLang(), $recoveryCode, $newPassword, $request);
                OTAPILib2::makeRequests();
                Users::AutoLogin($request->GetSessionId()->GetValue());

                return $this->redirect(UrlGenerator::toRoute('privateoffice'));
            }
        } catch (Exception $e) {
            if (RequestWrapper::isAjax()) {
                return $this->respondAjaxError($e);
            }
            $error = $this->errorHandler->getExceptionAsArray($e);
            $error = $error['message'];
        }

        return $this->render('controllers/authentication/recovery-confirm-password', [
            'error' => $error,
            'code' => $recoveryCode,
        ]);
    }

    public function registerAction()
    {
        CrumbsController::setCrumbs([
            ['title' => Lang::get('home'), 'url' => UrlGenerator::getHomeUrl()],
            ['title' => Lang::get('registration_tab')]
        ]);
        General::$_page['title_h1'] = Lang::get('registration_tab');

        $error = null;
        $login = $this->request->getValue('username', '');
        $email = $this->request->getValue('email', '');
        $isPhoneRegistrationAllowed = false;

        list($parentId, $parentLogin) = ReferalSystem::getReferrerInfo();
        $parentLogin = $this->request->getValue('parent', $parentLogin);

        try {
            $lang = Session::getActiveLang();
            $data = InstanceProvider::getObject()->GetCommonInstanceOptionsInfo($lang);
            $isPhoneRegistrationAllowed = $data->GetRegistration()->IsPhoneRegistrationAllowed();

            // подтвереждение почты 
            if ($this->request->valueExists('activation')) {
                $request = new OtapiSessionIdAnswer(null);
                OTAPILib2::ConfirmEmail($this->request->getValueSafe('activation'), $request);
                OTAPILib2::makeRequests();

                $sessionId = $request->GetSessionId()->GetValue();
                $this->authBySessionId($sessionId);

                return $this->redirect(UrlGenerator::toRoute('register_success'));
            }

            // если пользователь уже авторизован
            if ($this->getUser()->isAuthenticated()) {
                return $this->redirect(UrlGenerator::toRoute('privateoffice'));
            }

            if ($this->request->isPost()) {
                OTCaptcha::validate(RequestWrapper::allPost());

                $user = [
                    'username' => $login,
                    'email' => $email,
                    'phone' => $this->request->getValue('phone'),
                    'password' => $this->request->getValue('password'),
                    'agree' => $this->request->getValue('agree'),
                    'parent' => $this->request->getValue('parent'),
                    'parent_id' => $this->request->getValue('parent_id'),
                    'authenticationMethod' => $this->request->getValue('authenticationMethod'),
                ];
                list($sessionId, $isEmailVerificationUsed, $isPhoneVerificationUsed) = $this->registerUser($user);

                if ($isEmailVerificationUsed) {
                    return $this->redirect(UrlGenerator::toRoute('need_confirm_email'));
                }

                if ($isPhoneVerificationUsed) {
                    $redirectConfirmationUrl = UrlGenerator::toRoute('authentication/confirmation');

                    if (RequestWrapper::isAjax()) {
                        return $this->sendAjaxResponse([
                            'isPhoneVerificationUsed' => true,
                            'redirectConfirmation' => $redirectConfirmationUrl,
                        ]);
                    }

                    return $this->redirect($redirectConfirmationUrl);
                }

                if ($sessionId) {
                    $this->authBySessionId($sessionId);
                }

                return $this->redirect(UrlGenerator::toRoute('register_success'));
            }
        } catch (Exception $e) {
            if (RequestWrapper::isAjax()) {
                return $this->respondAjaxError($e);
            }
            $error = $this->errorHandler->getExceptionAsArray($e);
            $error = $error['message'];
        }

        return $this->render('controllers/authentication/register', [
            'error' => $error,
            'username' => $login,
            'email' => $email,
            'parentId' => $parentId,
            'parentLogin' => $parentLogin,
            'isPhoneRegistrationAllowed' => $isPhoneRegistrationAllowed,
        ]);
    }

    public function confirmationAction()
    {
        CrumbsController::setCrumbs([
            ['title' => Lang::get('home'), 'url' => UrlGenerator::getHomeUrl()],
            ['title' => Lang::get('confirmation_phone_tab')]
        ]);
        General::$_page['title_h1'] = Lang::get('confirmation_phone_tab');

        return $this->render('controllers/authentication/confirmation-form');
    }

    public function confirmAction()
    {
        try {
            $lang = Session::getActiveLang();
            $sid = User::getObject()->getSid();
            $code = $this->request->getValue('code');

            $response = new OtapiSessionIdAnswer(null);
            OTAPILib2::ConfirmPhone($lang, $sid, $code, $response);
            OTAPILib2::makeRequests();

            $sessionId = $response->GetSessionId()->GetValue();
            $this->authBySessionId($sessionId);
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse([
            'result' => 'ok',
            'redirect' => UrlGenerator::toRoute('register_success')
        ]);
    }

    private function registerUser($user)
    {
        $this->validateRegisterUser($user);

        $lang = Session::getActiveLang();
        $sid = User::getObject()->getSid();
        $xmlParams = $this->xmlRegisterUserParams($user);

        $response = new OtapiConfirmationInfoAnswer(null);
        OTAPILib2::RegisterUser($lang, $sid, $xmlParams, $response);
        OTAPILib2::makeRequests();

        $sessionId = $response->GetEmailConfirmationInfo()->GetSessionId()->GetValue();
        $isEmailVerificationUsed = $response->GetEmailConfirmationInfo()->IsEmailVerificationUsed();
        $isPhoneVerificationUsed = $response->GetEmailConfirmationInfo()->IsPhoneVerificationUsed();

        if ($response->GetEmailConfirmationInfo()->GetUserId()) {
            $user['id'] = $response->GetEmailConfirmationInfo()->GetUserId()->asString();

            // добавление пользователя в модуль рассылки
            if (General::IsFeatureEnabled('Newsletter')) {
                Subscribe::SetSubscribe($user['email'], $user['username'], $user['id']);
            }

            // добавление пользователя в реферальную программу
            if (General::IsFeatureEnabled('ReferralProgram')){
                ReferalSystem::onUserRegister($user);
            }
        }

        return [$sessionId, $isEmailVerificationUsed, $isPhoneVerificationUsed];
    }

    private function validateRegisterUser(array $user)
    {
        if (empty($user['username'])) {
            throw new Exception(Lang::get('not_entered_login'));
        }
        if (!isset($user['agree']) && $user['agree'] != 'on') {
            throw new Exception(Lang::get('not_agree_with_user_agreement'));
        }
        if (empty($user['password'])) {
            throw new Exception(Lang::get('not_entered_password'));
        }
        if (strlen($user['password']) < self::PASSWORD_MIN_LENGTH) {
            throw new Exception(Lang::get('pass_min_len'));
        }

        if ($user['authenticationMethod'] === 'phone') {
            if (empty($user['phone'])) {
                throw new Exception(Lang::get('not_entered_phone'));
            }
        } elseif (empty($user['email'])) {
            throw new Exception(Lang::get('not_entered_email'));
        } elseif (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception(Lang::get('incorrect_email'));
        }
    }

    private function xmlRegisterUserParams(array $user)
    {
        $xml = new SimpleXMLElement('<UserRegistrationData></UserRegistrationData>');
        $xml->addChild('Login', htmlspecialchars($user['username']));
        $xml->addChild('Email', htmlspecialchars($user['email']));
        $xml->addChild('Phone', htmlspecialchars($user['phone']));
        $xml->addChild('Password', htmlspecialchars($user['password']));

        return str_replace('<?xml version="1.0"?>', '', $xml->asXML());
    }

    private function authBySessionId($sessionId)
    {
        // проверяем корректность sid
        $answer = null;
        OTAPILib2::GetUserStatusInfo(Session::getActiveLang(), $sessionId, $answer);
        OTAPILib2::makeRequests();
        // логиним покупателя
        Users::AutoLogin($sessionId);
    }

    public function renderAuthSystemListAction()
    {
        $lang = Session::getActiveLang();
        if (!CMS::IsFeatureEnabled('ExternalAuthentication')) {
            return '';
        }

        $authSystemList = [];

        try {
            $request = InstanceProvider::getObject()->GetExternalAuthentications($lang);
            if ($request && $request->GetItem()) {
                $authSystemList = $request;
            }
        } catch(Exception $e) {
            $this->errorHandler->registerError($e);
        }

        return $this->renderPartial('controllers/authentication/auth-system-list', [
            'authSystemList' => $authSystemList
        ]);
    }
}