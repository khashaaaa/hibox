<?php
/*
$_GET['order_id'] = '2023';
$_GET['user_id']  = '1217';
$_GET['amount']   = '100';
$_GET['signature']   = '0ad714677266ebccb2923af842b36856';
/**/
/*
 * Скрипт принимает POST/GET парметры:
 * order_id - id заказа в системе БЛ
 * user_id - id пользователя в системе БЛ
 * amount - сумма платежа реферала
 * signature - подпись безопастности md5(ИК)
 *
 * Скрипт возвращает:
 * Ok - скрипт отработал успешно
 * ошибки логируются в таблицу `referral_logs`
 */

chdir(dirname(dirname(dirname(__FILE__))));
include_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
include_once(dirname(dirname(dirname(__FILE__))) . '/config/config.php');
General::init();
include_once(dirname(__FILE__) . '/ReferralAddBonus.class.php');

OTBase::import('system.lib.referral_system.lib.*');

$request = new RequestWrapper();

if ((md5(CFG_SERVICE_INSTANCEKEY)) !== $request->getValue('signature')) {
    $cms = new CMS();
    $cms->Check();
    $log = new ReferralLogManager($cms);
    $log->Add('signature_incorrect', 'incorrect signature: ' . $cms->escape($request->getValue('signature')) . ';');
    die('signature incorrect');
}

$R = new ReferralAddBonus($request);

try{
    $result = $R->runAction();
    header('Content-type: text/xml');
    print $result;
}
catch(Exception $e){
    print $e->getMessage();
}
