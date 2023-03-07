<?php

class OrdersExport
{
    private $aSheet;

    private $headRow = array(
        '№ заказа', 'Дата заказа', 'ID клиента', 'Артикул', 'ID магазина (shopId на ТАО)', 'Заказ URL (в админке)', 'Количество',
        'Размер', 'Цвет', 'Размер CNY', 'Цвет CNY',
        'Примечания', 'Стоимость с дост. по Китаю', 'ТАО URL', 'URL картинки (J_ImgBooth)', 'Имя клиента', 'Адрес доставки'
    );

    private function appendCellToRow($row, $val, $col = ''){
        $column = $col ? $col : $this->aSheet->getHighestColumn();

        if($this->aSheet->cellExists($column.$row))
            $column++;
        $this->aSheet->setCellValue($column.$row,$val);
    }

    private function getItemConfig($item_id, $config_row){
        global $otapilib;

        $itemConfig = array(
            'color' => '', 'size' => '',
            'color_cny' => '', 'size_cny' => '',
            'comment' => '', 'vendor' => '', 'title' => '', 'masterprice' => ''
        );

        $configs = explode(';', $config_row);

        $item = $otapilib->GetItemFullInfo($item_id);
        $itemConfig['vendor'] = $item['VendorId'];
        $itemConfig['title'] = $item['Title'];
        $itemConfig['masterprice'] = $item['masterprice'];
        foreach($configs as $c){
            if(!$c) continue;
            list($propId, $propValId) = explode(':', $c);
            foreach($item['configurations'][$propId]['values'] as $v){
                if($v['id'] == $propValId){
                    if($propId == '1627207'){
                        $itemConfig['color'] = $v['name'];
                        $itemConfig['color_cny'] = $v['name_cny'];
                    }
                    elseif($propId == '20509'){
                        $itemConfig['size'] = $v['name'];
                        $itemConfig['size_cny'] = $v['name_cny'];
                    }
                    else{
                        $itemConfig['comment'] .= $v['name'].';';
                    }

                    break;
                }
            }
        }
        return $itemConfig;
    }

