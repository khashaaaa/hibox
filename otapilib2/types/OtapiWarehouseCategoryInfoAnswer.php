<?php

class OtapiWarehouseCategoryInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiWarehouseCategoryInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiWarehouseCategoryInfo($value);
    }
}