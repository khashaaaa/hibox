<?php

class OtapiEditBlackListContentsResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetEditBlackListContentsResult(){
        $value = isset($this->xmlData->EditBlackListContentsResult) ? $this->xmlData->EditBlackListContentsResult : false;
        return new VoidOtapiAnswer($value);
    }
}