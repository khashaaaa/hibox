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

define('CACHE_PATH', dirname(dirname(dirname(__FILE__))) . '/cache/');

include('../lib/helpers/ProductsHelper.class.php');

define('ORDER_EXPORT_PACKAGE_PATH', dirname(__FILE__).'/');
$request = new RequestWrapper();
$fileName = Plugins::invokeEvent('onBatchExportOrders', array('orders' => $request->getValue('orders')));
print $fileName;
