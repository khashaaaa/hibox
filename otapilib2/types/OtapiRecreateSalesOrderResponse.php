<?php

class OtapiRecreateSalesOrderResponse extends BaseOtapiType{
    /**
     * @return OtapiSalesOrderInfoAnswer
     */
    public function GetRecreateSalesOrderResult(){
        $value = isset($this->xmlData->RecreateSalesOrderResult) ? $this->xmlData->RecreateSalesOrderResult : false;
        return new OtapiSalesOrderInfoAnswer($value);
    }
}