<?php

class OtapiFindCategoryItemSimpleInfoListFrameResponse extends BaseOtapiType{
    /**
     * @return OtapiItemInfoListFrameAnswer
     */
    public function GetFindCategoryItemSimpleInfoListFrameResult(){
        $value = isset($this->xmlData->FindCategoryItemSimpleInfoListFrameResult) ? $this->xmlData->FindCategoryItemSimpleInfoListFrameResult : false;
        return new OtapiItemInfoListFrameAnswer($value);
    }
}