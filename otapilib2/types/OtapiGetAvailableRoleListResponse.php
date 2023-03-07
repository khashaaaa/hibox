<?php

class OtapiGetAvailableRoleListResponse extends BaseOtapiType{
    /**
     * @return OtapiInstanceUserRoleInfoListAnswer
     */
    public function GetGetAvailableRoleListResult(){
        $value = isset($this->xmlData->GetAvailableRoleListResult) ? $this->xmlData->GetAvailableRoleListResult : false;
        return new OtapiInstanceUserRoleInfoListAnswer($value);
    }
}