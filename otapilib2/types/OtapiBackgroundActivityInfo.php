<?php

class OtapiBackgroundActivityInfo extends BaseOtapiType{
    /**
     * @return OtapiBackgroundActivityId
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? $this->xmlData->Id : false;
        return new OtapiBackgroundActivityId($value);
    }
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
    public function GetDescription(){
        $value = isset($this->xmlData->Description) ? (string)$this->xmlData->Description : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsFinished(){
        $value = isset($this->xmlData->IsFinished) ? (string)$this->xmlData->IsFinished : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsCancelled(){
        $value = isset($this->xmlData->IsCancelled) ? (string)$this->xmlData->IsCancelled : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsAwaitingAction(){
        $value = isset($this->xmlData->IsAwaitingAction) ? (string)$this->xmlData->IsAwaitingAction : false;
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
     * @return int
     */
    public function GetWorkTime(){
        $value = isset($this->xmlData->WorkTime) ? (string)$this->xmlData->WorkTime : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiArrayOfBackgroundActivityActionInfo
     */
    public function GetActions(){
        $value = isset($this->xmlData->Actions) ? $this->xmlData->Actions : false;
        return new OtapiArrayOfBackgroundActivityActionInfo($value);
    }
    /**
     * @return dateTime
     */
    public function GetStartTime(){
        $value = isset($this->xmlData->StartTime) ? (string)$this->xmlData->StartTime : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}