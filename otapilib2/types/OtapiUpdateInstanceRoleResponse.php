<?php

class OtapiUpdateInstanceRoleResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetUpdateInstanceRoleResult(){
        $value = isset($this->xmlData->UpdateInstanceRoleResult) ? $this->xmlData->UpdateInstanceRoleResult : false;
        return new VoidOtapiAnswer($value);
    }
}