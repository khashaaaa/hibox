<?php

class OtapiConfiguredItemListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfString
     */
    public function GetConfiguredItems(){
        $value = isset($this->xmlData->ConfiguredItems) ? $this->xmlData->ConfiguredItems : false;
        return new OtapiDataListOfString($value);
    }
}