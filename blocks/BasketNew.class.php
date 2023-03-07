<?php

OTBase::import('system.lib.service.*');

class BasketNew extends GenerateBlock
{
    protected $_template = 'basketnew'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/main/';
    protected $defaultAction = 'list';
    private $baseUrl;

    /**
     * @var InstanceProvider
     */
    private $instanceProvider;

    /**
     * @var UrlWrapper
     */
    private $urlWithoutSearch;
    private $userData;
    
    private $providers = array('Taobao', 'YahooJapan', 'Warehouse');

    public function __construct()
    {
        parent::__construct(true);
        $this->otapilib->setErrorsAsExceptionsOn();
        $this->baseUrl = new UrlWrapper();
        $this->baseUrl->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");

        $this->clearUrl = new UrlWrapper();
        $this->clearUrl->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
        $this->userData = new UserData();
        $this->instanceProvider = InstanceProvider::getObject();
    }

    public function listAction($request)
    {
        $this->prepareBaseUrl();

        $sid = Session::getUserOrGuestSession();

        $this->otapilib->setErrorsAsExceptionsOn();
        try {  
            $loggedIn = false;
			if (Session::get(Session::getHttpHost() . 'isMayAuthenticated')) {
                //Считаем что авторизованы
                $allUserBasketData = $this->otapilib->BatchGetUserData($sid, 'UserStatus,Basket');                
                if ($allUserBasketData['Status']['IsSessionExpired'] == 'false') {
                    Session::set(Session::getHttpHost() . 'isMayAuthenticated', true);
                    $loggedIn = true;
                } else {
                    Session::clearUserData();
                    $loggedIn = false;
                }
                $allBasket = $allUserBasketData['Basket'];
            } else {
                //Неавторизованы или неизвестно (все равно вызовем просто GetBasket :) )
                $allBasket = $this->otapilib->GetBasket($sid);
            }
            
            $GLOBALS['Basket'] = $allBasket;

            $basket = new BasketRecord($allBasket['Elements']);

            // Текущая вкладка провайдера
            $currentProviderAlias = $request->getValue('provider');
            $currentProvider = false;

            $providers = $basket->getItemProviders();
            $aliases = array();
            foreach ($providers as $provider) {
                $alias = $this->instanceProvider->GetAliasByProviderName(Session::getActiveLang(), $provider);
                $aliases[$provider] = $alias;
                if ($currentProviderAlias === $alias) {
                    $currentProvider = $provider;
                }
            }
            if (!$currentProvider && count($providers) > 0) {
                $currentProvider = $providers[0];
            }
            $this->tpl->assign('currentProvider', $currentProvider);
            
            $settings = $this->otapilib->GetCommonInstanceOptionsInfo();
            $minOrderCost = $settings['order'];
            $this->tpl->assign('loggedIn', $loggedIn);
            $this->tpl->assign('basket', $basket);
            $this->tpl->assign('providers', $providers);
            $this->tpl->assign('aliases', $aliases);
            $this->tpl->assign('minOrderCost', $minOrderCost);
        }
        catch(ServiceException $e){
            Session::setError($e->getMessage(), $e->getErrorCode());
        }
        catch(Exception $e){
            Session::setError($e->getMessage(), $e->getCode());
        }
    }

    public function changeConfigAction($request)
    {

        $basketItemId = $request->getValue('setconfig');
        $lang = Session::getActiveLang();
        $sid = Session::getUserOrGuestSession();
        $comment = null;
        $deliveryId = null;
        OTAPILib2::GetPartialBasket($lang, $sid, $basketItemId, $partBasket);
        OTAPILib2::makeRequests();
        foreach ($partBasket->GetCollectionInfo()->GetElements()->GetElementInfo() as $item) {
           foreach ($item->GetFields()->GetFieldInfo() as $value) {
               if ($value->GetNameAttribute() === 'Comment') {
                   $comment = $value->GetValueAttribute();
                   break;
               }
           }
        }

        $sid = Session::getUserOrGuestSession();

        if (! empty($basketItemId)) {
            $isDeleted = $this->otapilib->RemoveItemFromBasket($sid, $basketItemId);
            if($isDeleted === false){
                show_error();
            }

            $quantity = ((int)$request->getValue('quantity') <= 0) ? 1 : (int)$request->getValue('quantity');
            $this->otapilib->setErrorsAsExceptionsOn();

            try{
                $res = $this->otapilib->AddItemToBasket(
                    $sid,
                    $request->getValue('item_id'),
                    $quantity,
                    null,
                    $request->getValue('newconfig'),
                    $request->getValue('promoId'),
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    $request->getValue('ItemURL'),
                	$request->getValue('externalDeliveryId'),
                    $comment
                );

            } catch (Exception $e) {
                header('HTTP/1.1 500 ' . $e->getCode());
                die($e->getMessage());
            }

            $provider = $request->getValue('currentProvider');
            $alias = $this->instanceProvider->GetAliasByProviderName(Session::getActiveLang(), $provider);

            $url = General::generateUrl('content', 'basket');
            $url .= (strpos($url, '?') === false) ? '?' : '&';
            $url .= 'provider=' . $alias;
            header('Location: ' . $url);
            die();
        }
    }

