<?php

class OtapiGetCategoryItemInfoListFrameResponse extends BaseOtapiType{
    /**
     * @return OtapiItemInfoListFrameAnswer
     */
    public function GetGetCategoryItemInfoListFrameResult(){
        $value = isset($this->xmlData->GetCategoryItemInfoListFrameResult) ? $this->xmlData->GetCategoryItemInfoListFrameResult : false;
        return new OtapiItemInfoListFrameAnswer($value);
    }
}