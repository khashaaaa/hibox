<?php

class DataListOfOtapiTransactionInfo extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiTransactionInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new ArrayOfOtapiTransactionInfo($value);
    }
}