<?php

class OtapiGetSalesOrderShippingsResponse extends BaseOtapiType{
    /**
     * @return OtapiSalesShippingInfoListAnswer
     */
    public function GetGetSalesOrderShippingsResult(){
        $value = isset($this->xmlData->GetSalesOrderShippingsResult) ? $this->xmlData->GetSalesOrderShippingsResult : false;
        return new OtapiSalesShippingInfoListAnswer($value);
    }
}