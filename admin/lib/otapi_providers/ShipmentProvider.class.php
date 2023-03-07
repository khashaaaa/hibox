<?php

class ShipmentProvider {
    /**
     * @var OTAPIlib
     */
    private $otapilib;

    public function __construct($otapilib)
    {
        $this->otapilib = $otapilib;
    }

    public function GetDelivery()
    {
        $result = $this->otapilib->GetExternalDeliveryTypeList(Session::get('sid'));
        return $result;
    }

    public function GetDeliveryById($id)
    {
        $result = $this->otapilib->GetExternalDeliveryType(Session::get('sid'), $id);
        return $result;
    }

    public function GetDeliveriesById()
    {
        $delivery = $this->otapilib->GetExternalDeliveryTypeList(Session::get('sid'));
        $result = array();
        foreach ($delivery as $item) {
            $result[$item['id']] = $item;
        }
        return $result;
    }

    public function GetRates()
    {
        $rates = $this->otapilib->GetExternalDeliveryRateList(Session::get('sid'));
        $result = array();
        if ($rates) {
            foreach ($rates as $rate) {
                $result[$rate['externaldeliverytypeid']][] = $rate;
            }
        }
        return $result;
    }

    public function GetRateById($id)
    {
        $rate = $this->otapilib->GetExternalDeliveryRate(Session::get('sid'), $id);
        return $rate;
    }

    public function GetShowCase()
    {
        $rate = $this->otapilib->GetShowcase(Session::get('sid'));
        return $rate;
    }

    public function GetCountries()
    {
        $countries = $this->otapilib->GetCountryInfoList();

        $all_countries = array();
        if ($countries) {
            foreach ($countries as $country) {
                $all_countries[$country['id']] = $country['name'];
            }
        }
        return $all_countries;
    }

    public function GetCountriesWithoutRate($delivery, $alreadyUsed = array())
    {
        $usedCountries = array();

        $rates = $this->GetRates();

        if (isset($rates[$delivery['id']])) {
            foreach ($rates[$delivery['id']] as $rate) {
                $code = $rate['countrycode'];
                if (! in_array($code, $usedCountries) &&
                    ! in_array($code, $alreadyUsed)) {
                    $usedCountries[] = $code;
                }
            }
        }


        $countries = $this->otapilib->GetCountryInfoList('', Session::getActiveAdminLang());

        $notUsedCountries = array();

        foreach ($countries as $country) {
            if (! in_array($country['id'], $usedCountries)) {
                $notUsedCountries[$country['id']] = $country['name'];
            }
        }

        return $notUsedCountries;
    }

    public function SaveRate($request)
    {
        $xml = $this->_generateRateXml($request);
        $params = $request->getValue('tariff');

        if (isset($params['id'])) {
            $result = $this->otapilib
                            ->EditExternalDeliveryRate(Session::get('sid'), $xml);
        } else {
            $result = $this->otapilib
                            ->CreateExternalDeliveryRate(Session::get('sid'), $xml);
        }

        return $result;
    }

    public function SaveDelivery($params)
    {
        $xml = $this->_generateDeliveryXml($params);

        if (isset($params['id'])) {
            $result = $this->otapilib
                            ->EditExternalDeliveryType(Session::get('sid'), $xml);
        } else {
            $result = $this->otapilib
                            ->CreateExternalDeliveryType(Session::get('sid'), $xml);
        }

        return $result;
    }

    public function SaveCase($request)
    {
        $settings = $this->generateSaveShipmentXml($request);
        return $this->otapilib->SetShowcaseSettings(Session::get('sid'), $settings);
    }

    /**
     * @param RequestWrapper $request
     * @return string
     */
    public function generateSaveShipmentXml ($request)
    {

        $settings = new SimpleXMLElement('<Settings></Settings>');

        if ($request->getValue('DeliveryTypes') && is_array($request->getValue('DeliveryTypes'))){
            $deliveryTypes = $settings->addChild('DeliveryTypes');
            foreach ($request->getValue('DeliveryTypes') as $del) {
                $deliveryTypes->addChild('DeliveryTypes', htmlspecialchars($del));
            }
        }

        $arrayShowcaseSettings = array(
            'IsAuctionTypeItemSellAllowed',
            'IsNotDeliverableItemSellAllowed',
            'IsSecondhandItemSellAllowed',
            'IsInStockItemSellAllowed',
            'IsNotSelectorSellAllowed',
            'IsFilteredItemsSellAllowed',
            'IsUnknownQuantityItemSellAllowed',
            'LimitItemsByCatalog'
        );
        foreach ($arrayShowcaseSettings as $setting) {
            if ($request->valueExists($setting)){
                $settings->addChild($setting, $request->getValue($setting));
            }
        }

        $settingDom = dom_import_simplexml($settings);
        return $settingDom->ownerDocument->saveXML($settingDom->ownerDocument->documentElement);
    }

    public function DeleteDelivery($request)
    {
        if ($request->getValue('id')) {
            $result = $this->otapilib
                            ->RemoveExternalDeliveryType(Session::get('sid'), $request->getValue('id'));
        }
        return $result;
    }

    public function DeleteRate($request)
    {
        if ($request->getValue('id')) {
            $result = $this->otapilib
                            ->RemoveExternalDeliveryRate(Session::get('sid'), $request->getValue('id'));
        }
        return $result;
    }

