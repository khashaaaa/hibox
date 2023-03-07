<?php

class OtapiItemReviewAnswerInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetAuthorName(){
        $value = isset($this->xmlData->AuthorName) ? (string)$this->xmlData->AuthorName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetText(){
        $value = isset($this->xmlData->Text) ? (string)$this->xmlData->Text : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetLanguage(){
        $value = isset($this->xmlData->Language) ? (string)$this->xmlData->Language : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetOriginalText(){
        $value = isset($this->xmlData->OriginalText) ? (string)$this->xmlData->OriginalText : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetOriginalLanguage(){
        $value = isset($this->xmlData->OriginalLanguage) ? (string)$this->xmlData->OriginalLanguage : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetOperatorId(){
        $value = isset($this->xmlData->OperatorId) ? (string)$this->xmlData->OperatorId : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsOperator(){
        $value = isset($this->xmlData->IsOperator) ? (string)$this->xmlData->IsOperator : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiArrayOfString3
     */
    public function GetImageUrls(){
        $value = isset($this->xmlData->ImageUrls) ? $this->xmlData->ImageUrls : false;
        return new OtapiArrayOfString3($value);
    }
    /**
     * @return OtapiArrayOfString3
     */
    public function GetImagePreviewUrls(){
        $value = isset($this->xmlData->ImagePreviewUrls) ? $this->xmlData->ImagePreviewUrls : false;
        return new OtapiArrayOfString3($value);
    }
}