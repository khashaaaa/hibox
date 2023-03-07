<?php

class DataListOfOtapiItemInfo extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiItemInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new ArrayOfOtapiItemInfo($value);
    }
}