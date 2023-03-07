<?php

class PayController extends GeneralContoller
{
    /**
     * @var OtapiOrderDetailsInfo
     */
    private $orderDetails;

    /**
     * @throws InternalError
     * @throws NotFoundException
     */
    public function searchUserPaymentsAction() {
        try {
            $sid = Session::getUserSession();
            $lang = Session::getActiveLang();
            $xmlParameters = new SimpleXMLElement('<PaymentSearchParameters></PaymentSearchParameters>');
            $fromDate =  $this->request->getValue('from');
            $toDate =  $this->request->getValue('to');
            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $frameSize = 15;
            $framePosition = ($page-1) * $frameSize;

            if ($fromDate) {
                $dateFrom = strtotime($fromDate);
                $xmlParameters->addChild('DateFrom', date('c', $dateFrom));
            }
            if ($toDate) {
                $dateTo = strtotime($toDate);
                $xmlParameters->addChild('DateTo', date('c', $dateTo));
            }

            OTAPILib2::SearchUserPayments($lang, $sid, $xmlParameters->asXML(), $framePosition, $frameSize, $result);
            OTAPILib2::makeRequests();

            $count = $result->GetResult()->GetTotalCount();
            $paginator = new Paginator($count, $page, $frameSize);
        } catch(Exception $e) {
            return $this->respondAjaxError($e);
        }

        return $this->sendAjaxResponse(['html' => $this->renderPartial('controllers/pay/topuprequesthistory', [
            'userPayments' => $result->GetResult()->GetContent(),
            'paginator' => $paginator
        ])]);
    }

    public function formAction($orderId = null)
    {
        $isAjax = $this->request->isAjax();
        $data = [];
        $data['payGroups'] = [];
        $data['orderData'] = null;
        $data['currencySign'] = '';

        try {
            $orderData = $orderId ? $this->getSalesOrderDetailsResult($orderId) : null;
            $data['orderData'] = $orderData;
            $data['currencySign'] = $orderId ? $orderData->GetSalesOrderInfo()->GetCurrencySign() : InstanceProvider::getObject()->GetInternalCurrency()->GetSign();
            $data['payGroups'] = $this->getPayGroups($orderData);
        } catch (Exception $e) {
            ErrorHandler::registerError($e);
        }

        return $isAjax || $orderId ? $this->renderPartial('controllers/pay/form', $data) : $this->render('controllers/pay/index', $data);
    }

    /**
     * @return string|void
     */
    public function payAction()
    {
        $orderId = $this->request->getValue('salesId');
        $orderId = $this->request->getValue('orderid', $orderId);
        $paymentId = $this->request->getValue('paymentId');
        $amount = (float) $this->request->getValue('money');

        try {
            User::getObject()->clearAccountInfoCache();

            if ($paymentId === 'payment_in_cash') {
                $response = $this->payCash($orderId, $amount);
            } elseif ($paymentId === 'sberbank' ) {
                $response = $this->payQuittance($orderId, $amount);
            } elseif ($paymentId === 'from_account') {
                $response = $this->payPersonalAccount($orderId);
            } else {
                $response = $this->payDefault($orderId, $paymentId, $amount);
            }

            return $response;

        } catch (Exception $e) {
            if (OTBase::isTest()) {
                return $e->getMessage();
            } else {
                list($urlSuccess, $urlFailure) = $this->getResponseUrls($orderId);
                $this->redirect($urlFailure);
            }
        }
    }


    /**
     * @param string $orderId
     * @param float $amount
     * @return string
     */
    private function payQuittance($orderId, $amount)
    {
        $quittanceData = [];
        $configs = $this->cms->getSiteConfig();

        if ($configs[0]) {
            $quittanceData = [
                'payeeINN' => str_split($configs[1]['INN_of_payee'],1),
                'payeeName' => $configs[1]['name_of_payee'],
                'payeeBankName' => $configs[1]['bank_name_of_payee'],
                'payeeAccountNumber' => str_split($configs[1]['account_number_of_payee'],1),
                'paymentDescription' => $configs[1]['description_of_payment'],
                'bankIdentificationCode' => str_split($configs[1]['bank_identification_code'],1),
                'correspondentBankAccount' => str_split($configs[1]['correspondent_bank_account'],1),
            ];
        }

        return $this->renderPartial('controllers/pay/quittance', [
            'quittanceData' => $quittanceData,
            'userInfo' => $this->getUserInfo(),
            'accountNum' => User::getObject()->getAccountInfo()->GetNum(),
            'amount' => $orderId ? $this->getSalesOrderDetailsResult($orderId)->GetSalesOrderInfo()->GetRemainAmount() : $amount,
        ]);
    }

