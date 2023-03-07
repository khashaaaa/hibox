<?php

class Arca
{
    /**
     * @var ArcaModel|null
     */
    private $model;

    /**
     * @var string
     */
    private $server = 'http://paygate.otapi.net/arca.callback';

    /**
     * Список параметров, которые будут сохранены для подтвеждения и проверки платежа
     * @var SimpleXMLElement
     */
    private $availableParams;

    public function __construct(){
        $this->model = new ArcaModel();
        $this->availableParams = simplexml_load_file(CFG_ARCA_ROOT . '/config/available_payment_parameters.xml');

        if(!defined('CFG_ARCA_INTERNAL'))
            throw new Exception('Internal was not found');
    }

    /**
     * Сохраняет параметры платежа, для последующего подтвеждения и проверки
     *
     * @throws DBException, если есть ошибки при сохранении в базу данных
     * @param array $form
     * @param string $paymentSystemId
     * @return true, если платеж успешно сохранен
     */
    public function savePayment($form, $paymentSystemId){
        $formParameters = $this->extractFormParameters($form);
        $formParameters[] = array('Name' => 'payment_type', 'Value' => $paymentSystemId);
        $resultSave = $this->model->savePaymentForm($formParameters);
        return $resultSave;
    }

    /**
     * Удаляет платеж из базы данных
     *
     * @throws DBException, если есть ошибки
     * @param $id
     * @return bool
     */
    public function deletePayment($id){
        return $this->model->deletePayment($id);
    }

    /**
     * Делает пометку о том, что платеж успешно выставлен
     *
     * @param $orderId
     * @throws PaymentException
     * @return bool
     */
    public function onUserPaidSuccess($orderId){
        $paymentInfo = $this->model->getPaymentByOrderId($orderId);
        $checkParameters = $this->createARKAParametersFromPayment($paymentInfo);

        $method = $paymentInfo['payment_type'] == ArcaModel::PAYMENT_BY_VIRTUAL_CARD ? 'merchant_check'
            : 'emv_merchant_check';

        $merchantCheckResult = $this->callArcaRpc($method, $checkParameters);

        if(!$merchantCheckResult)
            throw new PaymentException('Arca merchant_check request error', $checkParameters);

        $this->model->markPaymentPaid($paymentInfo['id'], $merchantCheckResult['rrn']);
    }

    /**
     * Получение списка платежей в ожидании подтвеждения и дальнейшая обработка
     * @param int $limit
     */
    public function confirmPayments($limit = 1){
        $payments = $this->model->getPaidPayments($limit);
        foreach($payments as $payment){
            $this->confirmSinglePayment($payment);
        }
    }

    /**
     * Подтвеждение платежа
     * @param $paymentInfo
     * @throws PaymentException
     */
    public function confirmSinglePayment($paymentInfo){
        $checkParameters = $this->createARKAParametersFromPayment($paymentInfo);

        $merchantCheckResult = $this->callArcaRpc('confirmation', $checkParameters);
        if(!$merchantCheckResult)
            throw new PaymentException('Arca merchant_check request error', $checkParameters);

        $this->model->markPaymentConfirmed($paymentInfo['id']);
    }

    /**
     * Получение списка платежей в ожидании оповещения OTAPI
     * @param int $limit
     */
    public function multipleNotifyOTAPIPaymentSystem($limit = 1){
        $payments = $this->model->getConfirmedPayments($limit);
        foreach($payments as $payment){
            $this->notifyOTAPIPaymentSystem($payment);
        }
    }

    /**
     * Оповещает OTAPI о подтвежденном платеже
     * @param $paymentInfo
     * @return bool
     * @throws PaymentException
     */
    public function notifyOTAPIPaymentSystem($paymentInfo){
        $paymentInfo['internal'] = CFG_ARCA_INTERNAL;
        $paymentId = $paymentInfo['id'];
        unset($paymentInfo['id'], $paymentInfo['user_paid'], $paymentInfo['confirmed'], $paymentInfo['notified']);
        unset($paymentInfo['checked'], $paymentInfo['additionalURL']);

        $curl = new Curl($this->server.'?'.http_build_query($paymentInfo), 20, true);
        if(!$curl->connect()){
            throw new PaymentException('OTAPI connect error: '.$this->server.'?'.http_build_query($paymentInfo));
        }

        $notifyResult = $curl->getWebPage();
        if($notifyResult != 'OK'){
            throw new PaymentException('OTAPI call error: '.$this->server.'?'.http_build_query($paymentInfo), array(
                'error' => $notifyResult
            ));
        }

        $this->model->markPaymentNotified($paymentId);

        return true;
    }

    /**
     * Extract form parameters and validate them
     *
     * @param $form
     * @return array Form parameters
     * @throws NotFoundException
     */
    private function extractFormParameters($form){
        $formParameters = array();
        foreach($form['Parameters'] as $p){
            $isValidParameter = count($this->availableParams->xpath('//parameter[text() = "'.(string)$p['Name'].'"]'));
            if(!$isValidParameter){
                continue;
                throw new NotFoundException('Parameter '.(string)$p['Name'].' not found in available parameters list');
            }
            $formParameters[] = $p;
        }
        return $formParameters;
    }

    /**
     * Построение массива параметров для закпроса к АРКЕ из массива информации о платеже
     * @param $paymentInfo
     * @return array
     */
    private function createARKAParametersFromPayment($paymentInfo){
        return array(
            'hostID'=>$paymentInfo['hostID'],
            'orderID'=>$paymentInfo['orderID'],
            'amount'=>$paymentInfo['amount'],
            'currency'=>$paymentInfo['currency'],
            'mid'=>$paymentInfo['mid'],
            'tid'=>$paymentInfo['tid'],
            'mtpass'=>defined('CFG_ARCA_PRIVATE_PASS') ? CFG_ARCA_PRIVATE_PASS : 'asdasdasd',
            'trxnDetails'=> ''
        );
    }

    /**
     * Вызов АРКИ
     * @param $method
     * @param $arr
     * @return array|bool
     */
    private function callArcaRpc($method, $arr) {
        $url = "https://www.arca.am:8194/ssljson.yaws";
        $postData = array (
            "version" => "1.1",
            "id"=>"remoteRequest",
            "method"=>$method,
            "params"=> array($arr)
        );

        $postData = json_encode($postData);

        $private_cert=dirname(__FILE__)."/arca/crt/client.crt"; // Path to the .cert file provided by ArCa
        $private_key=dirname(__FILE__)."/arca/crt/client.key";   // Path to the .key file provided by ArCa

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($ch, CURLOPT_SSLCERT, $private_cert);
        curl_setopt($ch, CURLOPT_SSLKEY, $private_key);
        curl_setopt($ch, CURLOPT_SSLKEYPASSWD, defined('CFG_ARCA_PRIVATE_PASS') ? CFG_ARCA_PRIVATE_PASS : 'asdasdasd');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $ret = curl_exec($ch);
        curl_close($ch);

        $decoded = $ret ? get_object_vars(json_decode($ret)->result) : false;

        return $decoded;
    }
}
