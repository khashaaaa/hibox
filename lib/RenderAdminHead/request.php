<?php

header('Content-Type: text/html; charset=utf-8');
$GLOBALS['script_start_time'] = microtime(true);
$GLOBALS['trace'] = array();

chdir(dirname(dirname(dirname(__FILE__))));
include('config.php');
chdir('admin/');
include('cfg/main.cfg.php');
include('cfg/error.cfg.php');

$params = array(
    'locale' => Session::getActiveAdminLang(),
    'hash' => md5(CFG_SERVICE_INSTANCEKEY)
);

print Plugins::invokeEvent('getImportantMessage', array('params' => $params));
