<?php

class OtapiClearFavoriteVendorsResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetClearFavoriteVendorsResult(){
        $value = isset($this->xmlData->ClearFavoriteVendorsResult) ? $this->xmlData->ClearFavoriteVendorsResult : false;
        return new VoidOtapiAnswer($value);
    }
}