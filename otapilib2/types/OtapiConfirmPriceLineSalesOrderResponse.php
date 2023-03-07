<?php

class OtapiConfirmPriceLineSalesOrderResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetConfirmPriceLineSalesOrderResult(){
        $value = isset($this->xmlData->ConfirmPriceLineSalesOrderResult) ? $this->xmlData->ConfirmPriceLineSalesOrderResult : false;
        return new VoidOtapiAnswer($value);
    }
}