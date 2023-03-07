<?php

class OtapiAdditionalPriceCollectionInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfAdditionalPriceInfo
     */
    public function GetElements(){
        $value = isset($this->xmlData->Elements) ? $this->xmlData->Elements : false;
        return new OtapiArrayOfAdditionalPriceInfo($value);
    }
}