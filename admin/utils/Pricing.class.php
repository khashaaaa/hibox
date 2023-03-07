<?php
class Pricing extends GeneralUtil
{
    protected $_template = 'general';
    protected $_template_path = 'pricing/';
    /**
     * @var SiteConfigurationRepository
     */
    protected $siteConfigRepository;
    protected $categoriesProvider;
    /**
     * @var PricingProvider
     */
    protected $pricingProvider;

    /**
     * @var InstanceBillingInfo

     */
    protected $instanceBillingInfo;


    public function __construct()
    {
        parent::__construct();
        $this->cms->checkTable('site_config');
        $this->cms->checkTable('site_langs');
        $this->siteConfigRepository = new SiteConfigurationRepository($this->cms);
        $this->pricingProvider = new PricingProvider($this->getOtapilib());
        $this->categoriesProvider = new CategoriesProvider($this->cms, $this->getOtapilib());
        $this->instanceBillingInfo = new InstanceBillingInfo($this->otapilib);
    }

    public function defaultAction($request)
    {
        $this->authenticationListener->CheckAuthentication($request);
        try {
            $this->tpl->assign('CurrencyList', $this->pricingProvider->GetCurrencyList());
            $this->tpl->assign('CurrenciesSettings', $this->pricingProvider->GetCurrenciesSettings());
            $this->tpl->assign('PriceFormationSettings', $this->pricingProvider->GetPriceFormationSettings());
            $this->tpl->assign('SyncMode', $this->pricingProvider->GetCurrencySynchronizationModeList());
            $this->tpl->assign('NotExistsRates', $this->pricingProvider->CheckCurrencyRates());
            $this->tpl->assign('providerInfoList', $this->categoriesProvider->GetProviderInfoList());
        }
        catch (ServiceException $e) {
            $this->errorHandler->CheckSessionExpired($e, $request);
        }
        $this->assignSiteConfig();
        print $this->fetchTemplate();
    }

    public function costAction($request)
    {
        $this->authenticationListener->CheckAuthentication($request);
        $this->_template = 'cost';
        try {
            $providerInfoList = $this->categoriesProvider->GetProviderInfoList();
            $this->instanceBillingInfo->initInstanceOptions(Session::getActiveAdminLang(), Session::get('sid'));
            $this->instanceBillingInfo->doRequests();
            $defaultPriceProvider = $this->instanceBillingInfo->getInstanceOptions()->GetResult()->GetDefaultItemProvider();
            $defaultPriceCurrency = 'Undefined';
            foreach ($providerInfoList as $searchProvider) {
                if ($searchProvider['Type'] == $defaultPriceProvider) {
                    $defaultPriceCurrency = $searchProvider['CurrencyCode'];
                    break;
                }
            }
            $this->tpl->assign('Settings', $this->pricingProvider->GetShowCase());
            $this->tpl->assign('RoundSettings', $this->pricingProvider->GetPriceFormationSettings());
            $this->tpl->assign('defaultPriceCurrency', $defaultPriceCurrency);
            $this->tpl->assign('priceRoundingOriginal', General::getSiteNumConfig('price_rounding'));
        } catch (ServiceException $e) {
            $this->errorHandler->CheckSessionExpired($e, $request);
        }

        $this->assignSiteConfig();
        print $this->fetchTemplate();
    }

    public function bankerAction($request)
    {
        $this->authenticationListener->CheckAuthentication($request);
        $this->_template = 'banker';
        $groups = array();
        $strategies = array();
        try {
            $sid = Session::get('sid');
            $groups = $this->pricingProvider->GetPriceFormationGroupList($sid);
            $providerInfoList = $this->getProviderInfoListAsArray();
            foreach ($groups as $key => &$group) {
                $group['categories'] = $this->pricingProvider->GetCategoriesOfPriceFormationGroup($sid, $group['id']);
                $group['ProviderTypeName'] = $providerInfoList[$group['ProviderType']]['DisplayName'];
            }
            $strategies = $this->pricingProvider->GetPriceFormationStrategyList($sid);
        }
        catch (ServiceException $e) {
            $this->errorHandler->CheckSessionExpired($e, $request);
        }
        $this->tpl->assign('groups', $groups);
        $this->tpl->assign('strategies', $strategies);

        print $this->fetchTemplate();
    }

