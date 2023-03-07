<?php

class OtapiCancelOrdersResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetCancelOrdersResult(){
        $value = isset($this->xmlData->CancelOrdersResult) ? $this->xmlData->CancelOrdersResult : false;
        return new VoidOtapiAnswer($value);
    }
}