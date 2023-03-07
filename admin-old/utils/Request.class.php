<?php

OTBase::import('system.admin.lib.RightsManager');
OTBase::import('system.lib.Cache');
OTBase::import('system.lib.cache.Key');

Class Request {

    static function handle() {
        if (!isset($_REQUEST['cmd'])) $_REQUEST['cmd'] = Permission::default_cmd();/*{
            header ('Location:index.php?cmd='.Permission::default_cmd());
        }  elseif (!Permission::can_show_cmd($_REQUEST['cmd'])) {
            $_REQUEST['cmd'] = Permission::default_cmd();
            header ('Location:index.php?cmd='.Permission::default_cmd());
        }*/
        if (!isset($_REQUEST['do']))  $_REQUEST['do']  = 'default';

        if (! empty($_SESSION['sid'])) {
            RightsManager::getCurrentRights();
            if (! RightsManager::isSuperAdmin()) {
                unset($_SESSION['sid']);
                unset($_SESSION['ssid']);
                unset($_SESSION['current_roles']);
                $cacher = new Cache('Rights' . RightsManager::getCurrentRole());
                $cacher->drop();
                header('Location: index.php?cmd=login');
                die();
            }
        }

        $class_name = ucfirst($_REQUEST['cmd']);
        $method_name = $_REQUEST['do'].'Action';
        if (class_exists($class_name,true)) {

            $R = new RequestWrapper();
            $cmd = new $class_name();
            if (method_exists($cmd,$method_name)) {
                return $cmd->$method_name($R);
            }
        }

        throw new Exception('Unknown Request '.$class_name.'::'.$method_name,1);
    }

    static function http($url, $method, $params) {

        $ch = curl_init();

        $url = $url . '?' . http_build_query($params,'','&');
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  $method );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        $data = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        $data = new SimpleXmlElement($data);

        switch($data->ErrorCode){
            case 'Ok':
                break;
            case 'SessionExpired':
                throw new Exception($data->ErrorDescription,ERR_SESSION_EXPIRED);
                break;
            default:
                throw new Exception($data->ErrorDescription,ERR_UNKNOWN);
        }

        curl_close($ch);

        return $data;
    }
}