    public function defaultAction()
    {
        if (!Login::auth()){
            return ;
        }
        global $otapilib;

        include_once dirname(dirname(dirname(__FILE__))).'/lib/PhpExcel/PHPExcel.php';
        $pExcel = new PHPExcel();

        $pExcel->setActiveSheetIndex(0);
        $this->aSheet = $pExcel->getActiveSheet();
        $this->aSheet->setTitle('Список товаров');
        foreach($this->headRow as $v){
            $this->appendCellToRow(1, $v);
        }

        $orders = $otapilib->GetSalesOrdersListForOperator($_SESSION['sid'], '');
        $orders = array_reverse($orders);
        $row = 1;

        foreach($orders as $order){
            if($order['Id'] != $_GET['order']){
                continue;
            }

            $user = $otapilib->GetUserInfoForOperator($_SESSION['sid'],$order['CustId']);
            $order_info = $otapilib->GetSalesOrderDetailsForOperator($_SESSION['sid'], $order['Id'], '', 0);
            $itemid = $order_info['SalesLinesList'][0]['ItemTaobaoId'];
            $itemconf = $order_info['SalesLinesList'][0]['ConfigId'];
            $config = $this->getItemConfig($itemid, $itemconf);

            /**
             * Fill rows
             */
            $row++;
            $col = 'A';
            $this->appendCellToRow($row, $order_info['SalesOrderInfo']['Id'], $col++);
            $this->appendCellToRow($row, $order_info['SalesOrderInfo']['CreatedDateTime'], $col++);
            $this->appendCellToRow($row, $order['CustId'], $col++);
            $this->appendCellToRow($row, $order_info['SalesLinesList'][0]['ItemTaobaoId'], $col++);

            //id магазина
            $this->appendCellToRow($row, $config['vendor'], $col++);

            $this->appendCellToRow($row, 'http://tao141.com/admin-old/index.php?sid=&cmd=orders&do=orderinfo&id='.$order_info['SalesOrderInfo']['Id'], $col++);
            $this->appendCellToRow($row, (int)$order_info['SalesLinesList'][0]['ItemTaobaoId']['Qty'], $col++);
            $this->appendCellToRow($row, $config['size'], $col++);
            $this->appendCellToRow($row, $config['color'], $col++);
            $this->appendCellToRow($row, $config['size_cny'], $col++);
            $this->appendCellToRow($row, $config['color_cny'], $col++);
            $this->appendCellToRow($row, (string)$order_info['SalesLinesList'][0]['OperatorComment']."\n".$config['comment'], $col++);
            $this->appendCellToRow($row, (string)$order_info['SalesOrderInfo']['DeliveryAmountInternal'], $col++);
            $this->appendCellToRow($row, $order_info['SalesLinesList'][0]['ItemExternalURL'], $col++);
            $this->appendCellToRow($row, $order_info['SalesLinesList'][0]['ItemImageURL'], $col++);

            $this->appendCellToRow($row, $user['RecipientFirstName'].' '.$user['RecipientMiddleName'].' '.$user['RecipientLastName'], $col++);

            $address = array();
            if($user['country']) $address[] = $user['country'];
            if($user['city']) $address[] = $user['city'];
            if($user['region']) $address[] = $user['region'];
            if((int)$user['postalcode']) $address[] = (int)$user['postalcode'];
            if($user['address']) $address[] = $user['address'];
            if($user['phone']) $address[] = $user['phone'];

            $this->appendCellToRow($row,
                implode(', ', $address),
                $col++);

            $this->aSheet->getRowDimension($row)->setRowHeight(20);

            $filename = date('md ').' '.$order['Id'].' '.$user['Id'].' '.$user['RecipientFirstName'].' '.$user['RecipientMiddleName'].' '.$user['RecipientLastName'];

            if($row>5)
                break;
        }

        $this->aSheet->getColumnDimension('A')->setWidth(20);
        $this->aSheet->getColumnDimension('B')->setWidth(20);
        $this->aSheet->getColumnDimension('C')->setWidth(20);
        $this->aSheet->getColumnDimension('D')->setWidth(20);
        $this->aSheet->getColumnDimension('E')->setWidth(30);
        $this->aSheet->getColumnDimension('F')->setWidth(30);
        $this->aSheet->getColumnDimension('G')->setWidth(17);
        $this->aSheet->getColumnDimension('H')->setWidth(17);
        $this->aSheet->getColumnDimension('I')->setWidth(17);
        $this->aSheet->getColumnDimension('J')->setWidth(17);
        $this->aSheet->getColumnDimension('K')->setWidth(17);
        $this->aSheet->getColumnDimension('L')->setWidth(30);
        $this->aSheet->getColumnDimension('M')->setWidth(30);
        $this->aSheet->getColumnDimension('N')->setWidth(30);
        $this->aSheet->getColumnDimension('O')->setWidth(30);
        $this->aSheet->getColumnDimension('P')->setWidth(30);
        $this->aSheet->getColumnDimension('Q')->setWidth(30);

        include(dirname(dirname(dirname(__FILE__)))."/lib/PhpExcel/PHPExcel/Writer/Excel5.php");
        $objWriter = new PHPExcel_Writer_Excel5($pExcel);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
    }

