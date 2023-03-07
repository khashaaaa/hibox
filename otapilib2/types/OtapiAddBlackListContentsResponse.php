<?php

class OtapiAddBlackListContentsResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetAddBlackListContentsResult(){
        $value = isset($this->xmlData->AddBlackListContentsResult) ? $this->xmlData->AddBlackListContentsResult : false;
        return new VoidOtapiAnswer($value);
    }
}