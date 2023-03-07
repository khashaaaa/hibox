<?php

class OtapiGetInstanceUserListResponse extends BaseOtapiType{
    /**
     * @return OtapiInstanceUserInfoListAnswer
     */
    public function GetGetInstanceUserListResult(){
        $value = isset($this->xmlData->GetInstanceUserListResult) ? $this->xmlData->GetInstanceUserListResult : false;
        return new OtapiInstanceUserInfoListAnswer($value);
    }
}