<?php

class OtapiSearchWarehouseItemsResponse extends BaseOtapiType{
    /**
     * @return OtapiWarehouseItemInfoListFrameAnswer
     */
    public function GetSearchWarehouseItemsResult(){
        $value = isset($this->xmlData->SearchWarehouseItemsResult) ? $this->xmlData->SearchWarehouseItemsResult : false;
        return new OtapiWarehouseItemInfoListFrameAnswer($value);
    }
}