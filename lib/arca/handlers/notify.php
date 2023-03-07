<?php
header('Content-Type: text/html; charset=utf-8');
$GLOBALS['script_start_time'] = microtime(true);
$GLOBALS['trace'] = array();

chdir(dirname(dirname(dirname(dirname(__FILE__)))));
include('config.php');
include('config/config.php');
General::init();

$_SESSION['active_lang_admin'] = @$_SESSION['active_lang_admin'] ? $_SESSION['active_lang_admin'] : 'ru';

$A = new Arca();
$A->multipleNotifyOTAPIPaymentSystem();
