<?php

class DataListOfOtapiDeliveryCost extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiDeliveryCost1
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new ArrayOfOtapiDeliveryCost1($value);
    }
}