<?php
header('Content-Type: text/html; charset=utf-8');
$GLOBALS['script_start_time'] = microtime(true);
$GLOBALS['trace'] = array();

chdir(dirname(dirname(dirname(__FILE__))));
include('config.php');
chdir('admin/');
include('cfg/main.cfg.php');
include('cfg/error.cfg.php');

chdir('..');
include('lib/orders_excel_export/BatchExport.class.php');
include('lib/helpers/ProductsHelper.class.php');

Session::set('active_lang_admin', Session::getActiveAdminLang() ? Session::getActiveAdminLang() : 'ru');

ob_start();
if(!file_exists(dirname(__FILE__).'/utils/Lang.class.php'))
    General::init();
ob_end_clean();

define('CACHE_PATH', dirname(dirname(dirname(__FILE__))) . '/cache/');
$request = new RequestWrapper();
BatchExport::saveOrder(CACHE_PATH, $request->getValue('orderId'));