    private function getProviderInfoListAsArray()
    {
        $array = array();

        $providerInfoList = $this->categoriesProvider->GetProviderInfoList();
        foreach ($providerInfoList as $value) {
            $array[$value['Type']] = $value;
        }

        return $array;
    }

    public function priceGroupCategoriesAction($request)
    {
        $this->authenticationListener->CheckAuthentication($request);
        $this->_template_path = 'pricing/banker/';
        $this->_template = 'group_categories';
        $categories = array();
        $allCategories = array();
        $group = array();
        $id = 0;
        try {
            $sid = Session::get('sid');
            $id = $request->getValue('id');
            $group = $this->pricingProvider->GetPriceFormationGroup($sid, $request->getValue('id'));
            $categories = $this->pricingProvider->GetCategoriesOfPriceFormationGroup($sid, $id);
            $allCategories = $this->categoriesProvider->GetEditableCategorySubcategories(Session::get('sid'), 0, 'true');

            $i=0;
            foreach ($allCategories as $k => &$category) {
                $category['i'] = $i;
                $i++;
            }

            if (! is_array($allCategories)) {
                throw new ServiceException(__METHOD__, '', 'Could not load categories list', 1);
            }
        }
        catch (ServiceException $e) {
            $this->errorHandler->CheckSessionExpired($e, $request);
        }
        $this->tpl->assign('categories', $categories);
        $this->tpl->assign('allCategories', $allCategories);
        $this->tpl->assign('group', $group);

        print $this->fetchTemplate();
    }


    private function _parseCategoryLink($url) {
        if (preg_match( '/('.UrlGenerator::getProtocol().')/i', $url )) {
            if (in_array('Seo2', General::$enabledFeatures)) {
                $parts = explode("/", $url);
                $parts = preg_replace("/(\?.*)$/", "", end($parts));
                $cid = $this->cms->getCategoryIdByAlias($parts);
                if ($cid) {
                    return htmlspecialchars($cid);
                } else {
                    throw new Exception(LangAdmin::get('Category_url_is_invalid'));
                }
            } else {
                $url = parse_url($url);
                $params = array();
                if (isset($url['query'])) {
                    parse_str($url['query'], $params);
                    if (isset($params['cid'])) {
                        return htmlspecialchars($params['cid']);
                    } else {
                        throw new Exception(LangAdmin::get('Category_url_is_invalid'));
                    }
                } else {
                    throw new Exception(LangAdmin::get('Category_url_is_invalid'));
                }
            }
        } else {
            if (!empty($url)) {
                return $url;
            }
            throw new Exception(LangAdmin::get('Category_url_is_invalid'));
        }
    }

