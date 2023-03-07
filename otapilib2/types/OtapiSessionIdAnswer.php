<?php

class OtapiSessionIdAnswer extends OtapiAnswer{
    /**
     * @return OtapiSessionId
     */
    public function GetSessionId(){
        $value = isset($this->xmlData->SessionId) ? $this->xmlData->SessionId : false;
        return new OtapiSessionId($value);
    }
}