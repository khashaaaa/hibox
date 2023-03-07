<?php

class OtapiDataListOfDeliveryMode extends BaseOtapiType{
    /**
     * @return OtapiArrayOfDeliveryMode
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfDeliveryMode($value);
    }
}