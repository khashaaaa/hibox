<?php

class OtapiBrandInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiBrandInfo
     */
    public function GetBrandInfo(){
        $value = isset($this->xmlData->BrandInfo) ? $this->xmlData->BrandInfo : false;
        return new OtapiBrandInfo($value);
    }
}