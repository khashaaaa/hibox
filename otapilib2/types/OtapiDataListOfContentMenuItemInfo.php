<?php

class OtapiDataListOfContentMenuItemInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfContentMenuItemInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfContentMenuItemInfo($value);
    }
}