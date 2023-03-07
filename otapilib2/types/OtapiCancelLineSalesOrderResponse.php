<?php

class OtapiCancelLineSalesOrderResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetCancelLineSalesOrderResult(){
        $value = isset($this->xmlData->CancelLineSalesOrderResult) ? $this->xmlData->CancelLineSalesOrderResult : false;
        return new VoidOtapiAnswer($value);
    }
}