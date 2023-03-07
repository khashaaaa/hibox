<?php

class DataListOfOtapiWarehouseCategoryInfo extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiWarehouseCategoryInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new ArrayOfOtapiWarehouseCategoryInfo($value);
    }
}