<?php

class OtapiDetachActionsFromRoleResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetDetachActionsFromRoleResult(){
        $value = isset($this->xmlData->DetachActionsFromRoleResult) ? $this->xmlData->DetachActionsFromRoleResult : false;
        return new VoidOtapiAnswer($value);
    }
}