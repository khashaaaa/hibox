<?php

class OtapiPriceFormationGroupInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiPriceFormationGroupInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiPriceFormationGroupInfo($value);
    }
}