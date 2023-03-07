<?php

class OtapiItemReviewSettingsAnswer extends OtapiAnswer{
    /**
     * @return OtapiItemReviewSettings
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiItemReviewSettings($value);
    }
}