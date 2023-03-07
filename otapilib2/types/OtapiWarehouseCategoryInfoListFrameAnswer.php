<?php

class OtapiWarehouseCategoryInfoListFrameAnswer extends OtapiAnswer{
    /**
     * @return DataSubListOfOtapiWarehouseCategoryInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new DataSubListOfOtapiWarehouseCategoryInfo($value);
    }
}