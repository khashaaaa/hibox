<?php

class OtapiExportingTargetInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfExportingTargetInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfExportingTargetInfo($value);
    }
}