<?php

class OtapiRemoveCategoryResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetRemoveCategoryResult(){
        $value = isset($this->xmlData->RemoveCategoryResult) ? $this->xmlData->RemoveCategoryResult : false;
        return new VoidOtapiAnswer($value);
    }
}