<?php

class OtapiBackgroundActivityStepInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetType(){
        $value = isset($this->xmlData->Type) ? (string)$this->xmlData->Type : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetMessage(){
        $value = isset($this->xmlData->Message) ? (string)$this->xmlData->Message : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetTimeFromStart(){
        $value = isset($this->xmlData->TimeFromStart) ? (string)$this->xmlData->TimeFromStart : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiArrayOfBackgroundActivityStepActionInfo
     */
    public function GetActions(){
        $value = isset($this->xmlData->Actions) ? $this->xmlData->Actions : false;
        return new OtapiArrayOfBackgroundActivityStepActionInfo($value);
    }
}