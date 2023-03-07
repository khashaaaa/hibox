<?php

class OtapiCommonShowcaseOptionsInfo extends BaseOtapiType{
    /**
     * @return boolean
     */
    public function LimitItemsByCatalog(){
        $value = isset($this->xmlData->LimitItemsByCatalog) ? (string)$this->xmlData->LimitItemsByCatalog : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}