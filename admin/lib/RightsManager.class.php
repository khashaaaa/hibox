<?php

OTBase::import('system.lib.Cache');
OTBase::import('system.lib.cache.Key');
OTBase::import('system.admin.lib.otapi_providers.RolesProvider');

class RightsManager
{
    public static $rights = array();

    public static $role;

    // специальное право, которое разрешает выполнение action без проверки на наличие каких либо прав
    const RIGHT_AUTH_USER = '*';

    /**
     * Конфигурация доступа к контроллерам
     *
     * Структура:
     * 'ControllerName' => array(
     *      'ActionName' => array('Right_1', 'Right_2')
     *  ),
     *
     * Описание:
     * В контроллере ControllerName метод ActionName доступен только при наличии права Right_1 или Right_2.
     * ЗАМЕЧАНИЕ 1. Если Контроллер не объявлен в конфиге, то доступ к нему запрещен.
     * ЗАМЕЧАНИЕ 2. Если в качестве названия метода (action_name) указать
     * символ '*' - это правило будет действовать по умолчанию для всех методов, ограничения
     * которых не указаны явно.
     * ЗАМЕЧАНИЕ 3. Если объявлено право self::RIGHT_AUTH_USER - метод ActionName считается доступным
     * для пользователя.
     */
    public static $dependencies = array(
        'Orders' => array(
            'default' => array('OrderManagement'),
            'list' => array('OrderManagement'),
            'searchOrders' => array('ViewOrder'),
            'searchOrdersLines' => array('ViewOrderLine'),
            'getOrderItems' => array('ViewOrder'),
            'view' => array('ViewOrder'),
            'runExportedPackagesConfirming' => array('OrderPackageManagement'),
            'printdeclaration' => array('ViewOrder'),
            'getOrderItemComments' => array('ViewOrder'),
            'deleteItemFromOrder' => array('EditOrderLine'),
            'getItemConfig' => array('EditOrderLine'),
            'setItemConfig' => array('EditOrderLine'),
            'changeItemPrice' => array('EditOrderLine'),
            'changeItemWeight' => array('EditOrderLine'),
            'splitItemQuantity' => array('EditOrderLine'),
            'changeOrderWeight' => array('EditOrder'),
            'updateDeliveryMode' => array('EditOrder','EditPackage'),
            'changeOperatorComment' => array('EditOrder'),
            'changeOrderAdditionalInfo' => array('EditOrder'),
            'getOrderInfo' => array('ViewOrder'),
            'changeItemsStatus' => array('EditOrderLine'),
            'changeItemStatus' => array('EditOrderLine'),
            'cancelOrder' => array('EditOrder'),
            'closeOrder' => array('EditOrder'),
            'printPackageReceipt' => array('ViewPackage'),
            'exportPackage' => array('ViewPackage'),
            'deletePackage' => array('EditPackage'),
            'package' => array('ViewPackage'),
            'moveItemsToPackage' => array('EditPackage'),
            'savePackage' => array('EditPackage'),
            'paymentReserve' => array('OrderPayment'),
            'purchaseItems' => array('ViewOrder'),
            'restoreOrder' => array('EditOrder'),
            'removeOrderItemImage' => array('EditOrderLine'),
            'uploadOrderItemImage' => array('EditOrderLine'),
            'getOrdersListForMerge' => array('ViewOrder'),
            'mergeOrders' => array('EditOrder'),
            'editInternalDelivery' => array('EditOrderLine'),
            'editOriginalPrice' => array('EditOrderLine'),
            'recalculationItemPrice' => array('EditOrderLine'),
            'changeDeliveryAddress' => array('EditOrder'),
            'getPackageTracking' => array('ViewPackage')
        ),

        'Pricing' => array(
            'default' => array('ViewCurrencyRateSettings'),
            'cost' => array('ViewCurrencyRateSettings'),
            'saveCost' => array('EditCurrencyRateSettings', 'EditPriceFormation'),
            'saveCurrency' => array('EditCurrencyRateSettings'),
            'RemoveRate' => array('EditCurrencyRateSettings'),
            'banker' => array('ViewPriceFormation'),
            'priceGroupCategories' => array('EditPriceFormation'),
            'addPriceGroupCategory' => array('EditPriceFormation'),
            'deletePriceGroupCategory' => array('EditPriceFormation'),
            'deletePriceGroup' => array('EditPriceFormation'),
            'addPriceGroup' => array('EditPriceFormation'),
            'editPriceGroup' => array('EditPriceFormation'),
            'savePriceGroup' => array('EditPriceFormation'),
        ),
        'Discount' => array(
            'default' => array('Discount'),
            'editDiscount' => array('EditDiscounts'),
            'saveDiscount' => array('EditDiscounts'),
            'groupInfo' => array('Discount'),
            'deleteOrReplaceUserDiscount' => array('EditDiscounts'),
            'deleteGroup' => array('EditDiscounts'),
            'addUserDiscount' => array('EditDiscounts'),
            'getUsersForDiscount' => array('Discount')
        ),

        'Promo' => array(
            'default' => array('Seo'),
            'social' => array('Social'),
            'referral' => array('ReferralProgram'),
            'newsletters' => array('Newsletter'),
            'subscribers' => array('ManageListSubscribers'),
            'config' => array('NewsletterSettings'),
            'save' => array('Seo', 'Social'),
            'generateSiteMap' => array('Seo'),
        ),
        'Referral' => array(
            '*' => array('ReferralProgram'),
            'config' => array('ReferralProgramSettings'),
            'addGroupForm' => array('ReferralProgramCategories'),
            'addGroup' => array('ReferralProgramCategories'),
            'editGroupForm' => array('ReferralProgramCategories'),
            'editGroup' => array('ReferralProgramCategories'),
            'delGroup' => array('ReferralProgramCategories'),
            'replaceGroup' => array('ReferralProgramCategories'),
            'getCount' => array('ReferralProgramCategories'),
            'viewGroup' => array('ReferralProgramCategories'),
            'removeUser' => array('ReferralProgramUsers'),
            'searchUsers' => array('ReferralProgramUsers'),
            'addUserToGroup' => array('ReferralProgramUsers'),
        ),
        'Newsletters' => array(
            'default' => array('ViewNewsletter'),
            'add' => array('EditNewsletter'),
            'report' => array('EditNewsletter'),
            'edit' => array('EditNewsletter'),
            'stop' => array('EditNewsletter'),
            'restart' => array('EditNewsletter'),
            'start' => array('EditNewsletter'),
            'delete' => array('EditNewsletter'),
            'save' => array('EditNewsletter'),
            'config' => array('NewsletterSettings'),
            'test' => array('NewsletterSettings'),
        ),
        'Subscribers' => array(
            '*' => array('ManageListSubscribers')
        ),

        'Contents' => array(
            'default' => array('SiteContent'),
            'addNewPage' => array('SiteContent'),
            'editPage' => array('SiteContent'),
            'updateContentPage' => array('SiteContent'),
            'savePage' => array('SiteContent'),
            'deletePage' => array('SiteContent'),
            'navigation' => array('SiteContent'),
            'saveMenu' => array('SiteContent'),
            'contentPages' => array('SiteContent'),
            'addNewContentPage' => array('SiteContent'),
            'editMobileContentPage' => array('SiteContent'),
            'deleteContentPage' => array('SiteContent'),

            'news' => array('News'),
            'deleteNews' => array('News'),
            'editNews' => array('News'),
            'addNews' => array('News'),
            'updateContentNews' => array('News'),
            'saveNews' => array('News'),

            'banners' => array('SiteContent'),
            'deleteBanner' => array('SiteContent'),
            'bannerForm' => array('SiteContent'),
            'saveBanner' => array('SiteContent'),
            'saveBannerSort' => array('SiteContent'),

            'shopComments' => array('ShopComments'),
            'activateComment' => array('ManageShopComments'),
            'bulkActivateComment' => array('ManageShopComments'),
            'removeComment' => array('ManageShopComments'),
            'bulkRemoveComment' => array('ManageShopComments'),
            'answerComment' => array('ManageShopComments'),
        ),
        'Blog' => array(
            '*' => array('Digest'),
        ),

        'Catalog' => array(
            'default' => array('CatalogManagement')
        ),
        'Categories' => array(
            'default' => array('CatalogManagement'),
            'getCategories' => array('CatalogManagement'),
            'checkCategoryAlias' => array('EditCatalog'),
            'createCategory' => array('EditCatalog'),
            'updateCategory' => array('EditCatalog'),
            'visibleCategory' => array('EditCatalog'),
            'orderCategory' => array('EditCatalog'),
            'moveCategory' => array('EditCatalog'),
            'moveCategories' => array('EditCatalog'),
            'removeCategory' => array('EditCatalog'),
            'exportTxt' => array('CatalogManagement'),
            'file' => array('CatalogManagement'),
            'exportXml' => array('CatalogManagement'),
            'import' => array('EditCatalog'),
            'getCategoryData' => array('CatalogManagement'),
            'saveFilter' => array('EditCatalog'),
            'getHint' => array('CatalogManagement'),
            'copyPaste' => array('EditCatalog'),
            'getSearchParamsForm' => array('CatalogManagement'),
            'getCategoriesByProvider' => array('CatalogManagement'),
            'getCategoryFiltersData' => array('CatalogManagement'),
            'uploadImage' => array('EditCatalog')
        ),
        'Sets' => array(
            'default' => array('ItemRatingManagement'),
            'clearSet' => array('ItemRatingManagement'),
            'saveItemsOrder' => array('ItemRatingManagement'),
            'sellers' => array('ItemRatingManagement'),
            'moreSellers' => array('ItemRatingManagement'),
            'items' => array('ItemRatingManagement'),
            'moreItems' => array('ItemRatingManagement'),
            'getItemInfo' => array('ItemRatingManagement'),
            'addSetsBrand' => array('ItemRatingManagement'),
            'addSetsSeller' => array('ItemRatingManagement'),
            'updateSetsSeller' => array('ItemRatingManagement'),
            'updateSetsSellerInOtapilib' => array('ItemRatingManagement'),
            'addSetsItem' => array('ItemRatingManagement'),
            'addSetsItemsFile' => array('ItemRatingManagement'),
            'deleteItem' => array('ItemRatingManagement'),
            'updateSetsItem' => array('ItemRatingManagement'),
            'categories' => array('ItemRatingManagement'),
            'getAllCategories' => array('ItemRatingManagement'),
            'categoriesSettings' => array('ItemRatingManagement'),
            'autosets' => array('ItemRatingManagement'),
            'startAutosetsScan' => array('ItemRatingManagement'),
            'updateAutosetsSettings' => array('ItemRatingManagement'),
            'saveSiteCategories' => array('ItemRatingManagement'),
            'setItemPosition' => array('ItemRatingManagement'),
            'export' => array('SelectorExportingManagement'),
            'updateSelectorExportingSettings' => array('SelectorExportingManagement'),
            'startExportScan' => array('SelectorExportingManagement'),
            'recommendedCategory' => array('ItemRatingManagement'),
            'addRecommendedCategory' => array('ItemRatingManagement'),
            'deleteRecommendedCategory' => array('ItemRatingManagement'),
            'moreRecommendedCategories' => array('ItemRatingManagement'),
            'clearSetRecommendedCategories' => array('ItemRatingManagement'),
            'setRecommendedCategoryPosition' => array('ItemRatingManagement'),
            'updateSetsRecommendedCategory' => array('EditCatalog'),
            'delImageRecommendedCategory' => array('EditCatalog'),
            'editSeller' => array('EditCatalog'),
            'checkVendor' => array('EditCatalog'),
        ),
        'Reviews' => array(
            'default' => array('ReviewManagement'),
            'getReviewForOperator' => array('ReviewManagement'),
            'acceptReview' => array('EditReviews'),
            'deleteReview' => array('EditReviews'),
            'settings' => array('EditReviews'),
            'updateSettings' => array('EditReviews'),
            'rewardItemReview' => array('OrderManagement'),
            'addAnswerToItemReview' => array('EditReviews')
        ),
        'Brands' => array(
            'default' => array('BrandsManagement'),
            'hideBrand' => array('EditBrands'),
            'showBrand' => array('BrandsManagement'),
            'editBrand' => array('EditBrands'),
            'addBrand' => array('EditBrands'),
            'deleteBrand' => array('EditBrands'),
            'saveBrand' => array('EditBrands'),
            'searchBrands' => array('BrandsManagement'),
            'addBrands' => array('EditBrands')
        ),
        'Pristroy' => array(
            'default' => array('CatalogManagement'),
            'edit' => array('EditCatalog'),
            'save' => array('EditCatalog'),
            'reject' => array('EditCatalog'),
            'approve' => array('EditCatalog'),
            'remove' => array('EditCatalog'),
            'approveBulk' => array('EditCatalog'),
            'rejectBulk' => array('EditCatalog'),
            'removeBulk' => array('EditCatalog')
        ),
        'Warehouse' => array(
            'default' => array('Warehouse', 'ViewWarehouseInfo'),
            'getCategories' => array('Warehouse'),
            'createCategory' => array('EditWarehouseInfo'),
            'renameCategory' => array('EditWarehouseInfo'),
            'removeCategory' => array('EditWarehouseInfo'),
            'moveCategory' => array('EditWarehouseInfo'),
            'import' => array('EditWarehouseInfo'),
            'startImport' => array('EditWarehouseInfo'),
            'getItemDescription' => array('EditWarehouseInfo')
        ),
        'WarehouseProducts' => array(
            'default' => array('Warehouse'),
            'getCategories' => array('Warehouse'),
            'addProduct' => array('EditWarehouseInfo'),
            'editProduct' => array('EditWarehouseInfo'),
            'saveProduct' => array('EditWarehouseInfo'),
            'saveInline' => array('EditWarehouseInfo'),
            'delete' => array('EditWarehouseInfo'),
            'getItemData' => array('Warehouse'),
            'saveConfig' => array('EditWarehouseInfo'),
            'saveItemConfigurators' => array('EditWarehouseInfo'),
            'AddProperty' => array('EditWarehouseInfo'),
            'AddPropertyValue' => array('EditWarehouseInfo'),
            'saveItemConfigurations' => array('EditWarehouseInfo'),
            'removeProperty' => array('EditWarehouseInfo'),
            'removePropertyValue' => array('EditWarehouseInfo'),
            'productSetAllowed' => array('EditWarehouseInfo')
        ),
        'Restrictions' => array(
            'default' => array('StatisticsManagement'),
            'categories' => array('StatisticsManagement'),
            'sellers' => array('StatisticsManagement'),
            'searches' => array('StatisticsManagement'),
            'brands' => array('StatisticsManagement'),
            'addRestriction' => array('EditStatisticsSettings'),
            'deleteRestriction' => array('EditStatisticsSettings')
        ),
        'Items' => array(
            'default' => array('EditCatalog'),
            'editItem' => array('EditCatalog'),
            'edit' => array('EditCatalog'),
            'updateSettings' => array('EditCatalog'),
            'saveFilter' => array('EditCatalog'),
            'saveItem' => array('EditCatalog'),
            'resetItem' => array('EditCatalog')
        ),

        'SiteConfiguration' => array(
            'default' => array('SiteDesign'),
            'orders' => array('OrderSetting'),
            'updateCollectionsSettings' => array('OrderSetting'),
            'updateOrderSettings' => array('OrderSetting'),
            'bank' => array('OrderSetting'),
            'saveLogo' => array('SiteDesign'),
            'saveAdditionalBanner1' => array('SiteDesign'),
            'saveAdditionalBanner2' => array('SiteDesign'),
            'savePromoFashiBanner1' => array('SiteDesign'),
            'savePromoFashiBanner2' => array('SiteDesign'),
            'saveAdditionalFashiBanner1' => array('SiteDesign'),
            'saveAdditionalFashiBanner2' => array('SiteDesign'),
            'saveAdditionalFashiBanner3' => array('SiteDesign'),
            'saveFashiSideBanner1' => array('SiteDesign'),
            'saveFashiSideBanner2' => array('SiteDesign'),
            'saveFashiSideBanner3' => array('SiteDesign'),
            'saveAdvertisingBanner' => array('SiteDesign'),
            'system' => array('SystemSettings', 'EmailServerManagement'),
            'getEmailServerInfo' => array('SystemSettings'),
            'saveEmailServerInfo' => array('SystemSettings'),
            'testEmailServer' => array('SystemSettings'),
            'saveSmsServiceSettings' => array('SystemSettings'),
            'saveSmsConfirmation' => array('SystemSettings'),
            'selectEmailServerForBox' => array('SystemSettings'),
            'changepassword' => array('SystemSettings'),
            'save' => array('SiteConfigurations'),
            'saveLimitItemsByCatalog' => array('SystemSettings'),
            'saveShowcase' => array('SystemSettings'),
            'saveBank' => array('OrderSetting'),
            'users' => array('InstanceUsersAdministration'),
            'saveInstanceOptions' => array('SystemSettings'),
            'security' => array('SystemSettings', 'InstanceUserSelfManagement'),
            'deleteIp' => array('SystemSettings'),
            'addIp' => array('SystemSettings'),
            'addCurrentIp' => array('SystemSettings'),
            'SwitchOnIp' => array('SystemSettings'),
            'SwitchOffIp' => array('SystemSettings'),
            'compileScss' => array('SiteDesign'),
            'compileCustomScss' => array('SiteDesign'),
            'saveBackground' => array('SiteDesign'),
            'deleteCustomCss' => array('SiteDesign'),
            'saveMessageSettings' => array('SystemSettings'),
            'removeConfig' => array('SiteDesign'),
            'ordersProfile' => array('SystemSettings')
        ),
        'Shipment' => array(
            'default' => array('ViewDeliveryCalculator'),
            'internal' => array('ViewDeliveryCalculator'),
            'tariffs' => array('ViewDeliveryCalculator'),
            'edittariff' => array('EditDeliveryCalculator'),
            'addTariff' => array('EditDeliveryCalculator'),
            'editDelivery' => array('EditDeliveryCalculator'),
            'getIntegrationDeliveryModes' => array('ViewDeliveryCalculator'),
            'searchDeliveryModes' => array('ViewDeliveryCalculator'),
            'saveDelivery' => array('EditDeliveryCalculator'),
            'deleteDelivery' => array('EditDeliveryCalculator'),
            'saveTariff' => array('EditDeliveryCalculator'),
            'deleteTariff' => array('EditDeliveryCalculator'),
            'saveInternal' => array('EditDeliveryCalculator'),
            'deliveryService' => array('ViewDeliveryCalculator'),
            'getDeliveryService' => array('ViewDeliveryCalculator'),
            'updateTemplate' => array('EditDeliveryCalculator')
        ),
        'Translations' => array(
            '*' => array(self::RIGHT_AUTH_USER),
        ),
        'MultilingualSettings' => array(
            '*' => array('MultipleLanguages'),
        ),
        'ProviderConfiguration' => array(
            '*' => array('IntegrationSettings'),
        ),
        'LettersTemplates' => array(
            'default' => array('ViewMessageTemplates'),
            'getTemplate' => array('ViewMessageTemplates'),
            'updateTemplate' => array('EditMessageTemplates'),
            'testTemplate' => array('EditMessageTemplates'),
        ),
        'Update' => array(
            '*' => array('SystemSettings'),
        ),

        'Reports' => array(
            'default' => array('ServiceStats'),
            'calls' => array('ServiceStats'),
            'billing' => array('Finance'),
            'billingForPeriod' => array('Finance'),
            'viewBill' => array('Finance'),
            'exportBill' => array('Finance'),
            'getStatistic' => array('ServiceStats'),
            'operationLog' => array('ViewInstanceUserActionsLog'),
            'siteSpeed' => array('ViewInstanceUserActionsLog'),
            'getCurrentBoxStatistics' => array('ViewInstanceUserActionsLog'),
            'getBoxArchiveStatistics' => array('ViewInstanceUserActionsLog'),
            'userLog' => array('ViewInstanceUserActionsLog'),
            'orderLog' => array('ViewInstanceUserActionsLog'),
            'finance' => array('Finance'),
            'financeDetails' => array('OrderManagement'),
            'exportFinanceDetails' => array('Finance'),
            'dowmloadExportData' => array('Finance'),
            'calculation' => array('Finance'),
            'exportCalculation' => array('Finance'),
            'saveInline' => array('Finance')
        ),

        'PluginsUtil' => array(
            '*' => array('SitePlugins'),
        ),

        'Support' => array(
            'default' => array('Support'),
            'other' => array('Support'),
            'getChat' => array('Support'),
            'sendMessage' => array('ManageSupportRequests'),
            'closeTicket' => array('ManageSupportRequests'),
            'addTicketMessage' => array('ManageSupportRequests'),
            'markTicketMessagesRead' => array('Support')
        ),

        'ActivitiesUtil' => array(
            '*' => array(self::RIGHT_AUTH_USER),
        ),
        'CacheSettings' => array(
            '*' => array(self::RIGHT_AUTH_USER),
        ),
        'AdminLanguage' => array(
            '*' => array(self::RIGHT_AUTH_USER),
        ),
        'MetaUiUtil' => array(
            '*' => array(self::RIGHT_AUTH_USER),
        ),

        'Users' => array(
            'default' => array('UserManagement'),
            'list' => array('UserManagement'),
            'addUser' => array('EditUsers'),
            'editUser' => array('EditUsers'),
            'profile' => array('OrderManagement', 'ViewUserProfiles'),
            'getUserOrders' => array('ViewOrder'),
            'getAccountInfo' => array('OrderManagement'),
            'updateAccount' => array('OrderManagement'),
            'savePassword' => array('EditUsers'),
            'saveUser' => array('EditUsers'),
            'banUser' => array('EditUsers'),
            'unbanUser' => array('EditUsers'),
            'verifyUserEmail' => array('EditUsers'),
            'verifyUserPhone' => array('EditUsers'),
            'removeUser' => array('EditUsers'),
            'bulkBanUser' => array('EditUsers'),
            'bulkUnbanUser' => array('EditUsers'),
            'bulkVerifyUserEmail' => array('EditUsers'),
            'bulkVerifyUserPhone' => array('EditUsers'),
            'bulkRemoveUser' => array('EditUsers'),
            'ajaxSearch' => array('UserManagement'),
            'loginAsUser' => array('UserManagement'),
            'exportUsers' => array('UserManagement'),
            'dowmloadExportUsersFile' => array('UserManagement')
        ),
        'Administrators' => array(
            'default' => array('InstanceUsersAdministration'),
            'add' => array('EditInstanceAdministrationSettings'),
            'remove' => array('EditInstanceAdministrationSettings'),
            'save' => array('EditInstanceAdministrationSettings'),
            'edit' => array('EditInstanceAdministrationSettings')
        ),
        'Roles' => array(
            'default' => array('InstanceUsersAdministration'),
            'show' => array('InstanceUsersAdministration'),
            'edit' => array('EditInstanceAdministrationSettings'),
            'getRightsList' => array('InstanceUsersAdministration'),
            'save' => array('EditInstanceAdministrationSettings'),
            'remove' => array('EditInstanceAdministrationSettings')
        ),
    );

