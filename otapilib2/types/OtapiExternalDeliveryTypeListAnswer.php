<?php

class OtapiExternalDeliveryTypeListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfExternalDeliveryType
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfExternalDeliveryType($value);
    }
}