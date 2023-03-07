<?php

class OtapiAnswer extends OtapiGeneralServiceAnswer{
    /**
     * @return string
     */
    public function GetRequestId(){
        $value = isset($this->xmlData->RequestId) ? (string)$this->xmlData->RequestId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetRequestTimeStatistic(){
        $value = isset($this->xmlData->RequestTimeStatistic) ? (string)$this->xmlData->RequestTimeStatistic : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function HasTranslateErrors(){
        $value = isset($this->xmlData->HasTranslateErrors) ? (string)$this->xmlData->HasTranslateErrors : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}