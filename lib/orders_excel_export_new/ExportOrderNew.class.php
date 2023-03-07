<?php
class ExportOrderNew
{
    public static function checkAuth()
    {
        if(!Login::auth()){
            header('Location: /admin/index.php?expired');
            die();
        }
    }

    public static function onRenderOrder()
    {
        $tail = defined('CFG_ADMIN_VERSION') ? CFG_ADMIN_VERSION : date('Y-m');
        print LangAdmin::loadJSTranslation(array('export_items_list'));
        print '<script type="text/javascript" src="/lib/orders_excel_export_new/js/onRenderOrder.js?'.$tail.'"></script>';
    }

    public static function onRenderAdminOrdersList()
    {
        $tail = defined('CFG_ADMIN_VERSION') ? CFG_ADMIN_VERSION : date('Y-m');
        print LangAdmin::loadJSTranslation(array('export_items_list'));
        print '<script type="text/javascript" src="/lib/orders_excel_export_new/js/onRenderAdminOrdersList.js'.$tail.'"></script>';
    }

    public static function onExportOrderNew($orderId, $itemsIds = array())
    {
        global $otapilib;
        self::checkAuth();

        $sid = $_SESSION['sid'];
        $orderInfo = $otapilib->GetSalesOrderDetailsForOperator($sid, $orderId, '', 0);
        $currency_settings = $otapilib->GetInstanceCurrenciesSettings($sid);

        require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel.php';
        $pExcel = new PHPExcel();

        require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel/Writer/Excel5.php';
        $objWriter = new PHPExcel_Writer_Excel5($pExcel);

        require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel/Style/Color.php';

        require_once ORDER_EXCEL_EXPORT_NEW_PATH . 'ExportExcelNew.class.php';
        $E = new ExportExcelNew($pExcel, $objWriter);
        $groupBy = Cookie::get('itemsGroupBy', 'none');
        if ($groupBy == 'none') {
            $E->exportOrder($orderInfo, $currency_settings, $itemsIds);
        } else {
            $E->exportOrderWithGroupedItems($orderInfo, $currency_settings, $itemsIds, $groupBy);
        }
    }

    /**
     * @param $ordersItemsIds array [
     *      <orderId> => [
     *          <itemId>,
     *          <itemId>,
     *          ...
     *      ],
     *      ...
     *  ]
    **/
    public static function onBulkExportOrdersItems(array $ordersItemsIds = array())
    {
        global $otapilib;
        self::checkAuth();
        $sid = Session::get('sid');

        require_once dirname(__FILE__) . '/ExportExcelNew.class.php';
        require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel/Writer/Excel5.php';
        require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel/Style/Color.php';
        require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel.php';

        OTBase::import('system.admin.lib.otapi_providers.OrdersProvider');
        if (! class_exists('OrdersProvider')) {
            throw new Exception('Bulk export requires OrdersProvider class for proper work.');
        }

        $anotherParams = RequestWrapper::post('params', array());

        $ordersProvider = new OrdersProvider($otapilib);
        $filter = array(
            'fromdate' => $anotherParams['fromdate'],
            'todate' => $anotherParams['todate'],
            'orders_status' => array_flip($anotherParams['orders_status']),
            'items_status' => array_flip($anotherParams['items_status']),
        );
        $limit = 100;
        $offset = 0;
        $xml = $ordersProvider->generateItemsSearchParams($filter, array());
        $all = false;
        $items = array();
        while(! $all) {
            $data = $ordersProvider->SearchOrderLines($sid, $xml, $offset, $limit);
            $items1 = isset($data['Content']) ? $data['Content'] : $data;
            foreach ($items1 as $key1 => $value1) {
                $items[$key1] = $value1;
            }
            $all = isset($data['TotalCount']) && intval($data['TotalCount']) == count($items);
            $offset += $limit;
        }

        $currency_settings = $otapilib->GetInstanceCurrenciesSettings($sid);

        if (is_array($items)) {
            $grouppedItems = array();
            foreach ($ordersItemsIds as $orderId => $itemsIds) {
                foreach ($itemsIds as $itemId) {
                    if (isset($items[$itemId])) {
                        if (! isset($grouppedItems[$orderId])) {
                            $grouppedItems[$orderId] = array();
                        }
                        $grouppedItems[$orderId][] = $items[$itemId];
                    }
                }
            }
            $pExcel = new PHPExcel();
            $objWriter = new PHPExcel_Writer_Excel5($pExcel);
            $E = new ExportExcelNew($pExcel, $objWriter);
            $E->exportOrdersItems($grouppedItems, $currency_settings);
        }
    }

