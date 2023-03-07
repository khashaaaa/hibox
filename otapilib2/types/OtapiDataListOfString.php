<?php

class OtapiDataListOfString extends BaseOtapiType{
    /**
     * @return OtapiArrayOfString4
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfString4($value);
    }
}