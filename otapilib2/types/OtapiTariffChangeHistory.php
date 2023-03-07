<?php

class OtapiTariffChangeHistory extends BaseOtapiType{
    /**
     * @return OtapiArrayOfTariffHistoryElement
     */
    public function GetElements(){
        $value = isset($this->xmlData->Elements) ? $this->xmlData->Elements : false;
        return new OtapiArrayOfTariffHistoryElement($value);
    }
}