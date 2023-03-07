<?php

class OtapiGetSalesOrdersListForOperatorResponse extends BaseOtapiType{
    /**
     * @return OtapiSalesOrderInfoListAnswer
     */
    public function GetGetSalesOrdersListForOperatorResult(){
        $value = isset($this->xmlData->GetSalesOrdersListForOperatorResult) ? $this->xmlData->GetSalesOrdersListForOperatorResult : false;
        return new OtapiSalesOrderInfoListAnswer($value);
    }
}