<?php

class OtapiAccountStatementAdministrationAnswer extends OtapiAnswer{
    /**
     * @return OtapiAccountStatementAdministration
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiAccountStatementAdministration($value);
    }
}