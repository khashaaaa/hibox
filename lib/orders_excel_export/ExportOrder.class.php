<?php
class ExportOrder
{
    private static function checkAuth(){
        if(!Login::auth()){
            header('Location: /admin/index.php?expired');
            die();
        }
    }

    public static function onRenderAdminOrdersList()
    {
        $tail = defined('CFG_ADMIN_VERSION') ? CFG_ADMIN_VERSION : date('Y-m');
        print '<script type="text/javascript" src="/lib/orders_excel_export/js/onRenderAdminOrdersList.js?' . $tail . '"></script>';
        print LangAdmin::loadJSTranslation(array('export_items_list'));
        print '<script type="text/javascript" src="/lib/orders_excel_export_new/js/onRenderAdminOrdersList.js?' . $tail . '"></script>';
    }

    public static function onExportOrder($orderId){
        global $otapilib;
        self::checkAuth();

        $sid = $_SESSION['sid'];
        $orderInfo = $otapilib->GetSalesOrderDetailsForOperator($sid, $orderId, '', 0);

        if (!General::getConfigValue('export_canceled_items'))
            $orderInfo = Plugins::invokeEvent('ExportOrderCut', array('mass' => $orderInfo));


        require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel.php';
        $pExcel = new PHPExcel();

        require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel/Writer/Excel5.php';
        $objWriter = new PHPExcel_Writer_Excel5($pExcel);

        require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel/Style/Color.php';

        require_once ORDER_EXPORT_PACKAGE_PATH . 'ExportExcel.class.php';
        if (count($orderInfo['SalesLinesList'])) {
            $E = new ExportExcel($pExcel, $objWriter);
            $E->exportOrder($orderInfo);
        } else {
            echo LangAdmin::get('No_orders_found');
            die();
        }
    }

    public static function onBatchExportOrders($orders){
        $ordersData = array();
        foreach($orders as $orderId){
            if(!file_exists(CACHE_PATH . $orderId . '.xml')) continue;
            $ordersData[] = simplexml_load_file(CACHE_PATH . $orderId . '.xml');
        }

        require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel.php';
        $pExcel = new PHPExcel();

        require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel/Writer/Excel5.php';
        $objWriter = new PHPExcel_Writer_Excel5($pExcel);

        require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel/Style/Color.php';

        require_once ORDER_EXPORT_PACKAGE_PATH . 'BatchExport.class.php';
        $E = new BatchExport($pExcel, $objWriter);
        return $E->batchExport($ordersData, CACHE_PATH);
    }
}
