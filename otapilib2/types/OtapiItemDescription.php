<?php

class OtapiItemDescription extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetItemDescription(){
        $value = isset($this->xmlData->ItemDescription) ? (string)$this->xmlData->ItemDescription : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}