    private function payPersonalAccount($orderId)
    {
        $sid = User::getObject()->getSid();
        $amount = $this->getSalesOrderDetailsResult($orderId)->GetSalesOrderInfo()->GetRemainAmount();

        OTAPILib2::PaymentPersonalAccount($sid, $orderId, $amount, $answer);
        OTAPIlib2::makeRequests();

        $orderDetailsUrl = UrlGenerator::generateOrderDetailsUrl($orderId);
        return $this->redirect($orderDetailsUrl);
    }

    /**
     * @param string $orderId
     * @param float $amount
     */
    private function payCash($orderId, $amount)
    {
        return $orderId ? $this->payCashInOrder($orderId) : $this->payCashInPrivateOffice($amount);
    }

    /**
     * @param string $orderId
     */
    private function payCashInOrder($orderId)
    {
        $orderInfo = $this->getSalesOrderDetailsResult($orderId);
        $salesOrderInfo = $orderInfo->GetSalesOrderInfo();
        $order = array(
            'id' => $salesOrderInfo->GetId(),
            'currencySign' => $salesOrderInfo->GetCurrencySign(),
            'createdDateTime' => $salesOrderInfo->GetCreatedDateTime(),
        );

        $user = $this->getUserInfo();
        $amount = $salesOrderInfo->GetRemainAmount();

        try {
            Notifier::notifyAdminOnPaymentInCash($order, $user, $amount);
        } catch (Exception $e) {
            Session::setError($e->getMessage(), $e->getCode());
        }

        $currency = $salesOrderInfo->GetCurrencySign();
        $categoryId = $orderInfo->GetSalesLinesList()->GetSalesLine()->current()->GetCategoryId();
        $ticketMessage = $this->getPayCashTicketMessage($amount, $currency);
        return $this->payCashSupportTicket($ticketMessage, $user['id'], $user['login'], $orderId, $categoryId);
    }

    /**
     * @param float $amount
     */
    private function payCashInPrivateOffice($amount)
    {
        $user = $this->getUserInfo();
        $currencySign = InstanceProvider::getObject()->GetInternalCurrency()->GetSign();

        try {
            Notifier::generalNotification('payment_in_cash', Lang::get('payment_in_cash'), array(
                'user' => $user,
                'paymentSum' => $amount,
                'payment_sum_text' => $this->getPayCashAmount($amount, $currencySign)
            ));
        } catch (Exception $e) {
            Session::setError($e->getMessage(), $e->getCode());
        }

        $ticketMessage = $this->getPayCashTicketMessage($amount, $currencySign);
        return $this->payCashSupportTicket($ticketMessage, $user['id'], $user['login']);
    }

    /**
     * @param string $message
     * @param string $userId
     * @param string $userLogin
     * @param string $orderId
     * @param string $categoryId
     */
    private function payCashSupportTicket($message, $userId, $userLogin, $orderId = '', $categoryId = '')
    {
        $supportRepository = new SupportRepository(new CMS());

        $ticketId = $supportRepository->createTicket(
            $userId,
            $orderId,
            $categoryId,
            Lang::get('payment_in_cash'),
            $message,
            false,
            $userLogin
        );

        if (!$ticketId) {
            throw new Exception('error_has_occurred_try_later');
        }

        $supportUrl = UrlGenerator::generateSupportUrl(array('mode' => 'chat', 'id' => $ticketId));
        return $this->redirect($supportUrl);
    }

    /**
     * @param float $amount
     * @param string $currency
     * @return string
     */
    private function getPayCashAmount($amount, $currency)
    {
        $rounding = (int) General::getNumConfigValue('price_rounding');
        return number_format($amount, $rounding, '.', ' ') . ' ' . $currency;
    }

    /**
     * @param float $amount
     * @param string $currency
     * @return string
     */
    private function getPayCashTicketMessage($amount, $currency)
    {
        $formattedAmount = $this->getPayCashAmount($amount, $currency);
        return Lang::get('payment_in_cash_text') . '. ' . Lang::get('sum') . ': ' . $formattedAmount;
    }