    // Полный список возможных прав
    const RIGHT_DISCOUNT                            = 'Discount';
    const RIGHT_VIEWDISCOUNTS                       = 'ViewDiscounts';
    const RIGHT_EDITDISCOUNTS                       = 'EditDiscounts';
    const RIGHT_MARKET                              = 'Market';
    const RIGHT_CURRENCYRATEENHANCE                 = 'CurrencyRateEnhance';
    const RIGHT_EDITPRICEFORMATION                  = 'EditPriceFormation';
    const RIGHT_VIEWPRICEFORMATION                  = 'ViewPriceFormation';
    const RIGHT_INSTANCEUSERSELFMANAGEMENT          = 'InstanceUserSelfManagement';
    const RIGHT_INSTANCEUSERSADMINISTRATION         = 'InstanceUsersAdministration';
    const RIGHT_VIEWINSTANCEADMINISTRATIONSETTINGS  = 'ViewInstanceAdministrationSettings';
    const RIGHT_EDITINSTANCEADMINISTRATIONSETTINGS  = 'EditInstanceAdministrationSettings';
    const RIGHT_BRANDSMANAGEMENT                    = 'BrandsManagement';
    const RIGHT_VIEWBRANDS                          = 'ViewBrands';
    const RIGHT_EDITBRANDS                          = 'EditBrands';
    const RIGHT_CATALOGMANAGEMENT                   = 'CatalogManagement';
    const RIGHT_EDITCATALOG                         = 'EditCatalog';
    const RIGHT_VIEWCATALOG                         = 'ViewCatalog';
    const RIGHT_SIMPLEPRICEFORMATION                = 'SimplePriceFormation';
    const RIGHT_VIEWCURRENCYRATESETTINGS            = 'ViewCurrencyRateSettings';
    const RIGHT_EDITCURRENCYRATESETTINGS            = 'EditCurrencyRateSettings';
    const RIGHT_ITEMRATINGMANAGEMENT                = 'ItemRatingManagement';
    const RIGHT_USERMANAGEMENT                      = 'UserManagement';
    const RIGHT_VIEWUSERS                           = 'ViewUsers';
    const RIGHT_EDITUSERS                           = 'EditUsers';
    const RIGHT_STATISTICSMANAGEMENT                = 'StatisticsManagement';
    const RIGHT_EDITSTATISTICSSETTINGS              = 'EditStatisticsSettings';
    const RIGHT_VIEWSTATISTICSSETTINGS              = 'ViewStatisticsSettings';
    const RIGHT_ORDERPACKAGEMANAGEMENT              = 'OrderPackageManagement';
    const RIGHT_VIEWPACKAGE                         = 'ViewPackage';
    const RIGHT_EDITPACKAGE                         = 'EditPackage';
    const RIGHT_ORDERPAYMENT                        = 'OrderPayment';
    const RIGHT_ORDER                               = 'Order';
    const RIGHT_ORDERDELIVERYMANAGEMENT             = 'OrderDeliveryManagement';
    const RIGHT_VIEWDELIVERYCALCULATOR              = 'ViewDeliveryCalculator';
    const RIGHT_EDITDELIVERYCALCULATOR              = 'EditDeliveryCalculator';
    const RIGHT_EDITORDER                           = 'EditOrder';
    const RIGHT_ADMINPANEL                          = 'AdminPanel';
    const RIGHT_EDITUSERPROFILES                    = 'EditUserProfiles';
    const RIGHT_VIEWUSERPROFILES                    = 'ViewUserProfiles';
    const RIGHT_VIEWINSTANCEUSERACTIONSLOG          = 'ViewInstanceUserActionsLog';
    const RIGHT_WAREHOUSE                           = 'Warehouse';
    const RIGHT_EDITWAREHOUSEINFO                   = 'EditWarehouseInfo';
    const RIGHT_VIEWWAREHOUSEINFO                   = 'ViewWarehouseInfo';
    const RIGHT_ORDERMANAGEMENT                     = 'OrderManagement';
    const RIGHT_VIEWORDERLINE                       = 'ViewOrderLine';
    const RIGHT_EDITORDERLINE                       = 'EditOrderLine';
    const RIGHT_VIEWORDER                           = 'ViewOrder';
    const RIGHT_PROVIDERORDERSINTEGRATION           = 'ProviderOrdersIntegration';
    const RIGHT_DIRECTPAYMENTMANAGEMENT             = 'DirectPaymentManagement';

