<?php

class DataListOfOtapiCommonTranslatableOptionsInfo extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiCommonTranslatableOptionsInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new ArrayOfOtapiCommonTranslatableOptionsInfo($value);
    }
}