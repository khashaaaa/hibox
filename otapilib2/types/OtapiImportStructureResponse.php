<?php

class OtapiImportStructureResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetImportStructureResult(){
        $value = isset($this->xmlData->ImportStructureResult) ? $this->xmlData->ImportStructureResult : false;
        return new VoidOtapiAnswer($value);
    }
}