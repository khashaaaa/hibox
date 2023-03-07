<?php

OTBase::import('system.lib.Validation.*');
OTBase::import('system.lib.Validation.Rules.*');

class Shipment extends GeneralUtil
{
    protected $_cache = false;
    protected $_life_time = 3600;
    protected $_template = 'general';
    protected $_template_path = 'shipment/';
    /**
     * @var SiteConfigurationRepository
     */
    protected $siteConfigRepository;
    /**
     * @var ShipmentProvider
     */
    protected $shipmentProvider;
    protected $pricingProvider;


    public function __construct()
    {
        parent::__construct();
        $this->cms->checkTable('site_config');
        $this->cms->checkTable('site_langs');
        $this->siteConfigRepository = new SiteConfigurationRepository($this->cms);
        $this->shipmentProvider = new ShipmentProvider($this->getOtapilib());
        $this->pricingProvider = new PricingProvider($this->getOtapilib());
    }

    public function defaultAction($request)
    {
        $this->authenticationListener->CheckAuthentication($request);
        try {
            $this->tpl->assign('Delivery', $this->shipmentProvider->GetDelivery());
        } catch (ServiceException $e) {
            $this->errorHandler->CheckSessionExpired($e, $request);
        }
        $this->tpl->assign('Countries', $this->shipmentProvider->GetCountries());
        $this->tpl->assign('Rates', $this->shipmentProvider->GetRates());

        $this->assignSiteConfig();
        print $this->fetchTemplate();
    }

    public function internalAction($request)
    {
        $this->authenticationListener->CheckAuthentication($request);
        $this->_template = 'internal';

        $this->assignSiteConfig();
        print $this->fetchTemplate();
    }

    public function tariffsAction($request)
    {
        $this->authenticationListener->CheckAuthentication($request);
        $this->_template = 'tariffs';

        try {
            $this->tpl->assign('Delivery', $this->shipmentProvider->GetDeliveriesById());
            $this->tpl->assign('Countries', $this->shipmentProvider->GetCountries());
            $this->tpl->assign('Rates', $this->shipmentProvider->GetRates());
        } catch (Exception $e) {
            $this->tpl->assign('Delivery', array());
            $this->tpl->assign('Countries', array());
            $this->tpl->assign('Rates', array());
            ErrorHandler::registerError($e);
        }

        $this->assignSiteConfig();
        print $this->fetchTemplate();
    }

    public function edittariffAction($request)
    {
        $this->authenticationListener->CheckAuthentication($request);

        $this->_template = 'tariff_form';

        try {
            $Delivery = $this->shipmentProvider->GetDeliveriesById();
            if ($request->getValue('delivery')) {
                $this->tpl->assign('DeliveryItem', $Delivery[$request->getValue('delivery')]);
            }

            $tariff = $this->shipmentProvider->GetRateById($request->getValue('id'));
            $this->tpl->assign('Tariff', $tariff);
            $this->tpl->assign('Countries',
                $this->shipmentProvider->GetCountriesWithoutRate(
                    $Delivery[$request->getValue('delivery')],
                    array($tariff['countrycode'])));
        } catch (Exception $e) {
            $this->tpl->assign('Tariff', array());
            $this->tpl->assign('Countries', array());
            ErrorHandler::registerError($e);
        }

        $this->tpl->assign('actionTitle', LangAdmin::get('editing'));

        $this->assignSiteConfig();
        print $this->fetchTemplate();
    }

