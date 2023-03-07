<?php

class OtapiExportStructureByLanguageResponse extends BaseOtapiType{
    /**
     * @return OtapiExportStructureByLanguageAnswer
     */
    public function GetExportStructureByLanguageResult(){
        $value = isset($this->xmlData->ExportStructureByLanguageResult) ? $this->xmlData->ExportStructureByLanguageResult : false;
        return new OtapiExportStructureByLanguageAnswer($value);
    }
}