<?php

class OtapiFindBaseUserInfoListFrameResponse extends BaseOtapiType{
    /**
     * @return OtapiBaseUserInfoListFrameAnswer
     */
    public function GetFindBaseUserInfoListFrameResult(){
        $value = isset($this->xmlData->FindBaseUserInfoListFrameResult) ? $this->xmlData->FindBaseUserInfoListFrameResult : false;
        return new OtapiBaseUserInfoListFrameAnswer($value);
    }
}