    /**
     * @param RequestWrapper $request
     */
    public function addTariffAction($request)
    {
        $this->authenticationListener->CheckAuthentication($request);

        $this->_template = 'tariff_form';

        try {
            $Delivery = $this->shipmentProvider->GetDeliveriesById();
            if ($request->getValue('delivery')) {
                if ($Delivery[$request->getValue('delivery')]) {
                    $this->tpl->assign('DeliveryItem', $Delivery[$request->getValue('delivery')]);
                } else {
                    $request->RedirectToReferrer();
                }
            }
            $this->tpl->assign('Countries', $this->shipmentProvider->GetCountriesWithoutRate($Delivery[$request->getValue('delivery')]));
        } catch (Exception $e) {
            $this->tpl->assign('Countries', array());
            ErrorHandler::registerError($e);
        }

        $this->tpl->assign('actionTitle', LangAdmin::get('adding'));

        $this->assignSiteConfig();
        print $this->fetchTemplate();
    }

    public function editDeliveryAction($request)
    {
        $this->authenticationListener->CheckAuthentication($request);
        $this->_template = 'delivery_form';

        try {
            $instanceProvider = InstanceProvider::getObject();
            $providers = $instanceProvider->GetProviderInfoList(Session::getActiveAdminLang());

            if ($request->get('id')) {
                $delivery = $this->shipmentProvider->GetDeliveryById($request->get('id'));
                $parsed_formula = self::ParseFormula($delivery['formula']);
                $this->tpl->assign('Delivery', $delivery);
                $this->tpl->assign('parsed_formula', $parsed_formula);
                $this->tpl->assign('integrationDeliveryModes', $this->getDeliveryModes($delivery['IntegrationType']));

                $actionTitle = LangAdmin::get('editing');
                $this->tpl->assign('isRoundedStep', strstr($delivery['formula'], 'ceil') !== false);
            } else {
                $actionTitle = LangAdmin::get('adding');
                $this->tpl->assign('isRoundedStep', false);
            }
        } catch (Exception $e) {
            ErrorHandler::registerError($e);
        }

        $this->tpl->assign('providers', $providers);
        $this->tpl->assign('actionTitle', $actionTitle);
        $this->tpl->assign('Countries', $this->shipmentProvider->GetCountries());
        $this->tpl->assign('CurrencyList', $this->pricingProvider->GetCurrencyList());
        $this->tpl->assign('integrationTypes', $this->getOtapilib()->GetDeliveryServiceSystemInfoList());

        $this->assignSiteConfig();
        print $this->fetchTemplate();
    }

    public function getIntegrationDeliveryModesAction($request)
    {
        $serviceSystem = $request->getValue('serviceSystem');
        try {
            $modes = $this->getDeliveryModes($serviceSystem);
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array('modes' => $modes));
    }

