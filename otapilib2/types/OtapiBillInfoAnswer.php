<?php

class OtapiBillInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiBillInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiBillInfo($value);
    }
}