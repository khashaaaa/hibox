<?php

class OtapiDataListOfPriceFormationGroupInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfPriceFormationGroupInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfPriceFormationGroupInfo($value);
    }
}