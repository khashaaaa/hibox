<?php

class OtapiGetItemRootPathResponse extends BaseOtapiType{
    /**
     * @return OtapiCategoryListAnswer
     */
    public function GetGetItemRootPathResult(){
        $value = isset($this->xmlData->GetItemRootPathResult) ? $this->xmlData->GetItemRootPathResult : false;
        return new OtapiCategoryListAnswer($value);
    }
}