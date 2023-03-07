<?php

OTBase::import('system.lib.exceptions.ServiceException');

class ErrorHandler
{
    private static $errors = array();

    public static function init()
    {
        // регистрация ошибок
        set_error_handler(array('ErrorHandler', 'handle'), E_ALL);

        // перехват критических ошибок
        register_shutdown_function('ErrorHandler::myShutdownHandler');
    }

    public static function handle($errno, $errstr, $errfile, $errline)
    {
        $type = 'PHP Error';
        $exit = false;
        if ($errno) switch ($errno) {
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
        $exception = new ErrorException($type . ': ' . $errstr, 0, $errno, $errfile, $errline);

        // TODO: слишком много нотисов - запись в лог временно закомментирована
        //error_log($exception->getMessage() . ' TRACE: ' . $exception->getTraceAsString());
        
        if (OTBase::isTest() && $exit) {
            self::$errors[] = $exception;
        } elseif ($exit) {
            self::somethingWrong();
        }
        return false;
    }

    public static function myShutdownHandler()
    {
        // если была ошибка и она фатальна
        if ($error = error_get_last() AND $error['type'] & (E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR)) {
            ob_end_clean();
            $errors = array(
                E_ERROR => 'E_ERROR',
                E_WARNING => 'E_WARNING',
                E_PARSE => 'E_PARSE',
                E_NOTICE => 'E_NOTICE',
                E_CORE_ERROR => 'E_CORE_ERROR',
                E_CORE_WARNING => 'E_CORE_WARNING',
                E_COMPILE_ERROR => 'E_COMPILE_ERROR',
                E_COMPILE_WARNING => 'E_COMPILE_WARNING',
                E_USER_ERROR => 'E_USER_ERROR',
                E_USER_WARNING => 'E_USER_WARNING',
                E_USER_NOTICE => 'E_USER_NOTICE',
                E_STRICT => 'E_STRICT',
                E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
                E_DEPRECATED => 'E_DEPRECATED',
                E_USER_DEPRECATED => 'E_USER_DEPRECATED',
            );
            if (OTBase::isTest()) {
                
                print '<div style="clear: both;background: #fcc;border: 1px solid darkred;border-radius: 3px;padding: 10px;"><tt>';

                // выводим свое сообщение об ошибке
                print '<b>' . $errors[$error['type']] . '</b>[' . $error['type'] . '] ' . $error['message'] . ' [' . $error['file'] . ':' . $error['line'] . "]<br><br>\n";
                print "Backtrace:<br>\n";

                $trace = array_reverse(debug_backtrace());

                foreach ($trace as $i => $line) {
                    print '#' . $i . ' <b>' . $line['class'] . $line['type'] . $line['function'] . '</b>(' . implode(', ', $line['args']) . ')'
                        . (isset($line['file']) ? ' called at [' . $line['file'] . ':' . $line['line'] . ']' : '') . "<br>\n";
                }
                print '</tt></div>';
            } else {
                self::somethingWrong();
            }
        }
    }

    public static function somethingWrong($error = null)
    {
        if ($error instanceof ServiceException) {
            $message = $error->getErrorMessage();
        } elseif($error instanceof Exception) {
            $message = $error->getMessage();
        } elseif(is_string($error)) {
            $message = (string)$error;
        } else {
            $message = Lang::get('Service_page_something_wrong_text');
        }

        if (ob_get_contents()) {
            ob_end_clean();
        }

        $page = General::viewFetch('other/something-wrong');
        print str_replace("{{errorMessage}}", TextHelper::escape($message), $page);
        die();
    }

    public static function registerError($errorException)
    {
        /*
        // TODO: обрабатывать SessionExpired
        $request = new RequestWrapper();
        if (($errorException instanceof ServiceException) && ($errorException->getErrorCode() == 'SessionExpired')) {
            Session::clear('sid');
            $loginUrl = UrlGenerator::getProtocol() . '://' . $request->env('HTTP_HOST') . '/?p=login';
            if ($isAjax) {
                if ($request->getReferrer()) {
                    $loginUrl .= '&retpath=' . $request->getReferrer();
                }
                $message = 'Session expired. Relogin is required.';
                header('HTTP/1.1 500 ' . $message);
                $response = array(
                    'error' => 1,
                    'message' => $message,
                    'redirect' => $loginUrl,
                    'expired' => 1
                );
                echo json_encode($response);
            } else {
                header('Location: /?p=login');
            }
            die();
        }

        // TODO: обрабатывать AccessDenied
        if (method_exists($errorException, 'getErrorCode') && $errorException->getErrorCode() === 'AccessDenied') {
            self::$errors[] = $errorException;
            Session::setError(LangAdmin::get('Action_not_allowed_for_user'));
            header('Location: /');
            die();
        }
        */

        self::$errors[] = $errorException;
    }

    public static function getErrors()
    {
        $result = array();
        foreach (self::$errors as $error) {
            if (OTBase::isTest()) {
                $message = '<b>' . $error->getMessage() . "</b><br>" . $error->getTraceAsString();                
                $result[] = preg_replace('/\\r\\n?|\\n/', "<br>", $message);;
            } else {
                $result[] = $error->getMessage();
            }
        }
        return $result;
    }

    public static function getExceptionAsArray($e)
    {
        $error = array();
        if ($e instanceof ServiceException) {
            $error = array(
                'message' => $e->getErrorMessage(),
                'code' => $e->getErrorCode(),
                'subCode' => $e->getSubErrorCode(),
            );
        } else {
            $error = array(
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'subCode' => null,
            );
        }
        return $error;
    }
}
