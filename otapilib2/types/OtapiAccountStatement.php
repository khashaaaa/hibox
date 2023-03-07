<?php

class OtapiAccountStatement extends BaseOtapiType{
    /**
     * @return OtapiStatementHeader
     */
    public function GetHeader(){
        $value = isset($this->xmlData->Header) ? $this->xmlData->Header : false;
        return new OtapiStatementHeader($value);
    }
    /**
     * @return OtapiArrayOfTransInfo
     */
    public function GetTransList(){
        $value = isset($this->xmlData->TransList) ? $this->xmlData->TransList : false;
        return new OtapiArrayOfTransInfo($value);
    }
}