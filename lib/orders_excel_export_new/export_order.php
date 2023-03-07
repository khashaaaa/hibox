<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

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

define('ORDER_EXCEL_EXPORT_NEW_PATH', dirname(__FILE__) . '/');

$params = RequestWrapper::post('params', array());
$itemsIds = ! empty($params['itemsIds']) ? $params['itemsIds'] : '';
$ordersIds = ! empty($params['ordersIds']) ? $params['ordersIds'] : '';
$type = ! empty($params['type']) ? $params['type'] : 'default';

switch ($type) {
    case 'default':
        if (! is_string($itemsIds)) {
            throw new Exception('Param <itemsIds> MUST be of type <STRING>');
        }
        $itemsIds = array_filter(explode(',', $itemsIds));
        Plugins::invokeEvent('onExportOrderNew', array(
            'id' => RequestWrapper::post('id'),
            'itemsIds' => $itemsIds,
        ));
    break;
    case 'bulk':
        if (! is_array($itemsIds)) {
            throw new Exception('Param <itemsIds> MUST be of type <ARRAY>');
        }
        Plugins::invokeEvent('onBulkExportOrdersItems', array(
            'itemsIds' => $itemsIds,
        ));
    break;
    case 'erc':
        if (! is_array($ordersIds)) {
            throw new Exception('Param <ordersIds> MUST be of type <ARRAY>');
        }
        Plugins::invokeEvent('onExportOrdersERC', array(
            'ordersIds' => $ordersIds,
        ));
    break;
    case 'erc-agents':
        $from = ! empty($params['from']) ? $params['from'] : null;
        $to = ! empty($params['to']) ? $params['to'] : null;
        Plugins::invokeEvent('onExportAgentsERC', array(
            'from' => $from,
            'to' => $to,
        ));
    break;
}

