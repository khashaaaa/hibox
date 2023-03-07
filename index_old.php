<?php

Users::AutoLogin();

Plugins::invokeEvent('onPageStartLoad');

//подключаем базу данных
global $opentaoCMS, $otapilib, $HSTemplate;
$opentaoCMS = new SafedCMS();

if(file_exists(dirname(__FILE__).'/custom/custom.php')){
    require dirname(__FILE__).'/custom/custom.php';
}


//Сперва необходимо проверить смену языка до обработки остального
if(SCRIPT_NAME == 'setlang') {
    $activeLang = (isset($_REQUEST['lang'])) ? $_REQUEST['lang'] : 'en';
    Session::setActiveLang($activeLang);
    User::getObject()->setActiveLang($activeLang);
    if (isset($_REQUEST['from'])) {
        header('Location: ' . $_REQUEST['from']);
    } else {
        header('Location: /');
    }
    die;
}

$showPage = true; // флаг для нового app, позволяющий отменить старый render

if (
    General::getConfigValue('site_temporary_unavailable') && !Session::get('sid')
    && !in_array(SCRIPT_NAME, array('robo_request', 'onpay_request', 'get_user_data_summary', 'menushortajax', 'search_ajax', 'cron'))
) {
    General::setGroupId('site_unavailable');

    General::$isContent = true;
    $CFG_CREATE_BLOCKS = array ('CrumbsNew', 'HeaderNew', 'FooterNew', 'ContentData');
    $GLOBALS['CFG_CREATE_BLOCKS'] = $CFG_CREATE_BLOCKS;
    define('CFG_PAGE_TEMPLATE', 'cms');
} else {
    // В зависимости от страницы подгружаем необходимые блоки и задаем шаблон страницы
    switch(SCRIPT_NAME)
    {
        case 'test_furl':
            die('OK');
            break;
        case 'style':
            define('NO_DEBUG_STRONG', 1);
            break;
        case Plugins::onAddScriptProcessorCheck(SCRIPT_NAME, ''):
            $CFG_CREATE_BLOCKS = Plugins::onAddScriptProcessor(SCRIPT_NAME, '');
            break;
        case Plugins::onAddScriptProcessorCheck(SCRIPT_NAME, '_custom'):
            $CFG_CREATE_BLOCKS = Plugins::onAddScriptProcessor(SCRIPT_NAME, '_custom');
            break;
        case 'calculator':
            $CFG_CREATE_BLOCKS = array ('HeaderNew', 'FooterNew', 'Calculator');
            define('CFG_PAGE_TEMPLATE', 'calculator');
            break;
        case 'referral':
            $CFG_CREATE_BLOCKS = array ('CrumbsNew', 'HeaderNew', 'FooterNew', 'Menu', 'Referral');
            define('CFG_PAGE_TEMPLATE', 'referral');
            break;
        case 'outputofmoney':
            $CFG_CREATE_BLOCKS = array ('CrumbsNew', 'HeaderNew', 'OutputOfMoney', 'FooterNew', 'Menu');
            define('CFG_PAGE_TEMPLATE', 'outputofmoney');
            break;
        case 'set_currency':
            Session::set('currency', RequestWrapper::get('c'));
            Session::set('ManualVAL', "1");
            if(@$_SERVER['HTTP_REFERER'])
                header('Location: '.@$_SERVER['HTTP_REFERER']);
            die();
            break;
        case 'getpromotions':
            $CFG_CREATE_BLOCKS = array ();
            define('CFG_PAGE_TEMPLATE', '');

            $itemid = RequestWrapper::get('itemid');

            $promo = $otapilib->GetItemPromotionsWithAttempts($itemid, 10);
            if ($promo === false){
                $promo = $otapilib->error_code;
                print json_encode($promo);
            }
            else{
                if (! empty($promo[0]) && ! empty($promo[0]['ConfiguredItems'])) {
                    foreach($promo[0]['ConfiguredItems'] as &$val){
                        $newConvertedPriceList = array();
                        foreach($val['Price']['PriceWithoutDelivery']['ConvertedPriceList']['DisplayedMoneys'] as $displayedPrice){
                            $displayedPrice['Val'] = (float)$displayedPrice;
                            $newConvertedPriceList[] = array(
                                'Sign' => (string)$displayedPrice['Sign'],
                                'Val' => (string)$displayedPrice['Val'],
                            );
                        }
                        $val['Price']['PriceWithoutDelivery']['ConvertedPriceList'] = $newConvertedPriceList;
                    }
                    $promo[0]['ConfiguredItems']['PromoId'] = $promo[0]['Id'];
                    print json_encode($promo[0]['ConfiguredItems']);
                }
                elseif(! empty($promo[0])){
                    print json_encode(array(
                        'PromoId' => $promo[0]['Id']
                    ));
                }
                else {
                    print json_encode(array());
                }
            }
            die();
            break;
        case 'webcron':
            $cron = new WebCron();
            $cron->Process();
            die;
            break;
        case 'get_delivery':
            global $otapilib;
            $modes = $otapilib->GetDeliveryModesWithPrice(RequestWrapper::get('code'), RequestWrapper::get('weight'));
            if ($modes && is_array($modes)) {
                foreach ($modes as &$m) {
                    foreach($m['FullPrice']['ConvertedPriceList']['DisplayedMoneys'] as $i=>$d){
                        $price = $d;
                        $price->price = (float)$d;
                        $m['FullPrice']['ConvertedPriceList']['DisplayedMoneys'][$i] = $price;
                    }
                }
            }
            print json_encode(array('success' => (bool)$modes, 'data' => $modes));
            die();
            break;
        case 'robo_request':
            $R = new Robokassa();
            $R->handleRequest();
            die();
            break;
        case 'onpay_request':
            $R = new Onpay();
            $R->handleRequest();
            die();
            break;
        
        case 'support':
            $CFG_CREATE_BLOCKS = array ('CrumbsNew', 'HeaderNew', 'Menu', 'Support', 'Banners', 'FooterNew');
            if(RequestWrapper::get('mode') == 'new'){
                $HSTemplate->assignGlobal('support_title', Lang::get('new_message'));
            }
            elseif(RequestWrapper::get('mode') == 'view' || !RequestWrapper::get('mode')){
                $HSTemplate->assignGlobal('support_title', Lang::get('messages_list'));
            }
            define('CFG_PAGE_TEMPLATE', 'support');
            break;

        case 'content':
            $CFG_CREATE_BLOCKS = array ('HeaderNew', 'FooterNew', 'Menu', 'Content');
            define('CFG_PAGE_TEMPLATE', 'content');
            break;

        case 'register':
            $CFG_CREATE_BLOCKS = array ('CrumbsNew', 'HeaderNew', 'Register', 'FooterNew');
            define('CFG_PAGE_TEMPLATE', 'register');
            break;

        case 'profile':
            $CFG_CREATE_BLOCKS = array ('CrumbsNew', 'HeaderNew', 'Menu', 'Profile', 'FooterNew');
            define('CFG_PAGE_TEMPLATE', 'profile');
            break;

        case 'privateoffice':
            if (RequestWrapper::get('print')=='Y')
                $CFG_CREATE_BLOCKS = array ('HeaderNew', 'FooterNew', 'PrivateOffice');
            elseif(CMS::IsFeatureEnabled('Newsletter')) {
                if (RequestWrapper::getParamExists('subscribe') && ! RequestWrapper::get('subscribe')){
                    $CFG_CREATE_BLOCKS = array ('CrumbsNew', 'HeaderNew', 'FooterNew', 'Subscribe');
                } else {
                    $CFG_CREATE_BLOCKS = array ('CrumbsNew', 'HeaderNew', 'FooterNew', 'PrivateOffice', 'Menu', 'Subscribe');
                }
            } else
                $CFG_CREATE_BLOCKS = array ('CrumbsNew', 'HeaderNew', 'FooterNew', 'PrivateOffice', 'Menu');
            define('CFG_PAGE_TEMPLATE', 'privateoffice');
            break;

        case 'cancelorder':
            $CFG_CREATE_BLOCKS = array ();
            define('CFG_PAGE_TEMPLATE', '');
            $id = RequestWrapper::get('id');
            $sid = Session::getUserDataSid();
            $res = $otapilib->CancelSalesOrder($sid, $id);
            if($res){
                echo 'Ok';
            } else {
                echo (string)$otapilib->error_message;
            }
            die();
            break;

        case 'cancelorderitem':
            $CFG_CREATE_BLOCKS = array ();
            define('CFG_PAGE_TEMPLATE', '');
            $itemid = RequestWrapper::get('itemid');
            $orderid = RequestWrapper::get('orderid');
            $sid = Session::getUserDataSid();
            $res = $otapilib->CancelLineSalesOrder($sid, $orderid, $itemid);
            if ($res) {
                $userData = new UserData();
                $userData->ClearAccountInfoCache();
                echo 'Ok';
            } else {
                echo (string)$otapilib->error_message;
            }
            die();
            break;

        case 'login':
            $CFG_CREATE_BLOCKS = array ('CrumbsNew', 'HeaderNew', 'FooterNew', 'Login');
            define('CFG_PAGE_TEMPLATE', 'login');
            break;

        case 'recovery':
            $CFG_CREATE_BLOCKS = array ('CrumbsNew', 'HeaderNew', 'FooterNew', 'Recovery');
            define('CFG_PAGE_TEMPLATE', 'recovery');
            break;

        case 'logout':
            $CFG_CREATE_BLOCKS = array ('Logout');
            define('CFG_PAGE_TEMPLATE', '');
            break;

        case 'brands':
            $CFG_CREATE_BLOCKS = array ('HeaderNew', 'FooterNew', 'BrandsNew', 'CrumbsNew');
            define('CFG_PAGE_TEMPLATE', 'brandsnew');
            break;

        case 'all_vendors':
            $CFG_CREATE_BLOCKS = array ('HeaderNew', 'FooterNew', 'AllVendors', 'CrumbsNew');
            define('CFG_PAGE_TEMPLATE', 'all_vendors');
            break;

        case 'subcategory':
            if ((RequestWrapper::get('root') && substr(RequestWrapper::get('cid'),0,3) == 'CID') || RequestWrapper::getParamExists('virt'))
            {
                // Показ подкатегорий без товаров, в рутовых категориях с ручным переводом нет товаров
                $CFG_CREATE_BLOCKS = array ('SubCategoryNew', 'CrumbsNew', 'HeaderNew', 'FooterNew');
                define('CFG_PAGE_TEMPLATE', 'subcategorynew');
            } else {
                // Показ подкатегорий c товарами и фильтром
                $CFG_CREATE_BLOCKS = array ('ItemListNew', 'CrumbsNew', 'HeaderNew', 'FooterNew');
                define('CFG_PAGE_TEMPLATE', 'categorynew');
            }
            break;
        case 'reviews':
            // Показ товаров с отзывами
            $CFG_CREATE_BLOCKS = array ('CrumbsNew', 'HeaderNew', 'FooterNew', 'ItemWithReview');
            define('CFG_PAGE_TEMPLATE', 'reviews');
            break;

        case 'item':
            $CFG_CREATE_BLOCKS = array ('ItemInfoNew', 'HeaderNew', 'FooterNew', 'CrumbsNew');
            define('CFG_PAGE_TEMPLATE', 'iteminfonew');
            break;

        case 'pristroy':
            if (CMS::IsFeatureEnabled('FleaMarket')) {
                $CFG_CREATE_BLOCKS = array ('Pristroy', 'HeaderNew', 'FooterNew', 'CrumbsNew');
                define('CFG_PAGE_TEMPLATE', 'pristroy');
            }
            break;

        case 'itemajax':
            $CFG_CREATE_BLOCKS = array ('ItemInfoNew');
            define('CFG_PAGE_TEMPLATE', 'iteminfonew_ajax');
            break;

        case 'item_list_ajax':
            @define('NO_DEBUG_STRONG', 1);
            $CFG_CREATE_BLOCKS = array ('ItemListNew');
            define('CFG_PAGE_TEMPLATE', 'itemlistnew_ajax');
            break;

        case 'itemdescription':
            $CFG_CREATE_BLOCKS = array ();
            define('CFG_PAGE_TEMPLATE', 'itemdescription');
            $itemId = RequestWrapper::get('itemid');
            $itemdescription = $otapilib->GetItemOriginalDescription($itemId);
            if (@General::$siteConf['hide_item_external_links_in_description']) {
                $itemdescription = preg_replace('~(href=[\'|"|](http|https)?[^>]*)~is', '', $itemdescription);
            }

            $itemdescription = ProductsHelper::prepareDescription($itemdescription);

            $HSTemplate->assignGlobal('itemdescription', $itemdescription);
            @define('NO_DEBUG_STRONG', true);
            break;

        case 'itemdescriptiontranslated':
            $CFG_CREATE_BLOCKS = array ();
            define('CFG_PAGE_TEMPLATE', 'itemdescription');
            $itemId = RequestWrapper::get('itemid');
            $itemdescription = $otapilib->GetItemDescription($itemId, Session::getActiveLang());
            if (@General::$siteConf['hide_item_external_links_in_description']) {
                $itemdescription = preg_replace('~(href=[\'|"|](http|https)?[^>]*)~is', '', $itemdescription);
            }
            // исключение не корректных атрибутов и тегов из отзывов
            $excludeAttrAndTags = array('width:0.0px', 'height:0.0px');
            $itemdescription = str_replace($excludeAttrAndTags, '', $itemdescription);

            $HSTemplate->assignGlobal('itemdescription', $itemdescription);
            @define('NO_DEBUG_STRONG', true);
            break;

        case 'addnotetobasket':
            $CFG_CREATE_BLOCKS = array ();
            define('CFG_PAGE_TEMPLATE', '');
            $id = $_POST['id'];
            $itemId = $_POST['itemId'];
            $quantity = $_POST['quantity'];
            $item = $otapilib->GetItemInfo($itemId);
            $_POST['currencyName'] = $item['currencyname'];
            if(BasketNew::addToBasket($otapilib, $itemId, $quantity)){
                echo 'Ok';
            } else {
                echo 'Error';
            }
            die();
            break;

        case 'editnoteitemquantity':
            $CFG_CREATE_BLOCKS = array ();
            define('CFG_PAGE_TEMPLATE', '');
            $id = RequestWrapper::get('id');
            $quantity = (int)RequestWrapper::get('num');
            SupportListNew::editItemQuantity($otapilib, $id, $quantity);
            @define('NO_DEBUG_STRONG', true);
            die();
            
            break;

        case 'editnoteitemcomment':
            $CFG_CREATE_BLOCKS = array ();
            define('CFG_PAGE_TEMPLATE', '');
            $id = RequestWrapper::post('id');
            $comment = RequestWrapper::post('comment');
            SupportListNew::editItemComment($otapilib, $id, $comment);
            @define('NO_DEBUG_STRONG', true);
            break;

        case 'editbasketitemcomment':
            $CFG_CREATE_BLOCKS = array ();
            define('CFG_PAGE_TEMPLATE', '');
            $id = RequestWrapper::post('id');
            $comment = RequestWrapper::post('comment');
            BasketNew::editItemComment($otapilib, $id, $comment);
            @define('NO_DEBUG_STRONG', true);
            break;

        case 'editbasketitemquantity':
            $CFG_CREATE_BLOCKS = array ();
            define('CFG_PAGE_TEMPLATE', '');
            $id = RequestWrapper::post('id');
            $quantity = RequestWrapper::post('num');
            BasketNew::editItemQuantity($otapilib, $id, $quantity);
            @define('NO_DEBUG_STRONG', true);
            break;

        case 'get_item_config':
            $CFG_CREATE_BLOCKS = array ();
            define('CFG_PAGE_TEMPLATE', '');
            $id = RequestWrapper::get('itemid');
            $fulliteminfo = $otapilib->BatchGetItemFullInfo(User::getObject()->getSid(), $id, 'DeliveryCosts,Promotions');
            $itemBlock = new ItemInfoNew();
            $fulliteminfo['Item'] = $itemBlock->checkHierarchicalConfigurators($fulliteminfo['Item']);
            print json_encode($fulliteminfo);
            die();
            break;


        case 'itemtitle':
            $CFG_CREATE_BLOCKS = array ();
            define('CFG_PAGE_TEMPLATE', 'itemtitle');
            $itemId = RequestWrapper::get('itemid');
            try {
                $itemInfo = $otapilib->GetItemInfo($itemId);
            } catch (ServiceException $e) {
                /* TODO */
            }
            $HSTemplate->assignGlobal('itemtitle', isset($itemInfo['title']) ? $itemInfo['title'] : '');
            @define('NO_DEBUG_STRONG', true);
            break;

        case 'itemtraderateinfo':
            $CFG_CREATE_BLOCKS = array ();
            define('CFG_PAGE_TEMPLATE', 'itemtraderateinfo');
            $itemId = RequestWrapper::get('itemid');
            $from = RequestWrapper::get('from') ? RequestWrapper::get('from') : 0;
            $otapilib->setErrorsAsExceptionsOn();
            $itemdescription = array();
            try {
                $itemdescription = $otapilib->GetTradeRateInfoListFrame($itemId, $from);
            } catch (Exception $e) {
                $HSTemplate->assignGlobal('error', $e->getMessage());
            }
            $HSTemplate->assignGlobal('from', $from);
            $HSTemplate->assignGlobal('itemdescription', $itemdescription);
            @define('NO_DEBUG_STRONG', true);
            break;

        case 'vendor':
            $CFG_CREATE_BLOCKS = array ('CrumbsNew', 'HeaderNew', 'FooterNew', 'ItemListNew');
            define('CFG_PAGE_TEMPLATE', 'vendorinfonew');
            break;

        case 'download':
            $CFG_CREATE_BLOCKS = array ('HeaderNew', 'FooterNew', 'CrumbsNew', 'DownloadBox');
            define('CFG_PAGE_TEMPLATE', 'download');
            break;

        case 'supportlist':
            $CFG_CREATE_BLOCKS = array ('HeaderNew', 'SupportListNew', 'FooterNew', 'CrumbsNew');
            define('CFG_PAGE_TEMPLATE', 'supportlistnew');
            break;

        case 'MoveItemFromNoteToBasket':
            SupportListNew::moveToBasket(RequestWrapper::get('id'));
            die();
            break;

        case 'MoveItemsFromNoteToBasket':
            SupportListNew::moveItemsToBasket(RequestWrapper::get('items'));
            die();
            break;

        case 'supportlistremove':
            $CFG_CREATE_BLOCKS = array ();

            $sid = Session::getUserDataSid();
            if (! $sid) {
                $sid = isset($_COOKIE['NoteSid']) ? $_COOKIE['NoteSid'] : session_id();
            }

            // удалить файл кеша корзины и избранного
            $userData = new UserData();
            $userData->ClearUserDataCache();

            $res = $otapilib->RemoveItemFromNote($sid, RequestWrapper::get('id'));

            if($res){
                $items = $otapilib->GetNote($sid);
                echo json_encode(array('Success'=>'Ok', 'Count' => count($items)));
            }
            else{
                echo json_encode(array('Success'=>0));
            }

            @define('CFG_PAGE_TEMPLATE', '');
            die();
            break;

        case 'basketremove':
            $CFG_CREATE_BLOCKS = array ();
            $sid = Session::getUserOrGuestSession();
            // удалить файл кеша корзины и избранного
            $userData = new UserData();
            $userData->ClearUserDataCache();
            $isdeleted = $otapilib->RemoveItemFromBasket($sid, RequestWrapper::post('addedToCartId'));
            if($isdeleted)
            {
                $items = $otapilib->GetBasket($sid);
                $count = 0;
                if (isset($items['Elements']) && is_array($items['Elements'])) {
                    $count = count($items['Elements']);
                }
                echo json_encode(array('Success'=>'Ok', 'Count' => $count, 'itemId' => ''));
            } else {
                echo json_encode(array('Success'=>0));
            }
            @define('CFG_PAGE_TEMPLATE', '');
            die();
            break;

        case 'basket':
            $CFG_CREATE_BLOCKS = array ('CrumbsNew', 'BasketNew', 'HeaderNew', 'FooterNew');
            define('CFG_PAGE_TEMPLATE', 'basketnew');
            break;

        case 'userorder':
            $CFG_CREATE_BLOCKS = array ('CrumbsNew', 'HeaderNew', 'FooterNew', 'UserZakazNew');
            define('CFG_PAGE_TEMPLATE', 'userzakaznew');
            break;

        case 'createorderajax':
            print json_encode(UserZakazNew::createOrder());
            die();
            break;

        case 'admin':
            $CFG_CREATE_BLOCKS = array ();
            define('CFG_PAGE_TEMPLATE', '');
            break;

        case 'editorderweight':
            $CFG_CREATE_BLOCKS = array ();
            define('CFG_PAGE_TEMPLATE', '');
            $id = RequestWrapper::get('id');
            $weight = str_replace(',', '.', RequestWrapper::get('w'));
            @define('NO_DEBUG_STRONG', true);
            BasketNew::editItemWeight($otapilib, $id, $weight);
            break;

        case 'getbasketcount':
            $sid = Session::getUserOrGuestSession();
            $basket = $otapilib->GetBasket($sid);
            print $basket !== false ? count($basket) : 1;
            die();
            break;

        case 'itemcomments':
            $itemInfoNew = new ItemInfoNew();
            $itemInfoNew->itemComments();
            break;

        case 'gradeItemReview':
            $reviewsController = new ReviewsController();
            $reviewsController->gradeItemReviewAction();
            break;

        case 'addAnswerToItemReview':
            $reviewsController = new ReviewsController();
            $reviewsController->addAnswerToItemReviewAction();
            break;
        case 'digest':
            $CFG_CREATE_BLOCKS = array ('Digest', 'CrumbsNew', 'HeaderNew', 'FooterNew', 'ContentMenu');
            define('CFG_PAGE_TEMPLATE', 'digestnew');
            break;
        case 'post':
            $CFG_CREATE_BLOCKS = array ('Post','CrumbsNew', 'HeaderNew', 'FooterNew');
            define('CFG_PAGE_TEMPLATE', 'postnew');
            break;
        //Облегчаем шапку
        case 'menushortajax':
            $GLOBALS['menu_ajax'] = RequestWrapper::get('menu_ajax');
            $M = new MenuShortNew();
            echo $M->Generate();
            die();
            break;
        case 'search_ajax':
            $GLOBALS['searchcats_ajax'] = RequestWrapper::get('searchcats_ajax');
            $M = new SearchCategories();
            echo $M->Generate();
            die();
            break;
        case 'users_ajax':
            //Не используется - устарело
            $GLOBALS['userdata_ajax'] = RequestWrapper::get('userdata_ajax');
            $M = new HeaderNew();
            echo $M->Generate();
            die();
            break;

        case '404':
            header('HTTP/1.0 404 Not Found');
            if (file_exists(CFG_APP_ROOT . '/404.html')) {
                echo file_get_contents(CFG_APP_ROOT . '/404.html');
                die;
            } else {
                $CFG_CREATE_BLOCKS = array ('HeaderNew', 'FooterNew','ContentData');
            }
            General::$isContent = true;
            define('CFG_PAGE_TEMPLATE', 'cms');
            break;
        case 'userSetPreference':
            $request = new RequestWrapper();

            $xmlUpdateData = '<UserPreferencesUpdateData>';
            if ($request->valueExists('country')) {
                $xmlUpdateData .= '<CountryCode>'.$request->getValue('country').'</CountryCode>';
            }
            if ($request->valueExists('currency')) {
                Session::set('currency', $request->getValue('currency'));
                $xmlUpdateData .= '<CurrencyCode>'.$request->getValue('currency').'</CurrencyCode>';
            }
            $xmlUpdateData .= '</UserPreferencesUpdateData>';

            try {
                OTAPILib2::UpdateUserPreferences(Session::getActiveLang(), User::getObject()->getSid(), $xmlUpdateData, $answer);
                OTAPILib2::makeRequests();
                User::getObject()->clearUserPreferencesCache();
            } catch (Exception $e) {
                ErrorHandler::registerError($e);
            }

            if (isset($_SERVER['HTTP_REFERER']))
                header('Location: '.$_SERVER['HTTP_REFERER']);
            die();
            break;
        case 'plugin':
            $request = new RequestWrapper();
            $uri = $request->get('q', '');
            $params = explode('/', $uri);
            $pluginController = new PluginController();
            $pluginController->requestAction($params[2]);
            break;
        case 'getReview':
            $reviewsController = new ReviewsController();
            $reviewsController->getReviewAction();
            die();
            break;
        case 'addItemReview':
            $reviewsController = new ReviewsController();
            $reviewsController->addItemReviewAction();
            die();
            break;
        case 'closeLineOrder':
            $orderController = new OrderController();
            $orderController->closeLineOrderAction();
            die();
            break;
        case 'upload':
            $uploadController = new UploadController();
            $uploadController->defaultAction();
            break;
        case 'cron':
            $cronController = new CronController();

            $request = new RequestWrapper();
            $uri = $request->get('q', '');
            if (empty($uri)) {
                $action = 'defaultAction';
            } else {
                // Url вида site.com/cron/updateCommonInstanceOptionsInfo
                $params = explode('/', $uri);
                if (isset($params[1]) && method_exists($cronController, $params[1] . 'Action')) {
                    $action = $params[1] . 'Action';
                } else {
                    $action = 'defaultAction';
                }
            }
            $cronController->{$action}();
            die();
            break;
        default:
            // в случае нового шаблона всегда будет выводиться 404 страница,
            // остальные страницы выводятся по обычным правилам нового шаблона
            $pageAlias = rawurldecode(SCRIPT_NAME);
            if (!General::getConfigValue('is_old_platform')) {
                $content = new ContentController();
                print $content->defaultAction($pageAlias);
                $CFG_CREATE_BLOCKS = array();
                $showPage = false;
            } else {
                $cRep = new ContentRepository($opentaoCMS);
                $page = $cRep->GetPageByAlias($pageAlias);
                if(! $page) {
                    General::setGroupId('404');
                    header('HTTP/1.0 404 Not Found');

                    if(file_exists(CFG_APP_ROOT . '/404.html')) {
                        echo file_get_contents(CFG_APP_ROOT . '/404.html');
                        die;
                    } else {
                        $CFG_CREATE_BLOCKS = array ('HeaderNew', 'FooterNew','ContentData');
                    }
                } else {
                    General::setGroupId('content');
                    $CFG_CREATE_BLOCKS = array ('CrumbsNew', 'HeaderNew', 'FooterNew','ContentMenu', 'ContentData');
                }

                General::$isContent = true;
                // регистрируем SEO данные для нового шаблона
                General::$_page['is_index'] = $page['is_index'];
                General::$_page['title'] = (!empty($page['pagetitle'])) ? $page['pagetitle'] : $page['title'];
                General::$_page['seo_keywords'] = (!empty($page['seo_keywords'])) ? $page['seo_keywords'] : '';
                General::$_page['seo_description'] = (!empty($page['seo_description'])) ? $page['seo_description'] : '';
                General::$_page['title_h1'] = $page['title_h1'];

                define('CFG_PAGE_TEMPLATE', 'cms');
            }
            break;
    }

    $GLOBALS['CFG_CREATE_BLOCKS'] = $CFG_CREATE_BLOCKS;
}

