<?php

class OtapiSearchWarehouseCategoriesResponse extends BaseOtapiType{
    /**
     * @return OtapiWarehouseCategoryInfoListFrameAnswer
     */
    public function GetSearchWarehouseCategoriesResult(){
        $value = isset($this->xmlData->SearchWarehouseCategoriesResult) ? $this->xmlData->SearchWarehouseCategoriesResult : false;
        return new OtapiWarehouseCategoryInfoListFrameAnswer($value);
    }
}