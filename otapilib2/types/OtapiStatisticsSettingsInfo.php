<?php

class OtapiStatisticsSettingsInfo extends BaseOtapiType{
    /**
    * @return boolean
    */
    public function IsNeedCollectAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['IsNeedCollect']) ? (string)$attributes['IsNeedCollect'] : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}