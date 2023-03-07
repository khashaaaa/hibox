<?php

class EditableOtapiCategory extends OtapiCategory{
    /**
     * @return OtapiSearchItemsParameters
     */
    public function GetSearchParameters(){
        $value = isset($this->xmlData->SearchParameters) ? $this->xmlData->SearchParameters : false;
        return new OtapiSearchItemsParameters($value);
    }
    /**
     * @return string
     */
    public function GetDeleteStatus(){
        $value = isset($this->xmlData->DeleteStatus) ? (string)$this->xmlData->DeleteStatus : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}