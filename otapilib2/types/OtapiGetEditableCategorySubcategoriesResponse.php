<?php

class OtapiGetEditableCategorySubcategoriesResponse extends BaseOtapiType{
    /**
     * @return EditableOtapiCategoryListAnswer
     */
    public function GetGetEditableCategorySubcategoriesResult(){
        $value = isset($this->xmlData->GetEditableCategorySubcategoriesResult) ? $this->xmlData->GetEditableCategorySubcategoriesResult : false;
        return new EditableOtapiCategoryListAnswer($value);
    }
}