<?php

class OtapiGetOperatorRightTreeResponse extends BaseOtapiType{
    /**
     * @return OtapiInstanceRoleRightInfoListAnswer
     */
    public function GetGetOperatorRightTreeResult(){
        $value = isset($this->xmlData->GetOperatorRightTreeResult) ? $this->xmlData->GetOperatorRightTreeResult : false;
        return new OtapiInstanceRoleRightInfoListAnswer($value);
    }
}