    private function payDefault($orderId, $paymentId, $amount)
    {
        list($urlSuccess, $urlFailure) = $this->getResponseUrls($orderId);

        $lang = Session::getActiveLang();
        $sid = User::getObject()->getSid();
        $currencyCode = InstanceProvider::getObject()->GetInternalCurrency()->GetCode();
        $amount = $orderId ? $this->getSalesOrderDetailsResult($orderId)->GetSalesOrderInfo()->GetRemainAmount() : $amount;
        $xmlParams = $this->getPaymentParametersXml($amount, $currencyCode, $paymentId, $orderId, $urlSuccess, $urlFailure);

        OTAPILib2::GetPaymentParameters($lang, $sid, $xmlParams, $answer);
        OTAPILib2::makeRequests();

        $paymentParameters = $this->preparePaymentParameters($answer, $paymentId);

        return $this->render('controllers/pay/payment', $paymentParameters);
    }

    /**
     * @param OtapiPaymentFormAnswer $OTAPILib2Answer
     * @param $paymentId
     * @return array
     */
    private function preparePaymentParameters($OTAPILib2Answer, $paymentId)
    {
        $OTAPILib2AnswerResult = $OTAPILib2Answer->GetResult();

        $result = [
            'requestMethod' => $OTAPILib2AnswerResult->GetRequestMethod() ?: 'post',
            'requestUrl' => $OTAPILib2AnswerResult->GetRequestUrl(),
            'isNewWindow' => $OTAPILib2AnswerResult->IsNewWindow(),
            'isIFrame' => $OTAPILib2AnswerResult->IsIFrame(),
            'isImmediate' => $OTAPILib2AnswerResult->IsImmmediate(),
            'parameters' => [],
        ];

        foreach ($OTAPILib2AnswerResult->GetParameters()->GetParameter() as $parameter) {
            $result['parameters'][] = [
                'name' => $parameter->GetName(),
                'value' => $parameter->GetValue(),
                'isUserData' => $parameter->IsUserData(),
            ];
        }

        $parameters = [];
        if (substr($paymentId, 0, 7) === 'direct.') {
            if (!General::getConfigValue('use_https', false)) {
                $result['requestUrl'] = str_replace('https://', 'http://', $result['requestUrl']);
            }
            $parameters['cssBaseUrl'] = UrlGenerator::getHomeUrl();
            $parameters['css[0]'] = General::getThemeWebDir() . "/css/vendor/bootstrap.min.css";
            $parameters['css[1]'] = General::getThemeWebDir() . "/css/screen.css";
            if (file_exists(General::getThemeDir().'/css/screen-custom.css')) {
                $parameters['css[2]'] = General::getThemeWebDir() . "/css/screen-custom.css";
            }
        }
        if (isset($result['parameters'])) {
            foreach ($result['parameters'] as $parameter) {
                $parameters[$parameter['name']] = $parameter['value'];
            }
        }
        $result['iFrameQuery'] = http_build_query($parameters);
        return $result;
    }

    /**
     * @param string $orderId
     * @return array
     */
    private function getResponseUrls($orderId = null)
    {
        $isOrder = $orderId;
        $homeUrl = UrlGenerator::getHomeUrl();

        if ($isOrder) {
            $urlSuccess = defined('CFG_ORDER_PAID_SUCCESS_URL') ? CFG_ORDER_PAID_SUCCESS_URL : $homeUrl . '/?q=paymentsuccess';
            $urlFailure = defined('CFG_ORDER_PAID_FAIL_URL') ? CFG_ORDER_PAID_FAIL_URL : $homeUrl . '/?q=paymentfail';
        } else {
            $urlSuccess = defined('CFG_DEPOSIT_PAID_SUCCESS_URL') ? CFG_DEPOSIT_PAID_SUCCESS_URL : $homeUrl . '/?q=depositsuccess';
            $urlFailure = defined('CFG_DEPOSIT_PAID_FAIL_URL') ? CFG_DEPOSIT_PAID_FAIL_URL : $homeUrl . '/?q=depositfail';
        }

        return array($urlSuccess, $urlFailure);
    }

