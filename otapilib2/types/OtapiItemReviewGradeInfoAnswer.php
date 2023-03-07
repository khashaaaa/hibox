<?php

class OtapiItemReviewGradeInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiItemReviewGradeInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiItemReviewGradeInfo($value);
    }
}