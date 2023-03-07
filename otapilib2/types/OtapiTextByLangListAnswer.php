<?php

class OtapiTextByLangListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfTextByLang
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfTextByLang($value);
    }
}