<?php

OTBase::import('system.lib.exceptions.ServiceException');

class ErrorHandler
{
    /**
     * @var AuthenticationListener
     */
    protected $authenticationListener;

    private static $errors = array();

    public function __construct($authenticationListener)
    {
        $this->authenticationListener = $authenticationListener;
    }

    public static function init()
    {
        set_error_handler(array('ErrorHandler', 'handle'), E_ALL);
    }

    public static function handle($errno, $errstr, $errfile, $errline, $errcontext)
    {
        $type = 'PHP Error';
        $exit = false;
        if (OTBase::isTest() && $errno) {
            switch ($errno) {
                case E_USER_ERROR:
                    $type = 'Fatal Error';
                    $exit = true;
                break;
                case E_USER_WARNING:
                case E_WARNING:
                    $type = 'Warning';
                break;
                case E_USER_NOTICE:
                case E_NOTICE:
                case @E_STRICT:
                    $type = 'Notice';
                break;
                case @E_RECOVERABLE_ERROR:
                    $type = 'Catchable';
                break;
                default:
                    $type = 'Unknown Error';
                    $exit = true;
                break;
            }
        }

        $exception = new ErrorException($type . ': ' . $errstr, 0, $errno, $errfile, $errline);

        error_log($exception->getMessage() . ' TRACE: ' . $exception->getTraceAsString());

        if ($exit) {
            throw $exception;
        }
        return false;
    }

    public function CheckSessionExpired($e, $request)
    {
        if ($e->getErrorCode() == 'SessionExpired' && $e->getSubErrorCode() == 'SessionExpired') {
            $this->authenticationListener->Logout($request);
            return true;
        }
    }

    public function showErrorWithPNotify($errorException)
    {
        $E = new ErrorUtil();
        return $E->showErrorWithPNotify($errorException);
    }

    public static function registerError($errorException)
    {
        $request = new RequestWrapper();
        $authenticationListener = new AuthenticationListener();
        $isAjax = $request->isAjax();

        if (($errorException instanceof ServiceException) && ($errorException->getErrorCode() == 'SessionExpired') &&
                ($errorException->getSubErrorCode() == 'SessionExpired')) {

            $loginUrl = UrlGenerator::getProtocol() . '://' . $request->env('HTTP_HOST') . '/' . $request->getUriPart(0) . '/?cmd=Login';

            if ($isAjax) {
                $authenticationListener->ClearSession();
                if ($request->getReferrer()) {
                    $loginUrl .= '&retpath=' . $request->getReferrer();
                }
                $message = 'Session expired. Relogin is required.';
                header('HTTP/1.1 500 ' . $message);
                $response = array(
                    'error' => 1,
                    'message' => $message,
                    'redirect' => $loginUrl,
                    'expired' => 1,
                    'code' => $errorException->getErrorCode(),
                    'subcode' => $errorException->getSubErrorCode()
                );
                echo json_encode($response);
            } else {
                $authenticationListener->Logout($request);
            }
            die();
        }

        $message = $errorException->getMessage();

        error_log($message);

        if (method_exists($errorException, 'getErrorCode') && $errorException->getErrorCode() === 'AccessDenied') {
            self::$errors[] = $errorException;
            if (OTBase::isTest()) {
                Session::setError($errorException->getMessage());
            } else {
                Session::setError(LangAdmin::get('Action_not_allowed_for_user'));
            }

            // если вызов запрещен по ip - делаем логаут из админки
            if (in_array($errorException->getSubErrorCode(), array('CallFromIP', 'InstanceKeyBan'))) {
                $authenticationListener->ClearSession();
            }
            $urlWrapper = new AdminUrlWrapper();
            $urlWrapper = $urlWrapper->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");

            $path = RightsManager::defaultPath();
            $redirectUrl = $urlWrapper->AssignCmdAndDo($path['cmd'], $path['do']);

            header('Location: ' . $redirectUrl);
            die();
        }

        // Если мы в режиме разработки, то сразу показать исключение
        if (OTBase::isTest()) {
            $message = '<pre><h2>' . $errorException->getMessage() . "</h2>\n\n" . $errorException->getTraceAsString() . '</pre>';
            if (! $isAjax) {
                echo $message;
                die();
            }
        }

        if ($isAjax) {
            $message = '__FACEPALM__' . $message;
            $message = preg_replace('#[\r\n]+#si', '__newline__', $message);
            $message = str_replace('h2', 'b', $message);
            header('HTTP/1.1 500 ' . $message);
            die();
        }

        self::$errors[] = $errorException;
    }

    public static function showRegisteredErrorsWithPNotify()
    {
        $output = '';
        $E = new ErrorUtil();
        foreach (self::$errors as $error) {
            $output .= $E->showErrorWithPNotify($error);
        }

        return $output;
    }
}
