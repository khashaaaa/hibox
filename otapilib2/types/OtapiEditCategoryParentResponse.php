<?php

class OtapiEditCategoryParentResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetEditCategoryParentResult(){
        $value = isset($this->xmlData->EditCategoryParentResult) ? $this->xmlData->EditCategoryParentResult : false;
        return new VoidOtapiAnswer($value);
    }
}