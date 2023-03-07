<?php

class OtapiCreateInstanceRoleFromTemplateResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetCreateInstanceRoleFromTemplateResult(){
        $value = isset($this->xmlData->CreateInstanceRoleFromTemplateResult) ? $this->xmlData->CreateInstanceRoleFromTemplateResult : false;
        return new VoidOtapiAnswer($value);
    }
}