<?php

class OtapiTariffChangeHistoryAnswer extends OtapiAnswer{
    /**
     * @return OtapiTariffChangeHistory
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiTariffChangeHistory($value);
    }
}