<?php

class OtapiGetCategoryItemSimpleInfoListFrameResponse extends BaseOtapiType{
    /**
     * @return OtapiItemInfoListFrameAnswer
     */
    public function GetGetCategoryItemSimpleInfoListFrameResult(){
        $value = isset($this->xmlData->GetCategoryItemSimpleInfoListFrameResult) ? $this->xmlData->GetCategoryItemSimpleInfoListFrameResult : false;
        return new OtapiItemInfoListFrameAnswer($value);
    }
}