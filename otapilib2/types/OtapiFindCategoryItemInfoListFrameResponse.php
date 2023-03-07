<?php

class OtapiFindCategoryItemInfoListFrameResponse extends BaseOtapiType{
    /**
     * @return OtapiItemInfoListFrameAnswer
     */
    public function GetFindCategoryItemInfoListFrameResult(){
        $value = isset($this->xmlData->FindCategoryItemInfoListFrameResult) ? $this->xmlData->FindCategoryItemInfoListFrameResult : false;
        return new OtapiItemInfoListFrameAnswer($value);
    }
}