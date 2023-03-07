<?php

class OtapiSearchDeletedCategoriesIdsResponse extends BaseOtapiType{
    /**
     * @return OtapiCategoryListAnswer
     */
    public function GetSearchDeletedCategoriesIdsResult(){
        $value = isset($this->xmlData->SearchDeletedCategoriesIdsResult) ? $this->xmlData->SearchDeletedCategoriesIdsResult : false;
        return new OtapiCategoryListAnswer($value);
    }
}