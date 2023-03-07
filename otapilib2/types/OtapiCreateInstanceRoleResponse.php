<?php

class OtapiCreateInstanceRoleResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetCreateInstanceRoleResult(){
        $value = isset($this->xmlData->CreateInstanceRoleResult) ? $this->xmlData->CreateInstanceRoleResult : false;
        return new VoidOtapiAnswer($value);
    }
}