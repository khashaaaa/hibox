<?php

class OtapiDeleteInstanceRoleResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetDeleteInstanceRoleResult(){
        $value = isset($this->xmlData->DeleteInstanceRoleResult) ? $this->xmlData->DeleteInstanceRoleResult : false;
        return new VoidOtapiAnswer($value);
    }
}