    public function addPriceGroupCategoryAction($request)
    {
       try {
            $categoryId = $this->_parseCategoryLink($request->getValue('category-url'));
            $groupId = $request->getValue('id');
            $sid = Session::get('sid');
            $r = $this->pricingProvider->SetPriceFormationGroupToCategory($sid, $categoryId, $groupId);
        }
        catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array('result' => 'ok'));
    }

    public function deletePriceGroupCategoryAction($request)
    {
        try {
            $groupId = $request->getValue('groupId');
            $categoryId = $request->getValue('categoryId');
            $sid = Session::get('sid');
            $this->pricingProvider->RemoveCategoryFromPriceFormationGroup($sid, $categoryId, $groupId);
        }
        catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();

    }

    public function deletePriceGroupAction($request)
    {
        try {
            $groupId = $request->getValue('id');
            $sid = Session::get('sid');
            $this->pricingProvider->RemovePriceFormationGroup($sid, $groupId);
        }
        catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();
    }

    public function addPriceGroupAction($request)
    {
        $this->_template_path = 'pricing/banker/';
        $this->_template = 'price_group_form';
        $strategies = array();
        try{
            $sid = Session::get('sid');
            $groups = $this->pricingProvider->GetPriceFormationGroupList($sid);
            $strategies = $this->pricingProvider->GetPriceFormationStrategyList($sid);
            $strategiesByProviders = array();
            foreach ($strategies as $strategy) {
                $provider = empty($strategy['ProviderType']) ? 'All' : $strategy['ProviderType'];
                $strategiesByProviders[$provider][] = $strategy;
            }
            $providerInfoList = $this->categoriesProvider->GetProviderInfoList();
            $this->instanceBillingInfo->initInstanceOptions(Session::getActiveAdminLang(), $sid);
            $this->instanceBillingInfo->doRequests();
        } catch (ServiceException $e) {
            $this->errorHandler->CheckSessionExpired($e, $request);
        }

        $this->tpl->assign('isNew', true);
        $this->tpl->assign('strategies', $strategies);
        $this->tpl->assign('strategiesByProviders', $strategiesByProviders);
        $this->tpl->assign('providerInfoList', $providerInfoList);
        $this->tpl->assign('currentInstanceSettings', $this->instanceBillingInfo->getInstanceOptions()->GetResult());
        print $this->fetchTemplate();
    }

    public function editPriceGroupAction($request)
    {
        $this->_template_path = 'pricing/banker/';
        $this->_template = 'price_group_form';
        $strategies = array();
        $group = array();
        $preselectedDelivery = array();
        try {
            $sid = Session::get('sid');
            $group = $this->pricingProvider->GetPriceFormationGroup($sid, $request->getValue('id'));
            $strategies = $this->pricingProvider->GetPriceFormationStrategyList($sid);
            $strategiesByProviders = array();
            
            foreach ($strategies as $strategy) {
                $provider = empty($strategy['ProviderType']) ? 'All' : $strategy['ProviderType'];
                $strategiesByProviders[$provider][] = $strategy;
            }
            if (in_array('PreselectedDelivery', General::$enabledFeatures)) {
                $deliveryTypeList = $this->pricingProvider->GetExternalDeliveryTypeList($sid);
                foreach ($deliveryTypeList as $delivery) {
                    $preselectedDelivery[$delivery['Id']] = $delivery;
                }
                if (! empty($group['DeliveryIds'])) {
                    $tmp = array();
                    
                    foreach ($group['DeliveryIds'] as $id) {
                        $tmp[] = $preselectedDelivery[$id];
                    }

                    $group['DeliveryIds'] = $tmp;
                }
            }
            $providerInfoList = $this->categoriesProvider->GetProviderInfoList();
        } catch (ServiceException $e) {
            $this->errorHandler->CheckSessionExpired($e, $request);
        }

        $this->tpl->assign('preselectedDelivery', $preselectedDelivery);
        $this->tpl->assign('isNew', false);
        $this->tpl->assign('strategies', $strategies);
        $this->tpl->assign('strategiesByProviders', $strategiesByProviders);
        $this->tpl->assign('group', $group);
        $this->tpl->assign('providerInfoList', $providerInfoList);
        print $this->fetchTemplate();
    }

    private function _getPriceFormationGroupInfo($request) {
        $isNew = $request->getValue('id', 0) == 0;
        $xmlParams = '<PriceFormationGroupInfo Name="' . htmlspecialchars($request->getValue('name')) .
        '" Description="' . htmlspecialchars($request->getValue('description')) . '" StrategyType="' . $request->getValue('price-group-type') . '">';
        if ($request->getValue('id', false)) {
            $xmlParams .= '<Id>' . $request->getValue('id') . '</Id>';
        }
        if ($isNew) {
            $xmlParams .= '<ProviderType>' . $request->getValue('priceProviderType') . '</ProviderType>';
        }
        if ($request->getValue('delivery_code', false)) {
            $xmlParams .= '<DeliveryIds>';
            foreach ($request->getValue('delivery_code') as $delivery) {
                $xmlParams .= '<Id>' . $delivery . '</Id>';
            }
            $xmlParams .= '</DeliveryIds>';
        }

        if ($request->getValue('features', false)) {
            $xmlParams .= '<Features>';
            foreach ($request->getValue('features') as $feature) {
                $xmlParams .= "<Feature>$feature</Feature>";
            }
            $xmlParams .= '</Features>';
        }

        $settings  = '<Settings>';
        if ($request->getValue('delivery-all') != '') {
            $settings .= '<InternalDeliveryPrice>' . $request->getValue('delivery-all') . '</InternalDeliveryPrice>';
        }
        if ($request->getValue('delivery-all-margin') != '') {
            $settings .= '<TotalDeliveryFixedPrice>' . $request->getValue('delivery-all-margin') . '</TotalDeliveryFixedPrice>';
        }
        $settings .= '<PriceFormationIntervals>';
        $limit = $request->getValue('limit');
        $intervalId = $request->getValue('interval_id');
        $marginType = $request->getValue('margin_type');
        $margin = $request->getValue('margin');
        $marginFixed = $request->getValue('margin_fixed');
        $delivery = $request->getValue('delivery');
        $deliveryMargin = $request->getValue('delivery_margin');

        for ($i=0; $i<count($limit); $i++) {
            $id = (isset($intervalId[$i]) && $intervalId[$i]!='') ? ' Id="'.$intervalId[$i] . '"' : '';
            if (!empty($id)) {
                $settings .= '<PriceFormationIntervalInfo ' . $id . '>';
            } else {
                $settings .= '<PriceFormationIntervalInfo>';
            }

            if (! empty($margin[$i])) {
            	$settings .= '<MarginPercent>'.(($margin[$i]/100)+1).'</MarginPercent>';
            }
            
            if (! empty($marginFixed[$i])) {
            	$settings .= '<MarginFixed>'.$marginFixed[$i].'</MarginFixed>';
            }

            $settings .= '<MinimumLimit>'.$limit[$i].'</MinimumLimit>';
            
            if ($delivery[$i] !== '') {
                $settings .= '<InternalDeliveryPrice>' . $delivery[$i] . '</InternalDeliveryPrice>';
            }
            if ($deliveryMargin[$i] !== '') {
                $settings .= '<TotalDeliveryFixedPrice>' . $deliveryMargin[$i] . '</TotalDeliveryFixedPrice>';
            }
            $settings .= '</PriceFormationIntervalInfo>';
        }
        $settings .= '</PriceFormationIntervals>';
        $settings .= '</Settings>';

        if ($settings) {
            $xmlParams .=  $settings;
        }

        $xmlParams .= '</PriceFormationGroupInfo>';
        return $xmlParams;
    }

    public function savePriceGroupAction($request)
    {
        try {
            $isNew = $request->getValue('id', 0) == 0;
            $xmlParams = $this->_getPriceFormationGroupInfo($request);
            $sid = Session::get('sid');
            if ($isNew) {
                $groupId = $this->pricingProvider->AddPriceFormationGroup($sid, $xmlParams);
                if ($groupId) {
                    if ($request->getValue('default')) {
                        $sid = Session::get('sid');
                        $r = $this->pricingProvider->SetDefaultPriceFormationGroup($sid, $groupId);

                    }
                }

            } else {
                $r = $this->pricingProvider->EditPriceFormationGroup($sid, $request->getValue('id'), $xmlParams);
                if ($request->getValue('default')) {
                    $sid = Session::get('sid');
                    $r = $this->pricingProvider->SetDefaultPriceFormationGroup($sid, $request->getValue('id'));

                }
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e->getErrorMessage());
        }
        $this->sendAjaxResponse(array('result'=>'ok'));
    }

    public function saveSettingsCBAction($request)
    {
        $this->authenticationListener->CheckAuthentication($request);
        try {
            $this->pricingProvider->saveSettingsCB($request);
        }
        catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();
    }

    public function saveCostAction($request)
    {
        $this->authenticationListener->CheckAuthentication($request);
        try {
            if ($request->getValue('name') == 'round_settings') {
                $this->pricingProvider->saveSettingsRound($request);
                $this->siteConfigRepository->Set('price_rounding', $request->getValue('value'));
            } else if ($request->getValue('name') == 'price_rounding') {
                $this->siteConfigRepository->Set('price_rounding', $request->getValue('value'));
            } else {
                $this->pricingProvider->saveSettingsCost($request);
            }
            Cacher::rRmDir(CFG_APP_ROOT . '/cache/multi_search');
            Cacher::rRmDir(CFG_APP_ROOT . '/cache/ItemsWithReviews');
            Cacher::rRmDir(CFG_APP_ROOT . '/cache/GetBrandRatingList');
            Cacher::rRmDir(CFG_APP_ROOT . '/cache/GetItemRatingList');
        }
        catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();
    }

    public function saveCurrencyAction($request)
    {
        try {
            $this->authenticationListener->CheckAuthentication($request);
            $this->pricingProvider->UpdateInstanceCurrenciesSettings($request);
            InstanceProvider::getObject()->clearCurrencyInstanceListCache();
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $answer = array('message' => LangAdmin::get('Rate_set_succes'));
        $this->sendAjaxResponse($answer);
    }

    public function RemoveRateAction($request)
    {
        try {
            $this->pricingProvider->RemoveCurrencyRate($request);
        }
        catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse();
    }

    private function assignSiteConfig()
    {
        $this->siteConfigRepository->SetActiveLang(Session::get('active_lang_siteconfiguration'));
        $this->tpl->assign('Config', $this->siteConfigRepository);
    }

}