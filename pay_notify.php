<?php

// Подключаем файл с паролями от сервиса
require_once('config.php');

// Подключаем конфигурационный файл
require_once('config/config.php');
General::init();

define('RESPONSE_OK', 1);
define('RESPONSE_NO_ADMIN', 2);
define('RESPONSE_SEND_ERROR', 3);

Lang::getTranslations('', General::getConfigValue('admin_letter_lang', 'ru'));

if (count($_GET)) {
    $income_params = $_GET;
} else {
    $income_params = $_POST;
}



if (! isset($income_params['sign'])) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    die();
}
$sign = $income_params['sign'];
$md5 = $income_params;
unset($md5['sign']);
ksort($md5);
reset($md5);

if (strtoupper(md5(implode('', $md5) . CFG_SERVICE_INSTANCEKEY)) != $sign) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    die();
}
/*
ClientNotify|OK|http://dev.opentao.net/pay_notify.php|
amount=150.00
&currency=EUR
&externalTransactionId=6B0B1767B1
&internalTransactionId=737
&salesId=ORD-0000000000
&userId=3330&userLogin=mm182
&sign=AF91D23C1A0F7CD096BB0D3E3E20FCE4|2
*/

$cms = new CMS();
$cmsStatus = $cms->Check();
$r = $cms->getSiteConfig();

if ($r[0] && isset($r[1]['notification_email'])) {
    $arrayOfMails = str_replace(" ", "", $r[1]['notification_email']);
    $arrayOfMails = explode(';', $arrayOfMails);
    $params = array();
    
    $params['to'] = $arrayOfMails;

    $params['subject'] = (isset($income_params['salesId'])) ?
                Lang::get('Pay_notify_paymnet_order') . ' ' . $income_params['salesId'] : Lang::get('Pay_notify_depositing');

    $params['message'] = $params['subject'] . '<br/>';

    if (isset($income_params['userLogin'])) {
        $params['message'] .= Lang::get('Pay_notify_login_user') . ': ' . $income_params['userLogin'] . '<br/>';
        $userData = new UserData();
        $userData->ClearAccountInfoCacheByLogin($income_params['userLogin']);
    }

    if (isset($income_params['amount'])) {
        $params['message'] .= Lang::get('Pay_notify_amount') . ': ' . $income_params['amount'];
        if (isset($income_params['currency'])) {
            $params['message'] .= ' ' . $income_params['currency'];
        }
        $params['message'] .= '<br/>';
    }

    if (isset($income_params['externalTransactionId'])) {
        $params['message'] .= Lang::get('Pay_notify_external_number_payment') . ': ' . $income_params['externalTransactionId'] . '<br/>';
    }

    if (isset($income_params['internalTransactionId'])) {
        $params['message'] .= Lang::get('Pay_notify_internal_number_payment') . ': ' . $income_params['internalTransactionId'] . '<br/>';
    }

    if (isset($income_params['accountId'])) {
        $params['message'] .= Lang::get('Pay_notify_internal_number_payment') . ': ' . $income_params['accountId'] . '<br/>';
    }

    if (isset($income_params['userId'])) {
        $params['message'] .= Lang::get('Pay_notify_page_user') . ': <a href="http://'.rtrim($_SERVER['HTTP_HOST'],'/').'/admin/index.php?cmd=users&do=profile&id='.$income_params['userId'].'">Ссылка</a>' . '<br/>';
        
    }
     
    foreach($arrayOfMails as $mail) {
        $params['to'] = $mail;
        sendEmail($params);
    }
    echo RESPONSE_OK;
} else {
    echo RESPONSE_NO_ADMIN;
}


function sendEmail($params) {
    General::mail_utf8($params['to'], CFG_SITE_NAME, 'noreply@' . preg_replace('/^www\./','',$_SERVER['HTTP_HOST']), $params['subject'], $params['message']);    
}