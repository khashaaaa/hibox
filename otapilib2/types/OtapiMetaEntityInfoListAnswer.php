<?php

class OtapiMetaEntityInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfMetaEntityInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfMetaEntityInfo($value);
    }
}