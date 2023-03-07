<?php

class OtapiExternalDeliveryTypeAnswer extends OtapiAnswer{
    /**
     * @return OtapiExternalDeliveryType
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiExternalDeliveryType($value);
    }
}