    /**
     * Генерируем xml параметры для запроса GetPaymentParameters
     *
     * @param $amount
     * @param $currencyCode
     * @param $paymentId
     * @param $orderId
     * @param $urlSuccess
     * @param $urlFailure
     * @return mixed
     */
    private function getPaymentParametersXml($amount, $currencyCode, $paymentId, $orderId, $urlSuccess, $urlFailure) {
        $xml = new SimpleXMLElement('<PaymentRequest></PaymentRequest>');
        $xml->addChild('Amount', htmlspecialchars($amount));
        $xml->addChild('CurrencyCode', htmlspecialchars($currencyCode));
        $xml->addChild('PaymentSystemId', htmlspecialchars($paymentId));
        $xml->addChild('SuccessUrl', htmlspecialchars($urlSuccess));
        $xml->addChild('FailUrl', htmlspecialchars($urlFailure));
        $xml->addChild('IsPartialPayment', 'false');

        if ($orderId) {
            $xml->addChild('OrderId', htmlspecialchars($orderId));
        }

        $custom = $this->request->getValue('Custom');
        if (! empty($custom[$paymentId])) {
            $xml->addChild('Custom', htmlspecialchars($custom[$paymentId]));
        }

        return $xml->asXML();
    }


    /**
     * @param OtapiOrderDetailsInfo $orderData
     * @return array
     */
    private function getPayGroups($orderData)
    {
        $payGroups = [];

        $payGroups = $this->appendPayGroupSystem($payGroups);
        $payGroups = $this->appendPayGroupCash($payGroups);
        $payGroups = $this->appendPayGroupQuittance($payGroups);
        $payGroups = $this->appendPayGroupAccount($payGroups, $orderData);
        $payGroups = $this->sortPayGroups($payGroups);

        return $payGroups;
    }

    private function appendPayGroupSystem($payGroups)
    {
        $systemPayGroup = [];
        $lang = Session::getActiveLang();

        OTAPILib2::GetPaymentModes($lang, $payModes);
        OTAPILib2::makeRequests();

        // Группируем системные методы оплаты
        foreach ($payModes->GetResult()->GetPaymentMode() as $mode) {
            $sortCode = $mode->GetPaymSortCode();
            $sortCode = $sortCode ? $sortCode : 'other_payments';

            $sortName = $mode->GetPaymSortText();
            $sortName = $sortName ? $sortName : Lang::get('other_payments');

            if (! isset($systemPayGroup[$sortCode])) {
                $systemPayGroup[$sortCode] = array(
                    'code' => $sortCode,
                    'name' => $sortName
                );
            }

            $systemPayGroup[$sortCode]['childPayModes'][$mode->GetId()] = [
                'id' => $mode->GetId(),
                'name' => $mode->GetName(),
                'description' => $mode->GetDescription(),
                'absoluteImageUrl' => $mode->GetAbsoluteImageUrl(),
                'customField' => $mode->GetCustomField()
            ];
        }

        // Добавляем группу системных методов оплаты в основной массив
        if (! empty($systemPayGroup)) {
            $payGroups['payment_in_system'] = [
                'code' => 'payment_in_system',
                'name' => Lang::get('Payment_in_system'),
                'childPayModes' => $systemPayGroup
            ];
        }

        return $payGroups;
    }

    /**
     * Добавляем в группы, оплату с лицевого счета, если она доступна
     *
     * @param array $payGroups
     * @param OtapiOrderDetailsInfo $orderData
     * @return array
     */
    private function appendPayGroupAccount($payGroups, $orderData)
    {
        $accountInfo = User::getObject()->getAccountInfo();
        $availableAmount = (float) $accountInfo->GetAvailableAmount();
        $currencySign = $accountInfo->GetCurrencySign();

        if ($availableAmount && $orderData) {
            $amount = $orderData->GetSalesOrderInfo()->GetRemainAmount();

            $payModeDescription = '';
            if ($availableAmount < $amount) {
                $payModeDescription .= '<span class="color-red">' .Lang::get('Account_part_payment') . '</span><br>';
            }
            $payModeDescription .= Lang::get('on_account') . ' ' . General::getHtmlPrice(['Val' => $availableAmount, 'Sign' => $currencySign]);

            $payGroups['from_account'] = [
                'code' => 'from_account',
                'name' => Lang::get('from_account'),
                'childPayModes' => [
                    'from_account' => [
                        'code' => 'from_account',
                        'name' => Lang::get('from_account'),
                        'childPayModes' => [
                            'from_account' => [
                                'id' => 'from_account',
                                'name' => Lang::get('from_account'),
                                'description' => $payModeDescription,
                                'absoluteImageUrl' => '',
                                'customField' => 'None',
                                'isLeaf' => true
                            ]
                        ]
                    ]
                ]
            ];
        }

        return $payGroups;
    }

