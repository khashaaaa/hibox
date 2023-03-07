<?php

class OtapiGetTemplateRoleListResponse extends BaseOtapiType{
    /**
     * @return OtapiInstanceUserRoleInfoListAnswer
     */
    public function GetGetTemplateRoleListResult(){
        $value = isset($this->xmlData->GetTemplateRoleListResult) ? $this->xmlData->GetTemplateRoleListResult : false;
        return new OtapiInstanceUserRoleInfoListAnswer($value);
    }
}