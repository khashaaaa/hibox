<?php

class OtapiImportCatalogResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetImportCatalogResult(){
        $value = isset($this->xmlData->ImportCatalogResult) ? $this->xmlData->ImportCatalogResult : false;
        return new VoidOtapiAnswer($value);
    }
}