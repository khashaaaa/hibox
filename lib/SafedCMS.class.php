<?php


class SafedCMS extends CMS{
    private $dbConnected;
    protected $cms;
    
    public function __construct() {
        $this->dbConnected = $this->Check();
        $this->cms = new CMS();
    }
    
    public function callCMSMethod($method, $args){
        if(!$this->dbConnected){
            return false;
        }
        $reflectionMethod = new ReflectionMethod('CMS', $method);
        return $reflectionMethod->invokeArgs($this->cms, $args);
    }

    public function isConnected(){
        return $this->dbConnected;
    }
}

?>
