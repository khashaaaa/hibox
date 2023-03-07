<?php

class OtapiOrdersHistoryAnswer extends OtapiAnswer{
    /**
     * @return OtapiOrdersHistory
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiOrdersHistory($value);
    }
}