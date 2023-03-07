<?php

class OtapiRemoveInstanceUserFromRoleResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetRemoveInstanceUserFromRoleResult(){
        $value = isset($this->xmlData->RemoveInstanceUserFromRoleResult) ? $this->xmlData->RemoveInstanceUserFromRoleResult : false;
        return new VoidOtapiAnswer($value);
    }
}