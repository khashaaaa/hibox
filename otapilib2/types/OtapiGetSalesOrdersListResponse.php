<?php

class OtapiGetSalesOrdersListResponse extends BaseOtapiType{
    /**
     * @return OtapiSalesOrderInfoListAnswer
     */
    public function GetGetSalesOrdersListResult(){
        $value = isset($this->xmlData->GetSalesOrdersListResult) ? $this->xmlData->GetSalesOrdersListResult : false;
        return new OtapiSalesOrderInfoListAnswer($value);
    }
}