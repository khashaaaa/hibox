<?php

class OtapiExternalDeliveryRateAnswer extends OtapiAnswer{
    /**
     * @return OtapiExternalDeliveryRate
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiExternalDeliveryRate($value);
    }
}