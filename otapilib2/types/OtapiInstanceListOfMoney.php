<?php

class OtapiInstanceListOfMoney extends BaseOtapiType{
    /**
     * @return OtapiMoney
     */
    public function GetInternal(){
        $value = isset($this->xmlData->Internal) ? $this->xmlData->Internal : false;
        return new OtapiMoney($value);
    }
    /**
     * @return OtapiArrayOfMoney
     */
    public function GetDisplayedMoneys(){
        $value = isset($this->xmlData->DisplayedMoneys) ? $this->xmlData->DisplayedMoneys : false;
        return new OtapiArrayOfMoney($value);
    }
}