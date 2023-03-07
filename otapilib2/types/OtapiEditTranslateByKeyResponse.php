<?php

class OtapiEditTranslateByKeyResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetEditTranslateByKeyResult(){
        $value = isset($this->xmlData->EditTranslateByKeyResult) ? $this->xmlData->EditTranslateByKeyResult : false;
        return new VoidOtapiAnswer($value);
    }
}