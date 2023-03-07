<?php

class OtapiEmailServerInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiEmailServerInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiEmailServerInfo($value);
    }
}