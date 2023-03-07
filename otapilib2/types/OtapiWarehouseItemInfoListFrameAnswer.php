<?php

class OtapiWarehouseItemInfoListFrameAnswer extends OtapiAnswer{
    /**
     * @return DataSubListOfOtapiWarehouseItemInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new DataSubListOfOtapiWarehouseItemInfo($value);
    }
}