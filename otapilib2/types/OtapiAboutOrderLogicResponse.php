<?php

class OtapiAboutOrderLogicResponse extends BaseOtapiType{
    /**
     * @return OtapiAboutOrderLogicAnswer
     */
    public function GetAboutOrderLogicResult(){
        $value = isset($this->xmlData->AboutOrderLogicResult) ? $this->xmlData->AboutOrderLogicResult : false;
        return new OtapiAboutOrderLogicAnswer($value);
    }
}