class ShowPage extends GenerateBlock
{
    protected $_cache           = false; //- кэшируем или нет.
    protected $_life_time       = 30; //- время на которое будем кешировать.
    public    $_template        = 'index'; //- шаблон, на основе которого будем собирать блок.
    public    $_template_path   = '/';

    public function __construct()
    {
        parent::__construct();
    }

    protected function setVars()
    {
        // Шаблон страницы
        if (defined('CFG_PAGE_TEMPLATE'))
        {
            $this->_template = CFG_PAGE_TEMPLATE;
        }
        // Генерируем блоки
        if ((isset($GLOBALS['CFG_CREATE_BLOCKS'])) && (is_array($GLOBALS['CFG_CREATE_BLOCKS'])))
        {
            foreach ($GLOBALS['CFG_CREATE_BLOCKS'] as $class)
            {
                $block = new $class();
                $data = '';
                try {
                    if (General::getConfigValue('is_old_platform')) {
                        $data = $block->Generate();
                    } else {
                        if (($class == "HeaderNew" || $class == "FooterNew")) {
                            $data = General::runController(str_replace("New", "", $class));
                        } else {
                            $data = $block->Generate();
                        }
                    }
                } catch(ServiceException $e) {
                    $message = $e->getErrorMessage();
                    if (OTBase::isTest() && Session::get('sid')) {
                        $message = $e->getErrorCode().': '.$message;
                    }
                    show_error($message);
                } catch(Exception $e) {
                    $message = $e->getMessage();
                    if (OTBase::isTest() && Session::get('sid')) {
                        $message = $e->getCode().': '.$message;
                    }
                    show_error($message);
                }
                $this->tpl->assign($class, $data);
            }
        }
        if (defined('CFG_PAGE_TEMPLATE_PATH'))
        {
            $this->_template_path = CFG_PAGE_TEMPLATE_PATH;
        }
    }

