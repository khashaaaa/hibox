<?php

class OtapiFindItemSimpleInfoListFrameByTitleResponse extends BaseOtapiType{
    /**
     * @return OtapiItemInfoListFrameAnswer
     */
    public function GetFindItemSimpleInfoListFrameByTitleResult(){
        $value = isset($this->xmlData->FindItemSimpleInfoListFrameByTitleResult) ? $this->xmlData->FindItemSimpleInfoListFrameByTitleResult : false;
        return new OtapiItemInfoListFrameAnswer($value);
    }
}