    /**
     * @param $ordersItemsIds array [
     *      <orderId> => [
     *          <itemId>,
     *          <itemId>,
     *          ...
     *      ],
     *      ...
     *  ]
    **/
    public static function onExportOrdersERC(array $ordersIds = array())
    {
        global $otapilib;
        self::checkAuth();
        $sid = Session::get('sid');

        require_once dirname(__FILE__) . '/ExportExcelNew.class.php';
        require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel/Writer/Excel5.php';
        require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel/Style/Color.php';
        require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel.php';

        OTBase::import('system.admin.lib.otapi_providers.OrdersProvider');
        if (! class_exists('OrdersProvider')) {
            throw new Exception('ERC export requires OrdersProvider class for proper work.');
        }

        $anotherParams = RequestWrapper::post('params', array());

        $ordersProvider = new OrdersProvider($otapilib);
        $filter = array(
            'OrderIds' => $ordersIds,
        );
        $limit = ! empty($anotherParams['itemsCount']) ? $anotherParams['itemsCount'] : 1000;
        $limit = $limit > 0 ? $limit : 1000;
        $xml = $ordersProvider->generateSearchParams($filter, array());
        $orders = $ordersProvider->SearchOrders($sid, $xml, 0, $limit);
        $orders = isset($orders['Content']) ? $orders['Content'] : $orders;

        $currency_settings = $otapilib->GetInstanceCurrenciesSettings($sid);

        if (is_array($orders)) {
            $pExcel = new PHPExcel();
            $objWriter = new PHPExcel_Writer_Excel5($pExcel);
            $E = new ExportExcelNew($pExcel, $objWriter);
            $E->exportOrdersERC($orders, $currency_settings);
        }
    }

    public static function onExportAgentsERC($dateFrom = null, $dateTo = null)
    {
        global $otapilib;
        self::checkAuth();
        $sid = Session::get('sid');

        require_once dirname(__FILE__) . '/ExportExcelNew.class.php';
        require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel/Writer/Excel5.php';
        require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel/Style/Color.php';
        require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel.php';

        OTBase::import('system.admin.lib.otapi_providers.OrdersProvider');
        if (! class_exists('OrdersProvider')) {
            throw new Exception('ERC export requires OrdersProvider class for proper work.');
        }

        $ordersProvider = new OrdersProvider($otapilib);
        $dateFrom = $dateFrom ? $dateFrom : date('Y-m-d', strtotime('-1month'));
        $dateTo = $dateTo ? $dateTo : date('Y-m-d');
        $filter = array(
            'fromdate' => $dateFrom,
            'todate' => $dateTo,
        );
        $anotherParams = RequestWrapper::post('params', array());
        $limit = ! empty($anotherParams['itemsCount']) ? $anotherParams['itemsCount'] : 1000;
        $limit = $limit > 0 ? $limit : 1000;
        $xml = $ordersProvider->generateSearchParams($filter, array());
        $orders = $ordersProvider->SearchOrders($sid, $xml, 0, $limit);
        $orders = isset($orders['Content']) ? $orders['Content'] : $orders;

        $currency_settings = $otapilib->GetInstanceCurrenciesSettings($sid);

        if (is_array($orders)) {
            $pExcel = new PHPExcel();
            $objWriter = new PHPExcel_Writer_Excel5($pExcel);
            $E = new ExportExcelNew($pExcel, $objWriter);
            $E->exportAgentsERC($orders, $currency_settings);
        }
    }
}
