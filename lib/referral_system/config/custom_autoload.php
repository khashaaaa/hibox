<?php

if(!defined('REFERRAL_SYSTEM_PATH'))
    define('REFERRAL_SYSTEM_PATH', dirname(dirname(__FILE__)));

function ReferralSystemAutoload($className){
    if (file_exists(REFERRAL_SYSTEM_PATH.'/admin/utils/'.$className.'.class.php'))
    {
        include_once(REFERRAL_SYSTEM_PATH.'/admin/utils/'.$className.'.class.php');
    }
	else if (file_exists(REFERRAL_SYSTEM_PATH.'/admin/utils/'.$className.'.class.php'))
    {
        include_once(REFERRAL_SYSTEM_PATH.'/admin/utils/'.$className.'.class.php');
    }
    else if(file_exists(REFERRAL_SYSTEM_PATH.'/blocks/'.$className.'.class.php')){
        include_once(REFERRAL_SYSTEM_PATH.'/blocks/'.$className.'.class.php');
    }
    else if(file_exists(REFERRAL_SYSTEM_PATH.'/lib/'.$className.'.class.php')){
        include_once(REFERRAL_SYSTEM_PATH.'/lib/'.$className.'.class.php');
    }
}

$res = spl_autoload_register('ReferralSystemAutoload', true, true);
