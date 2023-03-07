<?php

class OtapiSearchOrdersResponse extends BaseOtapiType{
    /**
     * @return OtapiSalesOrderInfoListFrameAnswer
     */
    public function GetSearchOrdersResult(){
        $value = isset($this->xmlData->SearchOrdersResult) ? $this->xmlData->SearchOrdersResult : false;
        return new OtapiSalesOrderInfoListFrameAnswer($value);
    }
}