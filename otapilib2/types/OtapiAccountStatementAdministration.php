<?php

class OtapiAccountStatementAdministration extends BaseOtapiType{
    /**
     * @return OtapiStatementHeaderAdministration
     */
    public function GetHeader(){
        $value = isset($this->xmlData->Header) ? $this->xmlData->Header : false;
        return new OtapiStatementHeaderAdministration($value);
    }
    /**
     * @return OtapiArrayOfTransactionInfoAdministration1
     */
    public function GetTransList(){
        $value = isset($this->xmlData->TransList) ? $this->xmlData->TransList : false;
        return new OtapiArrayOfTransactionInfoAdministration1($value);
    }
}