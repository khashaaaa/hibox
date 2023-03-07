<?php

class OtapiImportStructureByLanguageResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetImportStructureByLanguageResult(){
        $value = isset($this->xmlData->ImportStructureByLanguageResult) ? $this->xmlData->ImportStructureByLanguageResult : false;
        return new VoidOtapiAnswer($value);
    }
}