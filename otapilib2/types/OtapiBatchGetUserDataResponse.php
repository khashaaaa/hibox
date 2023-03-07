<?php

class OtapiBatchGetUserDataResponse extends BaseOtapiType{
    /**
     * @return OtapiBatchUserDataAnswer
     */
    public function GetBatchGetUserDataResult(){
        $value = isset($this->xmlData->BatchGetUserDataResult) ? $this->xmlData->BatchGetUserDataResult : false;
        return new OtapiBatchUserDataAnswer($value);
    }
}