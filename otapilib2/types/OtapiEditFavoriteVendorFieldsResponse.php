<?php

class OtapiEditFavoriteVendorFieldsResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetEditFavoriteVendorFieldsResult(){
        $value = isset($this->xmlData->EditFavoriteVendorFieldsResult) ? $this->xmlData->EditFavoriteVendorFieldsResult : false;
        return new VoidOtapiAnswer($value);
    }
}