    // Список контроллеров

    // Контент
    const CMD_CONTENT               = 'contents';
    const CMD_SUPPORT               = 'support';

    // Промо
    const CMD_PROMO                 = 'promo';
    const CMD_SUBSCRIBERS           = 'subscribers';
    const CMD_NEWSLETTERS           = 'newsletters';
    const CMD_REFERRAL              = 'referral';

    // Кофигурация сайта
    const CMD_SITE_CONF             = 'siteconfiguration';
    const CMD_TRANSLATIONS          = 'translations';
    const CMD_MULTILINGUALSETTINGS  = 'multilingualsettings';

    const CMD_ORDERS                = 'orders';
    const CMD_PRICING               = 'pricing';
    const CMD_USERS                 = 'users';

    const CMD_REPORTS               = 'reports';
    const CMD_SHIPMENT              = 'shipment';
    const CMD_IPACCESS              = 'ipaccess';
    const CMD_UPDATE                = 'update';

    const CMD_PLUGINSUTIL           = 'pluginsutil';

    // Товары на складе
    const CMD_WAREHOUSE             = 'warehouse';
    const CMD_WAREHOUSEPRODUCTS     = 'warehouseproducts';

    // Управление пользователями
    const CMD_ADMINISTRATORS        = 'administrators';
    const CMD_ROLES                 = 'roles';

