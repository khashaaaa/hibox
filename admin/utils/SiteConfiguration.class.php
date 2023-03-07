<?php

OTBase::import('system.uploader.php.UploadHandler');
OTBase::import('system.lib.Validation.*');
OTBase::import('system.lib.Validation.Rules.*');
OTBase::import('system.admin.utils.CacheSettings');
OTBase::import('system.otapilib2.OTAPILib2');
OTBase::import('system.otapilib2.types.OtapiAnswer');

class SiteConfiguration extends GeneralUtil
{
    const MAX_NEWS_COUNT = 300;
    const MAX_VENDORS_COUNT = 20;
    const MAX_BEST_ITEMS_COUNT = 200;
    const MAX_LAST_ITEMS_COUNT = 30;
    const MAX_ITEMS_REVIEWED_COUNT = 20;
    const MAX_ITEMS_OFICIAL_CATALOG = 200;
    const MAX_ITEMS_EXTENDED_CATALOG = 200;
    const MAX_ITEMS_WAREHOUSE_CATALOG = 200;
    const MAX_ITEMS_COMMENTS_CATALOG = 100;
    const MIN_ITEMS_COUNT = 16;

    const CATALOG_MODE_EXTERNAL = 'External';
    const CATALOG_MODE_INTERNAL = 'InternalLeaf';
    const CATALOG_MODE_PREDEFINED = 'Predefined';
    const CATALOG_MODE_MIXED = 'LeafMixed';

    protected $_template = 'site_construction';
    protected $_template_path = 'site_config/';

    /**
     * @var OrderSettings
     */
    protected $orderSettings;
    /**
     * @var ShipmentProvider
     */
    protected $shipmentProvider;

    /**
     * @var InstanceOptionsInfo
     */
    protected $instanceOptionsInfo;
    
    /**
     * @var InstanceBillingInfo
     */
    protected $instanceBillingInfo;
    
    /**
     * @var CategoriesProvider
     */
    protected $categoriesProvider;

    protected $webUISettings;

    protected $config;

    /**
     * @var TranslationsRepository
     */
    protected $translationsRepository;

    public function __construct()
    {
        parent::__construct();
        $this->cms->checkTable('site_config');
        $this->cms->checkTable('site_langs');
        $this->cms->checkTable('blocks');
        $this->shipmentProvider = new ShipmentProvider($this->getOtapilib());
        $this->translationsRepository = new TranslationsRepository($this->cms);
        $this->instanceOptionsInfo = new InstanceOptionsInfo($this->otapilib);
        $this->instanceBillingInfo = new InstanceBillingInfo($this->otapilib);
        $this->categoriesProvider = new CategoriesProvider($this->cms, $this->getOtapilib());
        $this->webUISettings = new WebUISettings($this->getOtapilib());
        $this->config = new SiteConfigurationRepository($this->cms);
        $this->config->SetActiveLang(Session::get('active_lang_siteconfiguration'));
    }

    public function defaultAction($request)
    {
    	$this->assignSearchProvidersConfig();
        $this->assignOldDesignThemes();
        $this->assignCategoryStructureTypeSetting();

        $this->tpl->assign('Showcase', $this->shipmentProvider->GetShowCase());
        $this->tpl->assign('config', $this->config);
        
        $this->tpl->assign('newSearchSetting', General::onNewPlatformScript('search'));

        print $this->fetchTemplate();
    }

    public function getConfigInJSAction()
    {
        $this->_template_path = 'site_config/';
        $this->_template = 'config_js';
        print $this->fetchTemplateWithoutHeaderAndFooter();
    }

