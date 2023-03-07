<?php

class OtapiBaseUserInfoListFrameWithSummaryAnswer extends OtapiAnswer{
    /**
     * @return OtapiBaseUserInfoListFrameWithSummary
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiBaseUserInfoListFrameWithSummary($value);
    }
}