<?php
OTBase::import('system.lib.service.for_generator_2_0.BaseElement');
OTBase::import('system.lib.service.for_generator_2_0.complexType.DataListOfEditableOtapiCategory');

/**
 * Описывает струтуру данных о  категориях с сервисов
 * Class GetEditableCategorySubcategoriesResponse
 */
class GetEditableCategorySubcategoriesResponse extends BaseElement {
    /**
     * @return bool|DataListOfEditableOtapiCategory
     */
    public function GetResult(){
        return isset($this->xmlData->Result) ? new DataListOfEditableOtapiCategory($this->xmlData->Result) : false;
    }
}