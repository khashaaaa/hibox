<?php
/**
 * Класс для работы с сессиями
 */
class Session
{
    private static $autoClose = false;
    private static $data = null;

    private static function initData()
    {
        if (is_null(self::$data)) {
            if (!isset($_SESSION)) {
                self::start();
                if (self::$autoClose) {
                    self::close();
                }
            }

            self::$data = $_SESSION;
        }
    }

    public static function getHttpHost()
    {
        $result = str_replace(':8080', '', $_SERVER['HTTP_HOST']);
        return $result;
    }

    public static function isActive()
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    public static function start()
    {
        if (!self::isActive()) {
            session_start();
        }
    }

    public static function close()
    {
        if (self::isActive()) {
            session_write_close();
        }
    }

    public static function enableAutoClose()
    {
        self::$autoClose = true;
    }

    public static function getUserSession()
    {
        if (is_array(self::getUserData())) {
            if (! array_key_exists('sid', self::getUserData())) {
                throw new NotFoundException('Not found user session', '-1');
            }
        } else {
            throw new NotFoundException('Not found user session', '-1');
        }
        $sid = self::getUserData('sid');
        if (empty($sid)) {
            throw new InternalError('User session is empty');
        }
        return $sid;
    }

    public static function getUserOrGuestSession()
    {
        if (self::getUserData('sid')) {
            $sid = self::getUserData('sid');
            Users::setCookieSession($sid);
        } elseif (Users::getCookieSession()) {
            $sid = Users::getCookieSession();
        } else {
            $sid = session_id();
            Users::setCookieSession(session_id());
        }
        return $sid;
    }

    public static function isAuthenticated()
    {
        return User::getObject()->isAuthenticated();
    }

    public static function set($name, $value)
    {
        self::initData();

        if (self::$autoClose) {
            self::start();
        }

        $_SESSION[$name] = $value;
        self::$data[$name] = $value;

        if (self::$autoClose) {
            self::close();
        }
    }

    public static function get($name, $default = null)
    {
        self::initData();
        return array_key_exists($name, self::$data) ? self::$data[$name] : $default;
    }

    public function has($name)
    {
        self::initData();
        return array_key_exists($name, self::$data);
    }

    public static function extract($name)
    {
        $result = self::get($name);
        self::clear($name);
        return $result;
    }

    public static function clear($name)
    {
        self::initData();

        if (self::$autoClose) {
            self::start();
        }

        unset($_SESSION[$name]);
        unset(self::$data[$name]);

        if (self::$autoClose) {
            self::close();
        }
    }

    public static function setUserData($userData)
    {
        self::set(User::getKeySession(), $userData);
        if ($userData && ! empty($userData['IsAuthenticated'])) {
            self::set(self::getHttpHost() . 'isMayAuthenticated', true);
        }
    }

    public static function setUserDataKey($key, $data)
    {
        self::setUserData(array_merge(self::getUserData(), array($key => $data)));
    }

    public static function getUserData($key = null)
    {
        $data = self::get(User::getKeySession());

        if ($key) {
            return is_array($data) && array_key_exists($key, $data) ? $data[$key] : null;
        }

        return !is_null($data) ? $data : array();
    }

    public static function clearUserData()
    {
        self::clear(self::getHttpHost() . 'isMayAuthenticated');
        User::getObject()->logout();
        //self::clear(self::getHttpHost() . 'loginUserInfo'); TODO: устарело - вырезать, last version 1.3.10
        //session_destroy();
    }

    public static function getUserDataSid()
    {
        return self::getUserOrGuestSession();
    }

    public static function getUserDataByKey($key)
    {
        return self::getUserData($key);
    }

    public static function setError($description, $code = null, $subCode = null)
    {
        self::set('error', array(
            'description' => (string)$description,
            'code' => (string)$code,
            'subcode' => (string)$subCode,
        ));
    }

    public static function getErrorCode()
    {
        $error = self::get('error');
        return is_array($error) && !empty($error['code']) ? $error['code'] : null;
    }

    public static function getSubErrorCode()
    {
        $error = self::get('error');
        return is_array($error) && !empty($error['subcode']) ? $error['subcode'] : null;
    }

    public static function getErrorDescription()
    {
        $error = self::get('error');
        if (! empty($error['description'])) {
            $result = $error['description'];
            self::clear('error');
            return $result;
        }
    }

    public static function checkErrors()
    {
        return (bool) self::get('error');
    }

    public static function checkAdminErrors()
    {
        $error = self::get('error');
        if (is_array($error) && !empty($error['description'])) {
            print '<script> $(function () {
                show_error("'. $error['description'] .'"); });
            </script>';
            self::clear('error');
        }
    }

    public static function clearError()
    {
        self::clear('error');
    }

    public static function setMessage($message)
    {
        self::set('info-message', $message);
    }

    public static function getMessage()
    {
        $message = self::get('info-message');
        self::clear('info-message');
        return $message;
    }

    public static function setNotification($notification)
    {
        $notifications = self::get('info-notifications', []);
        $notifications[] = $notification;

        self::set('info-notifications', $notifications);
    }

    public static function getNotifications()
    {
        $notifications = self::get('info-notifications', []);
        self::clear('info-notifications');

        return $notifications;
    }

    public static function isSessionExpired()
    {
        return (bool) (self::getErrorCode() == 'SessionExpired');
    }

    public static function getActiveLang()
    {
        return self::get('active_lang');
    }

    public static function setActiveLang($value)
    {
        self::set('active_lang', $value);
    }

    public static function getActiveAdminLang()
    {
        if (! self::get('active_lang_admin')) {
            $defaultLang = 'ru';
            try {
                self::set('active_lang_admin', InstanceProvider::getObject()->getDefaultAdminPanelLanguage($defaultLang));
            } catch (Exception $e) {
                ErrorHandler::registerError($e);
                self::set('active_lang_admin', $defaultLang);
            }
        }

        return self::get('active_lang_admin');
    }

    public static function setActiveAdminLang($value)
    {
        self::set('active_lang_admin', $value);
    }
}