    // Каталог
    const CMD_CATALOG               = 'catalog';
    const CMD_CATEGORIES            = 'categories';
    const CMD_PRISTROY              = 'pristroy';
    const CMD_REVIEWS               = 'reviews';
    const CMD_SETS                  = 'sets';
    const CMD_BRANDS                = 'brands';
    const CMD_RESTRICTIONS          = 'restrictions';

    const ACTION_DEFAULT        = 'default';
    const ACTION_VIEW           = 'view';
    const ACTION_ORDERS         = 'orders';
    const ACTION_OPERATIONLOG   = 'operationlog';
    const ACTION_BILLING        = 'billing';
    const ACTION_SYSTEM         = 'system';
    const ACTION_SOCIAL         = 'social';
    const ACTION_REFERRAL       = 'referral';
    const ACTION_SAVE           = 'save';
    const ACTION_USERS          = 'users';

    // Шаблонные роли
    const ROLE_SUPERADMIN       = 'SuperAdmin';
    const ROLE_OPERATOR         = 'Operator';
    const ROLE_SUPEROPERATOR    = 'SuperOperator';
    const ROLE_CONTENTMANAGER   = 'ContentManager';
    const ROLE_SUPERMANAGER     = 'SuperManager';
    const ROLE_FINANCIER        = 'Financier';
    const ROLE_SELLFREE        = 'SellFree';

