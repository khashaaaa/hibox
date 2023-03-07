<?php

class OtapiItemDescriptionAnswer extends OtapiAnswer{
    /**
     * @return OtapiItemDescription
     */
    public function GetOtapiItemDescription(){
        $value = isset($this->xmlData->OtapiItemDescription) ? $this->xmlData->OtapiItemDescription : false;
        return new OtapiItemDescription($value);
    }
}