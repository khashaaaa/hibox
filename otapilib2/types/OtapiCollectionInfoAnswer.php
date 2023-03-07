<?php

class OtapiCollectionInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiCollectionInfo
     */
    public function GetCollectionInfo(){
        $value = isset($this->xmlData->CollectionInfo) ? $this->xmlData->CollectionInfo : false;
        return new OtapiCollectionInfo($value);
    }
}