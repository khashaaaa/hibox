<?php

class OtapiElementIdListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfElementId
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfElementId($value);
    }
}