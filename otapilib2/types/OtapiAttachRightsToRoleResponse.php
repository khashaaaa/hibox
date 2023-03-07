<?php

class OtapiAttachRightsToRoleResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetAttachRightsToRoleResult(){
        $value = isset($this->xmlData->AttachRightsToRoleResult) ? $this->xmlData->AttachRightsToRoleResult : false;
        return new VoidOtapiAnswer($value);
    }
}