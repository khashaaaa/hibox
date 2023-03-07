<?php

class DataListOfOtapiSearchBrandInfo extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiSearchBrandInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new ArrayOfOtapiSearchBrandInfo($value);
    }
}