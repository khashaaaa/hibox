<?php

class OtapiRecreateOrderResponse extends BaseOtapiType{
    /**
     * @return OtapiOrderInfoAnswer
     */
    public function GetRecreateOrderResult(){
        $value = isset($this->xmlData->RecreateOrderResult) ? $this->xmlData->RecreateOrderResult : false;
        return new OtapiOrderInfoAnswer($value);
    }
}