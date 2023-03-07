<?php

class OtapiGetUserProfileInfoListResponse extends BaseOtapiType{
    /**
     * @return OtapiUserProfileInfoListAnswer
     */
    public function GetGetUserProfileInfoListResult(){
        $value = isset($this->xmlData->GetUserProfileInfoListResult) ? $this->xmlData->GetUserProfileInfoListResult : false;
        return new OtapiUserProfileInfoListAnswer($value);
    }
}