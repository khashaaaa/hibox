<?php

OTBase::import('system.admin.lib.otapi_providers.CallStatistics');
OTBase::import('system.admin.lib.otapi_providers.InstanceOptionsInfo');
OTBase::import('system.admin.lib.otapi_providers.ReportsProvider');

class Reports extends GeneralUtil
{
    const ITEMS_PER_PAGE = 10;

    protected $_template = 'service_statistics';
    protected $_template_path = 'reports/';

    /**
     * @var CallStatistics
     */
    protected $callStatistics;

    /**
     * @var InstanceOptionsInfo
     */
    protected $instanceOptionsInfo;

    /**
     * @var InstanceBillingInfo
     */
    protected $InstanceBillingInfo;

    public function __construct()
    {
        parent::__construct();
        $this->callStatistics = new CallStatistics($this->otapilib);
        $this->instanceOptionsInfo = new InstanceOptionsInfo($this->otapilib);
        $this->InstanceBillingInfo = new InstanceBillingInfo($this->otapilib);
    }

    public function defaultAction($request)
    {
        try {
            $tarif = $this->instanceOptionsInfo->GetTariff(Session::get('sid'));
            
            $dateFrom = $request->getValue('fromdate');
            $dateFrom = ! empty($dateFrom) ? $dateFrom : date('d.m.Y', mktime(0, 0, 0, date('m'), date('d') - 30, date('Y')));
            $dateTo = $request->getValue('todate');
            $dateTo = ! empty($dateTo) ? $dateTo : date('d.m.Y', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')));
            $timeFrom = date('Y-m-d H:i:s', strtotime($dateFrom . ' 00:00:00'));
            $timeFrom = str_replace(' ', 'T', $timeFrom); 
            $timeTo = date('Y-m-d H:i:s', strtotime($dateTo . ' 23:59:59'));
            $timeTo = str_replace(' ', 'T', $timeTo);
            $timePeriod =  $request->getValue('timePeriod');
            $timePeriod = ! empty($timePeriod) ? $timePeriod : 'Daily';
            $statistic = $this->callStatistics->GetCallStatistics();
            $callsCount = $this->callStatistics->getCallsCount($timeFrom, $timeTo, $timePeriod);
        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
        }

        $this->tpl->assign('tarif', $tarif);
        $this->tpl->assign('statistic', $statistic);
        $this->tpl->assign('callsCount', $callsCount['callsCount']);
        $this->tpl->assign('totalCount', $callsCount['totalCount']);
        $this->tpl->assign('payedCount', $callsCount['payedCount']);
        $this->tpl->assign('errorCount', $callsCount['errorCount']);
        $this->tpl->assign('dateFrom', $dateFrom);
        $this->tpl->assign('dateTo', $dateTo);
        $this->tpl->assign('timePeriod', $timePeriod);
        print $this->fetchTemplate();
    }
    
    public function callsAction($request)
    {
        $this->_template = 'calls_statistics';
        try {
            $dateFrom = $request->getValue('fromdate');
            $dateFrom = ! empty($dateFrom) ? $dateFrom : date('01.m.Y');
            $dateTo = $request->getValue('todate');
            $dateTo = ! empty($dateTo) ? $dateTo : date('d.m.Y');
            $timeFrom = date('Y-m-d H:i:s', strtotime($dateFrom . ' 00:00:00'));
            $timeFrom = str_replace(' ', 'T', $timeFrom);
            $timeTo = date('Y-m-d H:i:s', strtotime($dateTo . ' 23:59:59'));
            $timeTo = str_replace(' ', 'T', $timeTo);
            $timePeriod =  $request->getValue('timePeriod');
            $timePeriod = ! empty($timePeriod) ? $timePeriod : 'Daily';
            $method = $request->getValue('method');
            if (empty($method)) {
                $callsCount = $this->callStatistics->getCallsCount($timeFrom, $timeTo, $timePeriod, true);
            } else {
                $callsCount = $this->callStatistics->getMethodCallsCount($timeFrom, $timeTo, $timePeriod, $method);
            }
        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
        }
    
        $this->tpl->assign('callsCount', $callsCount['callsCount']);
        $this->tpl->assign('totalCount', $callsCount['totalCount']);
        $this->tpl->assign('payedCount', $callsCount['payedCount']);
        $this->tpl->assign('errorCount', $callsCount['errorCount']);
        $this->tpl->assign('dateFrom', $dateFrom);
        $this->tpl->assign('dateTo', $dateTo);
        $this->tpl->assign('method', $method);
        $this->tpl->assign('timePeriod', $timePeriod);
        print $this->fetchTemplate();
    }

    public function billingAction($request)
    {
        $this->_template = 'billing_info';

        try {
            $sid = Session::get('sid');
            $language = Session::getActiveAdminLang();
            $page = $this->getPageDisplayParams($request, self::ITEMS_PER_PAGE);

            $this->InstanceBillingInfo->initSearchPaidBills($language, $sid, $page['offset'], $page['limit']);
            $this->InstanceBillingInfo->initSearchUnpaidBills($language, $sid);
            $this->InstanceBillingInfo->initRateHistory($language, $sid);
            $this->InstanceBillingInfo->initInstanceOptions($language, $sid);
            $this->InstanceBillingInfo->initCalculateRent($sid);

            $this->InstanceBillingInfo->doRequests();

            $paidBillsCount = (int) $this->InstanceBillingInfo->getPaidBills()->GetResult()->GetRawData()->TotalCount;
            $unpaidBillsCount = (int) $this->InstanceBillingInfo->getUnpaidBills()->GetResult()->GetRawData()->TotalCount;

            $this->tpl->assign('paginator', new Paginator($paidBillsCount, $page['number'], $page['limit']));

            $this->tpl->assign('paidBillsCount', $paidBillsCount);
            $this->tpl->assign('unpaidBillsCount', $unpaidBillsCount);
            $this->tpl->assign('paidBills', $this->InstanceBillingInfo->getPaidBills()->GetResult()->GetContent());
            $this->tpl->assign('unpaidBills', $this->InstanceBillingInfo->getUnpaidBills()->GetResult()->GetContent());
            $this->tpl->assign('rateHistory', $this->InstanceBillingInfo->getRateHistory()->GetResult()->GetElements());
            $this->tpl->assign('rent', $this->InstanceBillingInfo->getCalculateRent()->GetResult()->GetTotalRent());

            $this->tpl->assign('rate', $this->InstanceBillingInfo->getInstanceOptions()->GetResult()->GetTariff());
            $this->tpl->assign('account', $this->InstanceBillingInfo->getInstanceOptions()->GetResult()->GetAccount());//GetPrepayment GetDebt
            $this->tpl->assign('hosting', $this->InstanceBillingInfo->getInstanceOptions()->GetResult()->GetHosting());
        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
            $this->tpl->assign('bills', new OtapiArrayOfBillInfo());
        }
        print $this->fetchTemplate();
    }

    public function siteSpeedAction()
    {
        $this->_template = 'site_speed';

        print $this->fetchTemplate();
    }

    public function getCurrentBoxStatisticsAction($request)
    {
        $lang = $this->getActiveLang($request);
        $sid = Session::get('sid');
        $boxFullTime = null;
        $apiNetworkTimePerCall = null;
        try {
            OTAPILib2::GetCurrentBoxStatistics($lang, $sid, '', $answer);
            OTAPILib2::makeRequests();

            $boxFullTimeSeconds = round($answer->GetResult()->GetBoxFullTime() / 1000, 2);
            $apiNetworkTimePerCallSeconds = round($answer->GetResult()->GetApiNetworkTime() / 1000, 2);
        } catch (ServiceException $exception) {
            $this->respondAjaxError($exception->getMessage());
        }

        $this->sendAjaxResponse(array(
            'boxFullTime' => $boxFullTimeSeconds,
            'apiNetworkTimePerCall' => $apiNetworkTimePerCallSeconds
        ));
    }

    public function getBoxArchiveStatisticsAction($request)
    {
        $lang = $this->getActiveLang($request);
        $sid = Session::get('sid');
        $boxFullTime = null;
        $apiNetworkTimePerCall = null;
        $data = array();

        try {
            OTAPILib2::GetArchiveBoxStatistics($lang, $sid, '', $answer);
            OTAPILib2::makeRequests();

            foreach($answer->GetResult()->GetContent()->GetItem() as $key => $item) {
                $date = strtotime($item->GetDate());
                $date = date('Y-m-d', $date);

                $data[$key]['Time'] = $date;
                $data[$key]['BoxFullTime'] = round($item->GetBoxFullTime() / 1000, 2);
                $data[$key]['BoxAverageTime'] = round(($item->GetBoxOtherTime() + $item->GetBoxWorkTime()) / 1000, 2);
                $data[$key]['ApiWorktime'] = round($item->GetApiWorkTime() / 1000, 2);
                $data[$key]['ApiNetworkTime'] = round($item->GetApiNetworkTime() / 1000, 2);
            }

        } catch (ServiceException $exception) {
            $this->respondAjaxError($exception->getMessage());
        }

        $this->sendAjaxResponse($data, true, true);
    }

    public function billingForPeriodAction($request){
        $this->_template = 'billing_period_info';
        $info = '';
        try {
            $dateFrom = $request->getValue('dateFrom');
            $dateTo = $request->getValue('dateTo');
            
            $dateFrom = date('c', strtotime($dateFrom . ' 00:00:00'));
            $dateTo = date('c', strtotime($dateTo . ' 23:59:59'));
            
            $dateFrom = str_replace("+03:00", "+04:00", $dateFrom);
            $dateTo = str_replace("+03:00", "+04:00", $dateTo);
            
            $this->InstanceBillingInfo->initSearchBills(Session::getActiveAdminLang(), Session::get('sid'));
            $this->InstanceBillingInfo->initRateHistory(Session::getActiveAdminLang(), Session::get('sid'));
            $this->InstanceBillingInfo->initInstanceOptions(Session::getActiveAdminLang(), Session::get('sid'));
            $this->InstanceBillingInfo->initCalculateRentToBill(Session::get('sid'), $dateFrom, $dateTo);
            
            $xmlData = '<CallArchivesSearchParameters><TimePeriod>Daily</TimePeriod><StartDate>' . $dateFrom . '</StartDate><EndDate>' . $dateTo . '</EndDate></CallArchivesSearchParameters>';            
            $this->InstanceBillingInfo->initGetCallArchives($xmlData);
            
            $this->InstanceBillingInfo->doRequests();
    
            $transactionsFile = $this->prepareXMLTransactions($this->InstanceBillingInfo->getCalculateRent()->GetResult()->GetTotalRent()->GetTurnover()->GetTransactions());
            
            $this->tpl->assign('rent', $this->InstanceBillingInfo->getCalculateRent()->GetResult()->GetTotalRent());
            $this->tpl->assign('rate', $this->InstanceBillingInfo->getInstanceOptions()->GetResult()->GetTariff());
            $this->tpl->assign('callStatistic', $this->InstanceBillingInfo->getCallArchives()->GetResult()->GetPaidCallsArchive()->GetRecords());
            $this->tpl->assign('transactionsFile', $transactionsFile);            
            $info = $this->fetchTemplateWithoutHeaderAndFooter(false);
            
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array(
            'info' => $info
        ));
    }

    public function viewBillAction($request)
    {
        $this->_template = 'view_bill';
        try {
            $bill = $this->getOneBill($request->get('billId'));
            $this->InstanceBillingInfo->initCalculateRentToBill(
                Session::get('sid'),
                $bill->GetSettlingPeriod()->GetDateFrom(),
                $bill->GetSettlingPeriod()->GetDateTo()
            );
            $this->InstanceBillingInfo->doRequests();
            $this->tpl->assign('bill', $bill);
            $this->tpl->assign('rent', $this->InstanceBillingInfo->getCalculateRent()->GetResult()->GetTotalRent()->GetTurnover()->GetTransactions());
            $this->tpl->assign('autoPrint', $request->get('autoPrint'));
        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
        }

        print $this->fetchTemplateWithoutHeaderAndFooter();
    }

    public function exportBillAction($request)
    {
        try {
            $bill = $this->getOneBill($request->get('billId'));
            $this->InstanceBillingInfo->initCalculateRentToBill(
                Session::get('sid'),
                $bill->GetSettlingPeriod()->GetDateFrom(),
                $bill->GetSettlingPeriod()->GetDateTo()
            );
            $this->InstanceBillingInfo->doRequests();
            $rent = $this->InstanceBillingInfo->getCalculateRent()->GetResult()->GetTotalRent()->GetTurnover()->GetTransactions();
            if ($bill) {
                $file = '../cache/bill-' . $request->get('billId') . '.xls';
                require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel.php';
                $pExcel = new PHPExcel();
                require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel/Writer/Excel5.php';
                $objWriter = new PHPExcel_Writer_Excel5($pExcel);
                $pExcel->setActiveSheetIndex(0);
                $aSheet = $pExcel->getActiveSheet();
                $aSheet->setTitle(LangAdmin::get('Bill_info'));

                $aSheet->setCellValue('A1', LangAdmin::get('Creation_date'));
                $aSheet->setCellValue('B1', LangAdmin::get('Pay_date'));
                $aSheet->setCellValue('C1', LangAdmin::get('Url_to_pay'));
                $aSheet->setCellValue('D1', LangAdmin::get('Payed_amount'));
                $aSheet->setCellValue('E1', LangAdmin::get('Amount_USD'));
                $aSheet->setCellValue('F1', LangAdmin::get('Condition'));
                $aSheet->setCellValue('G1', LangAdmin::get('Bill_discription'));

                $aSheet->setCellValue('A2', date('d.m.Y', strtotime($bill->GetCreationDate())));
                $aSheet->setCellValue('B2', $bill->GetPaymentDate() ? date('d.m.Y', strtotime($bill->GetPaymentDate())) : '--');
                $aSheet->setCellValue('C2', $bill->GetPaymentUrl());
                $aSheet->setCellValue('D2', $bill->GetPaidSumInUSD()->asString() ? $bill->GetPaidSumInUSD()->asString() : '--');
                $aSheet->setCellValue('E2', $bill->GetSumToPayInUSD()->asString());
                $aSheet->setCellValue('F2', $bill->GetStatus()->GetDescription());
                $aSheet->setCellValue('G2', $bill->GetDescription());

                $aSheet->setCellValue('A4', LangAdmin::get('Transactions_list'));
                $aSheet->setCellValue('A5', LangAdmin::get('Date'));
                $aSheet->setCellValue('B5', LangAdmin::get('user_login'));
                $aSheet->setCellValue('C5', LangAdmin::get('Amount'));
                $aSheet->setCellValue('D5', LangAdmin::get('Description'));
                foreach ($rent->GetTransactionInfo() as $i => $transaction) {
                    $aSheet->setCellValue('A'.(6+$i), date('d.m.Y H:i:s', strtotime($transaction->GetTransactionDateTime())));
                    $aSheet->setCellValue('B'.(6+$i), $transaction->GetUserLogin());
                    $aSheet->setCellValue('C'.(6+$i), $transaction->GetAmountInternal() * (-1));
                    $aSheet->setCellValue('D'.(6+$i), $transaction->GetTransactionType()->GetName().' '.$transaction->GetComment());
                }

                $aSheet->getColumnDimension('A')->setWidth(20);
                $aSheet->getColumnDimension('B')->setWidth(20);
                $aSheet->getColumnDimension('C')->setWidth(40);
                $aSheet->getColumnDimension('D')->setWidth(20);
                $aSheet->getColumnDimension('E')->setWidth(20);
                $aSheet->getColumnDimension('F')->setWidth(20);
                $aSheet->getColumnDimension('G')->setWidth(40);


                $objWriter->save($file);
                header("Content-Type: application/vnd.ms-excel; charset=utf-8");
                header('Content-Disposition: attachment; filename="' . $file . '"');
                readfile($file);
            } else {
                echo ''.LangAdmin::get('Bill_info_not_found').'';
            }
        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
        }
    }


    public function getStatisticAction($request)
    {
        try {
            $this->getWebUISettings();
            $statistic = $this->callStatistics->GetCallStatistics();
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse($statistic);
    }

    public static function hasUnPayedBills()
    {
        try {
            if (! RightsManager::isSuperAdmin() && ! (RightsManager::getCurrentRole() == RightsManager::ROLE_SELLFREE)) {
                return false;
            }
            if (! self::isActionAllowed()) {
                return false;
            }
            
            $cacher = new FileAndMysqlMemoryCache(new CMS());
            if ($cacher->Exists('admin:hasUnPayedBillsResult')) {
                return false;
            }

            $instanceBillingInfo = new InstanceBillingInfo();
            $instanceBillingInfo->initSearchUnpaidBills(Session::getActiveAdminLang(), Session::get('sid'));
            $instanceBillingInfo->doRequests();
            $hasUnPayed = (int) $instanceBillingInfo->getUnpaidBills()->GetResult()->GetRawData()->TotalCount > 0;

            if (! $hasUnPayed) {
                // кеширование результата на 24 часа
                $cacher->AddCacheEl('admin:hasUnPayedBillsResult', HOURS_24, 'true');
            }

            return $hasUnPayed;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getOneBill($billId)
    {
        $bill = false;
        
        $this->InstanceBillingInfo->initGetBill(Session::getActiveAdminLang(), Session::get('sid'), $billId);
        $this->InstanceBillingInfo->doRequests();
        $bill = $this->InstanceBillingInfo->getBill()->GetResult();

        return $bill;
    }

    public function operationLogAction($request)
    {
        $records = array();
        $this->_template = 'operation_log';
        $reportsProvider = new ReportsProvider($this->cms, $this->otapilib); 
        try {
            $page = $this->getPageDisplayParams($request);

            $perpage = $page['limit'];
            $pageNum = $page['number'];
            $from = $page['offset'];
            $count = 0;

            $xmlSearchParameters = $this->_generateInstanceUserLogXml($request);
            $logs = $this->getOtapilib()->SearchInstanceUserLogEntries(Session::get('sid'), $from, $perpage, $xmlSearchParameters);
            $opTypes = $reportsProvider->GetOperationTypesAsKeyValue(Session::get('sid'), Session::getActiveAdminLang());

            if (array_key_exists('totalcount', $logs)) {
                $count = $logs['totalcount'];
            }

            if (array_key_exists('content', $logs)) {
                $records = $logs['content'];
                foreach ($records as $key => &$value) {
                    $opdescription = $reportsProvider->GetOperationTypeDescriptionByName(Session::get('sid'), $value['operationtype'], Session::getActiveAdminLang()); 
                    $value['operationtype'] = !empty($opdescription) ? $opdescription : $value['operationtype'];  
                }
            }

        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
        }

        $this->tpl->assign('opTypes', $opTypes );
        $this->tpl->assign('currentType', $request->getValue('optype'));
        $this->tpl->assign('records', $records );
        $this->tpl->assign('paginator', new Paginator($count, $pageNum, $perpage));

        print $this->fetchTemplate();
    }
    
    private function prepareXMLTransactions($transactions)
    {
        $checkEmpty = $transactions->asString();        
        if (! empty($checkEmpty)) {
            $file = '../cache/transactions.xls';
            $fileUrl = UrlGenerator::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . '/cache/transactions.xls';
            require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel.php';
            $pExcel = new PHPExcel();
            require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel/Writer/Excel5.php';
            $objWriter = new PHPExcel_Writer_Excel5($pExcel);
            $pExcel->setActiveSheetIndex(0);
            $aSheet = $pExcel->getActiveSheet();
            $aSheet->setTitle(LangAdmin::get('Transactions_list'));
            $aSheet->setCellValue('A1', LangAdmin::get('Date'));
            $aSheet->setCellValue('B1', LangAdmin::get('user_login'));
            $aSheet->setCellValue('C1', LangAdmin::get('Amount'));
            $aSheet->setCellValue('D1', LangAdmin::get('Description'));
            foreach ($transactions->GetTransactionInfo() as $i => $transaction) {
                $aSheet->setCellValue('A'.(2+$i), date('d.m.Y H:i:s', strtotime($transaction->GetTransactionDateTime())));
                $aSheet->setCellValue('B'.(2+$i), $transaction->GetUserLogin());
                $aSheet->setCellValue('C'.(2+$i), $transaction->GetAmountInternal() * (-1));
                $aSheet->setCellValue('D'.(2+$i), $transaction->GetTransactionType()->GetName().' '.$transaction->GetComment());
            }
            $aSheet->getColumnDimension('A')->setWidth(20);
            $aSheet->getColumnDimension('B')->setWidth(20);
            $aSheet->getColumnDimension('C')->setWidth(40);
            $aSheet->getColumnDimension('D')->setWidth(20);
            $objWriter->save($file);
            return $fileUrl;
        } else {
            return false;
        }
        
        
    }

    public function userLogAction($request)
    {
        $sid = Session::get('sid');
        $id = OrdersProxy::originOrderId($request->get('id'));

        $xml = '<LogEntrySearchParameters><OperationType>PostTransaction</OperationType><CustomerId>' . $id . '</CustomerId></LogEntrySearchParameters>';

        try {
            $orderLog = $this->getOtapilib()->SearchInstanceUserLogEntries($sid, 0, 1000, $xml);
            $result = array();

            foreach ($orderLog['Content'] as $item) {
                $result[] = array(
                    date('d.m.Y H:i:s', strtotime($item['DateTime'])),
                    str_replace(';', '; ', $item['Description']),
                    $item['InstanceUserLogin']
                );
            }

            $this->sendAjaxResponse((array('data' => $result)), true);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
    }

    public function orderLogAction($request)
    {
        $sid = Session::get('sid');
        $id = OrdersProxy::originOrderId($request->get('id'));
        $normalizeOrderId = OrdersProxy::normalizeOrderId($id);
        $xml = '<LogEntrySearchParameters><OrderId>' . OrdersProxy::getOrderNumericId($normalizeOrderId) . '</OrderId></LogEntrySearchParameters>';
        $reportsProvider = new ReportsProvider($this->cms, $this->otapilib); 

        try {
            $orderLog = $this->getOtapilib()->SearchInstanceUserLogEntries($sid, 0, 1000, $xml);
            $salesList = $this->getOtapilib()->GetSalesOrderDetailsForOperator($sid, $request->get('id'), '', 0);

            $result = array();
            $sales = array();

            foreach ($salesList['SalesLinesList'] as $salesLine) {
                $sales[$salesLine['id']] = $salesLine;
            }

            foreach ($orderLog['Content'] as $item) {
                if (isset($sales[$item['OrderLineId']])) {
                    $options = array();

                    if ($sales[$item['OrderLineId']]['configid']) {
                        $options['ConfigId'] = $sales[$item['OrderLineId']]['configid'];
                    }

                    $url = UrlGenerator::generateItemUrl($sales[$item['OrderLineId']]['itemid'], $options);
                    $num = '<a href="' . $url . '">№&nbsp;' . $item['OrderId'] . ($sales[$item['OrderLineId']]['LineNum'] ? '&nbsp;-&nbsp;' . $sales[$item['OrderLineId']]['LineNum'] : '') . '</a>';
                } elseif ($item['OrderLineId']) {
                    $num = '№&nbsp;' . $item['OrderLineId'];
                } else {
                    $num = '';
                }

                $result[] = array(
                    date('d.m.Y H:i:s', strtotime($item['DateTime'])),
                    $num,
                    $reportsProvider->GetOperationTypeDescriptionByName($sid, $item['OperationType'], Session::getActiveAdminLang()),
                    str_replace(';', '; ', $item['Description']), // TODO: удалить после исправлений на сервисах
                    $item['InstanceUserLogin']
                );
            }

            $this->sendAjaxResponse((array('data' => $result)), true);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
    }

    private function _generateInstanceUserLogXml($request)
    {
        $currentType = $request->getValue('optype');

        /* если не выбраны фильтры то отдаем пуствую строку */
        if (! trim($currentType)) {
            return '';
        }

        $xmlParams = new SimpleXMLElement('<LogEntrySearchParameters></LogEntrySearchParameters>');
        $xmlParams->addChild('OperationType', $currentType);

        return str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
    }

    public function financeAction($request)
    {
        $logs = array();
        $this->_template = 'finance/reports';

        $metaInfo = null;

        try {
            $financialReport = new OtapiFinancialReportAnswer(null);
            OTAPILib2::GetFinancialReport(Session::getActiveAdminLang(), Session::get('sid'), 'true', $financialReport);
            OTAPILib2::makeRequests();
            $metaInfo = $financialReport->GetResult()->GetRawData();
        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
        }

        $this->tpl->assign('metaInfo', $metaInfo);
        $this->tpl->assign('updateUrl', null);

        print $this->fetchTemplate();
    }
    

    public function financeDetailsAction($request)
    {
        $logs = array();
        $this->_template = 'finance/details';

        $page = $this->getPageDisplayParams($request);
        $perpage = $page['limit'];
        $pageNum = $page['number'];
        $from = $page['offset'];
        $count = 0;

        try {
            $xmlSearchParameters = $this->_generateSearchTransactionsXml($request);
            $logs = new OtapiTransactionInfoListFrameAnswer(null);
            OTAPILib2::SearchTransactions(Session::getActiveAdminLang(), Session::get('sid'), $xmlSearchParameters, $from, $perpage, $logs);
            OTAPILib2::makeRequests();
            $count = $logs->GetResult()->GetTotalCount();
        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
        }
        $this->tpl->assign('logs', $logs);
        $this->tpl->assign('paginator', new Paginator($count, $pageNum, $perpage));
        $this->tpl->assign('filter', $request->getValue('filter', array()));
        $this->tpl->assign('periodFilters', $this->getPeriodFilters());

        print $this->fetchTemplate();
    }
    
    public function exportFinanceDetailsAction($request)
    {
        $from = $request->getValue('position', 0);
        if ($from == 0) {
            Session::set('FinanceLog', array());
            Session::set('FinanceLogParams', $request->getValue('filter', array()));
        }
        try {
            $xmlSearchParameters = $this->_generateSearchTransactionsXml($request);
            $logs = new OtapiTransactionInfoListFrameAnswer(null);
            OTAPILib2::SearchTransactions(Session::getActiveAdminLang(), Session::get('sid'), $xmlSearchParameters, $from, 100, $logs);
            OTAPILib2::makeRequests();
            $count = $logs->GetResult()->GetTotalCount();
            
            $fullFinanceLog = Session::get('FinanceLog');
            foreach ($logs->GetResult()->GetContent()->GetItem() as $log) {
                $tmp['date'] = date("d.m.Y", strtotime($log->GetTransactionDate()));
                $tmp['login'] = $log->GetUserInfo()->GetLogin() ? TextHelper::escape($log->GetUserInfo()->GetLogin()) : TextHelper::escape($log->GetUserId()->asString());
                $tmp['amount'] = $log->GetAmount();
                $tmp['comment'] = $log->GetComment();
                $fullFinanceLog[] = $tmp;
            }
            Session::set('FinanceLog', $fullFinanceLog);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array(
            'isEnd' => (boolean)($count <= $from + 100)
        ));
    }
    
    public function dowmloadExportDataAction($request)
    {
        $fullFinanceLog = Session::get('FinanceLog');
        $financeLogParams = Session::get('FinanceLogParams');
        $dateFrom = ! empty($financeLogParams['fromdate']) ? '-' . $financeLogParams['fromdate'] : '';
        $dateTo = ! empty($financeLogParams['todate']) ? '-' . $financeLogParams['todate'] : '';
        if (! empty($fullFinanceLog)) {
            try {
                $file = '../cache/FinanceLog.xls';
                $fileName = 'FinanceLog' . $dateFrom . '' . $dateTo . '.xls';
                require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel.php';
                $pExcel = new PHPExcel();
                require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel/Writer/Excel5.php';
                $objWriter = new PHPExcel_Writer_Excel5($pExcel);
                $pExcel->setActiveSheetIndex(0);
                $aSheet = $pExcel->getActiveSheet();
                $aSheet->setTitle(LangAdmin::get('Details'));

                $aSheet->setCellValue('A1', LangAdmin::get('Date'));
                $aSheet->setCellValue('B1', LangAdmin::get('Login'));
                $aSheet->setCellValue('C1', LangAdmin::get('Amount'));
                $aSheet->setCellValue('D1', LangAdmin::get('Notice'));
                $i = 2;
                foreach ($fullFinanceLog as $financeLog) {
                    $aSheet->setCellValue('A' . $i, $financeLog['date']);
                    $aSheet->setCellValue('B' . $i, $financeLog['login']);
                    $aSheet->setCellValue('C' . $i, $financeLog['amount']);
                    $aSheet->setCellValue('D' . $i, $financeLog['comment']);
                    $i++;
                    
                }
                if ($i != 2) {
                    $aSheet->setCellValue('A' . $i, LangAdmin::get('in_total'));
                    $aSheet->setCellValue('C' . $i, '=sum(C2:C' . ($i-1) . ')');
                }
                
                $aSheet->getColumnDimension('A')->setWidth(40);
                $aSheet->getColumnDimension('B')->setWidth(40);
                $aSheet->getColumnDimension('C')->setWidth(40);
                $aSheet->getColumnDimension('D')->setWidth(40);


                $objWriter->save($file);
                header("Content-Type: application/vnd.ms-excel; charset=utf-8");
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                if (@readfile($file)) { 
                    unlink($file); 
                }
            } catch (ServiceException $e) {
                $this->errorHandler->registerError($e);
                die('Download error');
            }
        } else {
            die('Download error');
        }
        
        
    }
    
    

    private function _generateSearchTransactionsXml($request)
    {
        $filter = $request->getValue('filter', array());

        $xmlParams = new SimpleXMLElement('<TransactionSearchParameters></TransactionSearchParameters>');
        if (! empty($filter['fromdate'])) {
            $xmlParams->addChild('DateFrom', date("Y-m-d\T00:00:00", strtotime($filter['fromdate'])));
        }
        if (! empty($filter['todate'])) {
            $xmlParams->addChild('DateTo', date("Y-m-d\T23:59:59", strtotime($filter['todate'])));
        }

        return str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
    }
    
    private function _generateFinancialCalculationXml($request)
    {
        $filter = $request->getValue('filter', array());
        $dateFrom = ! empty($filter['fromdate']) ? strtotime($filter['fromdate']) : strtotime(date('Y-m-1'));
        $dateTo = ! empty($filter['todate']) ? strtotime($filter['todate']) : strtotime(date('Y-m-d'));
        
        if ($dateFrom > $dateTo) {
            throw new Exception(LangAdmin::get('End_date_must_be_greater'));
        }
        $adateFrom = ! empty($filter['fromdate']) ? new DateTime($filter['fromdate']) : new DateTime(date('Y-m-1'));
        $adateTo = ! empty($filter['todate']) ? new DateTime($filter['todate']) : new DateTime(date('Y-m-d'));
        $aDiff = $adateFrom->diff($adateTo);
        if ($aDiff->y >= 1) {
            throw new Exception(LangAdmin::get('Max_date_range_year'));
        }
        $xmlParams = new SimpleXMLElement('<FinancialCalculationParameters></FinancialCalculationParameters>');
        $xmlParams->addChild('DateFrom', date("Y-m-d\T00:00:00", $dateFrom));
        $xmlParams->addChild('DateTo', date("Y-m-d\T23:59:59", $dateTo));
        
        return str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
    }
    
    public function calculationAction($request)
    {
        $logs = array();
        $this->_template = 'finance/calculation';

        try {
            $xmlSearchParameters = $this->_generateFinancialCalculationXml($request);
            Session::set('reporst_calculation_filter', $xmlSearchParameters);
            $logs = new OtapiTransactionInfoListFrameAnswer(null);
            OTAPILib2::GetFinancialCalculation(Session::getActiveAdminLang(), Session::get('sid'), $xmlSearchParameters, $logs);
            OTAPILib2::makeRequests();
        } catch (Exception $e) {
            $this->errorHandler->registerError($e);
        }
        if (! empty($logs)) {
	        $currency = $logs->GetResult()->GetInternalCurrencyCode();
	        $logs = $this->prepareCalculationLogs($logs);
        }
        
        $this->tpl->assign('currency', $currency);
        $this->tpl->assign('logs', $logs);
        $this->tpl->assign('filter', $request->getValue('filter', array()));

        print $this->fetchTemplate();
    }
    
    
    public function exportCalculationAction($request)
    {
        Session::set('CalculationLog', false);
        try {
            $xmlSearchParameters = $this->_generateFinancialCalculationXml($request);
            $logs = new OtapiFinancialCalculationReportAnswer(null);
            OTAPILib2::GetFinancialCalculation(Session::getActiveAdminLang(), Session::get('sid'), $xmlSearchParameters, $calculationLogs);
            OTAPILib2::makeRequests();
            
            $filter = $request->getValue('filter', array());
            $dateFrom = ! empty($filter['fromdate']) ? '-' . $filter['fromdate'] : '';
            $dateTo = ! empty($filter['todate']) ? '-' . $filter['todate'] : '';
            $logs = $this->prepareCalculationLogs($calculationLogs);
            $currency = $calculationLogs->GetResult()->GetInternalCurrencyCode();
            $file = '../cache/CalculationLog.xls';
            $fileName = 'CalculationLog' . $dateFrom . '' . $dateTo . '.xls';
            require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel.php';
            $pExcel = new PHPExcel();
            require_once CFG_APP_ROOT . '/lib/PhpExcel/PHPExcel/Writer/Excel5.php';
            $objWriter = new PHPExcel_Writer_Excel5($pExcel);
            $pExcel->setActiveSheetIndex(0);
            $aSheet = $pExcel->getActiveSheet();
            $aSheet->setTitle(LangAdmin::get('Сalculation'));
            
            $aSheet->setCellValue('A1', LangAdmin::get('Date'));
            $aSheet->setCellValue('B1', LangAdmin::get('IncomeAmount') . $currency);
            $aSheet->setCellValue('C1', LangAdmin::get('Providers'));
            $aSheet->setCellValue('D1', LangAdmin::get('Currency'));
            $aSheet->setCellValue('E1', LangAdmin::get('OrdersReservedAmount') . $currency);
            $aSheet->setCellValue('F1', LangAdmin::get('Number_of_purchases'));
            $aSheet->setCellValue('G1', LangAdmin::get('Rate_to_rub') . $currency);
            $aSheet->setCellValue('H1', LangAdmin::get('PurchaseAmount') . $currency);
            $aSheet->setCellValue('I1', LangAdmin::get('ExternalDeliveryAmount'));
            $aSheet->setCellValue('J1', LangAdmin::get('Income'));
            $aSheet->setCellValue('K1', LangAdmin::get('Percentage_of_profit'));
            $i = 2;
            foreach ($logs as $dte => $log) {
                $aSheet->setCellValue('A' . $i, date("F Y", strtotime($log[0]->GetDate())));
                $aSheet->getStyle('A' . $i)->getFont()->setBold(true);
                $middleIncomeAmount = false;
                $middlePurchaseAmount = false;
                $middleOrdersReservedAmount = false;
                $middleExternalDeliveryAmount = false;
                $middleEarningsAmount = false;
                $middleEarningsPercent = false;
                $middleI = 0;
                $i = $i + 1;
                foreach ($log as $l) {
                    $middleIncomeAmount = $middleIncomeAmount + $l->GetIncomeAmount();
                    $middlePurchaseAmount = $middlePurchaseAmount + $l->GetPurchaseAmount();
                    $middleOrdersReservedAmount = $middleOrdersReservedAmount + $l->GetOrdersReservedAmount();
                    $middleExternalDeliveryAmount = $middleExternalDeliveryAmount + $l->GetExternalDeliveryAmount();
                    $middleEarningsAmount = $middleEarningsAmount + $l->GetEarningsAmount();
                    $middleEarningsPercent = $middleEarningsPercent + $l->GetEarningsPercent();
                    $middleI++;
                
                    $aSheet->setCellValue('A' . $i, date("d.m.Y", strtotime($l->GetDate())));
                    $aSheet->setCellValue('B' . $i, $l->GetIncomeAmount());
                    $aSheet->setCellValue('E' . $i, $l->GetOrdersReservedAmount());
                    $aSheet->setCellValue('F' . $i, $l->GetPurchaseAmount());
                    $aSheet->setCellValue('I' . $i, $l->GetExternalDeliveryAmount());
                    $aSheet->setCellValue('J' . $i, $l->GetEarningsAmount());
                    $aSheet->setCellValue('K' . $i, $l->GetEarningsPercent());
                    $i++;
                    foreach ($l->GetProviders()->GetProvider() as $provider) {
                        $aSheet->setCellValue('C' . $i, $provider->GetProviderType());                    
                        $aSheet->setCellValue('D' . $i, $provider->GetProviderCurrencyCode());
                        $aSheet->setCellValue('E' . $i, $provider->GetOrdersReservedAmount());
                        $aSheet->setCellValue('F' . $i, $provider->GetPurchaseAmount());
                        $aSheet->setCellValue('G' . $i, round($provider->GetExchangeRate(), 6));
                        $aSheet->setCellValue('H' . $i, $provider->GetPurchaseProviderAmount());
                        $aSheet->setCellValue('I' . $i, $provider->GetExternalDeliveryAmount());
                        $aSheet->setCellValue('J' . $i, $provider->GetEarningsAmount());
                        $aSheet->setCellValue('K' . $i, $provider->GetEarningsPercent());
                        $i++;
                    }
                }
                $aSheet->setCellValue('A' . $i, LangAdmin::get('in_total'));
                $aSheet->setCellValue('B' . $i, $middleIncomeAmount);
                $aSheet->setCellValue('E' . $i, $middleOrdersReservedAmount);
                $aSheet->setCellValue('H' . $i, $middlePurchaseAmount);
                $aSheet->setCellValue('I' . $i, $middleExternalDeliveryAmount);
                $aSheet->setCellValue('J' . $i, $middleEarningsAmount);
                $aSheet->setCellValue('K' . $i, $middleEarningsPercent/$middleI);
                $aSheet->getStyle('A' . $i)->getFont()->setBold(true);
                $aSheet->getStyle('B' . $i)->getFont()->setBold(true);
                $aSheet->getStyle('E' . $i)->getFont()->setBold(true);
                $aSheet->getStyle('H' . $i)->getFont()->setBold(true);
                $aSheet->getStyle('I' . $i)->getFont()->setBold(true);
                $aSheet->getStyle('J' . $i)->getFont()->setBold(true);
                $aSheet->getStyle('K' . $i)->getFont()->setBold(true);
                $i = $i + 3;
            }
            
            $aSheet->getColumnDimension('A')->setWidth(20);
            $aSheet->getColumnDimension('B')->setWidth(20);
            $aSheet->getColumnDimension('C')->setWidth(20);
            $aSheet->getColumnDimension('D')->setWidth(20);
            $aSheet->getColumnDimension('E')->setWidth(20);
            $aSheet->getColumnDimension('F')->setWidth(20);
            $aSheet->getColumnDimension('G')->setWidth(20);
            $aSheet->getColumnDimension('H')->setWidth(20);
            $aSheet->getColumnDimension('I')->setWidth(20);
            $aSheet->getColumnDimension('J')->setWidth(20);
            $aSheet->getColumnDimension('K')->setWidth(20);

            $objWriter->save($file);
            header("Content-Type: application/vnd.ms-excel; charset=utf-8");
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            if (@readfile($file)) { 
                unlink($file); 
            }
        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
                die('Download error');
        }
    }
    public function prepareCalculationLogs($logs)
    {
        $return = array();
        foreach ($logs->GetResult()->GetContent()->GetItem() as $log) {
            $return[date("m-Y", strtotime($log->GetDate()))][] = $log;
        }
        $return = array_reverse($return);
        return $return;
    }
    
    /**
     * @param RequestWrapper $request
     */
    public function saveInlineAction($request)
    {
    	try{
    		$date = $request->getValue('pk');
    		$param = $request->getValue('name');
    		$value = $request->getValue('value');
    		$provider = $request->get('provider');
    		$reset = $request->getValue('reset', false);
    		
    		$date = date("Y-m-d\T00:00:00", strtotime($date . ' 00:00:00'));
    		
    		$xmlUpdateData = '<FinancialCalculationUpdateData>';
    		if ($reset) {
    			$xmlUpdateData .= '<Reset' . $param . '>true</Reset' . $param . '>' ;
    		} else {
    			$xmlUpdateData .= '<' . $param . '>'. $value . '</' . $param . '>' ;
    		}
    		$xmlUpdateData .= '</FinancialCalculationUpdateData>';
    		
    		$answer = false;
    		OTAPILib2::UpdateFinancialCalculationData(Session::getActiveAdminLang(), Session::get('sid'), $provider, $date, $xmlUpdateData, $answer);
    		OTAPILib2::makeRequests();
    		
    		if (Session::get('reporst_calculation_filter')) {
    			$xmlParams = Session::get('reporst_calculation_filter');
    		}
    		$result = array();
    		$logs = new OtapiTransactionInfoListFrameAnswer(null);
    		OTAPILib2::GetFinancialCalculation(Session::getActiveAdminLang(), Session::get('sid'), $xmlParams, $logs);
    		OTAPILib2::makeRequests();
    		if (! empty($logs)) {
    			$logs = $this->prepareCalculationLogs($logs);
    			foreach ($logs as $m => $month) {
    				$monthLog = array(
    					'id' => date('F', strtotime($month[0]->GetDate())),
    					'middleIncomeAmount' => 0,
						'middlePurchaseAmount' => 0,
    					'middleOrdersReservedAmount' => 0,
    					'middleExternalDeliveryAmount' => 0,
    					'middleEarningsAmount' => 0,
    					'middleEarningsPercent' => 0,
    					'days' => array()
    				);
    				$middleI = 0;
    				foreach ($month as $d => $day) {
    					$dayLog = array();
    					$monthLog['middleIncomeAmount'] += $day->GetIncomeAmount();
    					$monthLog['middlePurchaseAmount'] += $day->GetPurchaseAmount();
    					$monthLog['middleOrdersReservedAmount'] += $day->GetOrdersReservedAmount();
    					$monthLog['middleExternalDeliveryAmount'] += $day->GetExternalDeliveryAmount();
    					$monthLog['middleEarningsAmount'] += $day->GetEarningsAmount();
    					$monthLog['middleEarningsPercent'] += $day->GetEarningsPercent();
    					
    					$dayLog['incomeAmount'] = $day->GetIncomeAmount();
    					$dayLog['purchaseAmount'] = $day->GetPurchaseAmount();
    					$dayLog['ordersReservedAmount'] = $day->GetOrdersReservedAmount();
    					$dayLog['externalDeliveryAmount'] = $day->GetExternalDeliveryAmount();
    					$dayLog['earningsAmount'] = $day->GetEarningsAmount();
    					$dayLog['earningsPercent'] = $day->GetEarningsPercent();
    					$dayLog['providers'] = array();
    					
    					foreach ($day->GetProviders()->GetProvider() as $p => $provider) {
    						$providerLog = array();
    						$providerLog['providerType'] = $provider->GetProviderType();
    						$providerLog['providerCurrencyCode'] = $provider->GetProviderCurrencyCode();
    						$providerLog['ordersReservedAmount'] = $provider->GetOrdersReservedAmount();
    						$providerLog['purchaseProviderAmount'] = $provider->GetPurchaseProviderAmount();
							$providerLog['exchangeRate'] = round($provider->GetExchangeRate(), 6);
    						$providerLog['purchaseAmount'] = $provider->GetPurchaseAmount();
    						$providerLog['externalDeliveryAmount'] = $provider->GetExternalDeliveryAmount();
    						$providerLog['earningsAmount'] = $provider->GetEarningsAmount();
    						$providerLog['earningsPercent'] = $provider->GetEarningsPercent();
    						$dayLog['providers'][$provider->GetProviderType()] = $providerLog;
    					}
    					
    					$monthLog['days'][date("d-m-Y", strtotime($day->GetDate()))] = $dayLog;
    					$middleI++;
    				}
    				$monthLog['middleEarningsPercent'] = $monthLog['middleEarningsPercent'] / $middleI;
    				$result[$m] = $monthLog;
    			}
    		}
    	} catch(ValidationException $e){
    		$this->respondAjaxError($e);
    	} catch(Exception $e){
    		$this->respondAjaxError($e);
    	}
    	$this->sendAjaxResponse(array('ok' => true, 'data' => $result));
    }
    
}
