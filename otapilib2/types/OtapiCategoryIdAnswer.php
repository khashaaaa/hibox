<?php

class OtapiCategoryIdAnswer extends OtapiAnswer{
    /**
     * @return OtapiCategoryId
     */
    public function GetCategoryId(){
        $value = isset($this->xmlData->CategoryId) ? $this->xmlData->CategoryId : false;
        return new OtapiCategoryId($value);
    }
}