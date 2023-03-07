<?php

OTBase::import('system.admin.lib.ERC');
OTBase::import('system.lib.helpers.ProductsHelper');

class ExportExcelNew
{
    private $aSheet;
    private $objWriter;

    private $validImages = array('jpg', 'png', 'gif', 'bmp', 'jpeg');

    const DEFAULT_IMAGE_SIZE = 160;

    public function __construct($pExcel, $objWriter)
    {
        $pExcel->setActiveSheetIndex(0);
        $this->aSheet = $pExcel->getActiveSheet();
        $this->objWriter = $objWriter;
    }

    public function exportOrder($orderInfo, $currencies, $itemsIds = array())
    {
        $curRate = ' * 0.35';
        //Знак действия нужен если мы найдем обратный курс валюты сайта к юаню и тогда надо в формуле будет делить
        //Берем кэфицент юаня к валюте сайта
        foreach ($currencies['CurrencyRateList'] as $currency) {
            if (($currency['FirstCode'] == 'CNY') and ($currency['SecondCode'] == $currencies['InternalCurrencyCode'])) {
                $curRate = ' * ' . $currency['Rate'];
                break;
            }
            if (($currency['SecondCode'] == 'CNY') and ($currency['FirstCode'] == $currencies['InternalCurrencyCode'])) {
                $curRate = ' / ' . $currency['Rate'];
                break;
            }
        }

        $this->aSheet->setTitle(Lang::get('goods_list'));
        ini_set('display_errors', 'On');

        $lang = isset($_SESSION['active_lang_admin']) ? $_SESSION['active_lang_admin'] : 'ru';
        $config = simplexml_load_file(ORDER_EXCEL_EXPORT_NEW_PATH.'config/export_'. $lang .'.xml');
        foreach ($config->header->column as $v) {
            $this->appendCellToRow(1, (string)$v);
        }

        $this->setColumnStyles();

        $number = 0;
        $itemsIndex = 0;
        foreach ($orderInfo['SalesLinesList'] as $line) {
            if (! empty($itemsIds) && ! in_array($line['id'], $itemsIds)) {
                // Экспортируем только те товары, которые были выбраны для экспорта
                continue;
            }

            $quantity = $line['Qty'];

            $col = 'A';
            $this->appendCellToRow($number+2, ++$itemsIndex, $col++); //A

            $col = 'B';
            $this->appendCellToRow($number+2, $line['linenum'], $col++); //B

            $this->aSheet
                ->getStyle('A'.($number+2))
                ->getFont()
                ->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ));

