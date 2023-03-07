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
        return $this->xmlData->asXML();
    }

    public function asString()
    {
        return (string)$this->xmlData;
    }
}