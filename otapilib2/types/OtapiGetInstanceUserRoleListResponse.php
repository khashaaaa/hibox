<?php

class OtapiGetInstanceUserRoleListResponse extends BaseOtapiType{
    /**
     * @return OtapiInstanceUserRoleInfoListAnswer
     */
    public function GetGetInstanceUserRoleListResult(){
        $value = isset($this->xmlData->GetInstanceUserRoleListResult) ? $this->xmlData->GetInstanceUserRoleListResult : false;
        return new OtapiInstanceUserRoleInfoListAnswer($value);
    }
}