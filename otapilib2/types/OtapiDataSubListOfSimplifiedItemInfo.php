<?php

class OtapiDataSubListOfSimplifiedItemInfo extends OtapiDataListOfSimplifiedItemInfo{
    /**
     * @return int
     */
    public function GetTotalCount(){
        $value = isset($this->xmlData->TotalCount) ? (string)$this->xmlData->TotalCount : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}