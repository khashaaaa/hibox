<?php

class OtapiExternalDeliveryRateListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfExternalDeliveryRate
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfExternalDeliveryRate($value);
    }
}