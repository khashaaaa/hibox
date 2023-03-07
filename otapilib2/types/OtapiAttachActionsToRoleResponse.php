<?php

class OtapiAttachActionsToRoleResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetAttachActionsToRoleResult(){
        $value = isset($this->xmlData->AttachActionsToRoleResult) ? $this->xmlData->AttachActionsToRoleResult : false;
        return new VoidOtapiAnswer($value);
    }
}