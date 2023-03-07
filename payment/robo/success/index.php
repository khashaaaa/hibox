<?php
header('Content-Type: text/html; charset=utf-8');
$GLOBALS['script_start_time'] = microtime(true);
$GLOBALS['trace'] = array();

include(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
include(dirname(dirname(dirname(dirname(__FILE__)))).'/config/config.php');
General::init();

$R = new Robokassa();
$R->success();