    /* TODO */
    private function prepareBaseUrl()
    {
        $this->clearUrl->DeleteKey('page');

        if ($this->request->getMethod() == 'POST') {
            $this->request->LocationRedirect($this->baseUrl->Get());
        }

        if (strpos($this->baseUrl->Get(),'?')) {
            $pageURL = $this->baseUrl->Get()."&";
        } else {
            $pageURL = $this->baseUrl->Get()."?";
        }

        $this->tpl->assign('pageUrl', $pageURL);
        $this->tpl->assign('clearUrl', $this->clearUrl->Get());
    }

    /**
     * @param RequestWrapper $request
     */
    public function addAction($request)
    {
        $quantity = ((int)$request->getValue('quantity') <= 0) ? 1 : (int)$request->getValue('quantity');
        $deliveryMode = $request->getValue('deliveryMode');
        $this->otapilib->setErrorsAsExceptionsOn();

        try {
            $res = $this->otapilib->AddItemToBasket(
                Session::getUserOrGuestSession(),
                $request->getValue('id'),
                $quantity,
                null,
                $request->getValue('configurationId'),
                $request->getValue('promoId'),
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                $request->getValue('ItemURL'),
            	$deliveryMode
            );

            $items = $this->otapilib->GetBasket(Session::getUserOrGuestSession());
            $this->userData->ClearUserDataCache();
            
            if ($deliveryMode) {
            	User::getObject()->setExternalDeliveryId($deliveryMode);
            }

            $count = 0;
            if (isset($items['Elements']) && is_array($items['Elements'])) {
                $count = count($items['Elements']);
            }
            $this->sendAjaxResponse(array('Success'=>'Ok', 'Count' => $count, 'itemId' => $res));
        }
        catch (ServiceException $e) {
            $this->throwAjaxError($e);
            header('HTTP/1.1 500 ' . $e->getErrorCode());            
            die($e->getErrorMessage());
        }
        catch (Exception $e) {
            $this->throwAjaxError($e);
            header('HTTP/1.1 500 ' . $e->getCode());
            die($e->getMessage());
        }
    }

    /**
     * @param RequestWrapper $request
     */
    public function deleteAction($request)
    {
        try{
            $deleteItems = explode(",", $request->getValue('del'));
            $this->otapilib->setErrorsAsExceptionsOn();
            foreach ($deleteItems as $item) {
                $this->otapilib->RemoveItemFromBasket(Session::getUserOrGuestSession(), $item);
            }
            $this->userData->ClearUserDataCache();
            User::getObject()->clearUserDataCache();
            if ($request->isAjax()) {
                $itemsInCart = User::getObject()->getCountInBasket();
                $this->sendAjaxResponse(array('itemCount' => $itemsInCart));
            }
        }
        catch(ServiceException $e){
            Session::setError($e->getMessage(), $e->getErrorCode());
        }
        header('Location: '.$request->RedirectToReferrer());
    }
    
    /**
     * @param RequestWrapper $request
     */
    public function deleteGroupAction($request)
    {
        try{
            $itms = explode("|", $this->request->get('delGroup'));
            foreach ($itms as $key => $value) {
               $this->otapilib->RemoveItemFromBasket(Session::getUserOrGuestSession(), $value);
            }
            $this->userData->ClearUserDataCache();
        }
        catch(ServiceException $e){
            Session::setError($e->getMessage(), $e->getErrorCode());
        }
        header('Location: '.$request->RedirectToReferrer());
    }

    /**
     * @param RequestWrapper $request
     */
    public function deleteAllAction($request)
    {
        try{
            $this->otapilib->setErrorsAsExceptionsOn();
            $this->otapilib->ClearBasket(Session::getUserOrGuestSession());
            $this->userData->ClearUserDataCache();
        }
        catch(ServiceException $e){
            Session::setError($e->getMessage(), $e->getErrorCode());
        }
        header('Location: '.$request->RedirectToReferrer());
    }