    public function searchDeliveryModesAction($request)
    {
        $countryCode = $request->getValue('countryCode');
        $weight = $request->getValue('weight');
        $cityCode = $request->getValue('cityCode');
        $providerTypeEnum = $request->getValue('providerTypeEnum');
        try {
            $ordersProvider = new OrdersProvider($this->getOtapilib());
            $deliveryModes  = $ordersProvider->SearchDeliveryModes($countryCode, $weight, $cityCode, $providerTypeEnum);
            $deliveryModes = $deliveryModes['Content'];
            $response = array(
                'deliveryModes' => $deliveryModes
            );
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse($response);
    }

    public function saveDeliveryAction($request)
    {
        try {
            $params = $this->checkDeliveryData($request);
            $r = $this->shipmentProvider->SaveDelivery($params);
            InstanceProvider::getObject()->clearDeliveryCountryInfoListCache(Session::getActiveAdminLang());
        } catch (ValidationException $e) {
            $this->respondAjaxError($e->getMessage());
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();
    }

    public function deleteDeliveryAction($request)
    {
        try {
            $r = $this->shipmentProvider->DeleteDelivery($request);
        } catch (ValidationException $e) {
            $this->respondAjaxError($e->getMessage());
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();
    }

    private function assignSiteConfig()
    {
        $this->siteConfigRepository->SetActiveLang(Session::get('active_lang_siteconfiguration'));
        $this->tpl->assign('Config', $this->siteConfigRepository);
    }

    private function checkDeliveryData($request)
    {
        $params = $request->getValue('delivery');
        if (!isset($params['name']))
            $params['name'] = '';
        if (!isset($params['step_weight']))
            $params['step_weight'] = 0;
        if (!isset($params['min_price_delivery']))
            $params['min_price_delivery'] = '';

        $validator = new Validator(array(
            'name' => $params['name'],
            'step_weight' => $params['step_weight'],
        ));

        $validator->addRule(new NotEmpty(), 'name', LangAdmin::get('Must_enter_delivery_name'));

        /** Если в параметрах нет кастомной формулы, то проверяем параметры */
        if (!isset($params['save_custom_formula'])) {
            if (!$params['min_price_delivery']) {
                $validator->addRule(new NotEmpty(), 'step_weight', LangAdmin::get('Must_be_checked_one_of_params_minpricedelivery_or_weightstep'));
            }
        }

        if (!$validator->validate()) {
            $errors = $validator->getLastError();
            throw new ValidationException((string)$errors);
        }
        return $params;
    }

    public function saveTariffAction($request)
    {
        try {
            $this->shipmentProvider->SaveRate($request);
            InstanceProvider::getObject()->clearDeliveryCountryInfoListCache(Session::getActiveAdminLang());
        } catch (ValidationException $e) {
            $this->respondAjaxError($e->getMessage());
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();
    }

    public function deleteTariffAction($request)
    {
        try {
            $r = $this->shipmentProvider->DeleteRate($request);
        } catch (ValidationException $e) {
            $this->respondAjaxError($e->getMessage());
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();
    }

    public function saveInternalAction($request)
    {
        try {
            $result = $this->shipmentProvider->SaveCase($request);
            if (empty($result)) {
                throw new ServiceException(__METHOD__, '', LangAdmin::get('Notify_error'), 1);
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }

    public function ParseFormula($formula)
    {
        //Парсим формулу
        $formula = "_" . $formula . "_"; //Так как не всегда видит первый и последний символ
        $mass = array();
        //$weight < 2 ? 0 : ($start + (ceil ($weight * 10) - 1) * $step)
        if (strpos($formula, "?")) { //Если есть зависимость от веса
            if ((strpos($formula, "&")) or (strpos($formula, "|"))) {
                //($weight > 20) && ($weight < 1)) ? 0 : ($start + (ceil ($weight * 10) - 1) * $step)               
                preg_match_all('/weight >(.*)\)/isU', $formula, $weight1, PREG_PATTERN_ORDER);
                preg_match_all('/weight <(.*)\)/isU', $formula, $weight2, PREG_PATTERN_ORDER);
                @$mass['max_weight'] = str_replace(' ', '', $weight1[1][0]);
                @$mass['min_weight'] = str_replace(' ', '', $weight2[1][0]);
            } else {
                //$weight < 2
                if (strpos($formula, "<")) {
                    preg_match_all('/weight <(.*)\?/isU', $formula, $weight1, PREG_PATTERN_ORDER);
                    @$mass['max_weight'] = str_replace(' ', '', $weight1[1][0]);
                    @$mass['min_weight'] = '';
                } else {
                    preg_match_all('/weight >(.*)\?/isU', $formula, $weight1, PREG_PATTERN_ORDER);
                    @$mass['max_weight'] = '';
                    @$mass['min_weight'] = str_replace(' ', '', $weight1[1][0]);
                }

            }
            //Выевялем ошибки
            if (($mass['max_weight'] == '') and ($mass['min_weight'] == '')) {
                @$mass['errorparse'] = '1';
            }

        } else {
            @$mass['max_weight'] = '';
            @$mass['min_weight'] = '';
        }


        //Шаг по весу
        preg_match_all('/weight \* (.*)\)/isU', $formula, $step_weight, PREG_PATTERN_ORDER);
        if (isset($step_weight[1][0]))
            $mass['step_weight'] = @$step_weight[1][0] / 100;

        preg_match_all('/weight \/ (.*)\)/isU', $formula, $step_weight, PREG_PATTERN_ORDER);
        if (@$step_weight[1][0] <> '')
            $mass['step_weight'] = @$step_weight[1][0];
        //Минимальная цена доставки
        if (strpos($formula, htmlspecialchars('$start')))
            $mass['min_price_delivery'] = 'checked';

        //Округляем иль нет
        if (strpos($formula, "ceil"))
            $mass['rounding'] = 'checked';


        //Подгатавливаем массив на выход к публике - убираем символу способные попасть в массив при разборе
        foreach ($mass as &$value) {
            $value = str_replace(' ', '', $value);
            $value = str_replace('_', '', $value);
            $value = str_replace(')', '', $value);
            $value = str_replace('(', '', $value);
        }

        return $mass;
    }

    private function getDeliveryModes($serviceSystem)
    {
        $lang = Session::getActiveAdminLang();
        OTAPILib2::GetDeliveryModesByServiceSystem($lang, $serviceSystem, $modes);
        OTAPILib2::makeRequests();

        $result = array();
        foreach ($modes->GetResult()->GetContent()->GetItem() as $mode) {
            $result[$mode->GetIntegrationDeliveryMode()] = $mode->GetName();
        }

        return $result;
    }

    public function deliveryServiceAction($request)
    {
        $this->_template = 'delivery_service';

        $lang = Session::get('active_lang_' . strtolower($request->get('cmd')), Session::getActiveAdminLang());

        $systems = array();
        try {
            /** @var OtapiDeliveryServiceSystemInfoListAnswer $deliverySystems */
            OTAPILib2::GetDeliveryServiceSystemInfoList($lang, $deliverySystems);
            OTAPILib2::makeRequests();

            foreach ($deliverySystems->GetResult()->GetContent()->GetItem() as $item) {
                if ($item->HasSettings()) {
                    $systems[] = $item;
                }
            }
        } catch (ServiceException $e) {
            ErrorHandler::registerError($e);
        }

        $this->tpl->assign('systems', $systems);
        $this->tpl->assign('updateTemplateUrl', 'index.php?cmd=Shipment&do=updateDeliveryServiceSystem');

        print $this->fetchTemplate();
    }

    public function getDeliveryServiceAction($request)
    {
        $html = '';

        try {
            $type = $request->getRequestValueSafe('type');
            $lang = Session::get('active_lang_' . strtolower($request->get('cmd')), Session::getActiveAdminLang());

            OTAPILib2::GetDeliveryServiceSystemSettings($lang, Session::get('sid'), $type, 'true', $request);
            OTAPILib2::makeRequests();

            if ($request && $request->GetResult()) {
                $settingsXmlData = $request->GetResult()->GetRawData();
                $html = '<div class="well form-horizontal ot_form">'
                    . MetaUI::render($settingsXmlData, $this->pageUrl->Add('do', 'updateTemplate')->Get() . '&integrationType=' . $type)
                    . '</div>';
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse(array(
            'html' => $html
        ));
    }

    public function updateTemplateAction($request)
    {
        $name = $request->post('name');
        $value = $request->post('value');
        $type = $request->get('type');
        $integrationType = $request->get('integrationType');

        try {
            $lang = Session::get('active_lang_' . strtolower($request->get('cmd')), Session::getActiveAdminLang());

            $params = explode(MetaUI::NODES_SEPARATOR, $name);
            if (is_array($params) && count($params) > 0) {
                $xmlParameters = MetaUI::generateSingleParamXml('OtapiDeliveryServiceSystemSettingsUpdateData', $params, $value, $type);
                $answer = false;
                OTAPILib2::UpdateDeliveryServiceSystemSettings($lang, Session::get('sid'), $integrationType, $xmlParameters, $answer);
                OTAPILib2::makeRequests();
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse(array(), true);
    }
}
