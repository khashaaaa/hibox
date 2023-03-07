<?php

class OtapiDeattachRightsFromRoleResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetDeattachRightsFromRoleResult(){
        $value = isset($this->xmlData->DeattachRightsFromRoleResult) ? $this->xmlData->DeattachRightsFromRoleResult : false;
        return new VoidOtapiAnswer($value);
    }
}