<?php

class OtapiDeleteInstanceUserResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetDeleteInstanceUserResult(){
        $value = isset($this->xmlData->DeleteInstanceUserResult) ? $this->xmlData->DeleteInstanceUserResult : false;
        return new VoidOtapiAnswer($value);
    }
}