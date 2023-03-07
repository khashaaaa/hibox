<?php

class OtapiDataListOfSimplifiedItemInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfSimplifiedItemInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfSimplifiedItemInfo($value);
    }
}