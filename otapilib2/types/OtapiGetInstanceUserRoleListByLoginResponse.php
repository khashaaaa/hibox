<?php

class OtapiGetInstanceUserRoleListByLoginResponse extends BaseOtapiType{
    /**
     * @return OtapiInstanceUserRoleInfoListAnswer
     */
    public function GetGetInstanceUserRoleListByLoginResult(){
        $value = isset($this->xmlData->GetInstanceUserRoleListByLoginResult) ? $this->xmlData->GetInstanceUserRoleListByLoginResult : false;
        return new OtapiInstanceUserRoleInfoListAnswer($value);
    }
}