<?php

class DataListOfOtapiOrderLineInfo extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiOrderLineInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new ArrayOfOtapiOrderLineInfo($value);
    }
}