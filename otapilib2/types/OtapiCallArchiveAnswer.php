<?php

class OtapiCallArchiveAnswer extends OtapiAnswer{
    /**
     * @return OtapiCallArchive
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiCallArchive($value);
    }
}