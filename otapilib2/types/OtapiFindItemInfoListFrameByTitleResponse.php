<?php

class OtapiFindItemInfoListFrameByTitleResponse extends BaseOtapiType{
    /**
     * @return OtapiItemInfoListFrameAnswer
     */
    public function GetFindItemInfoListFrameByTitleResult(){
        $value = isset($this->xmlData->FindItemInfoListFrameByTitleResult) ? $this->xmlData->FindItemInfoListFrameByTitleResult : false;
        return new OtapiItemInfoListFrameAnswer($value);
    }
}