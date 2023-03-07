<?php

class OtapiGetTranslatedListByKeyResponse extends BaseOtapiType{
    /**
     * @return OtapiTextByLangListAnswer
     */
    public function GetGetTranslatedListByKeyResult(){
        $value = isset($this->xmlData->GetTranslatedListByKeyResult) ? $this->xmlData->GetTranslatedListByKeyResult : false;
        return new OtapiTextByLangListAnswer($value);
    }
}