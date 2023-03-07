<?php

class OtapiBatchSimplifiedItemFullInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiBatchSimplifiedItemFullInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiBatchSimplifiedItemFullInfo($value);
    }
}