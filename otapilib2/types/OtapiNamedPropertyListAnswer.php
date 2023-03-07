<?php

class OtapiNamedPropertyListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfNamedProperty
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfNamedProperty($value);
    }
}