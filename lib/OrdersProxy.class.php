<?php
/**
 * Класс-прослойка для нормализации данных заказов
 */
class OrdersProxy
{
    /**
     * @var OTAPIlib
     */
    protected $otapilib;

    public static function normalizeOrderId($orderId)
    {
        $normalizedOrderId = defined('CFG_PREFIX_REPLACE_ORD') ? str_replace('ORD', CFG_PREFIX_REPLACE_ORD, (string)$orderId) : (string)$orderId;

        return $normalizedOrderId;
    }

    public static function originOrderId($orderId)
    {
        $orderId = defined('CFG_PREFIX_REPLACE_ORD') ? str_replace(CFG_PREFIX_REPLACE_ORD, 'ORD', (string)$orderId) : (string)$orderId;

        return $orderId;
    }

    public static function normalizeOrderIdForOtapi($orderId)
    {
        $orderId = self::getOrderNumericId($orderId);
        $countNumbers = strlen((string)$orderId);
        if ($countNumbers < 10) {
            $orderId = str_repeat('0', 10 - $countNumbers) . $orderId;
        }
        return $orderId ? 'ORD-' . $orderId : false;
    }

    public static function getOrderNumericId($orderId)
    {
        $numericId = $orderId;
        if (preg_match('#[1-9]+#si', $orderId, $m, PREG_OFFSET_CAPTURE) && !empty($m[0]) && isset($m[0][1])) {
            $numericId = substr($orderId, $m[0][1]);
        }
        return $numericId;
    }

    /**
     * Конструктор класса.
     */
    public function __construct(OTAPIlib $otapilib)
    {
        $this->otapilib = $otapilib;
    }

    public function CreateSalesOrder($sessionId, $deliveryModeId, $comment, $weight)
    {
        $order = $this->otapilib->CreateSalesOrder($sessionId, $deliveryModeId, $comment, $weight);
        $order['id'] = self::normalizeOrderId($order['id']);
        $order['Id'] = self::normalizeOrderId($order['Id']);

        return $order;
    }

    public function CreateOrder($sessionId, $xmlParams)
    {
        $order = $this->otapilib->CreateOrder($sessionId, $xmlParams);
        $order['id'] = self::normalizeOrderId($order['id']);
        $order['Id'] = self::normalizeOrderId($order['Id']);

        return $order;
    }

    public function RecreateSalesOrder($sessionId, $orderId, $weight)
    {
        $order = $this->otapilib->RecreateSalesOrder($sessionId, $orderId, $weight);
        $order['id'] = self::normalizeOrderId($order['id']);
        $order['Id'] = self::normalizeOrderId($order['Id']);

        return $order;
    }

    public function GetSalesOrderDetails($sessionId, $orderId)
    {
        $order = $this->otapilib->GetSalesOrderDetails($sessionId, $orderId);
        $normalizedOrderId = self::normalizeOrderId($order['SalesOrderInfo']['id']);
        $order['SalesOrderInfo']['id'] = $normalizedOrderId;
        $order['SalesOrderInfo']['Id'] = $normalizedOrderId;
        $order['salesorderinfo']['id'] = $normalizedOrderId;
        $order['salesorderinfo']['Id'] = $normalizedOrderId;

        return $order;
    }
    
    public static function prepareOrderComment($orderComment) {
        if (! $orderComment) {
            return '';
        }
    
        $orderComment = htmlspecialchars($orderComment);
        $orderComment = str_replace('&amp;amp;', '&', $orderComment);
        $orderComment = str_replace('&amp;quot;', '"', $orderComment);

        $orderComment = str_replace(array('\r\n', '\n\r', '\n' , '\r'), ' <br />', $orderComment);
        $orderComment = str_replace(array("\r\n", "\n\r", "\n" , "\r"), ' <br />', $orderComment);

        $orderComment = preg_replace('/^\<br \/\>/', '', $orderComment);
        
        $matches = array();
        if (preg_match('/\((\w+):\+(\d+)%\)/i', $orderComment, $matches)) {
            $insurance = Lang::get('order_insurance') . ' (' . $matches[2] . ' ' . Lang::get('insurance_percent') . ')' ;
            if (! preg_match('/\<br \/>( +)\((\w+):\+(\d+)%\)/i', $orderComment)) {
                $insurance = '<br />' . $insurance;
            }
            $orderComment = preg_replace('/\((\w+):\+(\d+)%\)/i', $insurance, $orderComment);
        }

        return stripslashes($orderComment);
    }
    
}
