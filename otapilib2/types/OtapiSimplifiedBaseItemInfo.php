<?php

class OtapiSimplifiedBaseItemInfo extends OtapiSimplifiedEntity{
    /**
     * @return OtapiSimplifiedValueWithIdOfBoolean
     */
    public function GetAvailability(){
        $value = isset($this->xmlData->Availability) ? $this->xmlData->Availability : false;
        return new OtapiSimplifiedValueWithIdOfBoolean($value);
    }
    /**
     * @return boolean
     */
    public function IsFiltered(){
        $value = isset($this->xmlData->IsFiltered) ? (string)$this->xmlData->IsFiltered : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiArrayOfFilterReason
     */
    public function GetFilterReasons(){
        $value = isset($this->xmlData->FilterReasons) ? $this->xmlData->FilterReasons : false;
        return new OtapiArrayOfFilterReason($value);
    }
    /**
     * @return string
     */
    public function GetTitle(){
        $value = isset($this->xmlData->Title) ? (string)$this->xmlData->Title : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiSimplifiedId
     */
    public function GetCategory(){
        $value = isset($this->xmlData->Category) ? $this->xmlData->Category : false;
        return new OtapiSimplifiedId($value);
    }
    /**
     * @return OtapiSimplifiedId
     */
    public function GetVendor(){
        $value = isset($this->xmlData->Vendor) ? $this->xmlData->Vendor : false;
        return new OtapiSimplifiedId($value);
    }
    /**
     * @return OtapiSimplifiedId
     */
    public function GetBrand(){
        $value = isset($this->xmlData->Brand) ? $this->xmlData->Brand : false;
        return new OtapiSimplifiedId($value);
    }
    /**
     * @return string
     */
    public function GetExternalItemUrl(){
        $value = isset($this->xmlData->ExternalItemUrl) ? (string)$this->xmlData->ExternalItemUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiArrayOfSimplifiedPicture
     */
    public function GetPictures(){
        $value = isset($this->xmlData->Pictures) ? $this->xmlData->Pictures : false;
        return new OtapiArrayOfSimplifiedPicture($value);
    }
    /**
     * @return OtapiArrayOfSimplifiedValueWithId
     */
    public function GetFeatures(){
        $value = isset($this->xmlData->Features) ? $this->xmlData->Features : false;
        return new OtapiArrayOfSimplifiedValueWithId($value);
    }
}