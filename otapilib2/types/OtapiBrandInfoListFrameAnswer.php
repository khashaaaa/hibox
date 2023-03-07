<?php

class OtapiBrandInfoListFrameAnswer extends OtapiAnswer{
    /**
     * @return DataSubListOfOtapiBrandInfo
     */
    public function GetBrandInfoList(){
        $value = isset($this->xmlData->BrandInfoList) ? $this->xmlData->BrandInfoList : false;
        return new DataSubListOfOtapiBrandInfo($value);
    }
}