<?php

class OtapiCatalogPackageAnswer extends OtapiAnswer{
    /**
     * @return OtapiCatalogPackage
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiCatalogPackage($value);
    }
}