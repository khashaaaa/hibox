<?php

class OtapiOperationTypeInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfOperationTypeInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfOperationTypeInfo($value);
    }
}