    private static $availableRoles = array(
        self::ROLE_SUPERADMIN,
        self::ROLE_OPERATOR,
        self::ROLE_SUPEROPERATOR,
        self::ROLE_CONTENTMANAGER,
        self::ROLE_FINANCIER,
        self::ROLE_SELLFREE,
    );

    // Список фич, отсутствие которых в правах юзера, говорит о том,
    // что фича ему недоступна, даже если глобальна она включена.
    public static $restrictedFeatures = array(
        'Discount',
        'Market',
        'CurrencyRateEnhance',
        'AdminPanel',
        'InstanceUsersAdministration',
        'Order',
        'Warehouse',
    );

    private static function getDependencies()
    {
        return self::$dependencies;
    }

    public static function defaultPath($cmd = null)
    {
        $dependencies = self::getDependencies();

        // найти defaultPath в выбранном контроллере
        if (! is_null($cmd)) {
            $cmd = ucfirst($cmd);

            // вернуть defaultAction, если доступно
            if (method_exists($cmd, self::ACTION_DEFAULT . 'Action') && self::isAvailableCmd($cmd, self::ACTION_DEFAULT)) {
                return array(
                    'cmd' => $cmd,
                    'do'  => self::ACTION_DEFAULT,
                );
            } else {
                // вернуть первый доступный action
                $loverCaseCmd = strtolower($cmd);
                $loverCaseDependencies = array_change_key_case($dependencies);

                if (isset($loverCaseDependencies[$loverCaseCmd])) {
                    $actions = $loverCaseDependencies[$loverCaseCmd];
                    foreach ($actions as $action => $rights) {
                        $do = $action != '*' ? $action : self::ACTION_DEFAULT;

                        if (method_exists($cmd, $do . 'Action') && self::isAvailableCmd($cmd, $action)) {
                            return array(
                                'cmd' => $cmd,
                                'do'  => $do,
                            );
                        }
                    }
                }
            }
        }

        // найти defaultPath перебором всех контроллеров
        foreach ($dependencies as $cmd => $actions) {
            foreach ($actions as $action => $rights) {
                $do = $action != '*' ? $action : self::ACTION_DEFAULT;

                if (method_exists($cmd, $do . 'Action') && self::isAvailableCmd($cmd, $action)) {
                    return array(
                        'cmd' => $cmd,
                        'do'  => $do,
                    );
                }
            }
        }

        return array(
            'cmd' => self::CMD_CONTENT,
            'do'  => self::ACTION_DEFAULT,
        );
    }

