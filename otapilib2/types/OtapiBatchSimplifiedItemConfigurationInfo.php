<?php

class OtapiBatchSimplifiedItemConfigurationInfo extends OtapiBatchResultOfGeneralErrorCode{
    /**
     * @return OtapiSimplifiedId
     */
    public function GetCurrency(){
        $value = isset($this->xmlData->Currency) ? $this->xmlData->Currency : false;
        return new OtapiSimplifiedId($value);
    }
    /**
     * @return OtapiSimplifiedItemConfigurationInfo
     */
    public function GetConfiguration(){
        $value = isset($this->xmlData->Configuration) ? $this->xmlData->Configuration : false;
        return new OtapiSimplifiedItemConfigurationInfo($value);
    }
    /**
     * @return OtapiArrayOfSimplifiedAdditionalPrice
     */
    public function GetAdditionalPrices(){
        $value = isset($this->xmlData->AdditionalPrices) ? $this->xmlData->AdditionalPrices : false;
        return new OtapiArrayOfSimplifiedAdditionalPrice($value);
    }
    /**
     * @return OtapiArrayOfSimplifiedDeliveryMode
     */
    public function GetDeliveryModes(){
        $value = isset($this->xmlData->DeliveryModes) ? $this->xmlData->DeliveryModes : false;
        return new OtapiArrayOfSimplifiedDeliveryMode($value);
    }
}