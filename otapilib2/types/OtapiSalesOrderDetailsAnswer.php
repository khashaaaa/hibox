<?php

class OtapiSalesOrderDetailsAnswer extends OtapiAnswer{
    /**
     * @return OtapiOrderDetailsInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiOrderDetailsInfo($value);
    }
}