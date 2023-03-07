<?php

class FinReport extends GeneralUtil
{
    protected $_cache = false;
    protected $_life_time = 3600;
    protected $_template = 'index';
    protected $_template_path = 'finreport/';

    public $error = '';
    
    public function defaultAction()
    {
        global $otapilib;
        $this->checkAuth();
        $sid = $_SESSION['sid'];
        
        $data = $otapilib->SearchUsersWithSummary($sid, '<UserFilterParameters></UserFilterParameters>', 0, 99999);
        $this->tpl->assign('data', $data);
        
        // =========
        
        if (!isset($_POST['filter']['fromdate'])) $_POST['filter']['fromdate'] = date('Y-m-01');
        if (!isset($_POST['filter']['todate'])) $_POST['filter']['todate'] = date('Y-m-t');
        
        $users = $otapilib->FindBaseUserInfoListFrame($sid, '<UserFilterParameters></UserFilterParameters>', 0, 100);
        //print_r($users);
        if ((int)$users['TotalCount'] > 100) {
            for ($i = 100; $i < (int)$users['TotalCount']; $i += 100)
            $users2 = $otapilib->FindBaseUserInfoListFrame($sid, '<UserFilterParameters></UserFilterParameters>', $i, 100);
            $users['Content'] = array_merge($users['Content'], $users2['Content']);
        }
        $this->tpl->assign('users', $users['Content']);
        $this->tpl->assign('error', isset($error)?$error:'');
        
        // =========
        
        $OrderSearchParameters = '<OrderSearchParameters>
                 <StatusIdList>
                    <Id>39</Id>
                    <Id>40</Id>
                 </StatusIdList>
                 <CreationDateFrom>'.$_POST['filter']['fromdate'].'T00:00:00</CreationDateFrom>
                 <CreationDateTo>'.$_POST['filter']['todate'].'T23:59:59</CreationDateTo>';
        if (!empty($_POST['filter']['order'])) $OrderSearchParameters .= '<Id>'.$_POST['filter']['order'].'</Id>';
        $OrderSearchParameters .= '<FramePosition>0</FramePosition>
                 <FrameSize>1000</FrameSize>
            </OrderSearchParameters>';
        $orders = $otapilib->SearchOrdersWithSummary($sid, $OrderSearchParameters, 0, 100);
        foreach($orders['SalesOrdersList'] as $id => &$order) {
            //
            if (substr((string)$order['ShipmentDate'], 0, 4) == '0001')
            {
                unset($orders['SalesOrdersList'][$id]);
            } else {
                if (!empty($order['RateList'])) {
                    foreach ($order['RateList'] as $rate) {
                        if ($rate['FirstCode'] == 'CNY' && $rate['SecondCode'] == 'USD') {
                            $order['Rate'] = round(1 / (float)$rate, 2);
                        }
                    }
                }
            }
        }
        //print_r($orders);
        $this->tpl->assign('orders', $orders['SalesOrdersList']);
        print $this->fetchTemplate();
    }
}
