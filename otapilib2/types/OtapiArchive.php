<?php

class OtapiArchive extends BaseOtapiType{
    /**
     * @return OtapiArrayOfArchiveRecord
     */
    public function GetRecords(){
        $value = isset($this->xmlData->Records) ? $this->xmlData->Records : false;
        return new OtapiArrayOfArchiveRecord($value);
    }
    /**
     * @return long
     */
    public function GetSumCall(){
        $value = isset($this->xmlData->SumCall) ? (string)$this->xmlData->SumCall : false;
        $propertyType = 'long';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}