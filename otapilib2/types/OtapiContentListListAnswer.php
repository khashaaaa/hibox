<?php

class OtapiContentListListAnswer extends OtapiAnswer{
    /**
     * @return OtapiArrayOfContentList
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiArrayOfContentList($value);
    }
}