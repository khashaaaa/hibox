<?php

class OtapiPostTransactionResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetPostTransactionResult(){
        $value = isset($this->xmlData->PostTransactionResult) ? $this->xmlData->PostTransactionResult : false;
        return new VoidOtapiAnswer($value);
    }
}