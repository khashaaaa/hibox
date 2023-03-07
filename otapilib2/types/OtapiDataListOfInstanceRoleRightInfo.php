<?php

class OtapiDataListOfInstanceRoleRightInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfInstanceRoleRightInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfInstanceRoleRightInfo($value);
    }
}