    /**
     * Определить доступен ли вызов метода контроллера
     * для текущей роли
     *
     * @param string $cmd
     * @param string $do
     * @return bool
     */
    public static function isAvailableCmd($cmd, $do = null)
    {
        $cmd = strtolower($cmd);
        $controllers = array_change_key_case(self::getDependencies());

        if (isset($controllers[$cmd])) {

            $rightsList = array();
            if (is_null($do)) {
                // все права перечисленные для выбранного контроллера
                foreach ($controllers[$cmd] as $action) {
                    $rightsList = array_merge($rightsList, $action);
                }
            } else {
                $actions = array_change_key_case($controllers[$cmd]);
                $do  = strtolower($do);

                if (isset($actions[$do])) {
                    // права для выбранного метода
                    $rightsList = $actions[$do];
                } elseif (isset($actions['*'])) {
                    // права по умолчанию для всего контроллера
                    $rightsList = $actions['*'];
                }
            }

            if (in_array(self::RIGHT_AUTH_USER, $rightsList)) {
                return true;
            }

            foreach ($rightsList as $right) {
                if (self::hasRight($right)) {
                    return true;
                }
            }
        }

        return false;
    }

    /*
     * TODO: избавиться от данного метода
     */
    public static function isFeatureAvailable($feature)
    {
        if (! in_array($feature, self::$restrictedFeatures)) {
            return true;
        }
        return self::hasRight($feature);
    }

    /*
     *
     */
    public static function hasRight($rightName)
    {
        $rights = self::getCurrentRights();
        return in_array($rightName, $rights);
    }

    public static function getCurrentRole()
    {
        if (Session::get('role')) {
            return Session::get('role');
        }
        return self::$role;
    }

    public static function setCurrentRole($role)
    {
        if (! empty($role[0])) {
            self::$role = $role[0]['Name'];
            Session::set('role', self::$role);
        }
    }

    public static function getCurrentRights()
    {
        if (! Session::get('sid')) {
            return array();
        }

        $cacher = new Cache('Rights' . RightsManager::getCurrentRole());

        if (! $cacher->has()) {
            $rolesProvider = new RolesProvider();
            $rolesProvider->setUserRoleAndRights(Session::get('sid'));
        }

        return $cacher->has() ? $cacher->get() : array();
    }

    public static function isSuperAdmin()
    {
        return self::getCurrentRole() === self::ROLE_SUPERADMIN;
    }

    public static function setCurrentRights($rights)
    {
        $cacher = new Cache('Rights' . RightsManager::getCurrentRole());
        $cacher->set($rights);
    }

}
