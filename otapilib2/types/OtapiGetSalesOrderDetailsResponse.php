<?php

class OtapiGetSalesOrderDetailsResponse extends BaseOtapiType{
    /**
     * @return OtapiSalesOrderDetailsAnswer
     */
    public function GetGetSalesOrderDetailsResult(){
        $value = isset($this->xmlData->GetSalesOrderDetailsResult) ? $this->xmlData->GetSalesOrderDetailsResult : false;
        return new OtapiSalesOrderDetailsAnswer($value);
    }
}