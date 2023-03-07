<?php

class OtapiGetInstanceUserActionListResponse extends BaseOtapiType{
    /**
     * @return OtapiActionInfoListAnswer
     */
    public function GetGetInstanceUserActionListResult(){
        $value = isset($this->xmlData->GetInstanceUserActionListResult) ? $this->xmlData->GetInstanceUserActionListResult : false;
        return new OtapiActionInfoListAnswer($value);
    }
}