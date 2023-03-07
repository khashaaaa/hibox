<?php

class OtapiBaseExporterIntegrationSettings extends BaseOtapiType{
    /**
     * @return boolean
     */
    public function BackgroundExporting(){
        $value = isset($this->xmlData->BackgroundExporting) ? (string)$this->xmlData->BackgroundExporting : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiValueListOfString
     */
    public function GetCategoryIds(){
        $value = isset($this->xmlData->CategoryIds) ? $this->xmlData->CategoryIds : false;
        return new OtapiValueListOfString($value);
    }
}