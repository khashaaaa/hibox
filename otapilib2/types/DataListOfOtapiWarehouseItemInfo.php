<?php

class DataListOfOtapiWarehouseItemInfo extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiWarehouseItemInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new ArrayOfOtapiWarehouseItemInfo($value);
    }
}