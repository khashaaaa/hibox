<?php

class OtapiBackgroundActivityIdentificationInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetType(){
        $value = isset($this->xmlData->Type) ? (string)$this->xmlData->Type : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiBackgroundActivityId
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? $this->xmlData->Id : false;
        return new OtapiBackgroundActivityId($value);
    }
}