<?php

class OtapiExportStructureResponse extends BaseOtapiType{
    /**
     * @return OtapiExportStructureAnswer
     */
    public function GetExportStructureResult(){
        $value = isset($this->xmlData->ExportStructureResult) ? $this->xmlData->ExportStructureResult : false;
        return new OtapiExportStructureAnswer($value);
    }
}