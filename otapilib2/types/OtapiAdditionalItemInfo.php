<?php

class OtapiAdditionalItemInfo extends BaseOtapiType{
    /**
     * @return OtapiMetaInfo
     */
    public function GetMetaInfo(){
        $value = isset($this->xmlData->MetaInfo) ? $this->xmlData->MetaInfo : false;
        return new OtapiMetaInfo($value);
    }
    /**
     * @return string
     */
    public function GetManualWeight(){
        $value = isset($this->xmlData->ManualWeight) ? (string)$this->xmlData->ManualWeight : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetApproxWeight(){
        $value = isset($this->xmlData->ApproxWeight) ? (string)$this->xmlData->ApproxWeight : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetRealWeight(){
        $value = isset($this->xmlData->RealWeight) ? (string)$this->xmlData->RealWeight : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}