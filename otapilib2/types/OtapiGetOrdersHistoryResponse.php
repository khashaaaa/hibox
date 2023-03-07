<?php

class OtapiGetOrdersHistoryResponse extends BaseOtapiType{
    /**
     * @return OtapiOrdersHistoryAnswer
     */
    public function GetGetOrdersHistoryResult(){
        $value = isset($this->xmlData->GetOrdersHistoryResult) ? $this->xmlData->GetOrdersHistoryResult : false;
        return new OtapiOrdersHistoryAnswer($value);
    }
}