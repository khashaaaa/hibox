<?php

class OtapiRemoveVendorFromFavoritesResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetRemoveVendorFromFavoritesResult(){
        $value = isset($this->xmlData->RemoveVendorFromFavoritesResult) ? $this->xmlData->RemoveVendorFromFavoritesResult : false;
        return new VoidOtapiAnswer($value);
    }
}