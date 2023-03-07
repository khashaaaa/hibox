<?php

class OtapiDataListOfEventInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfEventInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfEventInfo($value);
    }
}