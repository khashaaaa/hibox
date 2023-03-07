<?php

class OtapiSimplifiedItemFullInfo extends OtapiSimplifiedBaseItemInfo{
    /**
     * @return ArrayOfOtapiItemVideo
     */
    public function GetVideos(){
        $value = isset($this->xmlData->Videos) ? $this->xmlData->Videos : false;
        return new ArrayOfOtapiItemVideo($value);
    }
    /**
     * @return OtapiSimplifiedItemPropertyList
     */
    public function GetConfigurators(){
        $value = isset($this->xmlData->Configurators) ? $this->xmlData->Configurators : false;
        return new OtapiSimplifiedItemPropertyList($value);
    }
    /**
     * @return OtapiArrayOfSimplifiedItemProperty
     */
    public function GetProperties(){
        $value = isset($this->xmlData->Properties) ? $this->xmlData->Properties : false;
        return new OtapiArrayOfSimplifiedItemProperty($value);
    }
    /**
     * @return OtapiSimplifiedValueOfDecimal
     */
    public function GetWeight(){
        $value = isset($this->xmlData->Weight) ? $this->xmlData->Weight : false;
        return new OtapiSimplifiedValueOfDecimal($value);
    }
}