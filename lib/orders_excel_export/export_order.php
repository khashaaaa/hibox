<?php
header('Content-Type: text/html; charset=utf-8');
$GLOBALS['script_start_time'] = microtime(true);
$GLOBALS['trace'] = array();

chdir(dirname(dirname(dirname(__FILE__))));
include('config.php');
chdir('admin/');
include('cfg/main.cfg.php');
include('cfg/error.cfg.php');

$_SESSION['active_lang_admin'] = @$_SESSION['active_lang_admin'] ? $_SESSION['active_lang_admin'] : 'ru';

ob_start();
if(!file_exists(dirname(__FILE__).'/utils/Lang.class.php'))
    General::init();
ob_end_clean();

include('../lib/helpers/ProductsHelper.class.php');

define('ORDER_EXPORT_PACKAGE_PATH', dirname(__FILE__).'/');
Plugins::invokeEvent('onExportOrder', array('id' => preg_replace('/^[^\-]+/','ORD', $_GET['id'])));
