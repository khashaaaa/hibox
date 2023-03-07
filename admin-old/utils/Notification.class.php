<?php

class Notification
{

    public function defaultAction () {
        $onrendernotificationform = Plugins::onRenderNotificationForm();
        include(TPL_DIR.'notification/main.php');
    }

    public function sendemailAction() {
        global $otapilib;
        $sid = $_SESSION['sid']; 
        $users = $otapilib->FindBaseUserInfoListFrame($sid, '<UserFilterParameters></UserFilterParameters>', 1, 2);
        $count = (int)$users['totalcount'];
        $users = $otapilib->FindBaseUserInfoListFrame($sid, '<UserFilterParameters></UserFilterParameters>', 1, $count);
        if (defined('NOTIFICATION_LOGIN') && defined('NOTIFICATION_PASS')) {
            $params = array(
                'mess'      => $_POST['message'],
                'subject'   => $_POST['title'],
                'login'     => NOTIFICATION_LOGIN,
                'password'  => NOTIFICATION_PASS,
            );
            foreach ($users['content'] as $user) {
                if(preg_match('|([a-z0-9_\.\-]{1,20})@([a-z0-9\.\-]{1,20})\.([a-z]{2,4})|is', $user['email'])) {
                    $params['email'] = $user['email'];
                    self::sendEmail($params);
                    break;
                }
            }
            header('location:index.php?cmd=notification&success');
        } else {
            header('location:index.php?cmd=notification&error=[set login and password]');
        }
    }
    
    // TODO: не используется
    static function sendEmail ($params)
    {
        return false;
    }
}
