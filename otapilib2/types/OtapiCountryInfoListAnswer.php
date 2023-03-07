<?php

class OtapiCountryInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfCountryInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfCountryInfo($value);
    }
}