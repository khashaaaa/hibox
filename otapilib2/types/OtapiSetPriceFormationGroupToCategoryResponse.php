<?php

class OtapiSetPriceFormationGroupToCategoryResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetSetPriceFormationGroupToCategoryResult(){
        $value = isset($this->xmlData->SetPriceFormationGroupToCategoryResult) ? $this->xmlData->SetPriceFormationGroupToCategoryResult : false;
        return new VoidOtapiAnswer($value);
    }
}