<?php

class OtapiDataListOfFeatureInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfFeatureInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfFeatureInfo($value);
    }
}