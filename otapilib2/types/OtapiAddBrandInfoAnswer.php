<?php

class OtapiAddBrandInfoAnswer extends OtapiAnswer{
    /**
     * @return string
     */
    public function GetBrandId(){
        $value = isset($this->xmlData->BrandId) ? (string)$this->xmlData->BrandId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}