    public function exportOrdersAction(){
        if (!Login::auth())
            return ;
        global $otapilib;
        $sid = $_SESSION['sid'];
        $orders = $otapilib->GetSalesOrdersListForOperator($sid, '');
        $orders = Orders::filterOrders($orders, $_POST['filter']);

        include_once dirname(dirname(dirname(__FILE__))).'/lib/PhpExcel/PHPExcel.php';
        $pExcel = new PHPExcel();

        $pExcel->setActiveSheetIndex(0);
        $this->aSheet = $pExcel->getActiveSheet();
        $this->aSheet->setTitle('Список товаров');

        $this->appendCellToRow(1, 'номер заказа');
        $this->appendCellToRow(1, 'дата');
        $this->appendCellToRow(1, 'ФИО клиента');
        $this->appendCellToRow(1, 'ник (логин) клиента');
        $this->appendCellToRow(1, 'статус заказа');
        $this->appendCellToRow(1, 'фото товара');
        $this->appendCellToRow(1, 'наименование товара');
        $this->appendCellToRow(1, 'ссылка на товар в ТАОБАО');
        $this->appendCellToRow(1, 'цвет');
        $this->appendCellToRow(1, 'размер');
        $this->appendCellToRow(1, 'комментарий к заказу');
        $this->appendCellToRow(1, 'количество');
        $this->appendCellToRow(1, 'цена товара на ТАОБАО');
        $this->appendCellToRow(1, 'общая стоимость товара');
        $this->appendCellToRow(1, 'стоимость доставки по Китаю');

        $row = 1;
        foreach($orders as $order){
            $user = $otapilib->GetUserInfoForOperator($_SESSION['sid'],$order['CustId']);
            $order_info = $otapilib->GetSalesOrderDetailsForOperator($_SESSION['sid'], $order['Id'], '', 0);

            foreach($order_info['SalesLinesList'] as $order_details){
                $itemid = $order_details['ItemTaobaoId'];
                $itemconf = $order_details['ConfigId'];
                $config = $this->getItemConfig($itemid, $itemconf);

                $row++;
                $col = 'A';
                $this->appendCellToRow($row, $order_info['SalesOrderInfo']['Id'], $col++); //номер заказа
                $this->appendCellToRow($row, $order_info['SalesOrderInfo']['CreatedDateTime'], $col++); //дата
                $this->appendCellToRow($row, $user['RecipientFirstName'].' '.$user['RecipientMiddleName'].' '.$user['RecipientLastName'], $col++);//фио клиента
                $this->appendCellToRow($row, $user['Login'], $col++);//ник
                $this->appendCellToRow($row, $order['StatusName'], $col++); //статус заказа
                $this->appendCellToRow($row, $order_details['ItemImageURL'], $col++);//картинка
                $this->appendCellToRow($row, $config['title'], $col++); //название товара
                $this->appendCellToRow($row, $order_details['ItemExternalURL'], $col++); //ссылка на таобао
                $this->appendCellToRow($row, $config['color'], $col++); //цвет
                $this->appendCellToRow($row, $config['size'], $col++); //размер
                $this->appendCellToRow($row, (string)$order_details['OperatorComment']."\n".$config['comment'], $col++); //комментарий к заказу
                $this->appendCellToRow($row, (int)$order_details['ItemTaobaoId']['Qty'], $col++); //количество
                $this->appendCellToRow($row, $config['masterprice'], $col++); //цена на таобао
                $this->appendCellToRow($row, (float)$config['masterprice']*(int)$order_details['ItemTaobaoId']['Qty'], $col++); //общая цена
                $this->appendCellToRow($row, (string)$order_info['SalesOrderInfo']['DeliveryAmountInternal'], $col++);
                $this->aSheet->getRowDimension($row)->setRowHeight(20);
            }
        }

        $this->aSheet->getColumnDimension('A')->setWidth(20);
        $this->aSheet->getColumnDimension('B')->setWidth(20);
        $this->aSheet->getColumnDimension('C')->setWidth(20);
        $this->aSheet->getColumnDimension('D')->setWidth(20);
        $this->aSheet->getColumnDimension('E')->setWidth(30);
        $this->aSheet->getColumnDimension('F')->setWidth(30);
        $this->aSheet->getColumnDimension('G')->setWidth(17);
        $this->aSheet->getColumnDimension('H')->setWidth(17);
        $this->aSheet->getColumnDimension('I')->setWidth(17);
        $this->aSheet->getColumnDimension('J')->setWidth(17);
        $this->aSheet->getColumnDimension('K')->setWidth(17);
        $this->aSheet->getColumnDimension('L')->setWidth(30);
        $this->aSheet->getColumnDimension('M')->setWidth(30);
        $this->aSheet->getColumnDimension('N')->setWidth(30);
        $this->aSheet->getColumnDimension('O')->setWidth(30);
        $this->aSheet->getColumnDimension('P')->setWidth(30);
        $this->aSheet->getColumnDimension('Q')->setWidth(30);

        include(dirname(dirname(dirname(__FILE__)))."/lib/PhpExcel/PHPExcel/Writer/Excel5.php");
        $objWriter = new PHPExcel_Writer_Excel5($pExcel);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="export.xls"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
    }
}
?>