<?php

class OtapiBatchGetItemFullInfoResponse extends BaseOtapiType{
    /**
     * @return OtapiBatchItemFullInfoAnswer
     */
    public function GetBatchGetItemFullInfoResult(){
        $value = isset($this->xmlData->BatchGetItemFullInfoResult) ? $this->xmlData->BatchGetItemFullInfoResult : false;
        return new OtapiBatchItemFullInfoAnswer($value);
    }
}