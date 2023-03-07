<?php

class OtapiSearchStringRatingListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfString
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfString($value);
    }
}