<?php

class OtapiGetItemDescriptionResponse extends BaseOtapiType{
    /**
     * @return OtapiItemDescriptionAnswer
     */
    public function GetGetItemDescriptionResult(){
        $value = isset($this->xmlData->GetItemDescriptionResult) ? $this->xmlData->GetItemDescriptionResult : false;
        return new OtapiItemDescriptionAnswer($value);
    }
}