    public function removeConfigAction($request) {
        try {
            $configName = $request->post('configName');
            $configLang = $request->post('configLang');
            $siteConfigRepository = new SiteConfigurationRepository($this->cms);
            $siteConfigRepository->RemoveByLang($configName, $configLang);
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse(array(), true);
    }

    public function ordersAction($request)
    {
        $this->_template_path = 'site_config/orders/';
        $this->_template = 'general';
        $сollectionsSettingsMetaInfo = null;
        $orderSettingsMetaInfo = null;
        try {
            $settings = $this->renderMetaUISettingsByEntity($request, 'UserProfileSettings');
            $this->tpl->assign('UserProfileSettings', $settings);
            $this->tpl->assign('config', $this->config);
            $this->tpl->assign('Showcase', $this->shipmentProvider->GetShowCase());

            $сollectionsSettings = null;
            OTAPILib2::GetCollectionsSettings(Session::getActiveAdminLang(), Session::get('sid'), 'true', $сollectionsSettings);
            $orderSettings = null;
            if (CMS::IsFeatureEnabled('Order') && RightsManager::isFeatureAvailable('Order')) {
                OTAPILib2::GetOrderSettings(Session::getActiveAdminLang(), Session::get('sid'), 'true', $orderSettings);
            }
            OTAPILib2::makeRequests();

            if ($сollectionsSettings && $сollectionsSettings->GetResult()) {
                $сollectionsSettingsMetaInfo = $сollectionsSettings->GetResult()->GetRawData();
            }
            if ($orderSettings && $orderSettings->GetResult()) {
                $orderSettingsMetaInfo = $orderSettings->GetResult()->GetRawData();
            }
        } catch (Exception $e) {
            $this->tpl->assign('Showcase', false);
            ErrorHandler::registerError($e);
        }
        $this->langRepository = new LanguageRepository($this->cms);
        $CMSLanguages = $this->langRepository->GetLanguages();
        $langCodes = array();
        foreach ($CMSLanguages as $l) {
            $langCodes[] = $l['lang_code'];
        }
        $this->tpl->assign('langCodes', $langCodes);
        $this->tpl->assign('сollectionsSettingsMetaInfo', $сollectionsSettingsMetaInfo);
        $this->tpl->assign('updateCollectionsSettingsUrl', 'index.php?cmd=SiteConfiguration&do=updateCollectionsSettings');
        $this->tpl->assign('orderSettingsMetaInfo', $orderSettingsMetaInfo);
        $this->tpl->assign('updateOrderSettingsUrl', 'index.php?cmd=SiteConfiguration&do=updateOrderSettings');
        print $this->fetchTemplate();
    }

    public function ordersProfileAction($request) {
        $this->_template_path = 'site_config/orders/';
        $this->_template = 'orderProfile';
        $_GET['inputLanguage'] = $this->getActiveLang($request);

        try {
            $settings = $this->renderMetaUISettingsByEntity($request, 'UserProfileSettings');
            $this->tpl->assign('UserProfileSettings', $settings);

        } catch (Exception $e) {
            ErrorHandler::registerError($e);
        }
        print $this->fetchTemplate();
    }

    public function updateCollectionsSettingsAction($request)
    {
        $name = $request->post('name');
        $value = $request->post('value');
        $type = $request->get('type');

        try {
            $params = explode(MetaUI::NODES_SEPARATOR, $name);
            if (is_array($params) && count($params) > 0) {
                $xmlParameters = MetaUI::generateSingleParamXml('CollectionsSettingsUpdateData', $params, $value, $type);
                $answer = false;
                OTAPILib2::UpdateCollectionsSettings(Session::getActiveAdminLang(), Session::get('sid'), $xmlParameters, $answer);
                OTAPILib2::makeRequests();
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse(array(), true);
    }

    public function updateOrderSettingsAction($request)
    {
        $name = $request->post('name');
        $value = $request->post('value');
        $type = $request->get('type');

        try {
            $params = explode(MetaUI::NODES_SEPARATOR, $name);
            if (is_array($params) && count($params) > 0) {
                $xmlParameters = MetaUI::generateSingleParamXml('OrderSettingsUpdateData', $params, $value, $type);
                $answer = false;
                OTAPILib2::UpdateOrderSettings(Session::getActiveAdminLang(), Session::get('sid'), $xmlParameters, $answer);
                OTAPILib2::makeRequests();
                if (MetaUI::decodeParametersString(end($params)) == 'OrderPrefix') {
                    $siteConfigRepository = New SiteConfigurationRepository($this->cms);
                    $siteConfigRepository->Set('CFG_PREFIX_REPLACE_ORD', $value);
                }
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse(array(), true);
    }

    public function bankAction($request)
    {
        $this->_template_path = 'site_config/orders/';
        $this->_template = 'bank';
        $this->tpl->assign('config', $this->config);
        print $this->fetchTemplate();
    }

    public function saveLogoAction($request)
    {
        try {
            if ($request->getValue('delete_image')) {
                $this->config->Set('logo', '');
                $logoUrl = '/i/logo.png';
            } else {
                if (empty($_FILES['input_image']['tmp_name'])) {
                    $this->respondAjaxError('No image was selected to upload.');
                }
                $uploadResult = $this->uploadImage();
                if (isset($uploadResult['input_image'][0])) {
                    if (isset($uploadResult['input_image'][0]->url)) {
                        $logoUrl = $uploadResult['input_image'][0]->url;
                        $this->config->Set('logo', $logoUrl);
                    } else if (isset($uploadResult['input_image'][0]->error)) {
                        $this->respondAjaxError($uploadResult['input_image'][0]->error);
                    }
                } else {
                    $this->respondAjaxError('Unknown error occured while uploading image. Try again.');
                }
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array(
            'url' => $logoUrl,
        ));
    }

    private function uploadAdvertisingBanner()
    {
        $uploader = new UploadHandler(array(
            'param_name' => 'advertising-banner'
        ), false, null, '/uploaded/banners/');
        return $uploader->post(false);
    }

    private function uploadAdditionalBanners()
    {
        $uploader = new UploadHandler(array(
            'param_name' => 'additional-banner'
        ), false, null, '/uploaded/banners/');
        return $uploader->post(false);
    }

    public function saveAdvertisingBannerAction($request)
    {
        try {
            if ($request->getValue('delete_image')) {
                $this->config->Set('elastic_advertising_banner_image', '');
                $bannerUrl = '/i/noimg.png';
            } else {
                if (empty($_FILES['advertising-banner']['tmp_name'])) {
                    $this->respondAjaxError('No image was selected to upload.');
                }
                $uploadResult = $this->uploadAdvertisingBanner();
                if (isset($uploadResult['advertising-banner'][0])) {
                    if (isset($uploadResult['advertising-banner'][0]->url)) {
                        $bannerUrl = $uploadResult['advertising-banner'][0]->url;
                        $this->config->Set('elastic_advertising_banner_image', $bannerUrl);
                    } else if (isset($uploadResult['advertising-banner'][0]->error)) {
                        $this->respondAjaxError($uploadResult['advertising-banner'][0]->error);
                    }
                }
            }
        } catch(Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array(
            'url' => $bannerUrl,
        ));
    }

    public function saveAdditionalBanner1Action($request)
    {
        try {
            if ($request->getValue('delete_image')) {
                $this->config->Set('elastic_banner_1_img', '');
                $bannerUrl = '/i/noimg.png';
            } else {
                if (empty($_FILES['additional-banner']['tmp_name'])) {
                    $this->respondAjaxError('No image was selected to upload.');
                }
                $uploadResult = $this->uploadAdditionalBanners();
                if (isset($uploadResult['additional-banner'][0])) {
                    if (isset($uploadResult['additional-banner'][0]->url)) {
                        $bannerUrl = $uploadResult['additional-banner'][0]->url;
                        $this->config->Set('elastic_banner_1_img', $bannerUrl);
                    } else if (isset($uploadResult['additional-banner'][0]->error)) {
                        $this->respondAjaxError($uploadResult['additional-banner'][0]->error);
                    }
                } else {
                    $this->respondAjaxError('Unknown error occured while uploading image. Try again.');
                }
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array(
            'url' => $bannerUrl,
        ));
    }

    public function saveAdditionalBanner2Action($request)
    {
        try {
            if ($request->getValue('delete_image')) {
                $this->config->Set('elastic_banner_2_img', '');
                $bannerUrl = '/i/noimg.png';
            } else {
                if (empty($_FILES['additional-banner']['tmp_name'])) {
                    $this->respondAjaxError('No image was selected to upload.');
                }
                $uploadResult = $this->uploadAdditionalBanners();
                if (isset($uploadResult['additional-banner'][0])) {
                    if (isset($uploadResult['additional-banner'][0]->url)) {
                        $bannerUrl = $uploadResult['additional-banner'][0]->url;
                        $this->config->Set('elastic_banner_2_img', $bannerUrl);
                    } else if (isset($uploadResult['additional-banner'][0]->error)) {
                        $this->respondAjaxError($uploadResult['additional-banner'][0]->error);
                    }
                } else {
                    $this->respondAjaxError('Unknown error occured while uploading image. Try again.');
                }
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array(
            'url' => $bannerUrl,
        ));
    }

    /* Методы для баннеров Fashi-темы */
    public function savePromoFashiBanner1Action($request)
    {
        try {
            if ($request->getValue('delete_image')) {
                $this->config->Set('fashi_promo_banner_1_img', '');
                $bannerUrl = '/i/noimg.png';
            } else {
                if (empty($_FILES['additional-banner']['tmp_name'])) {
                    $this->respondAjaxError('No image was selected to upload.');
                }
                $uploadResult = $this->uploadAdditionalBanners();
                if (isset($uploadResult['additional-banner'][0])) {
                    if (isset($uploadResult['additional-banner'][0]->url)) {
                        $bannerUrl = $uploadResult['additional-banner'][0]->url;
                        $this->config->Set('fashi_promo_banner_1_img', $bannerUrl);
                    } else if (isset($uploadResult['additional-banner'][0]->error)) {
                        $this->respondAjaxError($uploadResult['additional-banner'][0]->error);
                    }
                } else {
                    $this->respondAjaxError('Unknown error occured while uploading image. Try again.');
                }
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array(
            'url' => $bannerUrl,
        ));
    }

    public function savePromoFashiBanner2Action($request)
    {
        try {
            if ($request->getValue('delete_image')) {
                $this->config->Set('fashi_promo_banner_2_img', '');
                $bannerUrl = '/i/noimg.png';
            } else {
                if (empty($_FILES['additional-banner']['tmp_name'])) {
                    $this->respondAjaxError('No image was selected to upload.');
                }
                $uploadResult = $this->uploadAdditionalBanners();
                if (isset($uploadResult['additional-banner'][0])) {
                    if (isset($uploadResult['additional-banner'][0]->url)) {
                        $bannerUrl = $uploadResult['additional-banner'][0]->url;
                        $this->config->Set('fashi_promo_banner_2_img', $bannerUrl);
                    } else if (isset($uploadResult['additional-banner'][0]->error)) {
                        $this->respondAjaxError($uploadResult['additional-banner'][0]->error);
                    }
                } else {
                    $this->respondAjaxError('Unknown error occured while uploading image. Try again.');
                }
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array(
            'url' => $bannerUrl,
        ));
    }

    public function saveAdditionalFashiBanner1Action($request)
    {
        try {
            if ($request->getValue('delete_image')) {
                $this->config->Set('fashi_banner_1_img', '');
                $bannerUrl = '/i/noimg.png';
            } else {
                if (empty($_FILES['additional-banner']['tmp_name'])) {
                    $this->respondAjaxError('No image was selected to upload.');
                }
                $uploadResult = $this->uploadAdditionalBanners();
                if (isset($uploadResult['additional-banner'][0])) {
                    if (isset($uploadResult['additional-banner'][0]->url)) {
                        $bannerUrl = $uploadResult['additional-banner'][0]->url;
                        $this->config->Set('fashi_banner_1_img', $bannerUrl);
                    } else if (isset($uploadResult['additional-banner'][0]->error)) {
                        $this->respondAjaxError($uploadResult['additional-banner'][0]->error);
                    }
                } else {
                    $this->respondAjaxError('Unknown error occured while uploading image. Try again.');
                }
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array(
            'url' => $bannerUrl,
        ));
    }

    public function saveAdditionalFashiBanner2Action($request)
    {
        try {
            if ($request->getValue('delete_image')) {
                $this->config->Set('fashi_banner_2_img', '');
                $bannerUrl = '/i/noimg.png';
            } else {
                if (empty($_FILES['additional-banner']['tmp_name'])) {
                    $this->respondAjaxError('No image was selected to upload.');
                }
                $uploadResult = $this->uploadAdditionalBanners();
                if (isset($uploadResult['additional-banner'][0])) {
                    if (isset($uploadResult['additional-banner'][0]->url)) {
                        $bannerUrl = $uploadResult['additional-banner'][0]->url;
                        $this->config->Set('fashi_banner_2_img', $bannerUrl);
                    } else if (isset($uploadResult['additional-banner'][0]->error)) {
                        $this->respondAjaxError($uploadResult['additional-banner'][0]->error);
                    }
                } else {
                    $this->respondAjaxError('Unknown error occured while uploading image. Try again.');
                }
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array(
            'url' => $bannerUrl,
        ));
    }

    public function saveAdditionalFashiBanner3Action($request)
    {
        try {
            if ($request->getValue('delete_image')) {
                $this->config->Set('fashi_banner_3_img', '');
                $bannerUrl = '/i/noimg.png';
            } else {
                if (empty($_FILES['additional-banner']['tmp_name'])) {
                    $this->respondAjaxError('No image was selected to upload.');
                }
                $uploadResult = $this->uploadAdditionalBanners();
                if (isset($uploadResult['additional-banner'][0])) {
                    if (isset($uploadResult['additional-banner'][0]->url)) {
                        $bannerUrl = $uploadResult['additional-banner'][0]->url;
                        $this->config->Set('fashi_banner_3_img', $bannerUrl);
                    } else if (isset($uploadResult['additional-banner'][0]->error)) {
                        $this->respondAjaxError($uploadResult['additional-banner'][0]->error);
                    }
                } else {
                    $this->respondAjaxError('Unknown error occured while uploading image. Try again.');
                }
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array(
            'url' => $bannerUrl,
        ));
    }

    public function saveFashiSideBanner1Action($request)
    {
        try {
            if ($request->getValue('delete_image')) {
                $this->config->Set('fashi_side_banner_1_img', '');
                $bannerUrl = '/i/noimg.png';
            } else {
                if (empty($_FILES['additional-banner']['tmp_name'])) {
                    $this->respondAjaxError('No image was selected to upload.');
                }
                $uploadResult = $this->uploadAdditionalBanners();
                if (isset($uploadResult['additional-banner'][0])) {
                    if (isset($uploadResult['additional-banner'][0]->url)) {
                        $bannerUrl = $uploadResult['additional-banner'][0]->url;
                        $this->config->Set('fashi_side_banner_1_img', $bannerUrl);
                    } else if (isset($uploadResult['additional-banner'][0]->error)) {
                        $this->respondAjaxError($uploadResult['additional-banner'][0]->error);
                    }
                } else {
                    $this->respondAjaxError('Unknown error occured while uploading image. Try again.');
                }
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array(
            'url' => $bannerUrl,
        ));
    }

    public function saveFashiSideBanner2Action($request)
    {
        try {
            if ($request->getValue('delete_image')) {
                $this->config->Set('fashi_side_banner_2_img', '');
                $bannerUrl = '/i/noimg.png';
            } else {
                if (empty($_FILES['additional-banner']['tmp_name'])) {
                    $this->respondAjaxError('No image was selected to upload.');
                }
                $uploadResult = $this->uploadAdditionalBanners();
                if (isset($uploadResult['additional-banner'][0])) {
                    if (isset($uploadResult['additional-banner'][0]->url)) {
                        $bannerUrl = $uploadResult['additional-banner'][0]->url;
                        $this->config->Set('fashi_side_banner_2_img', $bannerUrl);
                    } else if (isset($uploadResult['additional-banner'][0]->error)) {
                        $this->respondAjaxError($uploadResult['additional-banner'][0]->error);
                    }
                } else {
                    $this->respondAjaxError('Unknown error occured while uploading image. Try again.');
                }
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array(
            'url' => $bannerUrl,
        ));
    }

    public function saveFashiSideBanner3Action($request)
    {
        try {
            if ($request->getValue('delete_image')) {
                $this->config->Set('fashi_side_banner_3_img', '');
                $bannerUrl = '/i/noimg.png';
            } else {
                if (empty($_FILES['additional-banner']['tmp_name'])) {
                    $this->respondAjaxError('No image was selected to upload.');
                }
                $uploadResult = $this->uploadAdditionalBanners();
                if (isset($uploadResult['additional-banner'][0])) {
                    if (isset($uploadResult['additional-banner'][0]->url)) {
                        $bannerUrl = $uploadResult['additional-banner'][0]->url;
                        $this->config->Set('fashi_side_banner_3_img', $bannerUrl);
                    } else if (isset($uploadResult['additional-banner'][0]->error)) {
                        $this->respondAjaxError($uploadResult['additional-banner'][0]->error);
                    }
                } else {
                    $this->respondAjaxError('Unknown error occured while uploading image. Try again.');
                }
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array(
            'url' => $bannerUrl,
        ));
    }

    public function systemAction()
    {
        $this->_template_path = 'site_config/system/';
        $this->_template = 'general';

        $this->instanceBillingInfo->initInstanceOptions(Session::getActiveAdminLang(), Session::get('sid'));
        $this->instanceBillingInfo->doRequests();
        $defaultItemProvider = $this->instanceBillingInfo->getInstanceOptions()->GetResult()->GetDefaultItemProvider();
        $providerInfoList = array();
        $messageSettings = array();

        try {
            if (RightsManager::hasRight('EmailServerManagement')) {
                OTAPILib2::GetMessageSettings(Session::getActiveAdminLang(), Session::get('sid'), 'true', $messageSettingsData);
                OTAPILib2::makeRequests();

                $messageSettings['settings'] = $messageSettingsData->getResult()->GetRawData();
                $messageSettings['updateUrl'] = $this->pageUrl->Add('do', 'saveMessageSettings')->Get();
            }
        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
        }

        try {
            $providerInfoList = $this->categoriesProvider->GetProviderInfoList();
        } catch (ServiceException $e) {
            $this->errorHandler->registerError($e);
        }

        $themes = General::getDesignThemes();

        $this->tpl->assign('themes', $themes);
        $this->tpl->assign('messageSettings', $messageSettings);
        $this->tpl->assign('defaultItemProvider', $defaultItemProvider);
        $this->tpl->assign('providerInfoList', $providerInfoList);
        $this->tpl->assign('emailServerInfoSettingsHtml', $this->getHtmlForSettingsEmailServerInfo());
        $this->tpl->assign('smsServiceInfoSettingsHtml', $this->getHtmlForSettingsSmsService());

        print $this->fetchTemplate();
    }

    public function saveSmsServiceSettingsAction($request)
    {
        $name = $request->post('name');
        $value = $request->post('value');
        $type = $request->get('type');
        $lang = Session::getActiveAdminLang();
        $sid = Session::get('sid');

        try {
            $params = explode(MetaUI::NODES_SEPARATOR, $name);
            $xmlParameters = MetaUI::generateSingleParamXml('SmsServiceSettingsUpdateData', $params, $value, $type);

            OTAPILib2::UpdateSmsServiceSettings($lang, $sid, $xmlParameters, $answer);
            OTAPILib2::makeRequests();
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse(array(), true);
    }

    public function getEmailServerInfoAction($request)
    {
        $html = '';

        try {
            $id = $request->getValue('id');

            OTAPILib2::GetEmailServerInfo(Session::getActiveAdminLang(), Session::get('sid'), $id, 'true', $settings);
            OTAPILib2::makeRequests();
            if ($settings && $settings->GetResult()) {
                $setting = $settings->GetResult()->GetRawData();
                $html = General::viewFetch('site_config/system/email_server_info', array(
                    'path' => TPL_PATH,
                    'vars' => array(
                        'serverId' => $id,
                        'setting' => $setting,
                        'updateSettingsUrl' => $this->pageUrl->Add('do', 'saveEmailServerInfo')->Get() . '&serverId=' . $id,
                        'testEmailServerUrl' => $this->pageUrl->Add('do', 'testEmailServer')->Get() . '&serverId=' . $id,
                        'selectEmailServerForBoxUrl' => $this->pageUrl->Add('do', 'selectEmailServerForBox')->Get() . '&serverId=' . $id,
                    ),
                ));
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse(array('html' => $html));
    }

    public function saveEmailServerInfoAction($request)
    {
        $name = $request->post('name');
        $value = $request->post('value');
        $type = $request->get('type');

        try {
            $params = explode(MetaUI::NODES_SEPARATOR, $name);
            if (is_array($params) && count($params) > 0) {
                $serverId = $request->get('serverId');
                $xmlUpdateData = MetaUI::generateSingleParamXml('EmailServerUpdateData', $params, $value, $type);
                $answer = false;
                OTAPILib2::UpdateEmailServerInfo(Session::getActiveAdminLang(), Session::get('sid'), $serverId, $xmlUpdateData, $answer);
                OTAPILib2::makeRequests();

                // для smtp используемого на сайте - тоже изменить значение
                if (
                    General::getConfigValue('email_smtp_server_id') == $serverId &&
                    !OTBase::isLimitedFunctional('save_smtp_to_site')
                ) {
                    $siteConfigRepository = New SiteConfigurationRepository($this->cms);
                    switch ($name) {
                        case 'Host':
                            $siteConfigRepository->Set('email_smtp_adress', $value);
                            break;
                        case 'Port':
                            $siteConfigRepository->Set('email_smtp_port', $value);
                            break;
                        case 'UserName':
                            $siteConfigRepository->Set('email_smtp_user', $value);
                            break;
                        case 'Password':
                            $siteConfigRepository->Set('email_smtp_pass', $value);
                            break;
                        case 'SslMode':
                            $siteConfigRepository->Set('email_smtp_security', strtolower($value));
                            break;
                        case 'FromDisplayName':
                            $siteConfigRepository->Set('notification_send_from_name', $value);
                            break;
                        case 'FromEmail':
                            $siteConfigRepository->Set('notification_send_from', $value);
                            break;

                        default:
                            break;
                    }
                }
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse(array(), true);
    }

    public function testEmailServerAction($request)
    {
        try {
            $serverId = $request->get('serverId');
            $recipientEmail = $request->getValue('email');

            OTAPILib2::TestEmailServer(Session::getActiveAdminLang(), Session::get('sid'), $serverId, $recipientEmail, $answer);
            OTAPILib2::makeRequests();
            
            if (General::getConfigValue('email_smtp_server_id') == $serverId) {
            	OTAPILib2::GetEmailServerInfo(Session::getActiveAdminLang(), Session::get('sid'), $serverId, 'true', $serverInfo);
            	OTAPILib2::makeRequests();
            	if ($serverInfo && $serverInfo->GetResult()) {
            		$serverInfo = $serverInfo->GetResult()->GetRawData();
            		$subject = LangAdmin::get('Smtp_test_message_subject');
                    $msg = LangAdmin::get('Smtp_test_message_body');
    	        	General::mail_utf8($recipientEmail, (string)$serverInfo->FromDisplayName, (string)$serverInfo->FromEmail, $subject, $msg, true, array('useException' => true));
            	}
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse(array('message' => LangAdmin::get('Test_letter_successfuly_sent')));
    }

    public function selectEmailServerForBoxAction($request)
    {
        try {
            $id = (string)$request->get('serverId');
            // получаем информацию о выбранном smtp сервере
            $serverInfo = null;
            OTAPILib2::GetEmailServerInfo(Session::getActiveAdminLang(), Session::get('sid'), $id, 'true', $serverInfo);
            OTAPILib2::makeRequests();
            if ($serverInfo && $serverInfo->GetResult()) {
                $serverInfo = $serverInfo->GetResult()->GetRawData();
                $siteConfigRepository = New SiteConfigurationRepository($this->cms);
                $siteConfigRepository->Set('email_smtp_server_id', $id);
                $siteConfigRepository->Set('email_smtp_adress', (string)$serverInfo->Host);
                $siteConfigRepository->Set('email_smtp_port', (string)$serverInfo->Port);
                $siteConfigRepository->Set('email_smtp_user', (string)$serverInfo->UserName);
                $siteConfigRepository->Set('email_smtp_pass', (string)$serverInfo->Password);
                $siteConfigRepository->Set('email_smtp_security', strtolower((string)$serverInfo->SslMode));
                $siteConfigRepository->Set('notification_send_from_name', (string)$serverInfo->FromDisplayName);
                $siteConfigRepository->Set('notification_send_from', (string)$serverInfo->FromEmail);
            } else {
                $this->respondAjaxError(LangAdmin::get('exe_error'));
            }
        } catch (ServiceException $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse();
    }

    private function assignCategoryStructureTypeSetting(){
        $webUi = $this->webUISettings->GetWebUISettings();
        $this->tpl->assign('categoryStructureType', (string)$webUi->Settings->SelectedCategoryStructureType);
    }
    
    private function assignSearchProvidersConfig()
    {
    	try {
    		$searchMethods = $this->otapilib->GetProviderSearchMethodInfoList();
    	} catch (ServiceException $e) {
    		$searchMethods = array();
    		Session::setError($e->getMessage(), $e->getErrorCode());
    	}
    
    	$newSearchMethods = array();
    	$searchMethodsList = array();
    	foreach ($searchMethods as $method) {
    		if ($method['Provider'] == 'Warehouse' && !CMS::IsFeatureEnabled('Warehouse')) {
    			continue;
    		}
    		$providerMethod = $method['Provider'] . '_' . $method['SearchMethod'];
    		$newSearchMethods[] = $providerMethod;
    		$searchMethodsList[$providerMethod] = $method;
    	}
    
    	$usedSearchSettings = General::getConfigValue('orderSearchMethods') ?
    	unserialize(General::getConfigValue('orderSearchMethods')) :
    	$newSearchMethods;
    
    	$unUsedSearchSettings = unserialize(General::getConfigValue('orderUnUsedSearchMethods'));
    	$unUsedSearchSettings = is_array($unUsedSearchSettings) ? $unUsedSearchSettings : array();
    
    	$newSearchSettings = array();
    	foreach ($newSearchMethods as $type) {
    		if ((! in_array($type, (array)$usedSearchSettings)) && (! in_array($type, (array)$unUsedSearchSettings))) {
    			$newSearchSettings[] = $type;
    		}
    	}
    	if (count($newSearchSettings)) {
    		$usedSearchSettings = array_merge((array)$usedSearchSettings, ($newSearchSettings));
    	}
    	$newUsedSearchSettings = array();
    	foreach ($usedSearchSettings as &$searchType) {
    		if ($searchType == 'Warehouse_Default' && !CMS::IsFeatureEnabled('Warehouse')) {
    			continue;
    		}
    		if (empty($searchMethodsList[$searchType]['DisplayName'])) {
    			continue;
    		}
    		$newUsedSearchSettings[] = $searchType;
    	}
    	$usedSearchSettings = $newUsedSearchSettings;
    
    	$this->tpl->assign('searchMethodsList', $searchMethodsList);
    	$this->tpl->assign('usedSearchSettings', $usedSearchSettings);
    	$this->tpl->assign('unUsedSearchSettings', $unUsedSearchSettings);
    }    

    private function assignOldDesignThemes(){
        $nameFile = 'name_' . Session::getActiveAdminLang() . '.txt';
        $themes = array();
        $themesCustom = array();
        $themes[] = array(
            'image' => 'css/theme-default.jpg',
            'image_preview' => 'css/theme-default-preview.jpg',
            'name' => '',
            'title' => ''.LangAdmin::get('Standard').'',
        );
        foreach (glob(CFG_APP_ROOT . '/css/theme/*') as $themeDir) {
            if (strripos($themeDir, 'custom-') === false) {
                $themeDirInfo = pathinfo($themeDir);
                $themeTitle = 'Untitled';
                if (file_exists($themeDir . '/' . $nameFile)) {
                    $themeTitle = file_get_contents($themeDir . '/' . $nameFile);
                } elseif (file_exists($themeDir . '/name.txt')) {
                    $themeTitle = file_get_contents($themeDir . '/name.txt');
                }
                $themes[] = array(
                    'image' => 'css/theme/' . $themeDirInfo['filename'] . '/' . $themeDirInfo['filename'] . '.jpg',
                    'image_preview' => 'css/theme/' . $themeDirInfo['filename'] . '/' . $themeDirInfo['filename'] . '-preview.jpg',
                    'name' => $themeDirInfo['filename'],
                    'title' => $themeTitle,
                );
            } else {                
                $themeDirInfo = pathinfo($themeDir);
                $themeTitle = 'Untitled Custom';
                if (file_exists($themeDir . '/' . $nameFile)) {
                    $themeTitle = file_get_contents($themeDir . '/' . $nameFile);
                } elseif (file_exists($themeDir . '/name.txt')) {
                    $themeTitle = file_get_contents($themeDir . '/name.txt');
                }
                $themesCustom[] = array(
                    'image' => 'css/theme/' . $themeDirInfo['filename'] . '/' . $themeDirInfo['filename'] . '.jpg',
                    'image_preview' => 'css/theme/' . $themeDirInfo['filename'] . '/' . $themeDirInfo['filename'] . '-preview.jpg',
                    'name' => $themeDirInfo['filename'],
                    'title' => $themeTitle,
                );
            }
        }
        $this->tpl->assign('availableSiteThemes', $themes);
        $this->tpl->assign('availableCustomSiteThemes', $themesCustom);
    }

    private function onOficialCatalogPerpageValidate($value)
    {
        if (! is_numeric($value)) {
            $this->respondAjaxError(LangAdmin::get("Value_must_be_numeric"));
            return false;
        }
        if (intval($value) > self::MAX_ITEMS_OFICIAL_CATALOG) {
            $this->sendAjaxResponse(array('error' => 1, 'message' => LangAdmin::get("High_limit_is_exceeded"), 'newValue' => self::MAX_ITEMS_OFICIAL_CATALOG));
            return false;
        }
        if (intval($value) < self::MIN_ITEMS_COUNT) {
            $this->sendAjaxResponse(array('error' => 1, 'message' => LangAdmin::get("Low_limit_is_exceeded"), 'newValue' => self::MIN_ITEMS_COUNT));
            return false;
        }
        return true;
    }

    private function onExtendedCatalogPerpageValidate($value)
    {
        if (! is_numeric($value)) {
            $this->respondAjaxError(LangAdmin::get("Value_must_be_numeric"));
            return false;
        }
        if (intval($value) > self::MAX_ITEMS_EXTENDED_CATALOG) {
            $this->sendAjaxResponse(array('error' => 1, 'message' => LangAdmin::get("High_limit_is_exceeded"), 'newValue' => self::MAX_ITEMS_EXTENDED_CATALOG));
            return false;
        }
        if (intval($value) < self::MIN_ITEMS_COUNT) {
            $this->sendAjaxResponse(array('error' => 1, 'message' => LangAdmin::get("Low_limit_is_exceeded"), 'newValue' => self::MIN_ITEMS_COUNT));
            return false;
        }
        return true;
    }

    private function onWarehouseCatalogPerpageValidate($value)
    {
        if (! is_numeric($value)) {
            $this->respondAjaxError(LangAdmin::get("Value_must_be_numeric"));
            return false;
        }
        if (intval($value) > self::MAX_ITEMS_WAREHOUSE_CATALOG) {
            $this->sendAjaxResponse(array('error' => 1, 'message' => LangAdmin::get("High_limit_is_exceeded"), 'newValue' => self::MAX_ITEMS_WAREHOUSE_CATALOG));
            return false;
        }
        if (intval($value) < self::MIN_ITEMS_COUNT) {
            $this->sendAjaxResponse(array('error' => 1, 'message' => LangAdmin::get("Low_limit_is_exceeded"), 'newValue' => self::MIN_ITEMS_COUNT));
            return false;
        }
        return true;
    }

    private function onCommentsCatalogPerpageValidate($value)
    {
        if (! is_numeric($value)) {
            $this->respondAjaxError(LangAdmin::get("Value_must_be_numeric"));
            return false;
        }
        if (intval($value) > self::MAX_ITEMS_COMMENTS_CATALOG) {
            $this->sendAjaxResponse(array('error' => 1, 'message' => LangAdmin::get("High_limit_is_exceeded"), 'newValue' => self::MAX_ITEMS_COMMENTS_CATALOG));
            return false;
        }
        if (intval($value) < self::MIN_ITEMS_COUNT) {
            $this->sendAjaxResponse(array('error' => 1, 'message' => LangAdmin::get("Low_limit_is_exceeded"), 'newValue' => self::MIN_ITEMS_COUNT));
            return false;
        }
        return true;
    }

    private function validateValues($request)
    {
        $name = $request->getValue('name');
        $value = $request->getValue('value');
        $name = explode('_', $request->getValue('name'));
        foreach ($name as &$n) {
            $n = ucfirst($n);
        }
        $name = implode('', $name);
        if (method_exists($this, 'on' . $name . 'Validate')) {
            return call_user_func(array($this, 'on' . $name . 'Validate'), $value);
        }

        return true;
    }
    
    private function onNotificationSendFromValidate($value)
    {
        $emails = str_replace(" ", "", $value);
        $emails = explode(';', $emails);
        foreach ($emails as $mail) {
            if (filter_var($mail, FILTER_VALIDATE_EMAIL) == FALSE) {
                $this->respondAjaxError(LangAdmin::get("Invalid_email_in_group"));
                return false;
            }
        }
        return true;        
    }

    private function onItemsWithCommentsValidate($value)
    {
        if (empty($value)) {
            return true;
        }
        if (! is_numeric($value)) {
            $this->respondAjaxError(LangAdmin::get("Value_must_be_numeric"));
            return false;
        }
        if (intval($value) > self::MAX_ITEMS_REVIEWED_COUNT) {
            $this->sendAjaxResponse(array('error' => 1, 'message' => LangAdmin::get("High_limit_is_exceeded"), 'newValue' => self::MAX_ITEMS_REVIEWED_COUNT));
            return false;
        }
        if (intval($value) < 0) {
            $this->sendAjaxResponse(array('error' => 1, 'message' => LangAdmin::get("Low_limit_is_exceeded"), 'newValue' => 0));
            return false;
        }
        return true;
    }

    private function onNewsCountPrintValidate($value)
    {
        if (empty($value)) {
            return true;
        }
        if (! is_numeric($value)) {
            $this->respondAjaxError(LangAdmin::get("Value_must_be_numeric"));
            return false;
        }
        if (intval($value) > self::MAX_NEWS_COUNT) {
            $this->sendAjaxResponse(array('error' => 1, 'message' => LangAdmin::get("High_limit_is_exceeded"), 'newValue' => self::MAX_NEWS_COUNT));
            return false;
        }
        if (intval($value) < 0) {
            $this->sendAjaxResponse(array('error' => 1, 'message' => LangAdmin::get("Low_limit_is_exceeded"), 'newValue' => 0));
            return false;
        }
        return true;
    }

    private function onItemsWithVendorValidate($value)
    {
        if (empty($value)) {
            return true;
        }
        if (! is_numeric($value)) {
            $this->respondAjaxError(LangAdmin::get("Value_must_be_numeric"));
            return false;
        }

        if (intval($value) > self::MAX_VENDORS_COUNT) {
            $this->sendAjaxResponse(array('error' => 1, 'message' => LangAdmin::get("High_limit_is_exceeded"), 'newValue' => self::MAX_VENDORS_COUNT));
            return false;
        }
        if (intval($value) < 0) {
            $this->sendAjaxResponse(array('error' => 1, 'message' => LangAdmin::get("Low_limit_is_exceeded"), 'newValue' => 0));
            return false;
        }
        return true;
    }

    private function onBrandWithBestValidate($value)
    {
        if (empty($value)) {
            return true;
        }
        if (! is_numeric($value)) {
            $this->respondAjaxError(LangAdmin::get("Value_must_be_numeric"));
            return false;
        }
        if (intval($value) < 0) {
            $this->sendAjaxResponse(array('error' => 1, 'message' => LangAdmin::get("Low_limit_is_exceeded"), 'newValue' => 0));
            return false;
        }
        return true;
    }

    private function onItemsWithBestValidate($value)
    {
        if (empty($value)) {
            return true;
        }
        if (! is_numeric($value)) {
            $this->respondAjaxError(LangAdmin::get("Value_must_be_numeric"));
            return false;
        }
        if (intval($value) > self::MAX_BEST_ITEMS_COUNT) {
            $this->sendAjaxResponse(array('error' => 1, 'message' => LangAdmin::get("High_limit_is_exceeded"), 'newValue' => self::MAX_BEST_ITEMS_COUNT));
            return false;
        }
        if (intval($value) < 0) {
            $this->sendAjaxResponse(array('error' => 1, 'message' => LangAdmin::get("Low_limit_is_exceeded"), 'newValue' => 0));
            return false;
        }
        return true;
    }


    private function onItemsWithLastValidate($value)
    {
        if (empty($value)) {
            return true;
        }
        if (! is_numeric($value)) {
            $this->respondAjaxError(LangAdmin::get("Value_must_be_numeric"));
            return false;
        }
        if (intval($value) > self::MAX_LAST_ITEMS_COUNT) {
            $this->sendAjaxResponse(array('error' => 1, 'message' => LangAdmin::get("High_limit_is_exceeded"), 'newValue' => self::MAX_LAST_ITEMS_COUNT));
            return false;
        }
        if (intval($value) < 0) {
            $this->sendAjaxResponse(array('error' => 1, 'message' => LangAdmin::get("Low_limit_is_exceeded"), 'newValue' => 0));
            return false;
        }
        return true;
    }

    public function changepasswordAction($request)
    {
        try {
            if (! $request->post('new_password')) {
                $this->respondAjaxError(LangAdmin::get("New_password_error"));
                return false;
            }
            if (! $request->post('old_password')) {
                $this->respondAjaxError(LangAdmin::get("Old_password_error"));
                return false;
            }

            $this->otapilib->setErrorsAsExceptionsOn();
            $sid = Session::get('sid');
            $this->otapilib->ChangeOperatorPassword($sid, $request->post('old_password'), $request->post('new_password'));
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse();
    }

    /**
     * @param RequestWrapper $request
     */
    public function saveAction($request)
    {
        if (! $this->validateValues($request)) {
            return false;
        }

        $name = explode('_', $request->getValue('name'));
        foreach ($name as &$n) {
            $n = ucfirst($n);
        }
        $name = implode('', $name);
        if (method_exists($this, 'before' . $name . 'Save')) {
            $result = call_user_func(array($this, 'before' . $name . 'Save'), $request);
            if (isset($result['continue']) && $result['continue'] == false) {
                $this->respondAjaxError($result['message']);
                return false;
            }
        }

        $this->config->Set($request->getValue('name'), $request->getValue('value'));
        if (method_exists($this, 'on' . $name . 'Save')) {
            call_user_func(array($this, 'on' . $name . 'Save'), $request);
        }

        $this->sendAjaxResponse(array(
            'result' => 'ok',
        ));
    }

    /**
     * @param RequestWrapper $request
     */
    public function saveLimitItemsByCatalogAction($request)
    {
        try {
            $request->set('LimitItemsByCatalog', $request->getValue('value'));
            $this->shipmentProvider->SaveCase($request);
            InstanceProvider::getObject()->clearCommonInstanceOptionsInfoCache();
            /**
             * Если выставлен запрет, то необходимо сменить на Internal
             */
            if ($request->getValue('LimitItemsByCatalog') == 1) {
                if ($this->config->Get('search_category_mode') == self::CATALOG_MODE_EXTERNAL) {
                    $this->config->Set('search_category_mode', self::CATALOG_MODE_INTERNAL);
                }
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse(array(
            'reload' => 1,
            'timeout' => 5000,
            'message' => LangAdmin::get('settings_saved_and_reload')
        ));
    }

    public function beforeSimplifiedRegistrationSave($request)
    {
        // Если включена "Упрощенная регистрация" (simplified_registration)
        // то опция "Дополнительная активация через емейл" (IsEmailConfirmationUsed) должна быть выключена
        $result = array('continue' => true);
        if ((int)$request->getValue('value') == 1 && $this->instanceOptionsInfo->GetIsEmailConfirmationUsed() == 'true') {
            $result = array(
                'continue'  => false,
                'message'   => LangAdmin::get('simplified_registration_error_help'));
        }
        return $result;
    }

    public function beforeSearchCategoryModeSave($request)
    {
        $result = array('continue' => true);
        $showCase = $this->shipmentProvider->GetShowCase();
        if ($showCase->Settings->LimitItemsByCatalog == 'true') {
            if ($request->getValue('value') == self::CATALOG_MODE_EXTERNAL) {
                $result = array(
                    'continue'  => false,
                    'message'   => LangAdmin::get('Select_catalog_limit_error'));
            }
        }

        return $result;
    }

    public function onStructureTypeSave($request)
    {
        $this->webUISettings->SetCategoryMode($request->getValue('value'));
    }

    /**
     * @param RequestWrapper $request
     */
    public function saveShowcaseAction($request)
    {
        try {
            $request->set($request->getValue('name'), $request->getValue('value'));
            $this->shipmentProvider->SaveCase($request);
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse();
    }

    /**
     * @param RequestWrapper $request
     */
    public function saveBankAction($request)
    {
        $bankForm = $request->getValue('bank');
        if (! is_array($bankForm)) {
            $this->respondAjaxError('Invalid form data given.');
        }
        $validator = new Validator(array(
            'name_of_payee'              => $bankForm['name_of_payee'],
            'INN_of_payee'               => $bankForm['INN_of_payee'],
            'account_number_of_payee'    => $bankForm['account_number_of_payee'],
            'bank_name_of_payee'         => $bankForm['bank_name_of_payee'],
            'bank_identification_code'   => $bankForm['bank_identification_code'],
            'correspondent_bank_account' => $bankForm['correspondent_bank_account'],
            'description_of_payment'     => $bankForm['description_of_payment']
        ));
        $validator->addRule(new NotEmptyString(), 'name_of_payee', LangAdmin::get('Field_must_be_filled'));
        $validator->addRule(new NotEmptyString(), 'bank_name_of_payee', LangAdmin::get('Field_must_be_filled'));
        $validator->addRule(new NotEmptyString(), 'description_of_payment', LangAdmin::get('Field_must_be_filled'));
        $validator->addRule(new Regexp('#^(\d{10}|\d{12})$#'), 'INN_of_payee', LangAdmin::get('Inn_must_be_10_12_numbers'));
        $validator->addRule(new Regexp('#^\d{20}$#'), 'account_number_of_payee', LangAdmin::get('Account_payee_must_be_20_numbers'));
        $validator->addRule(new Regexp('#^\d{20}$#'), 'correspondent_bank_account', LangAdmin::get('Correspond_account_must_be_20_numbers'));
        $validator->addRule(new Regexp('#^\d{8,9}$#'), 'bank_identification_code', LangAdmin::get('BIK_must_be_8_9_numbers'));

        if (! $validator->validate()) {
            $this->respondAjaxError($validator->getErrors());
        }
        try {
            foreach ($validator->getData() as $key => $value) {
                $this->config->Set($key, $value);
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse();
    }

       
    //проверям на запись статистики В сервисы
    public function onItemsWithPopularSave($request)
    {
        if (($this->config->Get('items_with_popular') == 0) && ($this->config->Get('items_with_last') == 0)) {
            $this->webUISettings->UpdateStatisticsSettings('false');            	
		} else {
            $this->webUISettings->UpdateStatisticsSettings('true');			
		}
    }
    
    //проверям на запись статистики В сервисы
    public function onItemsWithLastSave($request)
    {
        if (($this->config->Get('items_with_popular') == 0) && ($this->config->Get('items_with_last') == 0)) {
            $this->webUISettings->UpdateStatisticsSettings('false');            	
		} else {
            $this->webUISettings->UpdateStatisticsSettings('true');			
		}
    }

    // TODO: Убрать дублирование с WarehouseProducts::uploadImage
    private function uploadImage()
    {
        $uploader = new UploadHandler(array(
            'param_name' => 'input_image',
            'image_versions' => array(
                '' => array(
                    'max_width' => null,
                    'max_height' => null,
                    'jpeg_quality' => 95
                ),
            ),
        ), false, null, '/uploaded/logo/');
        return $uploader->post(false);
    }
    
    private function uploadBgImage()
    {
        $uploader = new UploadHandler(array(
            'param_name' => 'input_image',
            'image_versions' => array(
                '' => array(
                    'max_width' => null,
                    'max_height' => null,
                    'jpeg_quality' => 95
                ),
            ),
        ), false, null, '/uploaded/bg/');
        return $uploader->post(false);
    }
    

    public function onMenuCountLvl1Save()
    {
        $this->onMenuTypeSave();
    }

    public function onMenuCountLvl2Save()
    {
        $this->onMenuTypeSave();
    }

    public function onMenuTypeSave()
    {
        Cacher::rRmDir(CFG_APP_ROOT . '/cache/menushortnew');
        Cacher::rRmDir(CFG_APP_ROOT . '/cache/Menu');
    }

    public function onSimpleSearchPerpageSave()
    {
        Cacher::rRmDir(CFG_APP_ROOT . '/cache/multi_search');
    }

    public function onTmallSearchPerpageSave()
    {
        Cacher::rRmDir(CFG_APP_ROOT . '/cache/multi_search');
    }

    public function onWarehouseSearchPerpageSave()
    {
        Cacher::rRmDir(CFG_APP_ROOT . '/cache/multi_search');
    }

    public function onCommentsSearchPerpageSave()
    {
        Cacher::rRmDir(CFG_APP_ROOT . '/cache/multi_search');
    }
    
    public function onUseMultiSearchSave()
    {
        try {
            $this->otapilib->ResetInstanceCaches();
            Cacher::rRmDir(CFG_APP_ROOT . '/cache/', false);
        } catch (ServiceException $e) {
            ErrorHandler::registerError($e);
        }
    }

    public function onIsNewPlatformSave($request)
    {
        // отключаем "Упрощенная регистрация"
        if ($this->config->Get('simplified_registration') == 1 && !$this->config->Get('is_old_platform')) {
            $this->config->Set('simplified_registration', 0);
        }
    }

    public function onGoogleRecaptchaKeyPublicSave($request)
    {
        $this->config->Set('google_recaptcha_key_public', trim($this->config->Get('google_recaptcha_key_public')));
    }

    public function onGoogleRecaptchaKeySecretSave ($request)
    {
        $this->config->Set('google_recaptcha_key_secret', trim($this->config->Get('google_recaptcha_key_secret')));
    }

    public function contentsAction($request)
    {
        $this->_template_path = 'site_config/contents/';
        $this->_template = 'contents';
        print $this->fetchTemplate();    }


    public function usersAction()
    {
        $this->_template_path = 'site_config/users/';
        $this->_template = 'general';

        try {
            $pageUrl = new UrlWrapper();
            $pageUrl->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
            $pageUrl->DeleteKey('cmd')->Add('cmd', 'SiteConfiguration')->DeleteKey('do')->Add('do', 'saveSmsConfirmation');


            $this->tpl->assign('smsConfirmation', $this->GetSmsConfirmation());
            $this->tpl->assign('smsConfirmationUrl', $pageUrl->Get());
            $this->tpl->assign('IsEmailConfirmationUsed', $this->instanceOptionsInfo->GetIsEmailConfirmationUsed());
            $this->tpl->assign('commonInstanceOptions', $this->instanceOptionsInfo->GetCommonInstanceOptionsInfo());
        } catch (Exception $e) {
            $this->tpl->assign('IsEmailConfirmationUsed', false);
            $this->tpl->assign('commonInstanceOptions', false);
            ErrorHandler::registerError($e);
        }
        print $this->fetchTemplate();
    }

    private function GetSmsConfirmation()
    {
        $lang = Session::getActiveAdminLang();
        $sid = Session::get('sid');

        OTAPILib2::GetUserSettings($lang, $sid, 'True', $answer);
        OTAPILib2::makeRequests();

        return $answer->GetResult()->GetRawData();
    }

    public function saveSmsConfirmationAction($request)
    {
        $name = $request->post('name');
        $value = $request->post('value');
        $type = $request->get('type');
        $lang = Session::getActiveAdminLang();
        $sid = Session::get('sid');

        try {
            $params = explode(MetaUI::NODES_SEPARATOR, $name);
            $xmlParameters = MetaUI::generateSingleParamXml('UserSettingsUpdateData', $params, $value, $type);

            OTAPILib2::UpdateUserSettings($lang, $sid, $xmlParameters, $answer);
            OTAPILib2::makeRequests();
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse(array(), true);
    }

    /**
     * @param RequestWrapper $request
     */
    public function saveInstanceOptionsAction($request)
    {
        try {
            $request->set($request->getValue('name'), $request->getValue('value'));

            // Если включена "Упрощенная регистрация" (simplified_registration)
            // то опция "Дополнительная активация через емейл" (IsEmailConfirmationUsed) должна быть выключена
            if ($request->getValue('name') == 'IsEmailConfirmationUsed' 
                && $request->getValue('value') == 'true' 
                && General::getConfigValue('simplified_registration')) {
                throw new Exception(LangAdmin::get('IsEmailConfirmationUsed_error_help'));
            }

            $this->instanceOptionsInfo->SaveOptions($request);
            $this->getOtapilib()->ResetInstanceCaches();
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse();
    }

    public function securityAction($request)
    {
        $this->_template_path = 'site_config/security/';
        $this->_template = 'general';

        if ($request->valueExists('use_https')) {
            $this->config->Set('use_https', $request->getValue('use_https'));
            $protocol = $request->getValue('use_https') ? 'https' : 'http';
            $this->redirect($protocol . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
        }
        $this->otapilib->setErrorsAsExceptionsOn();
        $sid = Session::get('sid');

        $ipaccessconfig = $this->otapilib->GetInstanceOptionsInfo($sid);
        $this->tpl->assign('AllowedIPs', $ipaccessconfig['AllowedIPs']);
        $this->tpl->assign('IsIPCheckUsed', $ipaccessconfig['IsIPCheckUsed']);

        print $this->fetchTemplate();

    }

    public function deleteIpAction($request)
    {
        try {
            $this->otapilib->setErrorsAsExceptionsOn();
            $sid = Session::get('sid');
            $xml = $this->generateDelXML($request->post('ip'));
            $this->otapilib->UpdateInstanceOptions($sid, $xml);
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse();
    }


    /**
     * @param $data     Передаваемые параметры
     * @return string   Сгенерированная xml строка
     */
    private function generateDelXML($data)
    {
        $sid = Session::get('sid');
        $ipaccessconfig = $this->otapilib->GetInstanceOptionsInfo($sid);
        $xml = '<InstanceOptionsData><AllowedIPs>';
        foreach ($ipaccessconfig['AllowedIPs'] as $item) {
            if ($item != htmlspecialchars($data)) {
                $xml .= '<string>' . $item . '</string>';
            }
        }
        $xml .= '</AllowedIPs></InstanceOptionsData>';
        return $xml;
    }

    public function addIpAction($request)
    {
        if ($request->post('ip') == '') {
            $this->respondAjaxError(LangAdmin::get('Value_must_not_be_empty'));
        }
        try {
            $this->otapilib->setErrorsAsExceptionsOn();
            $sid = Session::get('sid');
            $xml = $this->generateAddXML($request->post('ip'));
            $this->otapilib->UpdateInstanceOptions($sid, $xml);
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse();
    }

    public function addCurrentIpAction()
    {
        try {
            $this->otapilib->setErrorsAsExceptionsOn();
            $sid = Session::get('sid');
            $ip = $this->getIpForOutsideCall();
            $xml = $this->generateAddXML($ip);
            $this->otapilib->UpdateInstanceOptions($sid, $xml);
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse();
    }

    private function getIpForOutsideCall()
    {
        if (is_function_enabled('shell_exec')) {
            $ip = shell_exec("curl -s checkip.dyndns.org | sed -e 's/.*Current IP Address: //' -e 's/<.*$//'");
            if ($ip) {
                return $ip;
            }
            $ip = shell_exec("wget -qO- http://ipecho.net/plain ; echo");
            if ($ip) {
                return $ip;
            }

        }
        return $_SERVER['SERVER_ADDR'];
    }

    /**
     * @param $data     Передаваемые параметры
     * @return string   Сгенерированная xml строка
     */
    private function generateAddXML($data)
    {
        $sid = Session::get('sid');
        $ipaccessconfig = $this->otapilib->GetInstanceOptionsInfo($sid);
        $xml = '<InstanceOptionsData><AllowedIPs>';
        foreach ($ipaccessconfig['AllowedIPs'] as $item) {
            if ($item != htmlspecialchars($data)) {
                $xml .= '<string>' . $item . '</string>';
            }
        }
        $xml .= '<string>' . htmlspecialchars($data) . '</string>';
        $xml .= '</AllowedIPs></InstanceOptionsData>';
        return $xml;
    }

    public function SwitchOnIpAction()
    {
        try {
            $this->otapilib->setErrorsAsExceptionsOn();
            $sid = Session::get('sid');
            $xml = $this->generateSwitchXML('true');
            $this->otapilib->UpdateInstanceOptions($sid, $xml);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e);
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse();
    }

    public function SwitchOffIpAction()
    {
        try {
            $this->otapilib->setErrorsAsExceptionsOn();
            $sid = Session::get('sid');
            $xml = $this->generateSwitchXML('false');
            $this->otapilib->UpdateInstanceOptions($sid, $xml);
        } catch (ServiceException $e) {
            $this->respondAjaxError($e);
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse();
    }

    /**
     * @param $data     Передаваемые параметры
     * @return string   Сгенерированная xml строка
     */
    private function generateSwitchXML($data)
    {
        $sid = Session::get('sid');
        $ipaccessconfig = $this->otapilib->GetInstanceOptionsInfo($sid);
        $xml = '<InstanceOptionsData><IsIPCheckUsed>' . htmlspecialchars($data) . '</IsIPCheckUsed><AllowedIPs>';
        foreach ($ipaccessconfig['AllowedIPs'] as $item) {
            $xml .= '<string>' . $item . '</string>';
        }
        $xml .= '</AllowedIPs></InstanceOptionsData>';
        return $xml;
    }

    public function compileScssAction()
    {
        try {
            require_once CFG_LIB_ROOT . "/vendor/scssphp/scss.inc.php";
            require_once CFG_LIB_ROOT . "/vendor/scss-compass/compass.inc.php";

            $themeDir = General::getThemeDir();

            $scss = new scssc();
            new scss_compass($scss);
            $scss->addImportPath($themeDir . '/css/sass/'); // основной файл стилей screen.scss
            $scss->addImportPath(CFG_APP_ROOT . '/lib/vendor/scss-compass/stylesheets/'); // дополнительные библиотеки
            $scss->setFormatter('scss_formatter');

            $css = $scss->compile('
                @import "compass.scss";
                @import "variables.default.scss";
                @import "screen.scss";
            ');

            file_put_contents($themeDir . '/css/screen.css', $css);
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse();
    }

    public function compileCustomScssAction()
    {
        if (General::compileCustomScss()) {
            $this->sendAjaxResponse();
        }

        $this->respondAjaxError(LangAdmin::get('exe_error'));
    }

    public function saveBackgroundAction($request)
    {
        $currentTheme = General::getConfigValue('design_theme', General::$baseTheme);

        try {
            if ($request->getValue('delete_image')) {
                $this->config->Set('custom_scss_body_background_image_'.$currentTheme, '');
                $backgroundUrl = '/i/nobg.png';
            } else {
                if (empty($_FILES['input_image']['tmp_name'])) {
                    $this->respondAjaxError(LangAdmin::get('No_files_to_upload'));
                }
                $uploadResult = $this->uploadBgImage();
                if (isset($uploadResult['input_image'][0])) {
                    if (isset($uploadResult['input_image'][0]->url)) {
                        $backgroundUrl = $uploadResult['input_image'][0]->url;
                        $this->config->Set('custom_scss_body_background_image_'.$currentTheme, $backgroundUrl);
                    } else if (isset($uploadResult['input_image'][0]->error)) {
                        $this->respondAjaxError($uploadResult['input_image'][0]->error);
                    }
                } else {
                    $this->respondAjaxError('Unknown error occured while uploading image. Try again.');
                }
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->sendAjaxResponse(array(
            'url' => $backgroundUrl,
        ));
    }

    public function deleteCustomCssAction()
    {
        try {
            $themeDir = General::getThemeDir();;
            // переносим файл screen-custom.css в папку backup
            if (
                !file_exists($themeDir . '/css/screen-custom.css') ||
                rename($themeDir . '/css/screen-custom.css', CFG_APP_ROOT . '/backup/screen-custom.css')
            ) {
                $this->sendAjaxResponse();
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }

        $this->respondAjaxError(LangAdmin::get('exe_error'));
    }

    public function saveMessageSettingsAction($request)
    {
        $name = $request->post('name');
        $value = $request->post('value');
        $type = $request->get('type');

        try {
            $params = explode(MetaUI::NODES_SEPARATOR, $name);
            if (is_array($params) && count($params) > 0) {
                $xmlParameters = MetaUI::generateSingleParamXml('MessengerSettingsUpdateData', $params, $value, $type);
                $answer = false;
                OTAPILib2::UpdateMessageSettings(Session::getActiveAdminLang(), Session::get('sid'), $xmlParameters, $answer);
                OTAPILib2::makeRequests();

                /* продублировать некоторые настройки в коробку */
                $name = MetaUI::decodeParametersString(end($params));
                $saveInDbSettings = array('NotificationEmails' => 'notification_email', 'NotificationLanguage' => 'admin_letter_lang');
                if ($answer && isset($saveInDbSettings[$name])) {
                    if (is_array($value)) {
                        $value = implode(';', array_unique($value));
                    }
                    $this->config->Set($saveInDbSettings[$name], $value);
                }
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }
        $this->sendAjaxResponse();
    }

    private function beforeDisplayAsCarouselSave()
    {
        $this->clearSetsLayout();
    }

    private function beforeDesignThemeSave()
    {
        $this->clearSetsLayout();
    }

    private function clearSetsLayout()
    {
        $cache = new FileAndMysqlMemoryCache(new CMS());
        $routes = [
            'sets/render-categories-sets',
            'sets/render-recommend-sets',
            'sets/render-popular-sets',
            'sets/render-last-viewed-sets',
            'sets/render-warehouse-sets',
            'sets/render-items-with-review-sets',
            'sets/render-vendor-sets',
            'sets/render-brands-sets',
        ];
        foreach ($routes as $route) {
            $cacheKey = 'ContentSection:' . $route . '_' . Session::getActiveLang();
            $cache->DelCacheEl($cacheKey);
        }
    }
}
