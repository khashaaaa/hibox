<?php

class OtapiCreateInstanceUserResponse extends BaseOtapiType{
    /**
     * @return OtapiCreateInstanceUserAnswer
     */
    public function GetCreateInstanceUserResult(){
        $value = isset($this->xmlData->CreateInstanceUserResult) ? $this->xmlData->CreateInstanceUserResult : false;
        return new OtapiCreateInstanceUserAnswer($value);
    }
}