    public function Show()
    {
        $page = parent::Generate();
        Plugins::runSerialEvent('onIndexOldShowPage', array(
            'page' => $page
        ));
        echo $page;

        if (! General::getConfigValue('site_temporary_unavailable')) {
            SilentActions::backupMainPage($page);
        }
    }
}

try {
    if ($showPage) {
        global $HSTemplate;
        $HSTemplate->assignGlobal('script_name', SCRIPT_NAME);
        $show = new ShowPage();
        $show->Show();
    }
} catch (Exception $e) {
	if (OTBase::isTest()) {
		echo '<pre><h2>' . $e->getMessage() . "</h2>\n\n" . $e->getTraceAsString() . '</pre>';
	} else {
		echo MainPage::getBackup();
		echo 'Database connection error';
	}
}

try{
    General::storeRequestGroup();
}
catch (Exception $e) {
	ErrorHandler::registerError($e);
}

Plugins::invokeEvent('onPageCompleteLoad');

function show_error($msg='', $file='')
{
    global $otapilib;
    if (! $msg) {
        if (OTBase::isTest() && Session::get('sid')) {
            $msg = $otapilib->error_code.': '.$otapilib->error_message;
        } else {
            $msg = $otapilib->error_message;
        }
    }
    if (strripos(@$GLOBALS['error_text'], (string)$msg) === false) {
        @$GLOBALS['error_text'] .= (string)$msg;
    }
    @$GLOBALS['error_text_hidden'] .= $otapilib->error_message;
    $GLOBALS['file_with_error'] = $_SERVER["HTTP_HOST"].$_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING'];
    $GLOBALS['show_error'] = true;
    $GLOBALS['error_code'] = (string)$otapilib->error_code;
    $GLOBALS['error_method'] = $otapilib->call_method;
}

