<?php

class OtapiBackgroundActivityFullInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiBackgroundActivityFullInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiBackgroundActivityFullInfo($value);
    }
}