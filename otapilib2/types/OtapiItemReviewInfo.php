<?php

class OtapiItemReviewInfo extends BaseOtapiType{
    /**
     * @return OtapiItemReviewId
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? $this->xmlData->Id : false;
        return new OtapiItemReviewId($value);
    }
    /**
     * @return OtapiUserId
     */
    public function GetUserId(){
        $value = isset($this->xmlData->UserId) ? $this->xmlData->UserId : false;
        return new OtapiUserId($value);
    }
    /**
     * @return string
     */
    public function GetItemId(){
        $value = isset($this->xmlData->ItemId) ? (string)$this->xmlData->ItemId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetConfigurationId(){
        $value = isset($this->xmlData->ConfigurationId) ? (string)$this->xmlData->ConfigurationId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiOrderId
     */
    public function GetOrderId(){
        $value = isset($this->xmlData->OrderId) ? $this->xmlData->OrderId : false;
        return new OtapiOrderId($value);
    }
    /**
     * @return OtapiOrderLineId
     */
    public function GetOrderLineId(){
        $value = isset($this->xmlData->OrderLineId) ? $this->xmlData->OrderLineId : false;
        return new OtapiOrderLineId($value);
    }
    /**
     * @return dateTime
     */
    public function GetCreatedTime(){
        $value = isset($this->xmlData->CreatedTime) ? (string)$this->xmlData->CreatedTime : false;
        $propertyType = 'dateTime';
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
     * @return boolean
     */
    public function IsApproved(){
        $value = isset($this->xmlData->IsApproved) ? (string)$this->xmlData->IsApproved : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetUserName(){
        $value = isset($this->xmlData->UserName) ? (string)$this->xmlData->UserName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetRating(){
        $value = isset($this->xmlData->Rating) ? (string)$this->xmlData->Rating : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
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
    /**
     * @return OtapiArrayOfItemReviewAnswerInfo
     */
    public function GetAnswers(){
        $value = isset($this->xmlData->Answers) ? $this->xmlData->Answers : false;
        return new OtapiArrayOfItemReviewAnswerInfo($value);
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
    /**
     * @return boolean
     */
    public function IsRewarded(){
        $value = isset($this->xmlData->IsRewarded) ? (string)$this->xmlData->IsRewarded : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}