function process_error_message ($msg) {
    if (strpos($msg, 'RKdevException') !== false) {
        return Lang::get('error_business_logic');
    } elseif (strpos($msg, 'SessionExpired') !== false) {
        return Lang::get('error_session_expired');
    } elseif (strpos($msg, 'InternalError') !== false) {
        return Lang::get('error_internal');
    } elseif (strpos($msg, 'ContractViolation') !== false) {
        return Lang::get('error_contract_violation');
    }
    return Lang::get('error_internal');
}

function mail_utf8($to, $subject = '(No subject)', $message = '', $header = '')
{
    $header_ = 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n";
    mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $message, $header_ . $header);
}

function send_email($message, $file, $otapi, $errorCode)
{
    global $server;

    $mess  = " Сервер: ".$server;
    $mess .= " <br /><br /> URL: <a href='http://$file'>http://".$file."</a>";
    $mess .= " <br /><br /> Время: ".  date("F j, Y, g:i a");
    $mess .= " <br /><br /> Инстанс: ".  CFG_SERVICE_INSTANCEKEY;
    $mess .= " <br /><br /> OtapiUrl: <a href='$otapi' target='_blank'>".  $otapi . "</a>";
    $mess .= " <br /><br /> Код ошибки: ".  $errorCode;
    $mess .= " <br /><br /> Ошибка: ".  $message;

    $curl = new Curl(CFG_TOOLS_URL . '/smtp_sender/send.php', 60, false, 10, false, true, false);
    $params = array(
        'mess' => $mess,
        'server' => $server
    );
    $curl->setPost(http_build_query($params));
    $curl->connect();
    $info = $curl->getInfo();

    echo Lang::get('Letter_sent');
}

if (defined('CFG_NEED_CUSTOM_CONFIG') && !defined('NO_DEBUG'))
{
    $GLOBALS['show_error'] = true;
    $GLOBALS['error_text'] = Lang::get('Need_configcustom_error').'<br><br>';
}

$errors = ErrorHandler::getErrors(); ?>

<script>
    $(function(){
        <? if (General::getConfigValue('is_old_platform')) { ?>
            <? if (! empty($errors[0])) { ?>
                <? foreach ($errors as $error) { ?>
                    $().toastmessage('showToast', {'text': "<?=TextHelper::htmlToJS($error)?>", 'stayTime': 60000, 'type': 'error'});
                <? } ?>
            <? } ?>
            <? if (Session::checkErrors()) { ?>
                <? $message = Session::getErrorDescription(); ?>
                $().toastmessage('showToast', {'text': "<?=TextHelper::htmlToJS($message)?>", 'stayTime': 60000, 'type': 'error'});
            <? } ?>
        <? } ?>
    });
</script>

<? if (OTBase::isTest()) { ?>
    <?=Debugger::getRender()?>
<? } ?>
