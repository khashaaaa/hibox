<?php

class OtapiGetFavoriteVendorsResponse extends BaseOtapiType{
    /**
     * @return OtapiCollectionInfoAnswer
     */
    public function GetGetFavoriteVendorsResult(){
        $value = isset($this->xmlData->GetFavoriteVendorsResult) ? $this->xmlData->GetFavoriteVendorsResult : false;
        return new OtapiCollectionInfoAnswer($value);
    }
}