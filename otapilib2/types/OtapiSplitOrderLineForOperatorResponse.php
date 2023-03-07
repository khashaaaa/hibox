<?php

class OtapiSplitOrderLineForOperatorResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetSplitOrderLineForOperatorResult(){
        $value = isset($this->xmlData->SplitOrderLineForOperatorResult) ? $this->xmlData->SplitOrderLineForOperatorResult : false;
        return new VoidOtapiAnswer($value);
    }
}