    public static function editItemQuantity($otapilib, $id, $quantity)
    {
        $sid = Session::getUserOrGuestSession();
        $error = '';
        try {
            $res = $otapilib->EditBasketItemQuantity($sid, $id, $quantity);
        } catch (ServiceException $e) {
            $error = $e->getMessage();
        } catch (Exception $e) {
            $error = Lang::get('otapi_request_error');
        }
        if (! $error) {
            $error = ($res === false) ? (string)$otapilib->error_message : '';
        }
		
		$allBasket = $otapilib->GetBasket($sid);
        $basket['error'] = $error;
        $record = new BasketRecord($allBasket);
        $basket['Basket'] = $record->asArray();

        $elementsTotalCost = 0;
        foreach ($basket['Basket']['elements'] as $k => $item) {
            /* Новая цена внутренней доставки, с учетом округления установленного в админке */
            if (isset($item['groupconvertedpricelist']['internal'])) {
                $item['groupconvertedpricelist']['internal']['display'] = TextHelper::formatPrice(
                    $item['groupconvertedpricelist']['internal'][0],
                    $item['groupconvertedpricelist']['internal']['sign']
                );
            }
            if (isset($item['GroupConvertedPriceList']['Internal'])) {
                $basket['Basket']['Elements'][$k]['GroupConvertedPriceList']['Internal']['Display'] = TextHelper::formatPrice(
                    $item['GroupConvertedPriceList']['Internal'][0],
                    $item['GroupConvertedPriceList']['Internal']['Sign']
                );
            }

            /* Новая цена за единицу товара, с учетом округления установленного в админке */
            $item['displayprice'] = TextHelper::formatPrice($item['price'], $item['currencysign']);
            $basket['Basket']['Elements'][$k]['DisplayPrice'] = TextHelper::formatPrice($item['Price'], $item['CurrencySign']);

            $elementsTotalCost += $item['Cost'];
            if (isset($basket['Basket']['collectionsummaries'][$item['providertype']]['ElementsCost'])) {
                $basket['Basket']['collectionsummaries'][$item['providertype']]['ElementsCost'] = $basket['Basket']['collectionsummaries'][$item['providertype']]['ElementsCost'] + $item['Cost']; 
                $basket['Basket']['CollectionSummaries'][ucfirst($item['providertype'])]['ElementsCost'] = $basket['Basket']['CollectionSummaries'][ucfirst($item['providertype'])]['ElementsCost'] + $item['Cost'];
            } else {
                $basket['Basket']['collectionsummaries'][$item['providertype']]['ElementsCost'] = $item['Cost'];
                $basket['Basket']['CollectionSummaries'][ucfirst($item['providertype'])]['ElementsCost'] = $item['Cost'];
            }
        }
        $basket['ElementsCost'] = $elementsTotalCost;

        @define('NO_DEBUG', 1);
        print json_encode($basket);
        die();
    }

    public static function editItemComment($otapilib, $id, $comment)
    {
        $otapilib->setErrorsAsExceptionsOn();

        $sid = Session::getUserOrGuestSession();
        try {
            $fields = '<Fields><FieldInfo Name="Comment" Value="'.TextHelper::escape($comment).'"/></Fields>';
            $result = $otapilib->EditBasketItemFields($sid, $id, $fields);
        } catch (Exception $e) {
            print json_encode(['error' => $e->getMessage()]);
            die();
        }
        print json_encode($result);
        die();
    }


    public static function editItemWeight($otapilib, $id, $weight)
    {
        $sid = Session::getUserOrGuestSession();
        $fields = '<Fields><FieldInfo Name="Weight" Value="'.$weight.'"/></Fields>';
        $otapilib->EditBasketItemFields($sid, $id, $fields);
        $basket = $otapilib->GetBasket($sid);

        print json_encode(BasketNew::getBasketForProviderByItemId($sid, $basket, $id));
        die;
    }

    /* TODO */
    private static function getBasketForProviderByItemId ($sid, $basket, $itemId) {
        /** Определяем провайдера и разделяем итемы по провайдерам */
        $provider = '';
        $itemOrderIds = Session::get("itemsToOrder");
        $elementsByProvider = array();
        foreach ($basket['Elements'] as $item) {
            if (in_array($item['Id'], $itemOrderIds)) {
                $elementsByProvider[$item['providertype']][] = $item;
                if ($itemId == $item['Id']) {
                    $provider = $item['providertype'];
                }
            }
        }
        $basket['Elements'] = $elementsByProvider[$provider];

        return $basket;
    }

