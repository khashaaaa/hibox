<?php

class OtapiSearchOriginalBrandsFrameResponse extends BaseOtapiType{
    /**
     * @return OtapiBrandInfoListFrameAnswer
     */
    public function GetSearchOriginalBrandsFrameResult(){
        $value = isset($this->xmlData->SearchOriginalBrandsFrameResult) ? $this->xmlData->SearchOriginalBrandsFrameResult : false;
        return new OtapiBrandInfoListFrameAnswer($value);
    }
}