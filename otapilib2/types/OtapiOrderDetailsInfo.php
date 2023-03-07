<?php

class OtapiOrderDetailsInfo extends BaseOtapiType{
    /**
     * @return OtapiOrderInfo
     */
    public function GetSalesOrderInfo(){
        $value = isset($this->xmlData->SalesOrderInfo) ? $this->xmlData->SalesOrderInfo : false;
        return new OtapiOrderInfo($value);
    }
    /**
     * @return OtapiArrayOfSalesLine
     */
    public function GetSalesLinesList(){
        $value = isset($this->xmlData->SalesLinesList) ? $this->xmlData->SalesLinesList : false;
        return new OtapiArrayOfSalesLine($value);
    }
}