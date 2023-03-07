<?php

class OtapiSetDefaultPriceFormationGroupResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetSetDefaultPriceFormationGroupResult(){
        $value = isset($this->xmlData->SetDefaultPriceFormationGroupResult) ? $this->xmlData->SetDefaultPriceFormationGroupResult : false;
        return new VoidOtapiAnswer($value);
    }
}