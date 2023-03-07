<?php

class OtapiHideDeletedCategoriesWithoutChildrenResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetHideDeletedCategoriesWithoutChildrenResult(){
        $value = isset($this->xmlData->HideDeletedCategoriesWithoutChildrenResult) ? $this->xmlData->HideDeletedCategoriesWithoutChildrenResult : false;
        return new VoidOtapiAnswer($value);
    }
}