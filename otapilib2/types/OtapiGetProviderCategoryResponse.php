<?php

class OtapiGetProviderCategoryResponse extends BaseOtapiType{
    /**
     * @return OtapiCategoryAnswer
     */
    public function GetGetProviderCategoryResult(){
        $value = isset($this->xmlData->GetProviderCategoryResult) ? $this->xmlData->GetProviderCategoryResult : false;
        return new OtapiCategoryAnswer($value);
    }
}