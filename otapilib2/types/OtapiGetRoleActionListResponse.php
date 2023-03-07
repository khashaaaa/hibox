<?php

class OtapiGetRoleActionListResponse extends BaseOtapiType{
    /**
     * @return OtapiActionInfoListAnswer
     */
    public function GetGetRoleActionListResult(){
        $value = isset($this->xmlData->GetRoleActionListResult) ? $this->xmlData->GetRoleActionListResult : false;
        return new OtapiActionInfoListAnswer($value);
    }
}