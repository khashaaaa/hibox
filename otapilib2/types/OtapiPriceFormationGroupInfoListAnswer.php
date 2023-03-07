<?php

class OtapiPriceFormationGroupInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfPriceFormationGroupInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfPriceFormationGroupInfo($value);
    }
}