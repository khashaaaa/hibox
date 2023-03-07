<?php

class OtapiBasketCheckingResult extends BaseOtapiType{
    /**
     * @return boolean
     */
    public function IsFinished(){
        $value = isset($this->xmlData->IsFinished) ? (string)$this->xmlData->IsFinished : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetProgressPercent(){
        $value = isset($this->xmlData->ProgressPercent) ? (string)$this->xmlData->ProgressPercent : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiArrayOfBasketCheckingMessage
     */
    public function GetMessages(){
        $value = isset($this->xmlData->Messages) ? $this->xmlData->Messages : false;
        return new OtapiArrayOfBasketCheckingMessage($value);
    }
}