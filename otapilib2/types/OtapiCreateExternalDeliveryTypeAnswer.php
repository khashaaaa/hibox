<?php

class OtapiCreateExternalDeliveryTypeAnswer extends OtapiAnswer{
    /**
     * @return string
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? (string)$this->xmlData->Result : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}