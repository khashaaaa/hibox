<?php

class Login
{
    static function auth()
    {
        if (isset($_POST['login'], $_POST['password'])) {

            if(isset($_POST['ct_captcha'])){
                $res = self::checkCaptcha($_POST);
                if($res){
                    return ;
                }
            }
            $GLOBALS['log'] = $_POST['login'];

            /*
            *  SessionId
            *
            *  $_SESSIONS
            */

            global $otapilib;
            $data = $otapilib->AuthenticateInstanceOperator($_POST['login'], $_POST['password']);

            if($data === false){
                show_error();
                return ;
            }

            if (isset($data['SessionId'])) {
                $_SESSION['sid'] = (string)$data['SessionId'];
                unset($_SESSION['current_roles']);
                $GLOBALS['ssid'] = session_id();
                if(defined('CFG_SAVE_ADMIN_COOKIE')){
                    $cookieName = str_replace(array(' ',',',';','='),'',$_SERVER['HTTP_HOST']).'AuthAdmin';
                    $cookieValue = (string)$data['SessionId'];
                    $cookieExpires = time() + (10 * 24 * 60);
                    Cookie::set($cookieName, $cookieValue, $cookieExpires);
                }
            }
            header('Location: index.php');
        }

        $cookieName = str_replace(array(' ',',',';','='),'',$_SERVER['HTTP_HOST']).'AuthAdmin';
        if(defined('CFG_SAVE_ADMIN_COOKIE') && @$_COOKIE[$cookieName]){
            $_SESSION['sid'] = $_COOKIE[$cookieName];
            $GLOBALS['ssid'] = session_id();
        }
        if(isset($_GET['expired'])){
            $cookieExpires = time()-3600;
            Cookie::set($cookieName, '', $cookieExpires);
            header('Location: index.php?cmd=login');
        }

        if (@$_SESSION['sid'] != '')
        return true;

        /*
        if($GLOBALS['ssid'] != '')
            return true;
        */
        return false;
    }

    private static function checkCaptcha($fields){
        $captchapath = '../lib/securimage/securimage.php';
        require_once $captchapath;
        $securimage = new Securimage();

        if ($securimage->check($fields['ct_captcha']) == false) {
            return array(false, Lang::get('incorrect_code'));
        }

        return false;
    }

}