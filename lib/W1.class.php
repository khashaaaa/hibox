<?php

class W1 {
    private $_server;
    
    public function __construct() {
        $server = defined('CFG_PAYMENT_SERVER') ? CFG_PAYMENT_SERVER : 'http://paygate.otapi.net/';
        
        $internal = false;
        if(defined('CFG_SERVICE_INSTANCEKEY'))
            $internal = CFG_SERVICE_INSTANCEKEY;

        if($internal === false)
           die('Error: no Internal');

        $this->_server = $server.'w1.callback?instanceKey='.$internal;
    }
    
    private function __getData($method, $params){
        $curl = new Curl($this->_server, 60, false, 10, false, false, false);
        $curl->setPost(http_build_query($params));
        $cres = $curl->connect();
        $str = '';
        if($cres){
            $str = $curl->getWebPage();
        }
        return $str;
    }

    public function result($params){
        $res = $this->__getData('result', $params);
        return $res;
    }

    public function  success(){
        header('Location: '.UrlGenerator::getHomeUrl().'/?p=robo_success');
        die();
    }

    public function fail(){
        header('Location: '.UrlGenerator::getHomeUrl().'/?p=robo_fail');
        die();
    }
    
    public function handleRequest(){
        if(isset($_GET['successUrl'])){
            $this->success();
        }
        elseif(isset($_GET['failUrl'])){
            $this->fail();
        }
        else{
            print $this->result($_POST);
        }
    }
}

?>
