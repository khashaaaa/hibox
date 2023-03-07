<?php

class OtapiGetRightTreeResponse extends BaseOtapiType{
    /**
     * @return OtapiInstanceRoleRightInfoListAnswer
     */
    public function GetGetRightTreeResult(){
        $value = isset($this->xmlData->GetRightTreeResult) ? $this->xmlData->GetRightTreeResult : false;
        return new OtapiInstanceRoleRightInfoListAnswer($value);
    }
}