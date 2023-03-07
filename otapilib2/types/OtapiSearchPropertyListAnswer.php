<?php

class OtapiSearchPropertyListAnswer extends OtapiAnswer{
    /**
     * @return DataListOfOtapiItemSearchProperty
     */
    public function GetSearchPropertyInfoList(){
        $value = isset($this->xmlData->SearchPropertyInfoList) ? $this->xmlData->SearchPropertyInfoList : false;
        return new DataListOfOtapiItemSearchProperty($value);
    }
}