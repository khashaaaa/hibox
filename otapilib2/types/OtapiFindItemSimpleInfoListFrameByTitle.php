<?php

class OtapiFindItemSimpleInfoListFrameByTitle extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetInstanceKey(){
        $value = isset($this->xmlData->instanceKey) ? (string)$this->xmlData->instanceKey : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetLanguage(){
        $value = isset($this->xmlData->language) ? (string)$this->xmlData->language : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetSearchParameters(){
        $value = isset($this->xmlData->searchParameters) ? (string)$this->xmlData->searchParameters : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetItemTitle(){
        $value = isset($this->xmlData->itemTitle) ? (string)$this->xmlData->itemTitle : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetFramePosition(){
        $value = isset($this->xmlData->framePosition) ? (string)$this->xmlData->framePosition : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetFrameSize(){
        $value = isset($this->xmlData->frameSize) ? (string)$this->xmlData->frameSize : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetLanguageOfQuery(){
        $value = isset($this->xmlData->languageOfQuery) ? (string)$this->xmlData->languageOfQuery : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}