<?php
header('Content-Type: text/html; charset=utf-8');
$GLOBALS['script_start_time'] = microtime(true);
$GLOBALS['trace'] = array();

ob_start();
include_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
include_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config/config.php');
General::init();
ob_end_clean();

$R = new W1();
print $R->result(array_merge($_GET, $_POST));