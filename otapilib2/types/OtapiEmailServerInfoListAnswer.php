<?php

class OtapiEmailServerInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfEmailServerInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfEmailServerInfo($value);
    }
}