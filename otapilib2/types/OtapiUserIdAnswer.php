<?php

class OtapiUserIdAnswer extends OtapiAnswer{
    /**
     * @return OtapiUserId
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiUserId($value);
    }
}