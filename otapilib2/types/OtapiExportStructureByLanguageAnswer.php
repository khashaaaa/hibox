<?php

class OtapiExportStructureByLanguageAnswer extends OtapiAnswer{
    /**
     * @return string
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? (string)$this->xmlData->Content : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}