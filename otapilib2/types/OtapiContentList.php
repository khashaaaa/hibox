<?php

class OtapiContentList extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetContent(){
        return isset($this->xmlData->Content) ? new UnboundedElementsIterator(
                $this->xmlData->Content,
                array(
                    'type' => 'scalarType',
                    'name' => 'string'
                )
            ) : array();
    }
    /**
    * @return ContentTypeEnum
    */
    public function GetContentTypeAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['ContentType']) ? (string)$attributes['ContentType'] : false;
        $propertyType = 'ContentTypeEnum';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}