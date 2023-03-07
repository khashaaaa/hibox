<?php

class OtapiAuthenticateAnswer extends OtapiAnswer{
    /**
     * @return string
     */
    public function GetSessionId(){
        $value = isset($this->xmlData->SessionId) ? (string)$this->xmlData->SessionId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}