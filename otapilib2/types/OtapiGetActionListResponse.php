<?php

class OtapiGetActionListResponse extends BaseOtapiType{
    /**
     * @return OtapiActionInfoListAnswer
     */
    public function GetGetActionListResult(){
        $value = isset($this->xmlData->GetActionListResult) ? $this->xmlData->GetActionListResult : false;
        return new OtapiActionInfoListAnswer($value);
    }
}