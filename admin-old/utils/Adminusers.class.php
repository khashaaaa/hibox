<?php

class Adminusers {

    public $error = '';
    

    public function __construct()
    {
        if (defined('NO_REDIRECT_TO_NEW_ADM')) {
            return;
        }
        
        $newLink = 'http://' . HOST_NAME . '/admin/?cmd=roles&do=default';

        include(TPL_DIR . 'redirect_to_new_admin.php');
        die;
    }

    function defaultAction () {
        if (Login::auth()) {
            global $otapilib;
            $sid = $_SESSION['sid'];

            $perpage = isset($_GET['ps']) ? $_GET['ps'] : 10;
            $from = isset($_GET['p']) ? $_GET['p'] : 0;
            $from = ($from > 1) ? ($from-1) * $perpage : 0;

            $users = $otapilib->GetInstanceUserList($sid);

            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }

            $pageurl = $this->_getPageURL();

            include(TPL_DIR . 'adminusers/users.php');
        } else {
            include(TPL_DIR . 'login.php');
        }
    }


    function userinfoAction() {
        if (Login::auth()) {
            global $otapilib;
            $sid = $_SESSION['sid'];
            $userid = @$_GET['id'];
            $error = '';

            $users = $otapilib->GetInstanceUserList($sid);
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }
            if (!$users) $error = $otapilib->error_message;
            
            /*
             * получение профиля пользователя
             */
            $user_info = array();
            foreach ($users as $user) {
                if ((string)$user['id'] == $userid) {
                    $user_info = $user;
                    break;
                }
            }
            
            $user_active_roles = $user_info['Roles'];

            $available_roles = $otapilib->GetAvailableRoleList($sid);
            
            include(TPL_DIR . 'adminusers/useredit.php');
        } else {
            include(TPL_DIR . 'login.php');
        }
    }
    

    function usercreateAction() {
        if (Login::auth()) {
            global $otapilib;
            $sid = $_SESSION['sid'];
            $userid = @$_GET['id'];

            $result = $otapilib->GetWebUISettings($sid);
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }

            include(TPL_DIR . 'adminusers/usercreate.php');
        } else {
            include(TPL_DIR . 'login.php');
        }
    }
    
    
    function addroleAction() {
        global $otapilib;
        @define('NO_DEBUG', true);
        $sid = $_SESSION['sid'];
        $login = $_GET['login'];
        $new_role = $_GET['new_role'];

        $r = $otapilib->AddInstanceUserToRole($sid, $new_role, $login);
        if ($otapilib->error_message == 'SessionExpired') {
            print 'RELOGIN';
            die;
        }

        if (!$r) print $otapilib->error_message;
        else print 'Ok';

        die;
    }
    
    
    function deleteroleAction() {
        global $otapilib;
        @define('NO_DEBUG', true);
        $sid = $_SESSION['sid'];
        $login = $_GET['login'];
        $role = $_GET['role'];

        $r = $otapilib->RemoveInstanceUserFromRole($sid, $role, $login);
        if ($otapilib->error_message == 'SessionExpired') {
            print 'RELOGIN';
            die;
        }

        if (!$r) print $otapilib->error_message;
        else print 'Ok';

        die;
    }


    function saveuserAction() {
        if (Login::auth()) {
            global $otapilib;
            $sid = $_SESSION['sid'];

            if (isset($_POST['Id'])) {
                $fields = str_replace('<?xml version="1.0"?>', '', $this->_generateUserFields(false));
                $result = $otapilib->UpdateInstanceUser($sid, $fields);
            } else {
                $fields = str_replace('<?xml version="1.0"?>', '', $this->_generateUserFields());
                $result = $otapilib->CreateInstanceUser($sid, $fields);
            }

            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }

            if (!$result) $error = $otapilib->error_message;
            $message = ($error != '') ? '&error=' . $error : '';

            if (isset($_POST['Id'])) {
                header('Location:index.php?sid=&cmd=adminusers&do=userinfo&id=' . $_POST['Id'] . $message);
            } else {
                header('Location:index.php?sid=&cmd=adminusers' . $message);
            }
        } else {
            include(TPL_DIR . 'login.php');
        }
    }


    function deleteAction() {
        global $otapilib;
        @define('NO_DEBUG', true);
        $sid = $_SESSION['sid'];
        $login = $_GET['login'];

        $r = $otapilib->DeleteInstanceUser($sid, $login);
        if ($otapilib->error_message == 'SessionExpired') {
            print 'RELOGIN';
            die;
        }

        if (!$r) print $otapilib->error_message;
        else print 'Ok';

        die;
    }

    private function _generateUserFields($create = true) {
        if ($create) {
            $xmlParams = new SimpleXMLElement('<InstanceUserCreateData></InstanceUserCreateData>');
        } else {
            $xmlParams = new SimpleXMLElement('<InstanceUserUpdateData></InstanceUserUpdateData>');
        }

        if (@$_POST['Email']) $xmlParams->addChild('Email', @$_POST['Email']);
        if (@$_POST['Login']) $xmlParams->addChild('Login', @$_POST['Login']);
        if (@$_POST['Name']) $xmlParams->addChild('Name', @$_POST['Name']);
        if (@$_POST['Password']) $xmlParams->addChild('Password', @$_POST['Password']);
        
        return str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
    }

    private function _getPageURL()
    {
        $pageurl = 'index.php?';

        $params = explode('&', $_SERVER['QUERY_STRING']);

        foreach ($params as $param) {
            @list($key, $value) = explode('=', $param);
            if (in_array($key, array('error', 'success', 'do', 'id'))) continue;

            $pageurl .= "&$key=$value";
        }

        return $pageurl;
    }

}