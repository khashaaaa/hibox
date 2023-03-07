<?php

class OtapiCategoryAnswer extends OtapiAnswer{
    /**
     * @return OtapiCategory
     */
    public function GetOtapiCategory(){
        $value = isset($this->xmlData->OtapiCategory) ? $this->xmlData->OtapiCategory : false;
        return new OtapiCategory($value);
    }
}