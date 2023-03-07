<?php

class OtapiInstanceUserRoleInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfInstanceUserRoleInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfInstanceUserRoleInfo($value);
    }
}