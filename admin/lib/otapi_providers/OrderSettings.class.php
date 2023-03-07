<?php

class OrderSettings {
    /**
     * @var OTAPIlib
     */
    private $otapilib;

    public function __construct($otapilib){
        $this->otapilib = $otapilib;
    }

    public function Get(){
        $this->otapilib->setResultInXMLOn();
        $settings = $this->otapilib->GetOrderSettingsInfo(Session::get('sid'));
        $this->otapilib->setResultInXMLOff();
        return $settings;
    }

    public function SetMinOrderCost($value){
        $this->Set('MinOrderCost', $value);
    }

    public function SetMaxNoteItemsCount($value){
        $this->Set('MaxNoteItemsCount', $value);
    }

    public function SetMaxCartItemsCount($value){
        $this->Set('MaxCartItemsCount', $value);
    }

    public function Set($parameter, $value){
        $this->otapilib->setResultInXMLOn();
        $settings = $this->otapilib->GetOrderSettingsInfo(Session::get('sid'));
        $this->otapilib->setResultInXMLOff();
        $settings->Result->{$parameter} = $value;

        $requestXML = new SimpleXMLElement('<OrderSettingsUpdateData></OrderSettingsUpdateData>');
        $requestXML->addChild('MinOrderCost', $settings->Result->MinOrderCost);
        $requestXML->addChild('MaxCartItemsCount', $settings->Result->MaxCartItemsCount);
        $requestXML->addChild('MaxNoteItemsCount', $settings->Result->MaxNoteItemsCount);

        $this->otapilib->UpdateOrderSettings(Session::get('sid'),$requestXML->asXML());
    }
}