<?php

class OtapiAccountStatementAnswer extends OtapiAnswer{
    /**
     * @return OtapiAccountStatement
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiAccountStatement($value);
    }
}