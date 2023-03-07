<?php

class OtapiPriceFormationGroupIdAnswer extends OtapiAnswer{
    /**
     * @return OtapiPriceFormationGroupId
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiPriceFormationGroupId($value);
    }
}