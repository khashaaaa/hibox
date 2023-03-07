<?php

class OtapiBrandInfoListAnswer extends OtapiAnswer{
    /**
     * @return DataListOfOtapiBrandInfo
     */
    public function GetBrandInfoList(){
        $value = isset($this->xmlData->BrandInfoList) ? $this->xmlData->BrandInfoList : false;
        return new DataListOfOtapiBrandInfo($value);
    }
}