<?php


class CustomCurlRequest extends RollingCurlRequest
{
    public $url = false;
    public $method = 'POST';
    public $serviceMethod = '';
    public $parameters = array();
    public $post_data = null;
    public $headers = null;
    public $options = null;
    public $hash = null;
    public $typeName = null;

    function __construct($url, $method = "GET", $post_data = null, $headers = null, $options = null, $hash = null,
                         $typeName = '', $serviceMethod = '', $parameters = array())
    {
        $this->url = $url;
        $this->method = $method;
        $this->post_data = $post_data;
        $this->headers = $headers;
        $this->options = $options;
        $this->hash = $hash;
        $this->typeName = $typeName;
        $this->serviceMethod = $serviceMethod;
        $this->parameters = $parameters;

        $additionalHeader = array();
        if (RequestWrapper::getClientIp()) {
            $additionalHeader[] = 'X-OT-User-IP: ' . RequestWrapper::getClientIp();
        }
        if (isset($_SERVER["REMOTE_ADDR"])) {
            $additionalHeader[] = 'X-OT-ip: ' . $_SERVER["REMOTE_ADDR"];
        }

        if (empty($this->headers)) {
            $this->headers = $additionalHeader;
        } else {
            $this->headers = array_merge($this->headers, $additionalHeader);
        }
    }
}


class AbstractOTAPILib2 {

    protected static $serverUrl = '';

    protected static $curlTimeOut = 30;

    private static $responses = array();

    private static $windowSize = 0;

    /**
     * @var CustomCurlRequest[]
     */
    private static $requests = array();

    /**
     * @var Debugger[]
     */
    private static $debuggers = array();

    /**
     * @var RollingCurl
     */
    private static $curl = null;

    public static function init() {
        self::$serverUrl = defined('CFG_SERVICE_URL') ? CFG_SERVICE_URL : 'http://otapi.net/OtapiWebService2.asmx/';
        self::$windowSize = defined('CFG_MULTI_CURL') && CFG_MULTI_CURL ? 5 : 1;
    }

    public static function getServerUrl() {
        return self::$serverUrl;
    }

    /**
     * Данные по умолчанию для подключения к сервису
     * @param string $instanceKey
     * @return array $params
     */
    protected static function defaultLogin($instanceKey = '') {
        $params = array(
            'instanceKey' => $instanceKey ? $instanceKey : CFG_SERVICE_INSTANCEKEY
        );
        if (OTBase::onFullDebug()) {
            $params['debug'] = 'true'; // дебаг информация отсутствует, если отсутствует данный параметр
        }
        return $params;
    }

    /**
     * Добавляет параметры для подписи
     * @param string $methodName
     * @param string $params
     * @return array $params
     */
    protected static function checkSecret($methodName, $params) {
        if (defined('CFG_SERVICE_SECRET')) {
            $params = array_filter($params, function($v) {
                return !is_array($v);
            });
            $params['timestamp'] = gmdate('YmdHis');

            ksort($params);
            $signature = $methodName . implode('', $params) . CFG_SERVICE_SECRET;
            $signature = hash('sha256', $signature);

            $params['signature'] = $signature;
        }

        return $params;
    }

    /**
     * @param $output
     * @param $info
     * @param CustomCurlRequest $request
     * @throws Exception
     */
    public static function catchResponse($output, $info, $request)
    {
        extract(Plugins::runSerialEvent('onAbstractOTAPILib2BeforeCatchResponse' . $request->serviceMethod, array(
            'output' => $output,
            'request' => $request,
        )));

        $xml = simplexml_load_string($output);

        $totalTime = 0;
        $realTime = (float) $xml->RequestTime / 1000;

        if (
            isset($xml->ErrorCode)
            && (string) $xml->ErrorCode != 'Ok'
            && (string) $xml->ErrorCode != 'BatchError'
        ) {
            if (isset(self::$debuggers[$request->hash])) {
                $debugger = self::$debuggers[$request->hash];

                $additionalText = '<red>' . $xml->ErrorDescription . '</red> \n';
                $totalTime = $debugger->end('', [
                    'additionalText' => $additionalText,
                    'realTime' => $realTime,
                ]);

                unset(self::$debuggers[$request->hash]);
            }
            General::registerOtapiRequest((string)$xml->RequestId, $totalTime * 1000, $request->serviceMethod);
            unset(self::$requests[$request->hash]);

            throw new ServiceException($request->serviceMethod, $request->parameters, $xml->ErrorDescription, (string)$xml->ErrorCode, self::$serverUrl, (string)$xml->SubErrorCode);
        }

        if (isset(self::$debuggers[$request->hash])) {
            $debugger = self::$debuggers[$request->hash];

            $totalTime = $debugger->end('', [
                'realTime' => $realTime,
            ]);

            unset(self::$debuggers[$request->hash]);
        }
        General::registerOtapiRequest((string)$xml->RequestId, $totalTime * 1000, $request->serviceMethod);
        unset(self::$requests[$request->hash]);

        $typeName = $request->typeName;
        self::$responses[$request->hash] = new $typeName($xml);

        extract(Plugins::runSerialEvent('onAbstractOTAPILib2AfterCatchResponse' . $request->serviceMethod, array(
            'output' => $output,
            'request' => $request,
        )));
    }

    protected static function registerRequest($methodName, $parameters, $typeName, &$response)
    {
        extract(Plugins::runSerialEvent('onAbstractOTAPILib2BeforeRegisterRequest' . $methodName, array(
            'parameters' => $parameters,
            'typeName' => $typeName,
            'response' => $response,
        )));

        if (self::$curl == null) {
            self::$curl = new RollingCurl('AbstractOTAPILib2::catchResponse');
        }

        $parameters += self::defaultLogin();
        $parameters = self::checkSecret($methodName, $parameters);
        $responseHash = md5(serialize(array('parameters' => $parameters, 'method' => $methodName)));
        self::$responses[$responseHash] = &$response;

        $referrer = isset($_SERVER['HTTP_HOST']) ? 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : 'localhost';
        $request = new CustomCurlRequest(self::$serverUrl . $methodName, 'POST', http_build_query($parameters, '', '&'), null, array(
            CURLOPT_REFERER => $referrer
        ), $responseHash, $typeName, $methodName, $parameters);

        self::$curl->add($request);
        self::$requests[$responseHash] = $request;

        self::$debuggers[$responseHash] = new Debugger();

        if (self::$windowSize == 1) {
            self::makeRequests();
        }
    }

    public static function makeRequests()
    {
        if (!self::$requests) {
            return false;
        }

        foreach (self::$requests as $hash => $request) {
            $debugger = self::$debuggers[$hash];
            $debugger->start(Debugger::LOG_OTAPILIB_TYPE, [
                'startText' => $request->serviceMethod,
                'arguments' => $request->parameters,
                'method' => $request->serviceMethod,
            ]);
        }

        /*
         * выполняем запросы
         * execute() вызывает callback, переданный при инициализации объекта RollingCurl в self::$curl
         * */
        self::$curl->execute(self::$windowSize);
        self::$curl = null; // забываем отправленные запросы
    }

}