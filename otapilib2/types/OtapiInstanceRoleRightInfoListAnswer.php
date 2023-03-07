<?php

class OtapiInstanceRoleRightInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfInstanceRoleRightInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfInstanceRoleRightInfo($value);
    }
}