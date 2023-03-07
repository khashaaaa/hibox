<?php

class OtapiCancelSalesOrderResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetCancelSalesOrderResult(){
        $value = isset($this->xmlData->CancelSalesOrderResult) ? $this->xmlData->CancelSalesOrderResult : false;
        return new VoidOtapiAnswer($value);
    }
}