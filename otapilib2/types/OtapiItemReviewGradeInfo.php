<?php

class OtapiItemReviewGradeInfo extends BaseOtapiType{
    /**
     * @return int
     */
    public function GetPositiveGrade(){
        $value = isset($this->xmlData->PositiveGrade) ? (string)$this->xmlData->PositiveGrade : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetNegativeGrade(){
        $value = isset($this->xmlData->NegativeGrade) ? (string)$this->xmlData->NegativeGrade : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}