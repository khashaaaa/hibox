<?php

class OtapiEditExternalDeliveryTypeResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetEditExternalDeliveryTypeResult(){
        $value = isset($this->xmlData->EditExternalDeliveryTypeResult) ? $this->xmlData->EditExternalDeliveryTypeResult : false;
        return new VoidOtapiAnswer($value);
    }
}