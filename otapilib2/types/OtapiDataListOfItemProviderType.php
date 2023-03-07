<?php

class OtapiDataListOfItemProviderType extends BaseOtapiType{
    /**
     * @return OtapiArrayOfItemProviderType
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfItemProviderType($value);
    }
}