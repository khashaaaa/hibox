<?php

class OtapiDiscountInfo extends BaseOtapiType{
    /**
     * @return decimal
     */
    public function GetPercent(){
        $value = isset($this->xmlData->Percent) ? (string)$this->xmlData->Percent : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}