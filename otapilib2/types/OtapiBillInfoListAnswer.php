<?php

class OtapiBillInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfBillInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfBillInfo($value);
    }
}