    /**
     * Добавляем в группы, оплату наличными, если она доступна
     *
     * @param array $payGroups
     * @return array
     */
    private function appendPayGroupCash($payGroups)
    {
        if (General::getConfigValue('payment_in_cash')) {
            $payGroups['payment_in_cash'] = [
                'code' => 'payment_in_cash',
                'name' => Lang::get('cash'),
                'childPayModes' => [
                    'payment_in_cash' => [
                        'code' => 'payment_in_cash',
                        'name' => Lang::get('payment_in_cash'),
                        'childPayModes' => [
                            'payment_in_cash' => [
                                'id' => 'payment_in_cash',
                                'name' => Lang::get('payment_in_cash'),
                                'description' => Lang::get('payment_in_cash_description'),
                                'absoluteImageUrl' => '',
                                'customField' => 'None'
                            ]
                        ]
                    ]
                ]
            ];
        }

        return $payGroups;
    }

    /**
     * Добавляем в группы, оплату квитанцией сбербанк, если она доступна
     *
     * @param array $payGroups
     * @return array
     */
    private function appendPayGroupQuittance($payGroups)
    {
        $currencyCode = InstanceProvider::getObject()->GetInternalCurrency()->GetCode();
        $quittance = CMS::GetQuittanceMethod(['Code' => $currencyCode]);

        if (!empty($quittance)) {
            $payGroups[$quittance['PaymSortCode']] = [
                'code' => $quittance['PaymSortCode'],
                'name' => $quittance['PaymSortText'],
                'childPayModes' => [
                    $quittance['Id'] => [
                        'code' => $quittance['Id'],
                        'name' => $quittance['Name'],
                        'childPayModes' => [
                            $quittance['Id'] => [
                                'id' => $quittance['Id'],
                                'name' => $quittance['Name'],
                                'description' => $quittance['Description'],
                                'absoluteImageUrl' => $quittance['AbsoluteImageUrl'],
                                'customField' => $quittance['CustomField'],
                            ]
                        ]
                    ]
                ]
            ];
        }

        return $payGroups;
    }

    /**
     * Перемещаем методы оплаты без группировки, в конец массива
     *
     * @param array $payGroups
     * @return array
     */
    private function sortPayGroups($payGroups)
    {
        uksort($payGroups, function ($k1) {
            return $k1 === 'other_payments' ? 1 : 0;
        });

        if (isset($payGroups['from_account'])) {
            $payGroups = array_merge(['from_account' => $payGroups['from_account']], $payGroups);
        }

        return $payGroups;
    }


    /**
     * @return array
     */
    private function getUserInfo()
    {
        $userInfo = User::getObject()->getUserInfo();

        return [
            'id' => (string) $userInfo->GetId()->GetRawData(),
            'login' => $userInfo->GetLogin(),
            'firstName' => $userInfo->GetFirstName(),
            'lastName' => $userInfo->GetLastName(),
            'middleName' => $userInfo->GetMiddleName(),
            'recipientFirstName' => $userInfo->GetRecipientFirstName(),
            'recipientMiddleName' => $userInfo->GetRecipientMiddleName(),
            'recipientLastName' => $userInfo->GetRecipientLastName(),
            'postalCode' => $userInfo->GetPostalCode(),
            'country' => $userInfo->GetCountry(),
            'city' => $userInfo->GetCity(),
            'address' => $userInfo->GetAddress()
        ];
    }

    /**
     * @param $orderId
     * @return OtapiOrderDetailsInfo
     */
    private function getSalesOrderDetailsResult($orderId)
    {
        if (! $this->orderDetails) {
            $lang = Session::getActiveLang();
            $sid = User::getObject()->getSid();

            OTAPILib2::GetSalesOrderDetails($lang, $sid, $orderId, $answer);
            OTAPILib2::makeRequests();

            $this->orderDetails = $answer->GetResult();
        }

        return $this->orderDetails;
    }
}