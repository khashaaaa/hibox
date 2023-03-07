<?php

class OtapiBackgroundActivityStepActionInfo extends BaseOtapiType{
    /**
     * @return OtapiBackgroundActivityStepActionId
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? $this->xmlData->Id : false;
        return new OtapiBackgroundActivityStepActionId($value);
    }
    /**
     * @return string
     */
    public function GetDisplayName(){
        $value = isset($this->xmlData->DisplayName) ? (string)$this->xmlData->DisplayName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiNamedParameters
     */
    public function GetParameters(){
        $value = isset($this->xmlData->Parameters) ? $this->xmlData->Parameters : false;
        return new OtapiNamedParameters($value);
    }
}