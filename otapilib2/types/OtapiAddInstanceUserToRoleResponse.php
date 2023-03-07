<?php

class OtapiAddInstanceUserToRoleResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetAddInstanceUserToRoleResult(){
        $value = isset($this->xmlData->AddInstanceUserToRoleResult) ? $this->xmlData->AddInstanceUserToRoleResult : false;
        return new VoidOtapiAnswer($value);
    }
}