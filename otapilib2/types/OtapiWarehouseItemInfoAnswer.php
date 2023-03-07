<?php

class OtapiWarehouseItemInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiWarehouseItemInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiWarehouseItemInfo($value);
    }
}