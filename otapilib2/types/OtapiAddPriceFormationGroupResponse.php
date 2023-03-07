<?php

class OtapiAddPriceFormationGroupResponse extends BaseOtapiType{
    /**
     * @return OtapiPriceFormationGroupIdAnswer
     */
    public function GetAddPriceFormationGroupResult(){
        $value = isset($this->xmlData->AddPriceFormationGroupResult) ? $this->xmlData->AddPriceFormationGroupResult : false;
        return new OtapiPriceFormationGroupIdAnswer($value);
    }
}