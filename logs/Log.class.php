<?php

class Log {
    protected $notificationUrl;
    protected $beginTime;
    protected $loadingTime;

    protected $filePath;
    protected $backupPath;

    /**
     * @var RequestWrapper
     */
    protected $request;
    private $methodsCalls = array();

    public function __construct($request = null){
        $this->request = $request ? $request : new RequestWrapper();
        $this->filePath = dirname(__FILE__) . '/log.dat';
        $this->backupPath = dirname(__FILE__) . '/log.ready.dat';
        $this->notificationUrl = CFG_SUPPORT_URL . '/log_analyzer/on_ready_log';
        if(defined('CFG_LOG_ANALYZE_URL'))
            $this->notificationUrl = CFG_LOG_ANALYZE_URL;

        $this->Create();
    }

    public function setNotificationUrl($url)
    {
        $this->notificationUrl = $url;
    }

    public function Create(){
        if(!file_exists($this->filePath)){
            $f = fopen($this->filePath, 'w');
            fclose($f);
        }
    }

    public function Start(){
        $this->beginTime = microtime(1);
    }

    public function Stop(){
        $this->loadingTime = microtime(1) - $this->beginTime;
    }

    public function GetLoadingTime(){
        return $this->loadingTime;
    }

    public function Write(){
        $f = fopen($this->filePath, 'a');

        $query = http_build_query($this->request->getAll());
        $data = array($this->loadingTime, $this->request->getValue('p'), "/?{$query}");
        $data['calls'] = $this->methodsCalls;
        Session::set('otapi_calls', $data['calls']);
        Session::set('loading_time', $this->loadingTime);
        fwrite($f, json_encode($data)."\n");
        fclose($f);
    }

    public function Read(){
        return file_exists($this->filePath) ? file_get_contents($this->filePath) : '';
    }

    public function Release(){
        $this->Backup();
        $this->SendOnReadyNotification();
    }

    public function Backup(){
        file_put_contents($this->backupPath, file_get_contents($this->filePath));
        unlink($this->filePath);
    }

    public function SendOnReadyNotification(){
        $curl = new Curl($this->notificationUrl, 2, true, 3);
        $curl->setReferer(UrlGenerator::getProtocol() . '://' . trim($_SERVER['HTTP_HOST'], '/') . '/');
        $curl->connect();
    }

    public function DeleteBackup(){
        unlink($this->backupPath);
    }

    public function Size(){
        return count(file($this->filePath));
    }

    public function AddMethod($methodCallInfo)
    {
        preg_match('/<ErrorCode>(.+)<\/ErrorCode>/isU', isset($methodCallInfo['response']) ? $methodCallInfo['response'] : '', $success);
        $logData = array(
            'methodname' => $methodCallInfo['methodname'],
            'time' => $methodCallInfo['time'],
            'success' => isset($success[1]) ? $success[1] : 'Undefined'
        );
        if (! empty($methodCallInfo['errorMessage'])) {
            $logData['errorMessage'] = $methodCallInfo['errorMessage'];
        }
        $this->methodsCalls[] = $logData;
    }
}