<?php

require CFG_LIB_ROOT . DIRECTORY_SEPARATOR . 'OTBase.class.php';

//Загрузка классов
$GLOBALS['CFG_CLASS_FILE'] = array (
    // OTAPIlib
    'OTAPIlib' => CFG_APP_ROOT . '/otapilib.php',
    'TAOlib' => CFG_APP_ROOT . '/taolib.php',
    // Zakaz
    'Zakaz' => CFG_APP_ROOT . '/Zakaz.php',
    //
    'axapta' => CFG_APP_ROOT . '/axapta.php',
    // Классы ядра
    'Debugger' => CFG_LIB_ROOT . '/debugger.php',
);

// Функция для автозагрузки классов
function  opentao__autoload($class_name)
{
    if (OTBase::autoload($class_name)) {
        return;
    }

    if (file_exists(CFG_APP_ROOT.'/controllers/'.$class_name.'.class.php'))
    {
        include_once(CFG_APP_ROOT.'/controllers/'.$class_name.'.class.php');
    }
    elseif (file_exists(CFG_APP_ROOT.'/blockscustom/'.$class_name.'.class.php'))
    {
        include_once(CFG_APP_ROOT.'/blockscustom/'.$class_name.'.class.php');
    }
    elseif (file_exists(CFG_APP_ROOT.'/blocks/'.$class_name.'.class.php'))
    {
        include_once(CFG_APP_ROOT.'/blocks/'.$class_name.'.class.php');
    }
    elseif (file_exists(CFG_APP_ROOT.'/blocks/order/'.$class_name.'.class.php'))
    {
        include_once(CFG_APP_ROOT.'/blocks/order/'.$class_name.'.class.php');
    }
    elseif (file_exists(CFG_LIB_ROOT.'/'.$class_name.'.class.php'))
    {
        include_once(CFG_LIB_ROOT.'/'.$class_name.'.class.php');
    }
    elseif (file_exists(CFG_LIB_ROOT.'/repository/'.$class_name.'.class.php'))
    {
        include_once(CFG_LIB_ROOT.'/repository/'.$class_name.'.class.php');
    }
    elseif (file_exists(CFG_LIB_ROOT.'/interfaces/'.$class_name.'.interface.php'))
    {
        include_once(CFG_LIB_ROOT.'/interfaces/'.$class_name.'.interface.php');
    }
    elseif (file_exists(CFG_LIB_ROOT.'/otapi_providers/'.$class_name.'.class.php'))
    {
        include_once(CFG_LIB_ROOT.'/otapi_providers/'.$class_name.'.class.php');
    }
    elseif (file_exists(CFG_LIB_ROOT.'/helpers/'.$class_name.'.class.php'))
    {
        include_once(CFG_LIB_ROOT.'/helpers/'.$class_name.'.class.php');
    }
    elseif (file_exists(CFG_LIB_ROOT.'/exceptions/'.$class_name.'.class.php'))
    {
        include_once(CFG_LIB_ROOT.'/exceptions/'.$class_name.'.class.php');
    }
    elseif (file_exists(CFG_LIB_ROOT.'/arca/'.$class_name.'.class.php'))
    {
        include_once(CFG_LIB_ROOT.'/arca/'.$class_name.'.class.php');
    }
    elseif (file_exists(CFG_LIB_ROOT.'/referral_system/'.$class_name.'.php'))
    {
        include_once(CFG_LIB_ROOT.'/referral_system/'.$class_name.'.php');
    }
    elseif (file_exists(CFG_LIB_ROOT.'/referral_system/lib/'.$class_name.'.php'))
    {
        include_once(CFG_LIB_ROOT.'/referral_system/lib/'.$class_name.'.php');
    }
    elseif (file_exists(CFG_LIB_ROOT.'/startup_scripts/'.$class_name.'.php'))
    {
        include_once(CFG_LIB_ROOT.'/startup_scripts/'.$class_name.'.php');
    }
    elseif (file_exists(CFG_LIB_ROOT.'/widget/'.$class_name.'.class.php'))
    {
        include_once(CFG_LIB_ROOT.'/widget/'.$class_name.'.class.php');
    }
    elseif (file_exists(CFG_LIB_ROOT.'/Core/'.$class_name.'.php'))
    {
        include_once(CFG_LIB_ROOT.'/Core/'.$class_name.'.php');
    }
    elseif ((isset($GLOBALS['CFG_CLASS_FILE'][$class_name])) && (file_exists($GLOBALS['CFG_CLASS_FILE'][$class_name])))
    {
        include_once($GLOBALS['CFG_CLASS_FILE'][$class_name]);
    }
    else
    {
        Plugins::invokeEvent('onAppendCustomAutoload', array('className' => $class_name));
    }
}

spl_autoload_register('opentao__autoload');

Plugins::invokeEvent('onAddCustomAutoload');
