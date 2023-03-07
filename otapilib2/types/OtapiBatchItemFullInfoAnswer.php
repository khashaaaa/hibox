<?php

class OtapiBatchItemFullInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiBatchItemFullInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiBatchItemFullInfo($value);
    }
}