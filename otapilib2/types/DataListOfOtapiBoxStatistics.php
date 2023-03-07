<?php

class DataListOfOtapiBoxStatistics extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiBoxStatistics
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new ArrayOfOtapiBoxStatistics($value);
    }
}