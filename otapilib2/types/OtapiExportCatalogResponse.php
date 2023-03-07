<?php

class OtapiExportCatalogResponse extends BaseOtapiType{
    /**
     * @return OtapiCatalogPackageAnswer
     */
    public function GetExportCatalogResult(){
        $value = isset($this->xmlData->ExportCatalogResult) ? $this->xmlData->ExportCatalogResult : false;
        return new OtapiCatalogPackageAnswer($value);
    }
}