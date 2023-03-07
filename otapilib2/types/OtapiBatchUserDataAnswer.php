<?php

class OtapiBatchUserDataAnswer extends OtapiAnswer{
    /**
     * @return OtapiBatchUserData
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiBatchUserData($value);
    }
}