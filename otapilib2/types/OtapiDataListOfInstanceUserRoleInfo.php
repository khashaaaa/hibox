<?php

class OtapiDataListOfInstanceUserRoleInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfInstanceUserRoleInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfInstanceUserRoleInfo($value);
    }
}