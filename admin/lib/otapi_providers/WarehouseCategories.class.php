<?php

/**
 * Class WarehouseCategories
 */
class WarehouseCategories {
    /**
     * @var OTAPIlib
     */
    private $otapilib;

    public function __construct($otapilib)
    {
        $this->otapilib = $otapilib;
    }

    public function Add($adminSessionId, $categoryXML){
        return $this->otapilib->CreateWarehouseCategory($adminSessionId, $categoryXML);
    }

    public function Search($adminSessionId, $searchXML, $from=0, $frameSize=18){
        return $this->otapilib->SearchWarehouseCategories($adminSessionId, $searchXML, $from, $frameSize);
    }

    public function Delete($adminSessionId, $id){
        return $this->otapilib->DeleteWarehouseCategory($adminSessionId, $id);
    }
}