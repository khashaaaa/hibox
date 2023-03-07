<?php

class OtapiTicketCategoryInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetCategoryId(){
        $value = isset($this->xmlData->CategoryId) ? (string)$this->xmlData->CategoryId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetName(){
        $value = isset($this->xmlData->Name) ? (string)$this->xmlData->Name : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}