            $this->aSheet
            ->getStyle('B'.($number+2))
            ->getFont()
            ->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ));


            $this->appendCellToRow($number+2, $line['ItemExternalURL'], $col++); //C

            $this->aSheet
                ->getCell('C'.($number+2))
                ->getHyperlink()
                ->setUrl($line['ItemExternalURL']);

            $col_img = $col++;
            $img_local = $this->_downloadItemImage($line['ItemImageURL'], ProductsHelper::isYahooProduct($line));
            //echo $line['ItemImageURL'];die;
            if (file_exists($img_local) && @getimagesize($img_local)) {
                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                    $objDrawing->setPath($img_local);
                    $row = $number + 2;
                    $size = $this->_getImageSizeForExport();
                    $objDrawing->setCoordinates($col_img . $row);
                    $objDrawing->setWorksheet($this->aSheet);
                    $objDrawing->setHeight($size);
                    $objDrawing->setWidth($size);
            }

            $this->appendCellToRow($number+2, $line['ItemTaobaoId'], $col++); //E
            $this->appendCellToRow($number+2, preg_replace('/\s+/',' ',$line['ConfigText'].'
            '.$line['ConfigExternalTextOrig']), $col++); //F


            $this->appendCellToRow($number+2, (string)$quantity, $col++); //G
            $this->appendCellToRow($number+2, (string)$line['TaoBaoPrice'], $col++); //H
            $this->appendCellToRow($number+2, $line['taobaodelivery'], $col++); //I
            $this->appendCellToRow($number+2, '=G'.($number+2).'*H'.($number+2).'+I'.($number+2), $col++); //J

            $idx = $number+2;

            //Цена закупки должан быть без наценки и надо брать курс с настроек
            $buyPrice = "=(G$idx *(H$idx+I$idx))" . $curRate;
            $newBuyPrice = Plugins::invokeEvent('onChangeBuyPriceInExcelExport', array('idx' => $idx));
            $buyPrice = $newBuyPrice === false ? $buyPrice : $newBuyPrice;

            $this->appendCellToRow($number+2, $buyPrice, $col++); //K
            $this->appendCellToRow($number+2, round((float)$line['AmountCust']), $col++); //L
            $this->appendCellToRow($number+2, "=L$idx-K$idx", $col++); //M
            $this->aSheet->getRowDimension($number+2)->setRowHeight(120);

            $number++;
        }

        $this->aSheet->getStyle('A1:Z'.($number+2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->aSheet->getStyle('A1:Z'.($number+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->aSheet->getStyle('E1:E'.($number+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->aSheet->getStyle('A1:Z'.($number+2))->applyFromArray(
            array('font' => array('size' => 10))
        );

        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_DOUBLE
                )
            )
        );

        $this->aSheet->getStyle('A1:M'.($number+3))->applyFromArray($styleArray);

        $this->aSheet->setCellValue('G'.($number+3),'=sum(G1:G'.($number+2).')');
        $this->aSheet->setCellValue('H'.($number+3),'=sum(H1:H'.($number+2).')');
        $this->aSheet->setCellValue('I'.($number+3),'=sum(I1:I'.($number+2).')');
        $this->aSheet->setCellValue('J'.($number+3),'=sum(J1:J'.($number+2).')');

        $this->aSheet->setCellValue('K'.($number+3),'=sum(K1:K'.($number+2).')');
        $this->aSheet->setCellValue('L'.($number+3),'=sum(L1:L'.($number+2).')');
        $this->aSheet->setCellValue('M'.($number+3),'=sum(M1:M'.($number+2).')');
        $this->aSheet->getRowDimension($number+3)->setRowHeight(30);

        $this->saveExportFile(OrdersProxy::normalizeOrderId($orderInfo['SalesOrderInfo']['Id']) . '_' . date('j-m-y_h-i-s'));

    }
    public function exportOrderWithGroupedItems($orderInfo, $currencies, $itemsIds = array(), $groupBy)
    {
        $curRate = ' * 0.35';
        //Знак действия нужен если мы найдем обратный курс валюты сайта к юаню и тогда надо в формуле будет делить
        //Берем кэфицент юаня к валюте сайта
        foreach ($currencies['CurrencyRateList'] as $currency) {
            if (($currency['FirstCode'] == 'CNY') and ($currency['SecondCode'] == $currencies['InternalCurrencyCode'])) {
                $curRate = ' * ' . $currency['Rate'];
                break;
            }
            if (($currency['SecondCode'] == 'CNY') and ($currency['FirstCode'] == $currencies['InternalCurrencyCode'])) {
                $curRate = ' / ' . $currency['Rate'];
                break;
            }
        }

        $this->aSheet->setTitle(Lang::get('goods_list'));
        ini_set('display_errors', 'On');

        $lang = isset($_SESSION['active_lang_admin']) ? $_SESSION['active_lang_admin'] : 'ru';
        $config = simplexml_load_file(ORDER_EXCEL_EXPORT_NEW_PATH . 'config/export_' . $lang . '.xml');
        foreach ($config->header->column as $v) {
            $this->appendCellToRow(1, (string)$v);
        }

        $this->setColumnStyles();

        $itemsGrouped = array();
        foreach ($orderInfo['SalesLinesList'] as $item) {
            if (!empty($itemsIds) && !in_array($item['id'], $itemsIds)) {
                // Экспортируем только те товары, которые были выбраны для экспорта
                continue;
            }
            $itemsGrouped[$item[$groupBy]][] = $item;
        }
        if ($groupBy == 'statusid') {
            ksort($itemsGrouped);
        }

        $number = 0;
        $itemsIndex = 0;
        foreach ($itemsGrouped as $key => $item) {
            if ($groupBy == 'statusid') {
                $title = $item[0]['statusname'];
            } elseif ($groupBy == 'vendid') {
                $title = Lang::get('vendor') . ': ' . $key;
            } else {
                $title = '';
            }
            $number++;
            $this->appendCellToRow($number + 1 , $title, 'C');
            $from = $number + 2;
            $to = $from + count($item) - 1;
            $this->appendCellToRow($number + 1 , "=sum(J{$from}:J{$to})", 'J');
            $this->appendCellToRow($number + 1 , "=sum(K{$from}:K{$to})", 'K');
            $this->appendCellToRow($number + 1 , "=sum(L{$from}:L{$to})", 'L');

            foreach ($item as $line) {
                $quantity = $line['Qty'];

                $col = 'A';
                $this->appendCellToRow($number + 2, ++$itemsIndex, $col++); //A

                $this->appendCellToRow($number + 2, $line['linenum'], $col++); //B

                $this->aSheet
                    ->getStyle('A' . ($number + 2))
                    ->getFont()
                    ->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));

                $this->aSheet
                    ->getStyle('B' . ($number + 2))
                    ->getFont()
                    ->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED));


                $this->appendCellToRow($number + 2, $line['ItemExternalURL'], $col++); //C

                $this->aSheet
                    ->getCell('C' . ($number + 2))
                    ->getHyperlink()
                    ->setUrl($line['ItemExternalURL']);

                $col_img = $col++;
                $img_local = $this->_downloadItemImage($line['ItemImageURL'], ProductsHelper::isYahooProduct($line));
                //echo $line['ItemImageURL'];die;
                if (file_exists($img_local) && @getimagesize($img_local)) {
                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                    $objDrawing->setPath($img_local);
                    $row = $number + 2;
                    $size = $this->_getImageSizeForExport();
                    $objDrawing->setCoordinates($col_img . $row);
                    $objDrawing->setWorksheet($this->aSheet);
                    $objDrawing->setHeight($size);
                    $objDrawing->setWidth($size);
                }

                $this->appendCellToRow($number + 2, $line['ItemTaobaoId'], $col++); //E
                $this->appendCellToRow($number + 2, preg_replace('/\s+/', ' ', $line['ConfigText'] . '
                ' . $line['ConfigExternalTextOrig']), $col++); //F


                $this->appendCellToRow($number + 2, (string)$quantity, $col++); //G
                $this->appendCellToRow($number + 2, (string)$line['TaoBaoPrice'], $col++); //H
                $this->appendCellToRow($number + 2, $line['taobaodelivery'], $col++); //I
                $this->appendCellToRow($number + 2, '=G' . ($number + 2) . '*H' . ($number + 2) . '+I' . ($number + 2), $col++); //J

                $idx = $number + 2;

                //Цена закупки должан быть без наценки и надо брать курс с настроек
                $buyPrice = "=(G$idx *(H$idx+I$idx))" . $curRate;
                $newBuyPrice = Plugins::invokeEvent('onChangeBuyPriceInExcelExport', array('idx' => $idx));
                $buyPrice = $newBuyPrice === false ? $buyPrice : $newBuyPrice;

                $this->appendCellToRow($number + 2, $buyPrice, $col++); //K
                $this->appendCellToRow($number + 2, round((float)$line['AmountCust']), $col++); //L
                $this->appendCellToRow($number + 2, "=L$idx-K$idx", $col++); //M
                $this->aSheet->getRowDimension($number + 2)->setRowHeight(120);

                $number++;
        }
    }

        $this->aSheet->getStyle('A1:Z'.($number+2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->aSheet->getStyle('A1:Z'.($number+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->aSheet->getStyle('E1:E'.($number+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->aSheet->getStyle('A1:Z'.($number+2))->applyFromArray(
            array('font' => array('size' => 10))
        );

        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_DOUBLE
                )
            )
        );

        $this->aSheet->getStyle('A1:M'.($number+3))->applyFromArray($styleArray);

        $this->aSheet->setCellValue('G'.($number+3),'=sum(G1:G'.($number+2).')');
        $this->aSheet->setCellValue('H'.($number+3),'=sum(H1:H'.($number+2).')');
        $this->aSheet->setCellValue('I'.($number+3),'=sum(I1:I'.($number+2).')');
        $this->aSheet->setCellValue('J'.($number+3),'=sum(J1:J'.($number+2).')');

        $this->aSheet->setCellValue('K'.($number+3),'=sum(K1:K'.($number+2).')');
        $this->aSheet->setCellValue('L'.($number+3),'=sum(L1:L'.($number+2).')');
        $this->aSheet->setCellValue('M'.($number+3),'=sum(M1:M'.($number+2).')');
        $this->aSheet->getRowDimension($number+3)->setRowHeight(30);

        $this->saveExportFile(OrdersProxy::normalizeOrderId($orderInfo['SalesOrderInfo']['Id']) . '_' . date('j-m-y_h-i-s'));

    }

    public function exportOrdersItems(array $ordersItems, $currencies)
    {

        $curRate = ' * 0.35';
        //Знак действия нужен если мы найдем обратный курс валюты сайта к юаню и тогда надо в формуле будет делить
        //Берем кэфицент юаня к валюте сайта
        foreach ($currencies['CurrencyRateList'] as $currency) {
            if (($currency['FirstCode'] == 'CNY') and ($currency['SecondCode'] == $currencies['InternalCurrencyCode'])) {
                $curRate = ' * ' . $currency['Rate'];
                break;
            }
            if (($currency['SecondCode'] == 'CNY') and ($currency['FirstCode'] == $currencies['InternalCurrencyCode'])) {
                $curRate = ' / ' . $currency['Rate'];
                break;
            }
        }


        $this->aSheet->setTitle(Lang::get('goods_list'));
        ini_set('display_errors', 'On');

        $lang = isset($_SESSION['active_lang_admin']) ? $_SESSION['active_lang_admin'] : 'ru';
        $config = simplexml_load_file(ORDER_EXCEL_EXPORT_NEW_PATH.'config/export_'. $lang .'.xml');
        foreach ($config->header->column as $v) {
            $this->appendCellToRow(1, (string)$v);
        }

        $this->setColumnStyles();
        $number = 0;
        $itemsIndex = 0;
        foreach ($ordersItems as $orderId => $items) {

            $this->appendOrderHead($number, $config->header->column, OrdersProxy::normalizeOrderId($orderId));
            $number++;

            foreach ($items as $line) {
                $quantity = $line['Qty'];

                $col = 'A';
                $this->appendCellToRow($number+2, ++$itemsIndex, $col++); // A

                $col = 'B';
                $this->appendCellToRow($number+2, $line['linenum'], $col++); // B

                $this->aSheet
                    ->getStyle('A'.($number+2))
                    ->getFont()
                    ->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ));

                $this->aSheet
                ->getStyle('B'.($number+2))
                ->getFont()
                ->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED ));


                $this->appendCellToRow($number+2, $line['ItemExternalURL'], $col++); // C

                $this->aSheet
                    ->getCell('C'.($number+2))
                    ->getHyperlink()
                    ->setUrl($line['ItemExternalURL']);

                $col_img = $col++;
                // download img
                $img_local = $this->_downloadItemImage($line['ItemImageURL'], ProductsHelper::isYahooProduct($line));
                //echo $line['ItemImageURL'];die;
                if (file_exists($img_local) && @getimagesize($img_local)) {
                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                    $objDrawing->setPath($img_local);
                    $row = $number + 2;
                    $size = $this->_getImageSizeForExport();
                    $objDrawing->setCoordinates($col_img.$row);
                    $objDrawing->setWorksheet($this->aSheet);
                    $objDrawing->setHeight($size);
                    $objDrawing->setWidth($size);
                }

                $this->appendCellToRow($number+2, $line['ItemTaobaoId'], $col++); // E
                $this->appendCellToRow($number+2, preg_replace('/\s+/',' ',$line['ConfigText'].'
                '.$line['ConfigExternalTextOrig']), $col++); // F


                $this->appendCellToRow($number+2, (string)$quantity, $col++); // G
                $this->appendCellToRow($number+2, $line['taobaopricewithdiscount'], $col++); // H
                $this->appendCellToRow($number+2, $line['taobaodelivery'], $col++); // I
                //$this->appendCellToRow($number+2, '=F'.($number+2).'*G'.($number+2).'+H'.($number+2), $col++); //count * price + item delivery price

                //count * (price + item delivery price)
                $this->appendCellToRow($number+2, '=G'.($number+2).'* (H'.($number+2).'+I'.($number+2).')', $col++); //J

                $idx = $number+2;

                //Цена закупки должан быть без наценки надо брать курс с настроек
                $buyPrice = "=(G$idx *(H$idx+I$idx))" . $curRate;
                $newBuyPrice = Plugins::invokeEvent('onChangeBuyPriceInExcelExport', array('idx' => $idx));
                $buyPrice = $newBuyPrice === false ? $buyPrice : $newBuyPrice;

                $this->appendCellToRow($number+2, $buyPrice, $col++); // K
                $this->appendCellToRow($number+2, round((float)$line['AmountCust']), $col++); // L
                $this->appendCellToRow($number+2, "=L$idx-K$idx", $col++); // M
                $this->aSheet->getRowDimension($number+2)->setRowHeight(120);

                $number++;
            }
        }
        $this->aSheet->getStyle('A1:Z'.($number+2))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->aSheet->getStyle('A1:Z'.($number+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->aSheet->getStyle('E1:E'.($number+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->aSheet->getStyle('A1:Z'.($number+2))->applyFromArray(
            array('font' => array('size' => 10))
        );

        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_DOUBLE
                )
            )
        );

        $this->aSheet->getStyle('A1:M'.($number+3))->applyFromArray($styleArray);

        $this->aSheet->setCellValue('G'.($number+3),'=sum(G1:G'.($number+2).')');
        $this->aSheet->setCellValue('H'.($number+3),'=sum(H1:H'.($number+2).') - sum(I1:I'.($number+2).')');
        $this->aSheet->setCellValue('I'.($number+3),'=sum(I1:I'.($number+2).')');
        $this->aSheet->setCellValue('J'.($number+3),'=sum(J1:J'.($number+2).')');

        $this->aSheet->setCellValue('K'.($number+3),'=sum(K1:K'.($number+2).')');
        $this->aSheet->setCellValue('L'.($number+3),'=sum(L1:L'.($number+2).')');
        $this->aSheet->setCellValue('M'.($number+3),'=sum(M1:M'.($number+2).')');
        $this->aSheet->getRowDimension($number+3)->setRowHeight(30);

        $this->saveExportFile('BatchOrdersItemsExport');
    }

    private function appendCellToRow($row, $val, $col = '')
    {
        $column = $col ? $col : $this->aSheet->getHighestColumn();

        if($this->aSheet->cellExists($column.$row))
            $column++;
        $this->aSheet->setCellValue($column.$row,$val);
    }

    private function setColumnStyles()
    {
        $col = 'A';
        $this->aSheet->getColumnDimension($col++)->setWidth(5);
        $this->aSheet->getColumnDimension($col++)->setWidth(5);
        $this->aSheet->getColumnDimension($col++)->setWidth(65);

        $this->aSheet->getColumnDimension($col++)->setWidth(23);
        $this->aSheet->getColumnDimension($col++)->setWidth(14);

        $this->aSheet->getColumnDimension($col++)->setWidth(40);
        $this->aSheet->getColumnDimension($col++)->setWidth(10);
        $this->aSheet->getColumnDimension($col++)->setWidth(7);
        $this->aSheet->getColumnDimension($col++)->setWidth(10);
        $this->aSheet->getColumnDimension($col++)->setWidth(10);
        $this->aSheet->getColumnDimension($col++)->setWidth(15);
        $this->aSheet->getColumnDimension($col++)->setWidth(15);
        $this->aSheet->getColumnDimension($col++)->setWidth(15);
    }

    private function saveExportFile($filename)
    {
        $this->objWriter->save(CFG_APP_ROOT . '/cache/' . $filename . '.xls');
        echo json_encode(array(
            'url' => '/cache/' . $filename . '.xls'
        ));
    }

    /*
     * проверить существует ли каталог, если нет - создать
     */
    private function _isDir($path)
    {
        if (!file_exists($path)) mkdir($path, 0777);
    }

    private function _downloadItemImage($url, $isOriginalSize = false)
    {
        $urlInfo = explode('/', $url);
        $itemName = end($urlInfo);
        $cacheDir = CFG_APP_ROOT.'/cache/';
        $downloadPath = $cacheDir.'orders_excel_export_new/';
        $this->_isDir($cacheDir);
        $this->_isDir($downloadPath);
        $this->_isDir($downloadPath.substr($itemName, 0, 4).'/');
        $img_local = $downloadPath . substr($itemName, 0, 4).'/'.$itemName;
        if (!file_exists($img_local) || !@getimagesize($img_local)) {
            @unlink($img_local);
            $this->_downloadImage($url, $img_local, $isOriginalSize);
        }
        return $img_local;
    }

    private function _downloadImage($url, $img_local, $isOriginalSize = false)
    {
        $pathInfo = explode('.', $img_local);
        /*if (! in_array(end($pathInfo), $this->validImages)) {
            return false;
        }*/

        if (! $isOriginalSize) {
            $size = $this->_getImageSizeForExport();
            $url .= '_' . $size . 'x' . $size . '.jpg';
        }

        $ch = curl_init($url);

        $fp = fopen($img_local, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/534.30 (KHTML, like Gecko) Chrome/12.0.742.112 Safari/534.30");
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        return true;
    }

    private function _getImageSizeForExport () {
        return defined('CFG_CACHE_ADMIN_IMAGES_SIZE') ? CFG_CACHE_ADMIN_IMAGES_SIZE : self::DEFAULT_IMAGE_SIZE;
    }

    private function appendOrderHead($number, $config, $orderId)
    {
        $col = 'A';
        foreach ($config as $v) {
            $lastCol = $col;
            $this->appendCellToRow($number+2, ($col == 'B' ? $orderId : ''), $col++);
        }
        $this->aSheet->getStyle("A".($number+2).":".$lastCol.($number+2))->getFont()->setBold(true);
        $this->aSheet->getStyle("A".($number+2).":".$lastCol.($number+2))->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'FFFF00')
                )
            )
        );
    }

    public function exportOrdersERC(array $orders, $currencyData)
    {
        $this->aSheet->setTitle('Orders list');
        ini_set('display_errors', 'On');

        $lang = 'en';
        $config = simplexml_load_file(ORDER_EXCEL_EXPORT_NEW_PATH.'config/export_erc_'. $lang .'.xml');
        $i = 0;
        foreach ($config->header->column as $v) {
            $this->appendCellToRow(1, (string)$v);
            $i++;
        }
        $col = 'B';
        $j = 0;
        foreach ($config->header->column as $v) {
            if ($j < $i - 1) {
                $this->appendCellToRow(2, '$', $col++);
            }
            $j++;
        }

        $TotalAmount = 0;
        $TaoBaoPrice = 0;
        $DeliveryLocal = 0;
        $TotalDeliveryAmount = 0;
        $TotalDeliveryAmountLogist = 0;
        $VvodDSTotal = 0;
        $DAgentTotal = 0;
        $DLogistTotal = 0;

        $this->setColumnStyles();
        $number = 0;

        foreach ($orders as $order) {

            $stats = ERC::calculateOrderStats($order, $currencyData);

            $TotalAmount += $stats['TotalAmount'];
            $TaoBaoPrice += $stats['TaoBaoPrice'];
            //$TotalDeliveryAmount += $stats['TotalDeliveryAmount'];
            $TotalDeliveryAmountLogist += $stats['TotalDeliveryAmountLogist'];
            $VvodDSTotal += $stats['VvodDSTotal'];
            $DAgentTotal += $stats['DAgentTotal'];
            $DLogistTotal += $stats['DLogistTotal'];
            $DOpentao = round($stats['TotalAmount'] * 0.02, 2);

            $number++;

            $col = 'A';

            $this->appendCellToRow($number+2, OrdersProxy::normalizeOrderId($order['id']) . "\nSend: " . substr($order['ShipmentDate'],0,10), $col++);
            $this->appendCellToRow($number+2, $stats['TotalAmount'], $col++);
            $this->appendCellToRow($number+2, $stats['TaoBaoPrice'] . "\n(" . $stats['TaoBaoPriceCNY'].' CNY)', $col++);
            $this->appendCellToRow($number+2, $stats['DeliveryLocal'] . "\n(" . $stats['DeliveryLocalCNY'].' CNY)', $col++);
            $this->appendCellToRow($number+2, $stats['TotalDeliveryAmountLogist'], $col++);
            $this->appendCellToRow($number+2, $stats['VvodDSTotal'], $col++);
            $this->appendCellToRow($number+2, $stats['DLogistTotal'], $col++);
            $this->appendCellToRow($number+2, $DOpentao, $col++);
            $this->appendCellToRow($number+2, $stats['DAgentTotal'], $col++);

            $this->aSheet->getRowDimension($number+2)->setRowHeight(120);
        }

        $columns = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I');
        foreach ($columns as $col) {
            $this->aSheet->getColumnDimension($col)->setWidth(15);
        }
        $this->aSheet->getStyle('A1:Z'.($number+3))->getAlignment()->setWrapText(true);
        $this->aSheet->getStyle('A1:Z'.($number+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->aSheet->getStyle('A1:Z'.($number+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->aSheet->getStyle('A1:Z'.($number+3))->applyFromArray(
            array('font' => array('size' => 10))
        );

        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_DOUBLE
                )
            )
        );

        $this->aSheet->getStyle('A1:I'.($number+3))->applyFromArray($styleArray);

        array_shift($columns);
        foreach ($columns as $col) {
            if (! in_array($col, array('C', 'D'))) {
                $this->aSheet->setCellValue($col.($number+3),'=sum('.$col.'3:'.$col.($number+2).')');
            }
        }

        $this->aSheet->getRowDimension($number+3)->setRowHeight(30);

        $this->saveExportFile('BatchOrdersItemsExportERC');
    }

    public function exportAgentsERC(array $orders, $currencyData)
    {
        $this->aSheet->setTitle('Orders list');
        ini_set('display_errors', 'On');

        $lang = 'en';
        $config = simplexml_load_file(ORDER_EXCEL_EXPORT_NEW_PATH.'config/export_erc_'. $lang .'.xml');
        $i = 0;
        foreach ($config->header->column as $v) {
            $this->appendCellToRow(1, (string)$v);
            $i++;
        }
        $col = 'B';
        $j = 0;
        foreach ($config->header->column as $v) {
            if ($j < $i - 1) {
                $this->appendCellToRow(2, '$', $col++);
            }
            $j++;
        }

        $TotalAmount = 0;
        $TaoBaoPrice = 0;
        $DeliveryLocal = 0;
        $TotalDeliveryAmount = 0;
        $TotalDeliveryAmountLogist = 0;
        $VvodDSTotal = 0;
        $DAgentTotal = 0;
        $DLogistTotal = 0;

        $this->setColumnStyles();
        $number = 0;

        foreach ($orders as $order) {

            $stats = ERC::calculateOrderStats($order, $currencyData);

            $TotalAmount += $stats['TotalAmount'];
            $TaoBaoPrice += $stats['TaoBaoPrice'];
            //$TotalDeliveryAmount += $stats['TotalDeliveryAmount'];
            $TotalDeliveryAmountLogist += $stats['TotalDeliveryAmountLogist'];
            $VvodDSTotal += $stats['VvodDSTotal'];
            $DAgentTotal += $stats['DAgentTotal'];
            $DLogistTotal += $stats['DLogistTotal'];
            $DOpentao = round($stats['TotalAmount'] * 0.02, 2);

            $number++;

            $col = 'A';

            $this->appendCellToRow($number+2, OrdersProxy::normalizeOrderId($order['id']) . "\nSend: " . substr($order['ShipmentDate'],0,10), $col++);
            $this->appendCellToRow($number+2, $stats['TotalAmount'], $col++);
            $this->appendCellToRow($number+2, $stats['TaoBaoPrice'] . "\n(" . $stats['TaoBaoPriceCNY'].' CNY)', $col++);
            $this->appendCellToRow($number+2, $stats['DeliveryLocal'] . "\n(" . $stats['DeliveryLocalCNY'].' CNY)', $col++);
            $this->appendCellToRow($number+2, $stats['TotalDeliveryAmountLogist'], $col++);
            $this->appendCellToRow($number+2, $stats['VvodDSTotal'], $col++);
            $this->appendCellToRow($number+2, $stats['DLogistTotal'], $col++);
            $this->appendCellToRow($number+2, $DOpentao, $col++);
            $this->appendCellToRow($number+2, $stats['DAgentTotal'], $col++);

            $this->aSheet->getRowDimension($number+2)->setRowHeight(120);
        }

        $columns = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I');
        foreach ($columns as $col) {
            $this->aSheet->getColumnDimension($col)->setWidth(15);
        }
        $this->aSheet->getStyle('A1:Z'.($number+3))->getAlignment()->setWrapText(true);
        $this->aSheet->getStyle('A1:Z'.($number+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->aSheet->getStyle('A1:Z'.($number+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->aSheet->getStyle('A1:Z'.($number+3))->applyFromArray(
            array('font' => array('size' => 10))
        );

        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_DOUBLE
                )
            )
        );

        $this->aSheet->getStyle('A1:I'.($number+3))->applyFromArray($styleArray);

        array_shift($columns);
        foreach ($columns as $col) {
            if (! in_array($col, array('C', 'D'))) {
                $this->aSheet->setCellValue($col.($number+3),'=sum('.$col.'3:'.$col.($number+2).')');
            }
        }

        $this->aSheet->getRowDimension($number+3)->setRowHeight(30);

        $this->saveExportFile('BatchOrdersItemsExportERC');
    }
}
