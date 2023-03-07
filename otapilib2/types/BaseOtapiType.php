<?php

class BaseOtapiType {
    /**
     * @var \SimpleXMLElement
     */
    protected $xmlData;

    public function __construct($xmlData)
    {
        $this->xmlData = $xmlData;
    }

    public function asXML()
    {
        if ($this->xmlData)
            return $this->xmlData->asXML();
        else
            return '';
    }

    public function asString()
    {
        return (string)$this->xmlData;
    }

    public function GetRawData()
    {
        return $this->xmlData;
    }
}