<?php
OTBase::import('system.lib.service.for_generator_2_0.BaseElement');
OTBase::import('system.lib.service.for_generator_2_0.complexType.OtapiCategory');

/**
 * Описывает струтуру данных о  категориях с сервисов
 * Class GetCategoryInfoResponse
 */
class GetCategoryInfoResponse extends BaseElement {
    /**
     * @return bool|OtapiCategory
     */
    public function GetOtapiCategory(){
        return isset($this->xmlData->OtapiCategory) ? new OtapiCategory($this->xmlData->OtapiCategory) : false;
    }
}