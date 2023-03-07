<?php

class OtapiSearchUsersWithSummaryResponse extends BaseOtapiType{
    /**
     * @return OtapiBaseUserInfoListFrameWithSummaryAnswer
     */
    public function GetSearchUsersWithSummaryResult(){
        $value = isset($this->xmlData->SearchUsersWithSummaryResult) ? $this->xmlData->SearchUsersWithSummaryResult : false;
        return new OtapiBaseUserInfoListFrameWithSummaryAnswer($value);
    }
}