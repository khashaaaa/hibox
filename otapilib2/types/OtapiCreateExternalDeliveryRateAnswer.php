<?php

class OtapiCreateExternalDeliveryRateAnswer extends OtapiAnswer{
    /**
     * @return int
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? (string)$this->xmlData->Result : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}