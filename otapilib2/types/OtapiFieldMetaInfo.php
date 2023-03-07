<?php

class OtapiFieldMetaInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfFieldMetaConstraintDescription
     */
    public function GetConstraintDescriptions(){
        $value = isset($this->xmlData->ConstraintDescriptions) ? $this->xmlData->ConstraintDescriptions : false;
        return new OtapiArrayOfFieldMetaConstraintDescription($value);
    }
    /**
     * @return OtapiFieldMetaInfo[]
     */
    public function GetField(){
        return isset($this->xmlData->Field) ? new UnboundedElementsIterator(
                $this->xmlData->Field,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiFieldMetaInfo'
                )
            ) : array();
    }
    /**
     * @return OtapiArrayOfFieldValueMetaInfo
     */
    public function GetValues(){
        $value = isset($this->xmlData->Values) ? $this->xmlData->Values : false;
        return new OtapiArrayOfFieldValueMetaInfo($value);
    }
    /**
     * @return OtapiArrayOfFieldPlaceholderMetaInfo
     */
    public function GetPlaceholders(){
        $value = isset($this->xmlData->Placeholders) ? $this->xmlData->Placeholders : false;
        return new OtapiArrayOfFieldPlaceholderMetaInfo($value);
    }
    /**
    * @return string
    */
    public function GetNameAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Name']) ? (string)$attributes['Name'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return string
    */
    public function GetDisplayNameAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['DisplayName']) ? (string)$attributes['DisplayName'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return string
    */
    public function GetDescriptionAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Description']) ? (string)$attributes['Description'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return string
    */
    public function GetUnitAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Unit']) ? (string)$attributes['Unit'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return boolean
    */
    public function IsReadOnlyAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['IsReadOnly']) ? (string)$attributes['IsReadOnly'] : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return boolean
    */
    public function RequireReloadAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['RequireReload']) ? (string)$attributes['RequireReload'] : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return FieldMetaType
    */
    public function GetTypeAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Type']) ? (string)$attributes['Type'] : false;
        $propertyType = 'FieldMetaType';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return int
    */
    public function GetMinValueAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['MinValue']) ? (string)$attributes['MinValue'] : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return int
    */
    public function GetMaxValueAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['MaxValue']) ? (string)$attributes['MaxValue'] : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return DateFieldPrecision
    */
    public function GetDatePrecisionAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['DatePrecision']) ? (string)$attributes['DatePrecision'] : false;
        $propertyType = 'DateFieldPrecision';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}