<?php

class OtapiGetSalesOrderDetailsForOperatorResponse extends BaseOtapiType{
    /**
     * @return OtapiSalesOrderDetailsAnswer
     */
    public function GetGetSalesOrderDetailsForOperatorResult(){
        $value = isset($this->xmlData->GetSalesOrderDetailsForOperatorResult) ? $this->xmlData->GetSalesOrderDetailsForOperatorResult : false;
        return new OtapiSalesOrderDetailsAnswer($value);
    }
}