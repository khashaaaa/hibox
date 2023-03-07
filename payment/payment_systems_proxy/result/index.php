<?php
header('Content-Type: text/html; charset=utf-8');
$GLOBALS['script_start_time'] = microtime(true);
$GLOBALS['trace'] = array();

include(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
include(dirname(dirname(dirname(dirname(__FILE__)))).'/config/config.php');
General::init();

$ps = (isset($_POST['ps'])) ? $_POST['ps'] : $_GET['ps'];
$R = new PaymentProxy($ps, new CMS());
print $R->result(array_merge($_GET, $_POST));
