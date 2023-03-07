<?php

class OtapiUploadUrlInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiUploadUrlInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiUploadUrlInfo($value);
    }
}