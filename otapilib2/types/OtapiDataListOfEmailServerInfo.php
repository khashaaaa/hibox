<?php

class OtapiDataListOfEmailServerInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfEmailServerInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfEmailServerInfo($value);
    }
}