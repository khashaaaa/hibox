<?php

class OtapiGetItemOriginalDescriptionResponse extends BaseOtapiType{
    /**
     * @return OtapiItemDescriptionAnswer
     */
    public function GetGetItemOriginalDescriptionResult(){
        $value = isset($this->xmlData->GetItemOriginalDescriptionResult) ? $this->xmlData->GetItemOriginalDescriptionResult : false;
        return new OtapiItemDescriptionAnswer($value);
    }
}