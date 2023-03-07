<?php

class OtapiGetBrandInfoListFrameResponse extends BaseOtapiType{
    /**
     * @return OtapiBrandInfoListFrameAnswer
     */
    public function GetGetBrandInfoListFrameResult(){
        $value = isset($this->xmlData->GetBrandInfoListFrameResult) ? $this->xmlData->GetBrandInfoListFrameResult : false;
        return new OtapiBrandInfoListFrameAnswer($value);
    }
}