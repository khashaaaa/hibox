<?php

class OtapiGetCategorySearchPropertiesResponse extends BaseOtapiType{
    /**
     * @return OtapiSearchPropertyListAnswer
     */
    public function GetGetCategorySearchPropertiesResult(){
        $value = isset($this->xmlData->GetCategorySearchPropertiesResult) ? $this->xmlData->GetCategorySearchPropertiesResult : false;
        return new OtapiSearchPropertyListAnswer($value);
    }
}