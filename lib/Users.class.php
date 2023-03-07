<?php

/**
 * Работа с пользователями + ЛК
 */
class Users
{
    public static function loginUser()
    {
        global $otapilib;

        $sid = Session::getUserOrGuestSession();
        $user = $otapilib->GetUserStatusInfo($sid);

        if(!(bool)$user){
            Session::clearUserData();
        }

        return $user;
    }

    public static function AutoLogin($sid = null) {
        global $otapilib;

        $cookieName = User::getKeyCookieForServiceSid();

        if (is_null($sid) && isset($_COOKIE[$cookieName])) {
            $sid = $_COOKIE[$cookieName];
        } elseif (is_null($sid)) {
            return false;
        }

        $auth = $otapilib->GetUserStatusInfo($sid);
        if(! empty($auth['login'])) {
            Session::setUserData(array(
                'sid' => $sid,
                'username' => $auth['login'],
                'IsAuthenticated' => true
            ));
            Session::set(Session::getHttpHost() . 'isMayAuthenticated', true);
        }
        else{
            $cookieValue = false;
            $cookieExpires = time() - (60 * 60 * 24 * 60);

            Cookie::set($cookieName, $cookieValue, $cookieExpires);
            unset($_COOKIE[$cookieName]);
        }
        return $auth;
    }
    public static function Login($fields){
        global $otapilib;
        $otapilib->setErrorsAsExceptionsOn();

        User::getObject()->clearUserDataCache();
        
        $remember = @(bool)$fields['remember'] ? 'true' : 'false';
        $auth = $otapilib->Authenticate(Session::getUserOrGuestSession(), trim($fields['username']), trim($fields['password']), $remember);
        if($auth){
            Session::setUserData(array(
                'sid' => $auth,
                'username' => $fields['username'],
                'IsAuthenticated' => true
            ));
            Session::set(Session::getHttpHost() . 'isMayAuthenticated', true);
            
            if(@$fields['remember']){
                Cookie::set(User::getKeyCookieForServiceSid(), $auth, time() + (60 * 60 * 24 * 60));
            }

            $otapilib->setErrorsAsExceptionsOff();
            return array(true, '');
        }
        else{
            $otapilib->setErrorsAsExceptionsOff();
            return array(false, $otapilib->error_message);
        }
    }
    public static function Logout()
    {
        Session::clearUserData();
    }

    public static function getCookieSession()
    {
        $cookieName = User::getKeyCookieForNotAuthSid();

        if(isset($_COOKIE[$cookieName]))
            return $_COOKIE[$cookieName];
        else
            return false;
    }

    public static function setCookieSession($sid)
    {
        $cookieName = User::getKeyCookieForNotAuthSid();
        $cookieValue = $sid;
        $cookieExpires = time() + (60 * 60 * 24 * 60);

        Cookie::set($cookieName, $cookieValue, $cookieExpires);
    }

    public static function clearCookieSession()
    {
        $cookieName = User::getKeyCookieForNotAuthSid();
        Cookie::clear($cookieName);
    }
}
