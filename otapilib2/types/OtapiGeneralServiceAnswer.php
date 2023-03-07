<?php

class OtapiGeneralServiceAnswer extends OtapiServiceAnswerOfGeneralErrorCode{
    /**
     * @return OtapiSubErrorCode
     */
    public function GetSubErrorCode(){
        $value = isset($this->xmlData->SubErrorCode) ? $this->xmlData->SubErrorCode : false;
        return new OtapiSubErrorCode($value);
    }
    /**
     * @return string
     */
    public function GetErrorDescriptionTemplate(){
        $value = isset($this->xmlData->ErrorDescriptionTemplate) ? (string)$this->xmlData->ErrorDescriptionTemplate : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiNamedParameters
     */
    public function GetErrorDescriptionArguments(){
        $value = isset($this->xmlData->ErrorDescriptionArguments) ? $this->xmlData->ErrorDescriptionArguments : false;
        return new OtapiNamedParameters($value);
    }
    /**
     * @return OtapiDebugInfoCollection
     */
    public function GetDebugInfo(){
        $value = isset($this->xmlData->DebugInfo) ? $this->xmlData->DebugInfo : false;
        return new OtapiDebugInfoCollection($value);
    }
}