    public function moveToFavouritesAction($id)
    {
        $this->otapilib->setErrorsAsExceptionsOn();

        try{
            $sid = Session::getUserOrGuestSession();            
            $this->otapilib->MoveItemFromCartToNote($sid, $id);
            $this->userData->ClearUserDataCache();
        }
        catch(ServiceException $e){
            Session::setError($e->getMessage(), $e->getErrorCode());
        }
        catch(Exception $e){
            Session::setError($e->getMessage(), $e->getCode());
        }
        if (! RequestWrapper::get('isAjax')) {
            header('Location: ' . General::generateUrl('content', 'basket'));
        }
    }

    public function getDeliveryCostAction($request)
    {
        $this->otapilib->setErrorsAsExceptionsOn();
        try {
            $items = $this->request->get('items');
            $deliveryId = $this->request->get('deliveryId', 0);
            $provider = $this->checkProviderType($request);
        
            $basket = $this->otapilib->GetBasket(Session::getUserOrGuestSession());
            $items = explode(",", $items);

            $itemsToOrder = array();
            foreach ($basket['Elements'] as $i => $element) {
                if(in_array($element['Id'], $items)) {
                    $itemsToOrder[] = $element;     			
                } 
            }
        
            if ($deliveryId) {
		        $itemsWeight = 0;
		        foreach ($itemsToOrder as $item) {
                    $itemsWeight += isset($item['Weight']) ? $item['Weight'] * $item['Quantity'] : 0;
		        }
	    	    $xmlParams = $this->_generateXmlForSearchDelivery($provider, User::getObject()->getCountryCode(), $itemsWeight, $deliveryId);
	    	    $modes = $this->otapilib->SearchDeliveryModes($xmlParams);

                if (! empty($modes['Content'][0])) {
                    $return = array(
                       'CurrencySign'  => isset($modes['Content'][0]['CurrencySign']) ? $modes['Content'][0]['CurrencySign'] : false,
                       'Price'  => isset($modes['Content'][0]['Price']) ? $modes['Content'][0]['Price'] : false
                    );
                    return $this->sendAjaxResponse($return);
                } else {
                    if (General::getConfigValue('skip_reordering')) {
                        $message = Lang::get('calculation_delivery_cost_failed_no_reorder');                        
                    } else {
                        $message = Lang::get('calculation_delivery_cost_failed_reorder');                        
                    }
                    return $this->sendAjaxResponse(array('error' => $message));
                }
            } else {
                return $this->sendAjaxResponse(array('error' => Lang::get('Delivery_modes_not_received')));
            }
    	} catch (Exception $e) {
            return $this->throwAjaxError($e);
        }
    }
    
    private function checkProviderType($request)
    {
    	$instanceProvider = InstanceProvider::getObject();    
    	$alias = $request->get('type');
    	return $instanceProvider->GetProviderNameByAlias(Session::getActiveLang(), $alias);
    }

    /* TODO */
    private function _generateXmlForSearchDelivery($provider, $country, $weight, $deliveryId = false) {
    	$xml = new SimpleXMLElement('<DeliveryModeSearchParameters></DeliveryModeSearchParameters>');
    	$xml->addChild('CountryCode', htmlspecialchars($country));
    	$xml->addChild('Weight', htmlspecialchars($weight));
    	$xml->addChild('ProviderType', htmlspecialchars($provider));
    	if ($deliveryId) {
    		$xml->addChild('DeliveryModeIds');
    		$xml->DeliveryModeIds->addChild('Id', $deliveryId);
    	}
    
    	return str_replace('<?xml version="1.0"?>', '', $xml->asXML());
    }

    public function runBasketCheckingAction($request)
    {
        $sesionId = Session::getUserOrGuestSession();
        $items = implode(',', $request->getValue('items'));
        try {
            $this->otapilib->setErrorsAsExceptionsOn();
            $activity = $this->otapilib->RunBasketChecking($sesionId, $items);
        }
        catch (Exception $e) {
            $this->throwAjaxError($e);
        }

        return $this->sendAjaxResponse($activity);
    }

    public function checkBasketAction($request)
    {
        $sesionId = Session::getUserOrGuestSession();
        $activityId = $request->getValue('activityId');
        try {
            $this->otapilib->setErrorsAsExceptionsOn();
            $resultOfChecking = $this->otapilib->GetBasketCheckingResult($sesionId, $activityId);
        }
        catch (Exception $e) {
            $this->throwAjaxError($e);
        }

        return $this->sendAjaxResponse($resultOfChecking);
    }
}
