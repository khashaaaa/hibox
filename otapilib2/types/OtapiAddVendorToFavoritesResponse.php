<?php

class OtapiAddVendorToFavoritesResponse extends BaseOtapiType{
    /**
     * @return OtapiElementIdAnswer
     */
    public function GetAddVendorToFavoritesResult(){
        $value = isset($this->xmlData->AddVendorToFavoritesResult) ? $this->xmlData->AddVendorToFavoritesResult : false;
        return new OtapiElementIdAnswer($value);
    }
}