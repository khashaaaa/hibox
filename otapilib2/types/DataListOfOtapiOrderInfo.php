<?php

class DataListOfOtapiOrderInfo extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiOrderInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new ArrayOfOtapiOrderInfo($value);
    }
}