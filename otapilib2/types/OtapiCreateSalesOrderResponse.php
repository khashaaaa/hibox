<?php

class OtapiCreateSalesOrderResponse extends BaseOtapiType{
    /**
     * @return OtapiSalesOrderInfoAnswer
     */
    public function GetCreateSalesOrderResult(){
        $value = isset($this->xmlData->CreateSalesOrderResult) ? $this->xmlData->CreateSalesOrderResult : false;
        return new OtapiSalesOrderInfoAnswer($value);
    }
}