    public function GenerateFormula($params)
    {
        //Генерируем
        $end_formula = '';
        $weight = htmlspecialchars('$weight');
        $start = htmlspecialchars('$start');
        $step = htmlspecialchars('$step');
        //Если ограничения веса заданы
        if (($params['max_weight'] != '') or ($params['min_weight'] != '')) {
            if ($params['max_weight'] == '')  $params['max_weight'] = 999;
            if ($params['min_weight'] == '')  $params['min_weight'] = 0;
            $end_formula .= "(".$weight." > ".$params['max_weight'].") || (".$weight." < ".$params['min_weight'].") ? 0 : ";
        }
        //($weight > 20) && ($weight <= 1)) ? 0 : $start + (ceil ($weight / 10) - 1) * $step
        if (! empty($params['min_price_delivery']))
            $end_formula .= $start;
        if (! empty($params['step_weight'])) {
            if (! empty($params['min_price_delivery'])) {
                $end_formula .= '+ ';
            }
            $end_formula .= '( ';
            if (! empty($params['rounding']))
                $end_formula .= ' ceil ';
            $end_formula .= '( '.$weight.' ';
            if (! empty($params['step_weight']))
                $end_formula .= '/ '.$params['step_weight'];
            $end_formula .= ' )';
            if (! empty($params['min_price_delivery']))
                $end_formula .= ' - 1';
            $end_formula .= ' )';
            if (! empty($params['step_weight']))
                $end_formula .= " * ".$step;
        }
        return $end_formula;
    }

    private function _generateDeliveryXml($params)
    {
        $xmlParams = new SimpleXMLElement('<ExternalDeliveryType></ExternalDeliveryType>');

        if (isset($params['save_custom_formula'])) {
            $formula = $params['complex_formula'];
        } else {
            $formula = self::GenerateFormula($params);
        }

        if (! empty($params['id'])) $xmlParams->addChild('Id', $params['id']);
        if (! empty($params['name'])) $xmlParams->addChild('Name', htmlspecialchars($params['name']));
        if (isset($params['description'])) $xmlParams->addChild('Description', htmlspecialchars($params['description']));
        if (strlen($formula)) $xmlParams->addChild('Formula', htmlspecialchars($formula));
        if (! empty($params['currencycode'])) $xmlParams->addChild('CurrencyCode', $params['currencycode']);
        if (! empty($params['order'])) $xmlParams->addChild('Order', $params['order']);
        if (! empty($params['IsHidden'])) { 
        	$xmlParams->addChild('IsHidden', $params['IsHidden']==1 ? 'true' : 'false');
        } else {
        	$xmlParams->addChild('IsHidden', 'false');
        }
        if (
                (! empty($params['integration_type'])) &&
                ($params['integration_type'] != $params['current_integration_type'])
        ) {
            $xmlParams->addChild('IntegrationType', $params['integration_type']);
        }

        $xmlParams->addChild('ProviderTypes');

        if (! empty($params['provider'])) {
            foreach ($params['provider'] as $provider => $value) {
                $xmlParams->ProviderTypes->addChild('ItemProviderType', $provider);
            }
        }
        if (! empty($params['integration_mode'])) {
            $xmlParams->addChild('IntegrationDeliveryMode', $params['integration_mode']);
        }

        return str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
    }

    private function _generateRateXml($request)
    {
        $xmlParams = new SimpleXMLElement('<ExternalDeliveryRate></ExternalDeliveryRate>');
        $params = $request->getValue('tariff');

        if (@$params['id']) $xmlParams->addChild('Id', $params['id']);
        if (@$params['delivery']) $xmlParams->addChild('ExternalDeliveryTypeId', htmlspecialchars($params['delivery']));
        if (@$params['countrycode']) $xmlParams->addChild('CountryCode', htmlspecialchars($params['countrycode']));
        if (@$params['start']) $xmlParams->addChild('Start', htmlspecialchars($params['start']));
        if (@$params['step']) $xmlParams->addChild('Step', $params['step']);
        if (@$params['isenabled']) {
            $xmlParams->addChild('IsEnabled', $params['isenabled']);
        } else {
            $xmlParams->addChild('IsEnabled', 0);
        }

        return str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
    }

    private function _getSubarea($all_regions, $parent)
    {
        $regions = array();
        $children = array();

        foreach ($all_regions as $reg){
            if ($reg['ParentId'] != $parent) {
                continue;
            }
            $children[] = $reg;
        }

        foreach ($children as $reg) {
            $zip = $reg['Zip'] ? '('.$reg['Zip'].')' : '';
            $text = $reg['Id'].' '.$reg['Name'].' '.$zip.' <a href="#" class="region-select" regid="'.$reg['Id'].'" regname="'.$reg['Name'].'">'.LangAdmin::get('choose').'</a>';

            if (count($children)) {
                $result =  array(
                    'text'          => $text,
                    'id'            => $reg['Id'],
                    'expanded'      => false,
                    'hasChildren'   => true,
                    'children'      => $this->_getSubarea($all_regions, $reg['Id']),
                );
            } else {
                $result =  array(
                    'text'          => $text,
                    'id'            => $reg['Id'],
                    'expanded'      => false,
                    'hasChildren'   => false
                );
            }
            $regions[] = $result;
        }

        return $regions;
    }
}