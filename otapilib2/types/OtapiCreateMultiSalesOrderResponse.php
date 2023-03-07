<?php

class OtapiCreateMultiSalesOrderResponse extends BaseOtapiType{
    /**
     * @return OtapiSalesOrderInfoListAnswer
     */
    public function GetCreateMultiSalesOrderResult(){
        $value = isset($this->xmlData->CreateMultiSalesOrderResult) ? $this->xmlData->CreateMultiSalesOrderResult : false;
        return new OtapiSalesOrderInfoListAnswer($value);
    }
}