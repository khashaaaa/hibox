<?php

class OtapiFeatureInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfFeatureInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfFeatureInfo($value);
    }
}