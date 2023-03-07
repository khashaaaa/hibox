<?php

class OtapiDataListOfActionInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfActionInfo1
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfActionInfo1($value);
    }
}