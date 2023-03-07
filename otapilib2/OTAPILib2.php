<?php
/**
 * Класс-хелпер для общения с OpenTao API.
 */
class OTAPILib2 extends AbstractOTAPILib2 {

    /**
     *  Зарегистрировать произвольный метод сервисов
     *
     * @param string $methodName
     * @param array $params
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiAnswer

     */
    public static function simpleRequest($methodName, array $params, &$request) {
        self::registerRequest($methodName, $params, 'OtapiAnswer', $request);
    }

    /**
     *  Импорт заранее заготовленной структуры каталога по языку<br/><a href="/docs/IOtapiService.html#M:ImportStructureByLanguage">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $language    
     * @param string $source    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function ImportStructureByLanguage($sessionId, $language, $source, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'language' => $language,
            'source' => $source,
        );
        self::registerRequest('ImportStructureByLanguage', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Объединение заказов<br/><a href="/docs/IOtapiService.html#M:MergeOrders">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $language    
     * @param string $targetOrderId    
     * @param string $mergedOrderId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function MergeOrders($sessionId, $language, $targetOrderId, $mergedOrderId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'language' => $language,
            'targetOrderId' => $targetOrderId,
            'mergedOrderId' => $mergedOrderId,
        );
        self::registerRequest('MergeOrders', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Переместить все элементы из корзины в избранное<br/><a href="/docs/IOtapiService.html#M:MoveAllItemsFromCartToNote">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function MoveAllItemsFromCartToNote($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('MoveAllItemsFromCartToNote', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Переместить все элементы из избранного в корзину<br/><a href="/docs/IOtapiService.html#M:MoveAllItemsFromNoteToBasket">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function MoveAllItemsFromNoteToBasket($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('MoveAllItemsFromNoteToBasket', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Переместить элемент из корзины в избранное<br/><a href="/docs/IOtapiService.html#M:MoveItemFromCartToNote">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $elementId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function MoveItemFromCartToNote($sessionId, $elementId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'elementId' => $elementId,
        );
        self::registerRequest('MoveItemFromCartToNote', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Переместить элемент из избранного в корзину<br/><a href="/docs/IOtapiService.html#M:MoveItemFromNoteToBasket">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $elementId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function MoveItemFromNoteToBasket($sessionId, $elementId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'elementId' => $elementId,
        );
        self::registerRequest('MoveItemFromNoteToBasket', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Оплатить заказ с лицевого счета<br/><a href="/docs/IOtapiService.html#M:PaymentPersonalAccount">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $salesId    
     * @param string $amount    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function PaymentPersonalAccount($sessionId, $salesId, $amount, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'salesId' => $salesId,
            'amount' => $amount,
        );
        self::registerRequest('PaymentPersonalAccount', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Провести операцию по лицевому счету во внутренней валюте расчетов<br/><a href="/docs/IOtapiService.html#M:PostTransaction">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $customerId    
     * @param string $amount    
     * @param string $comment    
     * @param string $isDebit    
     * @param string $transactionDate    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function PostTransaction($sessionId, $customerId, $amount, $comment, $isDebit, $transactionDate, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'customerId' => $customerId,
            'amount' => $amount,
            'comment' => $comment,
            'isDebit' => $isDebit,
            'transactionDate' => $transactionDate,
        );
        self::registerRequest('PostTransaction', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Выгрузка данных о посылке в систему службы доставки<br/><a href="/docs/IOtapiService.html#M:PrintPackageReceipt">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $language    
     * @param string $xmlPrintReceiptParameters    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiPrintPackageReceiptAnswer

     */
    public static function PrintPackageReceipt($sessionId, $language, $xmlPrintReceiptParameters, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'language' => $language,
            'xmlPrintReceiptParameters' => $xmlPrintReceiptParameters,
        );
        self::registerRequest('PrintPackageReceipt', $params, 'OtapiPrintPackageReceiptAnswer', $request);
    }

    /**
     *  Заказать товар у поставщика<br/><a href="/docs/IOtapiService.html#M:PurchaseItems">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $saleLineList    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function PurchaseItems($sessionId, $saleLineList, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'saleLineList' => $saleLineList,
        );
        self::registerRequest('PurchaseItems', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Создание дозаказа<br/><a href="/docs/IOtapiService.html#M:RecreateOrder">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlRecreateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiOrderInfoAnswer

     */
    public static function RecreateOrder($sessionId, $xmlRecreateData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlRecreateData' => $xmlRecreateData,
        );
        self::registerRequest('RecreateOrder', $params, 'OtapiOrderInfoAnswer', $request);
    }

    /**
     *  Дозаказ<br/><a href="/docs/IOtapiService.html#M:RecreateSalesOrder">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $salesOrderId    
     * @param string $weight    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSalesOrderInfoAnswer

     */
    public static function RecreateSalesOrder($language, $sessionId, $salesOrderId, $weight, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'salesOrderId' => $salesOrderId,
            'weight' => $weight,
        );
        self::registerRequest('RecreateSalesOrder', $params, 'OtapiSalesOrderInfoAnswer', $request);
    }

    /**
     *  Регистрация пользователя<br/><a href="documentation?name=RegisterUser">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $userParameters
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiConfirmationInfoAnswer

     */
    public static function RegisterUser($language, $sessionId, $userParameters, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'userParameters' => $userParameters,
        );
        self::registerRequest('RegisterUser', $params, 'OtapiConfirmationInfoAnswer', $request);
    }

    /**
     *  Удаление подборки любых элементов<br/><a href="/docs/IOtapiService.html#M:RemoveAllElementsRatingList">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $itemRatingTypeId    
     * @param string $contentType    
     * @param string $categoryId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function RemoveAllElementsRatingList($sessionId, $itemRatingTypeId, $contentType, $categoryId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'itemRatingTypeId' => $itemRatingTypeId,
            'contentType' => $contentType,
            'categoryId' => $categoryId,
        );
        self::registerRequest('RemoveAllElementsRatingList', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удаление подборки товаров<br/><a href="/docs/IOtapiService.html#M:RemoveAllItemRatingList">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $itemRatingType    
     * @param string $categoryId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function RemoveAllItemRatingList($sessionId, $itemRatingType, $categoryId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'itemRatingType' => $itemRatingType,
            'categoryId' => $categoryId,
        );
        self::registerRequest('RemoveAllItemRatingList', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удаление бренда<br/><a href="/docs/IOtapiService.html#M:RemoveBrandInfo">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $brandId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function RemoveBrandInfo($sessionId, $brandId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'brandId' => $brandId,
        );
        self::registerRequest('RemoveBrandInfo', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удаление категории вместе со всеми подкатегориями (только внутренняя)<br/><a href="/docs/IOtapiService.html#M:RemoveCategory">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $categoryId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function RemoveCategory($sessionId, $categoryId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'categoryId' => $categoryId,
        );
        self::registerRequest('RemoveCategory', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удаление связки категории и группы ценообразования<br/><a href="/docs/IOtapiService.html#M:RemoveCategoryFromPriceFormationGroup">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $categoryId    
     * @param string $priceFormationGroupId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function RemoveCategoryFromPriceFormationGroup($sessionId, $categoryId, $priceFormationGroupId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'categoryId' => $categoryId,
            'priceFormationGroupId' => $priceFormationGroupId,
        );
        self::registerRequest('RemoveCategoryFromPriceFormationGroup', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удаление курса валют.<br/><a href="/docs/IOtapiService.html#M:RemoveCurrencyRate">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $firstCurrencyCode    
     * @param string $secondCurrencyCode    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function RemoveCurrencyRate($sessionId, $firstCurrencyCode, $secondCurrencyCode, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'firstCurrencyCode' => $firstCurrencyCode,
            'secondCurrencyCode' => $secondCurrencyCode,
        );
        self::registerRequest('RemoveCurrencyRate', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удаление группы скидок<br/><a href="/docs/IOtapiService.html#M:RemoveDiscountGroupFromInstance">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $discountGroupId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function RemoveDiscountGroupFromInstance($sessionId, $discountGroupId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'discountGroupId' => $discountGroupId,
        );
        self::registerRequest('RemoveDiscountGroupFromInstance', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удаление элементов подборки любых элементов<br/><a href="/docs/IOtapiService.html#M:RemoveElementsSetRatingList">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $itemRatingTypeId    
     * @param string $contentType    
     * @param string $categoryId    
     * @param string $itemList    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function RemoveElementsSetRatingList($sessionId, $itemRatingTypeId, $contentType, $categoryId, $itemList, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'itemRatingTypeId' => $itemRatingTypeId,
            'contentType' => $contentType,
            'categoryId' => $categoryId,
            'itemList' => $itemList,
        );
        self::registerRequest('RemoveElementsSetRatingList', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удаление тарифа внешней доставки<br/><a href="/docs/IOtapiService.html#M:RemoveExternalDeliveryRate">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $externalDeliveryRateId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function RemoveExternalDeliveryRate($sessionId, $externalDeliveryRateId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'externalDeliveryRateId' => $externalDeliveryRateId,
        );
        self::registerRequest('RemoveExternalDeliveryRate', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удаление способа внешней доставки<br/><a href="/docs/IOtapiService.html#M:RemoveExternalDeliveryType">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $externalDeliveryTypeId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function RemoveExternalDeliveryType($sessionId, $externalDeliveryTypeId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'externalDeliveryTypeId' => $externalDeliveryTypeId,
        );
        self::registerRequest('RemoveExternalDeliveryType', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Отсоеденить пользователя приложения от роли<br/><a href="/docs/IOtapiService.html#M:RemoveInstanceUserFromRole">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $roleName    
     * @param string $userLogin    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function RemoveInstanceUserFromRole($sessionId, $roleName, $userLogin, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'roleName' => $roleName,
            'userLogin' => $userLogin,
        );
        self::registerRequest('RemoveInstanceUserFromRole', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удаление товара из корзины<br/><a href="/docs/IOtapiService.html#M:RemoveItemFromBasket">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $elementId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function RemoveItemFromBasket($sessionId, $elementId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'elementId' => $elementId,
        );
        self::registerRequest('RemoveItemFromBasket', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удаление товара из блокнота<br/><a href="/docs/IOtapiService.html#M:RemoveItemFromNote">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $elementId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function RemoveItemFromNote($sessionId, $elementId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'elementId' => $elementId,
        );
        self::registerRequest('RemoveItemFromNote', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удаление элементов подборки товаров<br/><a href="/docs/IOtapiService.html#M:RemoveItemRatingList">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $itemRatingType    
     * @param string $categoryId    
     * @param string $itemList    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function RemoveItemRatingList($sessionId, $itemRatingType, $categoryId, $itemList, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'itemRatingType' => $itemRatingType,
            'categoryId' => $categoryId,
            'itemList' => $itemList,
        );
        self::registerRequest('RemoveItemRatingList', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  <a href="documentation?name=RemoveItemsFromBasket">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $elements
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function RemoveItemsFromBasket($language, $sessionId, $elements, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'elements' => $elements,
        );
        self::registerRequest('RemoveItemsFromBasket', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  <a href="documentation?name=RemoveItemsFromNote">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $elements
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function RemoveItemsFromNote($language, $sessionId, $elements, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'elements' => $elements,
        );
        self::registerRequest('RemoveItemsFromNote', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  <a href="documentation?name=MoveItemsFromNoteToBasket">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $elements
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function MoveItemsFromNoteToBasket($language, $sessionId, $elements, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'elements' => $elements,
        );
        self::registerRequest('MoveItemsFromNoteToBasket', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  <a href="documentation?name=MoveItemsFromBasketToNote">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $elements
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function MoveItemsFromBasketToNote($language, $sessionId, $elements, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'elements' => $elements,
        );
        self::registerRequest('MoveItemsFromBasketToNote', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удаление группы ценообразования<br/><a href="/docs/IOtapiService.html#M:RemovePriceFormationGroup">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $priceFormationGroupId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function RemovePriceFormationGroup($sessionId, $priceFormationGroupId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'priceFormationGroupId' => $priceFormationGroupId,
        );
        self::registerRequest('RemovePriceFormationGroup', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Отсоедениеть пользователя от группы скидок<br/><a href="/docs/IOtapiService.html#M:RemoveUserFromDiscountGroup">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $discountGroupId    
     * @param string $userId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function RemoveUserFromDiscountGroup($sessionId, $discountGroupId, $userId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'discountGroupId' => $discountGroupId,
            'userId' => $userId,
        );
        self::registerRequest('RemoveUserFromDiscountGroup', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удаление продавца из избранного<br/><a href="/docs/IOtapiService.html#M:RemoveVendorFromFavorites">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $elementId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function RemoveVendorFromFavorites($sessionId, $elementId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'elementId' => $elementId,
        );
        self::registerRequest('RemoveVendorFromFavorites', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  <a href="documentation?name=RemoveVendorsFromFavorites">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $elements
     * @param string $vendors
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function RemoveVendorsFromFavorites($language, $sessionId, $elements, $vendors, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'elements' => $elements,
            'vendors' => $vendors,
        );
        self::registerRequest('RemoveVendorsFromFavorites', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Запрос восстановления пароля<br/><a href="/docs/IOtapiService.html#M:RequestPasswordRecovery">Документация</a> 
     *
     * @param string $userIdentifier    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiPasswordRecoveryRequestResultInfoAnswer

     */
    public static function RequestPasswordRecovery($userIdentifier, &$request) {
        $params = array(
            'userIdentifier' => $userIdentifier,
        );
        self::registerRequest('RequestPasswordRecovery', $params, 'OtapiPasswordRecoveryRequestResultInfoAnswer', $request);
    }

    /**
     *  Сброс кэша данных приложения<br/><a href="/docs/IOtapiService.html#M:ResetInstanceCaches">Документация</a> 
     *
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function ResetInstanceCaches(&$request) {
        $params = array(
        );
        self::registerRequest('ResetInstanceCaches', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Восстановить строку заказа<br/><a href="/docs/IOtapiService.html#M:RestoreLineSalesOrderForOperator">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $salesId    
     * @param string $salesLineId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function RestoreLineSalesOrderForOperator($sessionId, $salesId, $salesLineId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'salesId' => $salesId,
            'salesLineId' => $salesLineId,
        );
        self::registerRequest('RestoreLineSalesOrderForOperator', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Зачислить бонус за отзыв<br/><a href="documentation?name=RewardItemReview">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $itemReviewId
     * @param string $amount
     * @param string $comment
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function RewardItemReview($language, $sessionId, $itemReviewId, $amount, $comment, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'itemReviewId' => $itemReviewId,
            'amount' => $amount,
            'comment' => $comment,
        );
        self::registerRequest('RewardItemReview', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Резервирвание денег под заказ<br/><a href="/docs/IOtapiService.html#M:SalesPaymentReserve">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $salesId    
     * @param string $amount    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSalesPaymentInfoAnswer

     */
    public static function SalesPaymentReserve($sessionId, $salesId, $amount, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'salesId' => $salesId,
            'amount' => $amount,
        );
        self::registerRequest('SalesPaymentReserve', $params, 'OtapiSalesPaymentInfoAnswer', $request);
    }

/**
     *  Получить список счетов<br/><a href="documentation?name=SearchBills">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $xmlSearchParameters    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBillInfoListAnswer

     */
    public static function SearchBills($language, $sessionId, $xmlSearchParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlSearchParameters' => $xmlSearchParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchBills', $params, 'OtapiBillInfoListAnswer', $request);
    }
    
    /**
     *  Поиск удаленных таобао категорий<br/><a href="/docs/IOtapiService.html#M:SearchDeletedCategoriesIds">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCategoryListAnswer

     */
    public static function SearchDeletedCategoriesIds($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('SearchDeletedCategoriesIds', $params, 'OtapiCategoryListAnswer', $request);
    }

    /**
     *  Поиск записей о действиях пользователей инстанса.<br/><a href="/docs/IOtapiService.html#M:SearchInstanceUserLogEntries">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $language    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param string $xmlSearchParameters    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiLogEntryInfoListFrameAnswer

     */
    public static function SearchInstanceUserLogEntries($sessionId, $language, $framePosition, $frameSize, $xmlSearchParameters, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'language' => $language,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
            'xmlSearchParameters' => $xmlSearchParameters,
        );
        self::registerRequest('SearchInstanceUserLogEntries', $params, 'OtapiLogEntryInfoListFrameAnswer', $request);
    }

    /**
     *  Глобальный поиск товаров<br/><a href="/docs/IOtapiService.html#M:SearchItemsFrame">Документация</a> 
     *
     * @param string $language    
     * @param string $xmlParameters    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemSearchResultAnswer

     */
    public static function SearchItemsFrame($language, $xmlParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'xmlParameters' => $xmlParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchItemsFrame', $params, 'OtapiItemSearchResultAnswer', $request);
    }

    /**
     *  Поиск строк заказов<br/><a href="/docs/IOtapiService.html#M:SearchOrderLines">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $language    
     * @param string $xmlSearchParameters    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiOrderLineInfoListFrameAnswer

     */
    public static function SearchOrderLines($sessionId, $language, $xmlSearchParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'language' => $language,
            'xmlSearchParameters' => $xmlSearchParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchOrderLines', $params, 'OtapiOrderLineInfoListFrameAnswer', $request);
    }

    /**
     *  Поиск заказов<br/><a href="/docs/IOtapiService.html#M:SearchOrders">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $language    
     * @param string $xmlSearchParameters    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSalesOrderInfoListFrameAnswer

     */
    public static function SearchOrders($sessionId, $language, $xmlSearchParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'language' => $language,
            'xmlSearchParameters' => $xmlSearchParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchOrders', $params, 'OtapiSalesOrderInfoListFrameAnswer', $request);
    }

    /**
     *  Поиск заказов c подсчетом сводных данных<br/><a href="/docs/IOtapiService.html#M:SearchOrdersWithSummary">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $language    
     * @param string $xmlSearchParameters    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiOrderListFrameWithSummaryAnswer

     */
    public static function SearchOrdersWithSummary($sessionId, $language, $xmlSearchParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'language' => $language,
            'xmlSearchParameters' => $xmlSearchParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchOrdersWithSummary', $params, 'OtapiOrderListFrameWithSummaryAnswer', $request);
    }

    /**
     *  Получение частичного списка оригинальных брендов (от провайдера товаров)<br/><a href="/docs/IOtapiService.html#M:SearchOriginalBrandsFrame">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $name    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBrandInfoListFrameAnswer

     */
    public static function SearchOriginalBrandsFrame($sessionId, $name, $framePosition, $frameSize, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'name' => $name,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchOriginalBrandsFrame', $params, 'OtapiBrandInfoListFrameAnswer', $request);
    }

    /**
     *  Поиск посылок<br/><a href="/docs/IOtapiService.html#M:SearchPackages">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlSearchParameters    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiPackageAdminInfoListFrameAnswer

     */
    public static function SearchPackages($sessionId, $xmlSearchParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlSearchParameters' => $xmlSearchParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchPackages', $params, 'OtapiPackageAdminInfoListFrameAnswer', $request);
    }

    /**
     *  Поиск рекламируемых товаров<br/><a href="/docs/IOtapiService.html#M:SearchPromoteItemsFrame">Документация</a> 
     *
     * @param string $language    
     * @param string $xmlParameters    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiPromoItemInfoListFrameAnswer

     */
    public static function SearchPromoteItemsFrame($language, $xmlParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'xmlParameters' => $xmlParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchPromoteItemsFrame', $params, 'OtapiPromoItemInfoListFrameAnswer', $request);
    }

    /**
     *  Получение части списка пользователей с подчетом сводных данных<br/><a href="/docs/IOtapiService.html#M:SearchUsersWithSummary">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $userFilter    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBaseUserInfoListFrameWithSummaryAnswer

     */
    public static function SearchUsersWithSummary($sessionId, $userFilter, $framePosition, $frameSize, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'userFilter' => $userFilter,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchUsersWithSummary', $params, 'OtapiBaseUserInfoListFrameWithSummaryAnswer', $request);
    }

    /**
     *  Найти список категорий товаров на складе по определенным условиям<br/><a href="/docs/IOtapiService.html#M:SearchWarehouseCategories">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlSearchParameters    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiWarehouseCategoryInfoListFrameAnswer

     */
    public static function SearchWarehouseCategories($sessionId, $xmlSearchParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlSearchParameters' => $xmlSearchParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchWarehouseCategories', $params, 'OtapiWarehouseCategoryInfoListFrameAnswer', $request);
    }

    /**
     *  Найти список товаров на складе по определенным условиям<br/><a href="/docs/IOtapiService.html#M:SearchWarehouseItems">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlSearchParameters    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiWarehouseItemInfoListFrameAnswer

     */
    public static function SearchWarehouseItems($sessionId, $xmlSearchParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlSearchParameters' => $xmlSearchParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchWarehouseItems', $params, 'OtapiWarehouseItemInfoListFrameAnswer', $request);
    }

    /**
     *  Установка группы ценообразования по умолчанию<br/><a href="/docs/IOtapiService.html#M:SetDefaultPriceFormationGroup">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $priceFormationGroupId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function SetDefaultPriceFormationGroup($sessionId, $priceFormationGroupId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'priceFormationGroupId' => $priceFormationGroupId,
        );
        self::registerRequest('SetDefaultPriceFormationGroup', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Привязывание группы ценообразования к категории из каталога пользователя<br/><a href="/docs/IOtapiService.html#M:SetPriceFormationGroupToCategory">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $categoryId    
     * @param string $priceFormationGroupId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function SetPriceFormationGroupToCategory($sessionId, $categoryId, $priceFormationGroupId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'categoryId' => $categoryId,
            'priceFormationGroupId' => $priceFormationGroupId,
        );
        self::registerRequest('SetPriceFormationGroupToCategory', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Установить оператора заказа<br/><a href="/docs/IOtapiService.html#M:SetSalesOperator">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $salesId    
     * @param string $salesOperatorId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function SetSalesOperator($sessionId, $salesId, $salesOperatorId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'salesId' => $salesId,
            'salesOperatorId' => $salesOperatorId,
        );
        self::registerRequest('SetSalesOperator', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Изменение настроек фичи Витрина<br/><a href="/docs/IOtapiService.html#M:SetShowcaseSettings">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $settings    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function SetShowcaseSettings($sessionId, $settings, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'settings' => $settings,
        );
        self::registerRequest('SetShowcaseSettings', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Блокировка учетной записи пользователя<br/><a href="/docs/IOtapiService.html#M:SetUserBan">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $userId    
     * @param string $isBanned    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function SetUserBan($sessionId, $userId, $isBanned, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'userId' => $userId,
            'isBanned' => $isBanned,
        );
        self::registerRequest('SetUserBan', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Изменение настроек фичи WebUI<br/><a href="/docs/IOtapiService.html#M:SetWebUISettings">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $settings    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function SetWebUISettings($sessionId, $settings, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'settings' => $settings,
        );
        self::registerRequest('SetWebUISettings', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Разделение строк заказа оператором<br/><a href="/docs/IOtapiService.html#M:SplitOrderLineForOperator">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $orderId    
     * @param string $orderLineId    
     * @param string $xmlSplitData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function SplitOrderLineForOperator($sessionId, $orderId, $orderLineId, $xmlSplitData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'orderId' => $orderId,
            'orderLineId' => $orderLineId,
            'xmlSplitData' => $xmlSplitData,
        );
        self::registerRequest('SplitOrderLineForOperator', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Синхронизация данных о посылке с системой службы доставки<br/><a href="/docs/IOtapiService.html#M:SynchronizePackageWithDeliveryServiceSystem">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $language    
     * @param string $packageId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function SynchronizePackageWithDeliveryServiceSystem($sessionId, $language, $packageId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'language' => $language,
            'packageId' => $packageId,
        );
        self::registerRequest('SynchronizePackageWithDeliveryServiceSystem', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удаление группы скидок<br/><a href="/docs/IOtapiService.html#M:UpdateDiscountGroup">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $discountGroupId    
     * @param string $xmlUpdateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateDiscountGroup($sessionId, $discountGroupId, $xmlUpdateData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'discountGroupId' => $discountGroupId,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateDiscountGroup', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Редактирование настроек валюты приложения.<br/><a href="/docs/IOtapiService.html#M:UpdateInstanceCurrenciesSettings">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlSettings    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateInstanceCurrenciesSettings($sessionId, $xmlSettings, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlSettings' => $xmlSettings,
        );
        self::registerRequest('UpdateInstanceCurrenciesSettings', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Обновить настройки инстанса<br/><a href="/docs/IOtapiService.html#M:UpdateInstanceOptions">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlInstanceOptionsData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateInstanceOptions($sessionId, $xmlInstanceOptionsData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlInstanceOptionsData' => $xmlInstanceOptionsData,
        );
        self::registerRequest('UpdateInstanceOptions', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Обновить информацию о роли пользователя<br/><a href="/docs/IOtapiService.html#M:UpdateInstanceRole">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $roleName    
     * @param string $xmlUpdateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateInstanceRole($sessionId, $roleName, $xmlUpdateData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'roleName' => $roleName,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateInstanceRole', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Обновить информацию о пользователе приложения<br/><a href="/docs/IOtapiService.html#M:UpdateInstanceUser">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlUpdateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateInstanceUser($sessionId, $xmlUpdateData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateInstanceUser', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Oбновление идентификатора категории в подбороке<br/><a href="/docs/IOtapiService.html#M:UpdateItemRatingCategoryId">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $itemRatingTypeId    
     * @param string $contentType    
     * @param string $oldCategoryId    
     * @param string $newCategoryId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateItemRatingCategoryId($sessionId, $itemRatingTypeId, $contentType, $oldCategoryId, $newCategoryId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'itemRatingTypeId' => $itemRatingTypeId,
            'contentType' => $contentType,
            'oldCategoryId' => $oldCategoryId,
            'newCategoryId' => $newCategoryId,
        );
        self::registerRequest('UpdateItemRatingCategoryId', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Обновление данных заказа покупателем<br/><a href="/docs/IOtapiService.html#M:UpdateOrder">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $orderId    
     * @param string $xmlUpdateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateOrder($sessionId, $orderId, $xmlUpdateData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'orderId' => $orderId,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateOrder', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Обновление данных заказа оператором<br/><a href="/docs/IOtapiService.html#M:UpdateOrderForOperator">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $orderId    
     * @param string $xmlUpdateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateOrderForOperator($sessionId, $orderId, $xmlUpdateData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'orderId' => $orderId,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateOrderForOperator', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Обновление строки заказа покупателем<br/><a href="/docs/IOtapiService.html#M:UpdateOrderLine">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $orderId    
     * @param string $orderLineId    
     * @param string $xmlUpdateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateOrderLine($sessionId, $orderId, $orderLineId, $xmlUpdateData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'orderId' => $orderId,
            'orderLineId' => $orderLineId,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateOrderLine', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Обновление данных строки заказа оператором<br/><a href="/docs/IOtapiService.html#M:UpdateOrderLineForOperator">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $orderId    
     * @param string $orderLineId    
     * @param string $xmlUpdateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateOrderLineForOperator($sessionId, $orderId, $orderLineId, $xmlUpdateData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'orderId' => $orderId,
            'orderLineId' => $orderLineId,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateOrderLineForOperator', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Обновление данных нескольких строк заказа оператором<br/><a href="/docs/IOtapiService.html#M:UpdateOrderLinesForOperator">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $orderId    
     * @param string $orderLineIds    
     * @param string $xmlUpdateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateOrderLinesForOperator($sessionId, $orderId, $orderLineIds, $xmlUpdateData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'orderId' => $orderId,
            'orderLineIds' => $orderLineIds,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateOrderLinesForOperator', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Обновление настроек заказа<br/><a href="documentation?name=UpdateOrderSettings">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $xmlUpdateData
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateOrderSettings($language, $sessionId, $xmlUpdateData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateOrderSettings', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Изменить данные по посылке<br/><a href="/docs/IOtapiService.html#M:UpdatePackage">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $packageId    
     * @param string $packageAdminUpdateInfo    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiPackageAdminInfoAnswer

     */
    public static function UpdatePackage($sessionId, $packageId, $packageAdminUpdateInfo, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'packageId' => $packageId,
            'packageAdminUpdateInfo' => $packageAdminUpdateInfo,
        );
        self::registerRequest('UpdatePackage', $params, 'OtapiPackageAdminInfoAnswer', $request);
    }

    /**
     *  Получение настроек статистики.<br/><a href="/docs/IOtapiService.html#M:UpdateStatisticsSettings">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlUpdateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateStatisticsSettings($sessionId, $xmlUpdateData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateStatisticsSettings', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Обновление информации о пользователе<br/><a href="/docs/IOtapiService.html#M:UpdateUser">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $userParameters    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateUser($sessionId, $userParameters, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'userParameters' => $userParameters,
        );
        self::registerRequest('UpdateUser', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Обновление профиля пользователя<br/><a href="/docs/IOtapiService.html#M:UpdateUserProfile">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $profileId    
     * @param string $updateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateUserProfile($sessionId, $profileId, $updateData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'profileId' => $profileId,
            'updateData' => $updateData,
        );
        self::registerRequest('UpdateUserProfile', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Обновление профиля пользователя<br/><a href="/docs/IOtapiService.html#M:UpdateUserProfileForOperator">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $userId    
     * @param string $profileId    
     * @param string $updateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateUserProfileForOperator($sessionId, $userId, $profileId, $updateData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'userId' => $userId,
            'profileId' => $profileId,
            'updateData' => $updateData,
        );
        self::registerRequest('UpdateUserProfileForOperator', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Обновить настройки для полей профиля<br/><a href="documentation?name=UpdateUserProfileSettings">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $inputLanguage
     * @param string $xmlUpdateData
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateUserProfileSettings($language, $sessionId, $inputLanguage, $xmlUpdateData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'inputLanguage' => $inputLanguage,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateUserProfileSettings', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Обновить категорию товаров на складе<br/><a href="/docs/IOtapiService.html#M:UpdateWarehouseCategory">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $categoryId    
     * @param string $xmlUpdateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateWarehouseCategory($sessionId, $categoryId, $xmlUpdateData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'categoryId' => $categoryId,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateWarehouseCategory', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Обновить товар на складе<br/><a href="/docs/IOtapiService.html#M:UpdateWarehouseItem">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $itemId    
     * @param string $xmlUpdateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateWarehouseItem($sessionId, $itemId, $xmlUpdateData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'itemId' => $itemId,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateWarehouseItem', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Получение информации о системе заказов<br/><a href="/docs/IOtapiService.html#M:AboutOrderLogic">Документация</a> 
     *
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiAboutOrderLogicAnswer

     */
    public static function AboutOrderLogic(&$request) {
        $params = array(
        );
        self::registerRequest('AboutOrderLogic', $params, 'OtapiAboutOrderLogicAnswer', $request);
    }

    /**
     *  Добавить элементы в черный список для статистики.<br/><a href="/docs/IOtapiService.html#M:AddBlackListContents">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlContentList    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function AddBlackListContents($sessionId, $xmlContentList, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlContentList' => $xmlContentList,
        );
        self::registerRequest('AddBlackListContents', $params, 'VoidOtapiAnswer', $request);
    }
    
    /**
     *  Удалить элементы из черного списока для статистики.<br/><a href="documentation?name=DeleteBlackListContents">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $xmlContentList
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer
    
     */
    public static function DeleteBlackListContents($language, $sessionId, $xmlContentList, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlContentList' => $xmlContentList,
        );
        self::registerRequest('DeleteBlackListContents', $params, 'VoidOtapiAnswer', $request);
    }    

    /**
     *  Добавление бренда<br/><a href="/docs/IOtapiService.html#M:AddBrandInfo">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlBrandInfo    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiAddBrandInfoAnswer

     */
    public static function AddBrandInfo($sessionId, $xmlBrandInfo, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlBrandInfo' => $xmlBrandInfo,
        );
        self::registerRequest('AddBrandInfo', $params, 'OtapiAddBrandInfoAnswer', $request);
    }

    /**
     *  Добавление категории<br/><a href="/docs/IOtapiService.html#M:AddCategory">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $categoryName    
     * @param string $parentCategoryId    
     * @param string $categoryId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCategoryIdAnswer

     */
    public static function AddCategory($sessionId, $categoryName, $parentCategoryId, $categoryId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'categoryName' => $categoryName,
            'parentCategoryId' => $parentCategoryId,
            'categoryId' => $categoryId,
        );
        self::registerRequest('AddCategory', $params, 'OtapiCategoryIdAnswer', $request);
    }

    /**
     *  Добавление категории по языку<br/><a href="/docs/IOtapiService.html#M:AddCategoryByLanguage">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $language    
     * @param string $categoryName    
     * @param string $parentCategoryId    
     * @param string $categoryId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCategoryIdAnswer

     */
    public static function AddCategoryByLanguage($sessionId, $language, $categoryName, $parentCategoryId, $categoryId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'language' => $language,
            'categoryName' => $categoryName,
            'parentCategoryId' => $parentCategoryId,
            'categoryId' => $categoryId,
        );
        self::registerRequest('AddCategoryByLanguage', $params, 'OtapiCategoryIdAnswer', $request);
    }

    /**
     *  Добавление категории категории<br/><a href="/docs/IOtapiService.html#M:AddCategoryInfo">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $language    
     * @param string $inputLanguage    
     * @param string $xmlCategoryInfo    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCategoryAnswer

     */
    public static function AddCategoryInfo($sessionId, $language, $inputLanguage, $xmlCategoryInfo, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'language' => $language,
            'inputLanguage' => $inputLanguage,
            'xmlCategoryInfo' => $xmlCategoryInfo,
        );
        self::registerRequest('AddCategoryInfo', $params, 'OtapiCategoryAnswer', $request);
    }

    /**
     *  Добавление курса валют.<br/><a href="/docs/IOtapiService.html#M:AddCurrencyRate">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $firstCurrencyCode    
     * @param string $secondCurrencyCode    
     * @param string $rate    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function AddCurrencyRate($sessionId, $firstCurrencyCode, $secondCurrencyCode, $rate, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'firstCurrencyCode' => $firstCurrencyCode,
            'secondCurrencyCode' => $secondCurrencyCode,
            'rate' => $rate,
        );
        self::registerRequest('AddCurrencyRate', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Добавление группы скидок<br/><a href="/docs/IOtapiService.html#M:AddDiscountGroupToInstance">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlAddData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiDiscountGroupIdAnswer

     */
    public static function AddDiscountGroupToInstance($sessionId, $xmlAddData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlAddData' => $xmlAddData,
        );
        self::registerRequest('AddDiscountGroupToInstance', $params, 'OtapiDiscountGroupIdAnswer', $request);
    }

    /**
     *  Добавление подборки любых элементов<br/><a href="/docs/IOtapiService.html#M:AddElementsSetToRatingList">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $itemRatingTypeId    
     * @param string $contentType    
     * @param string $categoryId    
     * @param string $itemList    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function AddElementsSetToRatingList($sessionId, $itemRatingTypeId, $contentType, $categoryId, $itemList, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'itemRatingTypeId' => $itemRatingTypeId,
            'contentType' => $contentType,
            'categoryId' => $categoryId,
            'itemList' => $itemList,
        );
        self::registerRequest('AddElementsSetToRatingList', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Присоеденить пользователя приложения к роли<br/><a href="/docs/IOtapiService.html#M:AddInstanceUserToRole">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $roleName    
     * @param string $userLogin    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function AddInstanceUserToRole($sessionId, $roleName, $userLogin, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'roleName' => $roleName,
            'userLogin' => $userLogin,
        );
        self::registerRequest('AddInstanceUserToRole', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Добавление подборки товаров<br/><a href="/docs/IOtapiService.html#M:AddItemRatingList">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $itemRatingTypeId    
     * @param string $categoryId    
     * @param string $itemList    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function AddItemRatingList($sessionId, $itemRatingTypeId, $categoryId, $itemList, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'itemRatingTypeId' => $itemRatingTypeId,
            'categoryId' => $categoryId,
            'itemList' => $itemList,
        );
        self::registerRequest('AddItemRatingList', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Добавление товара в корзину<br/><a href="/docs/IOtapiService.html#M:AddItemToBasket">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $itemId    
     * @param string $configurationId    
     * @param string $price    
     * @param string $quantity    
     * @param string $currencyName    
     * @param string $fieldParameters    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiElementIdAnswer

     */
    public static function AddItemToBasket($language, $sessionId, $itemId, $configurationId, $quantity, $fieldParameters, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'itemId' => $itemId,
            'configurationId' => $configurationId,
            'quantity' => $quantity,
            'fieldParameters' => $fieldParameters,
        );
        self::registerRequest('AddItemToBasket', $params, 'OtapiElementIdAnswer', $request);
    }

    /**
     *  Добавление товара в блокнот<br/><a href="/docs/IOtapiService.html#M:AddItemToNote">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $itemId    
     * @param string $configurationId    
     * @param string $price    
     * @param string $quantity    
     * @param string $currencyName    
     * @param string $fieldParameters    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiElementIdAnswer

     */
    public static function AddItemToNote($language, $sessionId, $itemId, $configurationId, $quantity, $fieldParameters, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'itemId' => $itemId,
            'configurationId' => $configurationId,
            'quantity' => $quantity,
            'fieldParameters' => $fieldParameters,
        );
        self::registerRequest('AddItemToNote', $params, 'OtapiElementIdAnswer', $request);
    }

    /**
     *  Добавление группы ценообразования<br/><a href="/docs/IOtapiService.html#M:AddPriceFormationGroup">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlPriceFormationGroupInfo    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiPriceFormationGroupIdAnswer

     */
    public static function AddPriceFormationGroup($sessionId, $xmlPriceFormationGroupInfo, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlPriceFormationGroupInfo' => $xmlPriceFormationGroupInfo,
        );
        self::registerRequest('AddPriceFormationGroup', $params, 'OtapiPriceFormationGroupIdAnswer', $request);
    }

    /**
     *  Добавление пользователя<br/><a href="/docs/IOtapiService.html#M:AddUser">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $userParameters    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiUserIdAnswer

     */
    public static function AddUser($sessionId, $userParameters, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'userParameters' => $userParameters,
        );
        self::registerRequest('AddUser', $params, 'OtapiUserIdAnswer', $request);
    }

    /**
     *  Привязка пользователя к группе скидок<br/><a href="/docs/IOtapiService.html#M:AddUserToDiscountGroup">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $discountGroupId    
     * @param string $userId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function AddUserToDiscountGroup($sessionId, $discountGroupId, $userId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'discountGroupId' => $discountGroupId,
            'userId' => $userId,
        );
        self::registerRequest('AddUserToDiscountGroup', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Добавление продавца в избранное<br/><a href="/docs/IOtapiService.html#M:AddVendorToFavorites">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $vendorId    
     * @param string $fieldParameters    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiElementIdAnswer

     */
    public static function AddVendorToFavorites($sessionId, $vendorId, $fieldParameters, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'vendorId' => $vendorId,
            'fieldParameters' => $fieldParameters,
        );
        self::registerRequest('AddVendorToFavorites', $params, 'OtapiElementIdAnswer', $request);
    }

    /**
     *  Добавить к роли пользователя действия<br/><a href="/docs/IOtapiService.html#M:AttachActionsToRole">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlIds    
     * @param string $roleName    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function AttachActionsToRole($sessionId, $xmlIds, $roleName, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlIds' => $xmlIds,
            'roleName' => $roleName,
        );
        self::registerRequest('AttachActionsToRole', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Присоединить права к роли<br/><a href="/docs/IOtapiService.html#M:AttachRightsToRole">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $roleName    
     * @param string $rightIdList    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function AttachRightsToRole($sessionId, $roleName, $rightIdList, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'roleName' => $roleName,
            'rightIdList' => $rightIdList,
        );
        self::registerRequest('AttachRightsToRole', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Аутентификация покупателя<br/><a href="/docs/IOtapiService.html#M:Authenticate">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $userLogin    
     * @param string $userPassword    
     * @param string $rememberMe    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiAuthenticateAnswer

     */
    public static function Authenticate($sessionId, $userLogin, $userPassword, $rememberMe, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'userLogin' => $userLogin,
            'userPassword' => $userPassword,
            'rememberMe' => $rememberMe,
        );
        self::registerRequest('Authenticate', $params, 'OtapiAuthenticateAnswer', $request);
    }

    /**
     *  Аутентификация оператора под логином юзера<br/><a href="/docs/IOtapiService.html#M:AuthenticateAsUser">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $userLogin    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiAuthenticateAsUserAnswer

     */
    public static function AuthenticateAsUser($sessionId, $userLogin, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'userLogin' => $userLogin,
        );
        self::registerRequest('AuthenticateAsUser', $params, 'OtapiAuthenticateAsUserAnswer', $request);
    }

    /**
     *  Аутентификация оператора экземпляра приложения<br/><a href="/docs/IOtapiService.html#M:AuthenticateInstanceOperator">Документация</a> 
     *
     * @param string $userLogin    
     * @param string $userPassword    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSessionIdAnswer

     */
    public static function AuthenticateInstanceOperator($userLogin, $userPassword, &$request) {
        $params = array(
            'userLogin' => $userLogin,
            'userPassword' => $userPassword,
        );
        self::registerRequest('AuthenticateInstanceOperator', $params, 'OtapiSessionIdAnswer', $request);
    }

    /**
     *  Аутентификация оператора экземпляра приложения из определенного сервиса<br/><a href="/docs/IOtapiService.html#M:AuthenticateInstanceServiceOperator">Документация</a> 
     *
     * @param string $userLogin    
     * @param string $userPassword    
     * @param string $service    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSessionIdAnswer

     */
    public static function AuthenticateInstanceServiceOperator($userLogin, $userPassword, $service, &$request) {
        $params = array(
            'userLogin' => $userLogin,
            'userPassword' => $userPassword,
            'service' => $service,
        );
        self::registerRequest('AuthenticateInstanceServiceOperator', $params, 'OtapiSessionIdAnswer', $request);
    }

    /**
     *  Получение полной информации о товаре, одновременно с дополнительными блоками<br/><a href="/docs/IOtapiService.html#M:BatchGetItemFullInfo">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $itemId    
     * @param string $blockList    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBatchItemFullInfoAnswer

     */
    public static function BatchGetItemFullInfo($language, $sessionId, $itemId, $blockList, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'itemId' => $itemId,
            'blockList' => $blockList,
        );
        self::registerRequest('BatchGetItemFullInfo', $params, 'OtapiBatchItemFullInfoAnswer', $request);
    }

    /**
     *  Получить упрощенный вариант информации о конфигурациях товаре, одновременно с дополнительными блоками<br/><a href="documentation?name=BatchGetSimplifiedItemConfigurationInfo">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $itemId
     * @param string $xmlRequest
     * @param string $blockList
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBatchSimplifiedItemConfigurationInfoAnswer

     */
    public static function BatchGetSimplifiedItemConfigurationInfo($language, $sessionId, $itemId, $xmlRequest, $blockList, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'itemId' => $itemId,
            'xmlRequest' => $xmlRequest,
            'blockList' => $blockList,
        );
        self::registerRequest('BatchGetSimplifiedItemConfigurationInfo', $params, 'OtapiBatchSimplifiedItemConfigurationInfoAnswer', $request);
    }

    /**
     *  Получить упрощенный вариант полной информации о товаре, одновременно с дополнительными блоками<br/><a href="documentation?name=BatchGetSimplifiedItemFullInfo">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $itemId
     * @param string $blockList
     * @param string $itemParameters
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBatchSimplifiedItemFullInfoAnswer

     */
    public static function BatchGetSimplifiedItemFullInfo($language, $sessionId, $itemId, $blockList, $itemParameters, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'itemId' => $itemId,
            'blockList' => $blockList,
            'itemParameters' => $itemParameters,
        );
        self::registerRequest('BatchGetSimplifiedItemFullInfo', $params, 'OtapiBatchSimplifiedItemFullInfoAnswer', $request);
    }

    /**
     *  Получение общих данных по пользователю<br/><a href="/docs/IOtapiService.html#M:BatchGetUserData">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $blockList    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBatchUserDataAnswer

     */
    public static function BatchGetUserData($language, $sessionId, $blockList, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'blockList' => $blockList,
        );
        self::registerRequest('BatchGetUserData', $params, 'OtapiBatchUserDataAnswer', $request);
    }

    /**
     *  Глобальный поиск товаров, одновременно с дополнительными блоками<br/><a href="/docs/IOtapiService.html#M:BatchSearchItemsFrame">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $xmlParameters    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param string $blockList    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBatchItemSearchResultAnswer

     */
    public static function BatchSearchItemsFrame($language, $sessionId, $xmlParameters, $framePosition, $frameSize, $blockList, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlParameters' => $xmlParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
            'blockList' => $blockList,
        );
        self::registerRequest('BatchSearchItemsFrame', $params, 'OtapiBatchItemSearchResultAnswer', $request);
    }

    /**
     *  Расчет арендной платы за период<br/><a href="/docs/IOtapiService.html#M:CalculateRent">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $dateFrom    
     * @param string $dateTo    
     * @param string $includeCalculationData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiRentalPaymentSummaryInfoAnswer

     */
    public static function CalculateRent($sessionId, $dateFrom, $dateTo, $includeCalculationData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'includeCalculationData' => $includeCalculationData,
        );
        self::registerRequest('CalculateRent', $params, 'OtapiRentalPaymentSummaryInfoAnswer', $request);
    }

    /**
     *  Отменить строку заказа<br/><a href="/docs/IOtapiService.html#M:CancelLineSalesOrder">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $salesId    
     * @param string $salesLineId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function CancelLineSalesOrder($sessionId, $salesId, $salesLineId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'salesId' => $salesId,
            'salesLineId' => $salesLineId,
        );
        self::registerRequest('CancelLineSalesOrder', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удалить строку заказа<br/><a href="/docs/IOtapiService.html#M:CancelLineSalesOrderForOperator">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $salesId    
     * @param string $salesLineId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function CancelLineSalesOrderForOperator($sessionId, $salesId, $salesLineId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'salesId' => $salesId,
            'salesLineId' => $salesLineId,
        );
        self::registerRequest('CancelLineSalesOrderForOperator', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Отменить заказ<br/><a href="/docs/IOtapiService.html#M:CancelSalesOrder">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $salesId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function CancelSalesOrder($sessionId, $salesId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'salesId' => $salesId,
        );
        self::registerRequest('CancelSalesOrder', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Отмена заказа оператором<br/><a href="/docs/IOtapiService.html#M:CancelSalesOrderForOperator">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $salesId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function CancelSalesOrderForOperator($sessionId, $salesId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'salesId' => $salesId,
        );
        self::registerRequest('CancelSalesOrderForOperator', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Изменение адреса почты<br/><a href="/docs/IOtapiService.html#M:ChangeEmail">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $currentPassword    
     * @param string $newEmail    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiEmailConfirmationInfoAnswer

     */
    public static function ChangeEmail($sessionId, $currentPassword, $newEmail, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'currentPassword' => $currentPassword,
            'newEmail' => $newEmail,
        );
        self::registerRequest('ChangeEmail', $params, 'OtapiEmailConfirmationInfoAnswer', $request);
    }

    /**
     *  [Obsolete] Изменить статус строки заказа оператором. Альтернатива UpdateOrderLineForOperator<br/><a href="/docs/IOtapiService.html#M:ChangeLineStatus">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $salesId    
     * @param string $salesLineId    
     * @param string $statusId    
     * @param string $comment    
     * @param string $quantity    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function ChangeLineStatus($sessionId, $salesId, $salesLineId, $statusId, $comment, $quantity, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'salesId' => $salesId,
            'salesLineId' => $salesLineId,
            'statusId' => $statusId,
            'comment' => $comment,
            'quantity' => $quantity,
        );
        self::registerRequest('ChangeLineStatus', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Смена пароля оператора<br/><a href="/docs/IOtapiService.html#M:ChangeOperatorPassword">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $currentPassword    
     * @param string $newPassword    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function ChangeOperatorPassword($sessionId, $currentPassword, $newPassword, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'currentPassword' => $currentPassword,
            'newPassword' => $newPassword,
        );
        self::registerRequest('ChangeOperatorPassword', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  <a href="/docs/IOtapiService.html#M:ChangePackageStatus">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $packageId    
     * @param string $statusId    
     * @param string $statusDate    
     * @param string $comment    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function ChangePackageStatus($sessionId, $packageId, $statusId, $statusDate, $comment, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'packageId' => $packageId,
            'statusId' => $statusId,
            'statusDate' => $statusDate,
            'comment' => $comment,
        );
        self::registerRequest('ChangePackageStatus', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Смена  пароля<br/><a href="/docs/IOtapiService.html#M:ChangePassword">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $currentPassword    
     * @param string $newPassword    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function ChangePassword($sessionId, $currentPassword, $newPassword, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'currentPassword' => $currentPassword,
            'newPassword' => $newPassword,
        );
        self::registerRequest('ChangePassword', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Изменение данных о заказе таобао в строке заказа<br/><a href="/docs/IOtapiService.html#M:ChangeSalesOrderLinePurchaseInfoForOperator">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $salesId    
     * @param string $salesLineId    
     * @param string $vendPurchId    
     * @param string $vendPurchWaybill    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function ChangeSalesOrderLinePurchaseInfoForOperator($sessionId, $salesId, $salesLineId, $vendPurchId, $vendPurchWaybill, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'salesId' => $salesId,
            'salesLineId' => $salesLineId,
            'vendPurchId' => $vendPurchId,
            'vendPurchWaybill' => $vendPurchWaybill,
        );
        self::registerRequest('ChangeSalesOrderLinePurchaseInfoForOperator', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Проверка допустимости вызова методов, модифицирующих данные<br/><a href="/docs/IOtapiService.html#M:CheckIfStatefullMethodsAllowed">Документация</a> 
     *
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function CheckIfStatefullMethodsAllowed(&$request) {
        $params = array(
        );
        self::registerRequest('CheckIfStatefullMethodsAllowed', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Очистка корзины<br/><a href="/docs/IOtapiService.html#M:ClearBasket">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function ClearBasket($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('ClearBasket', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Очистка списка избранных продавцов<br/><a href="/docs/IOtapiService.html#M:ClearFavoriteVendors">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function ClearFavoriteVendors($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('ClearFavoriteVendors', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Очистка блокнота<br/><a href="/docs/IOtapiService.html#M:ClearNote">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function ClearNote($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('ClearNote', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Очистка истории заказа<br/><a href="/docs/IOtapiService.html#M:ClearOrdersHistory">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $ordersHistoryItemIds    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function ClearOrdersHistory($sessionId, $ordersHistoryItemIds, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'ordersHistoryItemIds' => $ordersHistoryItemIds,
        );
        self::registerRequest('ClearOrdersHistory', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Закрытие заказа покупателем<br/><a href="/docs/IOtapiService.html#M:CloseOrder">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $orderId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function CloseOrder($sessionId, $orderId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'orderId' => $orderId,
        );
        self::registerRequest('CloseOrder', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Закрытие заказа оператором<br/><a href="/docs/IOtapiService.html#M:CloseSalesOrderForOperator">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $salesId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function CloseSalesOrderForOperator($sessionId, $salesId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'salesId' => $salesId,
        );
        self::registerRequest('CloseSalesOrderForOperator', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Подтверждение адреса электронной почты<br/><a href="/docs/IOtapiService.html#M:ConfirmEmail">Документация</a> 
     *
     * @param string $confirmationCode    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSessionIdAnswer

     */
    public static function ConfirmEmail($confirmationCode, &$request) {
        $params = array(
            'confirmationCode' => $confirmationCode,
        );
        self::registerRequest('ConfirmEmail', $params, 'OtapiSessionIdAnswer', $request);
    }

    /**
     *  Подтвердить упаковку товаров заказа<br/><a href="/docs/IOtapiService.html#M:ConfirmOrderPackaging">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $orderId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function ConfirmOrderPackaging($sessionId, $orderId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'orderId' => $orderId,
        );
        self::registerRequest('ConfirmOrderPackaging', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Подтверждение восстановления пароля<br/><a href="/docs/IOtapiService.html#M:ConfirmPasswordRecovery">Документация</a> 
     *
     * @param string $confirmationCode    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiPasswordRecoveryConfirmationResultInfoAnswer

     */
    public static function ConfirmPasswordRecovery($confirmationCode, &$request) {
        $params = array(
            'confirmationCode' => $confirmationCode,
        );
        self::registerRequest('ConfirmPasswordRecovery', $params, 'OtapiPasswordRecoveryConfirmationResultInfoAnswer', $request);
    }

    /**
     *  Подтвердить цену в строке заказа<br/><a href="/docs/IOtapiService.html#M:ConfirmPriceLineSalesOrder">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $salesId    
     * @param string $salesLineId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function ConfirmPriceLineSalesOrder($sessionId, $salesId, $salesLineId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'salesId' => $salesId,
            'salesLineId' => $salesLineId,
        );
        self::registerRequest('ConfirmPriceLineSalesOrder', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Создание тарифа внешней доставки<br/><a href="/docs/IOtapiService.html#M:CreateExternalDeliveryRate">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlExternalDeliveryRate    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCreateExternalDeliveryRateAnswer

     */
    public static function CreateExternalDeliveryRate($sessionId, $xmlExternalDeliveryRate, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlExternalDeliveryRate' => $xmlExternalDeliveryRate,
        );
        self::registerRequest('CreateExternalDeliveryRate', $params, 'OtapiCreateExternalDeliveryRateAnswer', $request);
    }

    /**
     *  Создание способа внешней доставки<br/><a href="/docs/IOtapiService.html#M:CreateExternalDeliveryType">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlExternalDeliveryType    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCreateExternalDeliveryTypeAnswer

     */
    public static function CreateExternalDeliveryType($sessionId, $xmlExternalDeliveryType, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlExternalDeliveryType' => $xmlExternalDeliveryType,
        );
        self::registerRequest('CreateExternalDeliveryType', $params, 'OtapiCreateExternalDeliveryTypeAnswer', $request);
    }

    /**
     *  Создать роль пользователя<br/><a href="/docs/IOtapiService.html#M:CreateInstanceRole">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlCreateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function CreateInstanceRole($sessionId, $xmlCreateData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlCreateData' => $xmlCreateData,
        );
        self::registerRequest('CreateInstanceRole', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Создать роль пользователя из шаблона<br/><a href="/docs/IOtapiService.html#M:CreateInstanceRoleFromTemplate">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $templateRoleName    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function CreateInstanceRoleFromTemplate($sessionId, $templateRoleName, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'templateRoleName' => $templateRoleName,
        );
        self::registerRequest('CreateInstanceRoleFromTemplate', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Создать пользователя приложения<br/><a href="/docs/IOtapiService.html#M:CreateInstanceUser">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlCreateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCreateInstanceUserAnswer

     */
    public static function CreateInstanceUser($sessionId, $xmlCreateData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlCreateData' => $xmlCreateData,
        );
        self::registerRequest('CreateInstanceUser', $params, 'OtapiCreateInstanceUserAnswer', $request);
    }

    /**
     *  Создание заказа<br/><a href="/docs/IOtapiService.html#M:CreateOrder">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlCreateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiOrderInfoAnswer

     */
    public static function CreateOrder($sessionId, $xmlCreateData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlCreateData' => $xmlCreateData,
        );
        self::registerRequest('CreateOrder', $params, 'OtapiOrderInfoAnswer', $request);
    }

    /**
     *  [Obsolete] Создать новую посылку (для оператора)<br/><a href="/docs/IOtapiService.html#M:CreatePackage">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $salesId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiPackageAdminInfoAnswer

     */
    public static function CreatePackage($sessionId, $salesId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'salesId' => $salesId,
        );
        self::registerRequest('CreatePackage', $params, 'OtapiPackageAdminInfoAnswer', $request);
    }

    /**
     *  Создать новую посылку (для оператора)<br/><a href="/docs/IOtapiService.html#M:CreatePackageForOperator">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlCreateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCreatePackageForOperatorAnswer

     */
    public static function CreatePackageForOperator($sessionId, $xmlCreateData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlCreateData' => $xmlCreateData,
        );
        self::registerRequest('CreatePackageForOperator', $params, 'OtapiCreatePackageForOperatorAnswer', $request);
    }

    /**
     *  Создание заказа<br/><a href="/docs/IOtapiService.html#M:CreateSalesOrder">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $deliveryModeId    
     * @param string $comment    
     * @param string $weight    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSalesOrderInfoAnswer

     */
    public static function CreateSalesOrder($language, $sessionId, $deliveryModeId, $comment, $weight, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'deliveryModeId' => $deliveryModeId,
            'comment' => $comment,
            'weight' => $weight,
        );
        self::registerRequest('CreateSalesOrder', $params, 'OtapiSalesOrderInfoAnswer', $request);
    }

    /**
     *  Создание нового профиля пользователя<br/><a href="/docs/IOtapiService.html#M:CreateUserProfile">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $createData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiUserProfileIdAnswer

     */
    public static function CreateUserProfile($sessionId, $createData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'createData' => $createData,
        );
        self::registerRequest('CreateUserProfile', $params, 'OtapiUserProfileIdAnswer', $request);
    }

    /**
     *  Создание нового профиля пользователя<br/><a href="/docs/IOtapiService.html#M:CreateUserProfileForOperator">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $userId    
     * @param string $createData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiUserProfileIdAnswer

     */
    public static function CreateUserProfileForOperator($sessionId, $userId, $createData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'userId' => $userId,
            'createData' => $createData,
        );
        self::registerRequest('CreateUserProfileForOperator', $params, 'OtapiUserProfileIdAnswer', $request);
    }

    /**
     *  Создать категорию товаров на складе<br/><a href="/docs/IOtapiService.html#M:CreateWarehouseCategory">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlCreateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCreateWarehouseCategoryAnswer

     */
    public static function CreateWarehouseCategory($sessionId, $xmlCreateData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlCreateData' => $xmlCreateData,
        );
        self::registerRequest('CreateWarehouseCategory', $params, 'OtapiCreateWarehouseCategoryAnswer', $request);
    }

    /**
     *  Создать товар на складе<br/><a href="/docs/IOtapiService.html#M:CreateWarehouseItem">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlCreateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCreateWarehouseItemAnswer

     */
    public static function CreateWarehouseItem($sessionId, $xmlCreateData, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlCreateData' => $xmlCreateData,
        );
        self::registerRequest('CreateWarehouseItem', $params, 'OtapiCreateWarehouseItemAnswer', $request);
    }

    /**
     *  Отсоединить права от роли<br/><a href="/docs/IOtapiService.html#M:DeattachRightsFromRole">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $roleName    
     * @param string $rightIdList    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function DeattachRightsFromRole($sessionId, $roleName, $rightIdList, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'roleName' => $roleName,
            'rightIdList' => $rightIdList,
        );
        self::registerRequest('DeattachRightsFromRole', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удаление учетной записи пользователем<br/><a href="/docs/IOtapiService.html#M:DeleteAccount">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $currentPassword    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function DeleteAccount($sessionId, $currentPassword, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'currentPassword' => $currentPassword,
        );
        self::registerRequest('DeleteAccount', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удалить роль приложения<br/><a href="/docs/IOtapiService.html#M:DeleteInstanceRole">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $roleName    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function DeleteInstanceRole($sessionId, $roleName, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'roleName' => $roleName,
        );
        self::registerRequest('DeleteInstanceRole', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удалить пользователя приложения<br/><a href="/docs/IOtapiService.html#M:DeleteInstanceUser">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $userLogin    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function DeleteInstanceUser($sessionId, $userLogin, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'userLogin' => $userLogin,
        );
        self::registerRequest('DeleteInstanceUser', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удалить посылку<br/><a href="/docs/IOtapiService.html#M:DeletePackage">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $packageId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function DeletePackage($sessionId, $packageId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'packageId' => $packageId,
        );
        self::registerRequest('DeletePackage', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удаление пользователя<br/><a href="/docs/IOtapiService.html#M:DeleteUser">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $userId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function DeleteUser($sessionId, $userId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'userId' => $userId,
        );
        self::registerRequest('DeleteUser', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удаление профиля пользователя<br/><a href="/docs/IOtapiService.html#M:DeleteUserProfile">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $profileId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function DeleteUserProfile($sessionId, $profileId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'profileId' => $profileId,
        );
        self::registerRequest('DeleteUserProfile', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удаление профиля пользователя<br/><a href="/docs/IOtapiService.html#M:DeleteUserProfileForOperator">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $userId    
     * @param string $profileId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function DeleteUserProfileForOperator($sessionId, $userId, $profileId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'userId' => $userId,
            'profileId' => $profileId,
        );
        self::registerRequest('DeleteUserProfileForOperator', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удалить категорию товаров на складе<br/><a href="/docs/IOtapiService.html#M:DeleteWarehouseCategory">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $categoryId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function DeleteWarehouseCategory($sessionId, $categoryId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'categoryId' => $categoryId,
        );
        self::registerRequest('DeleteWarehouseCategory', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удалить товар на складе<br/><a href="/docs/IOtapiService.html#M:DeleteWarehouseItem">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $itemId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function DeleteWarehouseItem($sessionId, $itemId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'itemId' => $itemId,
        );
        self::registerRequest('DeleteWarehouseItem', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Отсоединить действие от роли пользователя<br/><a href="/docs/IOtapiService.html#M:DetachActionsFromRole">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlIds    
     * @param string $roleName    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function DetachActionsFromRole($sessionId, $xmlIds, $roleName, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlIds' => $xmlIds,
            'roleName' => $roleName,
        );
        self::registerRequest('DetachActionsFromRole', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Редактирование полей товара в корзине<br/><a href="/docs/IOtapiService.html#M:EditBasketItemFields">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $elementId    
     * @param string $fieldParameters    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function EditBasketItemFields($sessionId, $elementId, $fieldParameters, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'elementId' => $elementId,
            'fieldParameters' => $fieldParameters,
        );
        self::registerRequest('EditBasketItemFields', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Редактирование количества товара в корзине<br/><a href="/docs/IOtapiService.html#M:EditBasketItemQuantity">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $elementId    
     * @param string $quantity    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function EditBasketItemQuantity($sessionId, $elementId, $quantity, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'elementId' => $elementId,
            'quantity' => $quantity,
        );
        self::registerRequest('EditBasketItemQuantity', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Редактировать черный список для статистики.<br/><a href="/docs/IOtapiService.html#M:EditBlackListContents">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlContentsList    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function EditBlackListContents($sessionId, $xmlContentsList, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlContentsList' => $xmlContentsList,
        );
        self::registerRequest('EditBlackListContents', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Редактирование бренда<br/><a href="/docs/IOtapiService.html#M:EditBrandInfo">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $brandId    
     * @param string $xmlBrandInfo    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiEditBrandInfoAnswer

     */
    public static function EditBrandInfo($sessionId, $brandId, $xmlBrandInfo, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'brandId' => $brandId,
            'xmlBrandInfo' => $xmlBrandInfo,
        );
        self::registerRequest('EditBrandInfo', $params, 'OtapiEditBrandInfoAnswer', $request);
    }

    /**
     *  Редактирование видимости категорий<br/><a href="/docs/IOtapiService.html#M:EditCategoriesVisible">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $categoriesVisibleSettings    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function EditCategoriesVisible($sessionId, $categoriesVisibleSettings, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'categoriesVisibleSettings' => $categoriesVisibleSettings,
        );
        self::registerRequest('EditCategoriesVisible', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Редактирование внешнего идентификатора категории<br/><a href="/docs/IOtapiService.html#M:EditCategoryExternalId">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $categoryId    
     * @param string $externalCategoryId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function EditCategoryExternalId($sessionId, $categoryId, $externalCategoryId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'categoryId' => $categoryId,
            'externalCategoryId' => $externalCategoryId,
        );
        self::registerRequest('EditCategoryExternalId', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Редактирование категории<br/><a href="/docs/IOtapiService.html#M:EditCategoryInfo">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $language    
     * @param string $categoryId    
     * @param string $xmlCategoryInfo    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCategoryAnswer

     */
    public static function EditCategoryInfo($sessionId, $language, $categoryId, $xmlCategoryInfo, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'language' => $language,
            'categoryId' => $categoryId,
            'xmlCategoryInfo' => $xmlCategoryInfo,
        );
        self::registerRequest('EditCategoryInfo', $params, 'OtapiCategoryAnswer', $request);
    }

    /**
     *  Редактирование имени категории<br/><a href="/docs/IOtapiService.html#M:EditCategoryName">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $categoryId    
     * @param string $categoryName    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function EditCategoryName($sessionId, $categoryId, $categoryName, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'categoryId' => $categoryId,
            'categoryName' => $categoryName,
        );
        self::registerRequest('EditCategoryName', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Редактирование имени категории по языку<br/><a href="/docs/IOtapiService.html#M:EditCategoryNameByLanguage">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $language    
     * @param string $categoryId    
     * @param string $categoryName    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function EditCategoryNameByLanguage($sessionId, $language, $categoryId, $categoryName, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'language' => $language,
            'categoryId' => $categoryId,
            'categoryName' => $categoryName,
        );
        self::registerRequest('EditCategoryNameByLanguage', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Редактирование родительской категории<br/><a href="/docs/IOtapiService.html#M:EditCategoryParent">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $categoryId    
     * @param string $parentCategoryId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function EditCategoryParent($sessionId, $categoryId, $parentCategoryId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'categoryId' => $categoryId,
            'parentCategoryId' => $parentCategoryId,
        );
        self::registerRequest('EditCategoryParent', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Редактирование тарифа внешней доставки<br/><a href="/docs/IOtapiService.html#M:EditExternalDeliveryRate">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlExternalDeliveryRate    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function EditExternalDeliveryRate($sessionId, $xmlExternalDeliveryRate, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlExternalDeliveryRate' => $xmlExternalDeliveryRate,
        );
        self::registerRequest('EditExternalDeliveryRate', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Редактирование способа внешней доставки<br/><a href="/docs/IOtapiService.html#M:EditExternalDeliveryType">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlExternalDeliveryType    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function EditExternalDeliveryType($sessionId, $xmlExternalDeliveryType, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlExternalDeliveryType' => $xmlExternalDeliveryType,
        );
        self::registerRequest('EditExternalDeliveryType', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Редактирование полей элемента в списке избранных продавцов<br/><a href="/docs/IOtapiService.html#M:EditFavoriteVendorFields">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $elementId    
     * @param string $fieldParameters    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function EditFavoriteVendorFields($sessionId, $elementId, $fieldParameters, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'elementId' => $elementId,
            'fieldParameters' => $fieldParameters,
        );
        self::registerRequest('EditFavoriteVendorFields', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Редактирование полей товара в блокноте<br/><a href="/docs/IOtapiService.html#M:EditNoteItemFields">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $elementId    
     * @param string $fieldParameters    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function EditNoteItemFields($sessionId, $elementId, $fieldParameters, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'elementId' => $elementId,
            'fieldParameters' => $fieldParameters,
        );
        self::registerRequest('EditNoteItemFields', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Редактирование количества товара в блокноте<br/><a href="/docs/IOtapiService.html#M:EditNoteItemQuantity">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $elementId    
     * @param string $quantity    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function EditNoteItemQuantity($sessionId, $elementId, $quantity, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'elementId' => $elementId,
            'quantity' => $quantity,
        );
        self::registerRequest('EditNoteItemQuantity', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Редактирование данных об операторе<br/><a href="/docs/IOtapiService.html#M:EditOperatorInfo">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlOperatorInfo    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function EditOperatorInfo($sessionId, $xmlOperatorInfo, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlOperatorInfo' => $xmlOperatorInfo,
        );
        self::registerRequest('EditOperatorInfo', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Редактирование порядка категории<br/><a href="/docs/IOtapiService.html#M:EditOrderOfCategory">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $categoryId    
     * @param string $index    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function EditOrderOfCategory($sessionId, $categoryId, $index, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'categoryId' => $categoryId,
            'index' => $index,
        );
        self::registerRequest('EditOrderOfCategory', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Редактирование группы ценообразования<br/><a href="/docs/IOtapiService.html#M:EditPriceFormationGroup">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $priceFormationGroupId    
     * @param string $xmlPriceFormationGroupInfo    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function EditPriceFormationGroup($sessionId, $priceFormationGroupId, $xmlPriceFormationGroupInfo, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'priceFormationGroupId' => $priceFormationGroupId,
            'xmlPriceFormationGroupInfo' => $xmlPriceFormationGroupInfo,
        );
        self::registerRequest('EditPriceFormationGroup', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Изменение настроек ценообразования инстанса<br/><a href="/docs/IOtapiService.html#M:EditPriceFormationSettings">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlPriceFormationSettings    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function EditPriceFormationSettings($sessionId, $xmlPriceFormationSettings, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlPriceFormationSettings' => $xmlPriceFormationSettings,
        );
        self::registerRequest('EditPriceFormationSettings', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Редактирование ручного перевода по контексту и идентификатору<br/><a href="/docs/IOtapiService.html#M:EditTranslateByKey">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $language    
     * @param string $inputLanguage    
     * @param string $text    
     * @param string $key    
     * @param string $idInContext    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function EditTranslateByKey($sessionId, $language, $inputLanguage, $text, $key, $idInContext, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'language' => $language,
            'inputLanguage' => $inputLanguage,
            'text' => $text,
            'key' => $key,
            'idInContext' => $idInContext,
        );
        self::registerRequest('EditTranslateByKey', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Обновление информации о пользователе<br/><a href="/docs/IOtapiService.html#M:EditUser">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $userParameters    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function EditUser($sessionId, $userParameters, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'userParameters' => $userParameters,
        );
        self::registerRequest('EditUser', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Экспорт текущего пакета каталога<br/><a href="/docs/IOtapiService.html#M:ExportCatalog">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCatalogPackageAnswer

     */
    public static function ExportCatalog($sessionId, $language, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'language' => $language,
        );
        self::registerRequest('ExportCatalog', $params, 'OtapiCatalogPackageAnswer', $request);
    }

    /**
     *  Выгрузка данных о посылке в систему службы доставки<br/><a href="/docs/IOtapiService.html#M:ExportPackageToDeliveryServiceSystem">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $language    
     * @param string $xmlExportParameters    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function ExportPackageToDeliveryServiceSystem($sessionId, $language, $xmlExportParameters, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'language' => $language,
            'xmlExportParameters' => $xmlExportParameters,
        );
        self::registerRequest('ExportPackageToDeliveryServiceSystem', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Экспорт текущей структуры каталога по языку<br/><a href="/docs/IOtapiService.html#M:ExportStructureByLanguage">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiExportStructureByLanguageAnswer

     */
    public static function ExportStructureByLanguage($sessionId, $language, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'language' => $language,
        );
        self::registerRequest('ExportStructureByLanguage', $params, 'OtapiExportStructureByLanguageAnswer', $request);
    }

    /**
     *  Получение части списка пользователей<br/><a href="/docs/IOtapiService.html#M:FindBaseUserInfoListFrame">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $userFilter    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBaseUserInfoListFrameAnswer

     */
    public static function FindBaseUserInfoListFrame($sessionId, $userFilter, $framePosition, $frameSize, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'userFilter' => $userFilter,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('FindBaseUserInfoListFrame', $params, 'OtapiBaseUserInfoListFrameAnswer', $request);
    }

    /**
     *  Поиск брендов по названию<br/><a href="/docs/IOtapiService.html#M:FindBrandInfoList">Документация</a> 
     *
     * @param string $language    
     * @param string $name    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBrandInfoListAnswer

     */
    public static function FindBrandInfoList($language, $name, &$request) {
        $params = array(
            'language' => $language,
            'name' => $name,
        );
        self::registerRequest('FindBrandInfoList', $params, 'OtapiBrandInfoListAnswer', $request);
    }

    /**
     *  Поиск товаров по категории и фильтрам<br/><a href="/docs/IOtapiService.html#M:FindCategoryItemInfoListFrame">Документация</a> 
     *
     * @param string $language    
     * @param string $categoryId    
     * @param string $categoryItemFilter    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemInfoListFrameAnswer

     */
    public static function FindCategoryItemInfoListFrame($language, $categoryId, $categoryItemFilter, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'categoryId' => $categoryId,
            'categoryItemFilter' => $categoryItemFilter,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('FindCategoryItemInfoListFrame', $params, 'OtapiItemInfoListFrameAnswer', $request);
    }

    /**
     *  Поиск товаров по категории и фильтрам, с выдачей упрощенной информации (без названия)<br/><a href="/docs/IOtapiService.html#M:FindCategoryItemSimpleInfoListFrame">Документация</a> 
     *
     * @param string $language    
     * @param string $categoryId    
     * @param string $categoryItemFilter    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemInfoListFrameAnswer

     */
    public static function FindCategoryItemSimpleInfoListFrame($language, $categoryId, $categoryItemFilter, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'categoryId' => $categoryId,
            'categoryItemFilter' => $categoryItemFilter,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('FindCategoryItemSimpleInfoListFrame', $params, 'OtapiItemInfoListFrameAnswer', $request);
    }

    /**
     *  Поиск группы скидок<br/><a href="/docs/IOtapiService.html#M:FindDiscountGroup">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlFindParameters    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiDiscountGroupInfoAnswer

     */
    public static function FindDiscountGroup($sessionId, $xmlFindParameters, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlFindParameters' => $xmlFindParameters,
        );
        self::registerRequest('FindDiscountGroup', $params, 'OtapiDiscountGroupInfoAnswer', $request);
    }

    /**
     *  Поиск категорий по части названия<br/><a href="/docs/IOtapiService.html#M:FindHintCategoryInfoList">Документация</a> 
     *
     * @param string $language    
     * @param string $hintTitle    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCategoryListAnswer

     */
    public static function FindHintCategoryInfoList($language, $hintTitle, &$request) {
        $params = array(
            'language' => $language,
            'hintTitle' => $hintTitle,
        );
        self::registerRequest('FindHintCategoryInfoList', $params, 'OtapiCategoryListAnswer', $request);
    }

    /**
     *  Поиск товаров по заголовку<br/><a href="/docs/IOtapiService.html#M:FindItemInfoListFrameByTitle">Документация</a> 
     *
     * @param string $language    
     * @param string $searchParameters    
     * @param string $itemTitle    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param string $languageOfQuery    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemInfoListFrameAnswer

     */
    public static function FindItemInfoListFrameByTitle($language, $searchParameters, $itemTitle, $framePosition, $frameSize, $languageOfQuery, &$request) {
        $params = array(
            'language' => $language,
            'searchParameters' => $searchParameters,
            'itemTitle' => $itemTitle,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
            'languageOfQuery' => $languageOfQuery,
        );
        self::registerRequest('FindItemInfoListFrameByTitle', $params, 'OtapiItemInfoListFrameAnswer', $request);
    }

    /**
     *  Поиск товаров по заголовку с выдачей упрощенной информации (без названия)<br/><a href="/docs/IOtapiService.html#M:FindItemSimpleInfoListFrameByTitle">Документация</a> 
     *
     * @param string $language    
     * @param string $searchParameters    
     * @param string $itemTitle    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param string $languageOfQuery    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemInfoListFrameAnswer

     */
    public static function FindItemSimpleInfoListFrameByTitle($language, $searchParameters, $itemTitle, $framePosition, $frameSize, $languageOfQuery, &$request) {
        $params = array(
            'language' => $language,
            'searchParameters' => $searchParameters,
            'itemTitle' => $itemTitle,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
            'languageOfQuery' => $languageOfQuery,
        );
        self::registerRequest('FindItemSimpleInfoListFrameByTitle', $params, 'OtapiItemInfoListFrameAnswer', $request);
    }

    /**
     *  Информация о лицевом счете<br/><a href="/docs/IOtapiService.html#M:GetAccountInfo">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiAccountInfoAnswer

     */
    public static function GetAccountInfo($language, $sessionId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetAccountInfo', $params, 'OtapiAccountInfoAnswer', $request);
    }

    /**
     *  Информация о лицевом счете<br/><a href="/docs/IOtapiService.html#M:GetAccountInfoForOperator">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $customerId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiAccountAdministrationInfoAnswer

     */
    public static function GetAccountInfoForOperator($sessionId, $customerId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'customerId' => $customerId,
        );
        self::registerRequest('GetAccountInfoForOperator', $params, 'OtapiAccountAdministrationInfoAnswer', $request);
    }

    /**
     *  Получить список доступных действий<br/><a href="/docs/IOtapiService.html#M:GetActionList">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiActionInfoListAnswer

     */
    public static function GetActionList($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetActionList', $params, 'OtapiActionInfoListAnswer', $request);
    }

    /**
     *  Получить полный список округов/административный делений КНР<br/><a href="/docs/IOtapiService.html#M:GetAllAreaList">Документация</a> 
     *
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiAreaListAnswer

     */
    public static function GetAllAreaList($language, &$request) {
        $params = array(
            'language' => $language,
        );
        self::registerRequest('GetAllAreaList', $params, 'OtapiAreaListAnswer', $request);
    }
    
    /**
     *  Получить настройки автоматических подборок<br/><a href="documentation?name=GetAutoRatingListsSettings">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $includeMetaInfo
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiAutoRatingListsSettingsAnswer
    
     */
    public static function GetAutoRatingListsSettings($language, $sessionId, $includeMetaInfo, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'includeMetaInfo' => $includeMetaInfo,
        );
        self::registerRequest('GetAutoRatingListsSettings', $params, 'OtapiAutoRatingListsSettingsAnswer', $request);
    }
    
    /**
     *  Обновить настройки автоматических подборок<br/><a href="documentation?name=UpdateAutoRatingListsSettings">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $xmlUpdateData
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer
    
     */
    public static function UpdateAutoRatingListsSettings($language, $sessionId, $xmlUpdateData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateAutoRatingListsSettings', $params, 'VoidOtapiAnswer', $request);
    }    
    
    /**
     *  Запустить экспорт заказа в провайдера<br/><a href="documentation?name=RunAutoRatingListsUpdating">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBackgroundActivityIdentificationInfoAnswer
    
     */
    public static function RunAutoRatingListsUpdating($language, $sessionId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
        );
        self::registerRequest('RunAutoRatingListsUpdating', $params, 'OtapiBackgroundActivityIdentificationInfoAnswer', $request);
    }    

    /**
     *  Получение списка возможных фич текущего источника товаров<br/><a href="/docs/IOtapiService.html#M:GetAvailableFeatureInfoList">Документация</a> 
     *
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiFeatureInfoListAnswer

     */
    public static function GetAvailableFeatureInfoList(&$request) {
        $params = array(
        );
        self::registerRequest('GetAvailableFeatureInfoList', $params, 'OtapiFeatureInfoListAnswer', $request);
    }

    /**
     *  Получение списка доступных ролей пользователя приложения для инстанса<br/><a href="/docs/IOtapiService.html#M:GetAvailableRoleList">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiInstanceUserRoleInfoListAnswer

     */
    public static function GetAvailableRoleList($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetAvailableRoleList', $params, 'OtapiInstanceUserRoleInfoListAnswer', $request);
    }

    /**
     *  Получение содержимого корзины<br/><a href="/docs/IOtapiService.html#M:GetBasket">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCollectionInfoAnswer

     */
    public static function GetBasket($language, $sessionId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetBasket', $params, 'OtapiCollectionInfoAnswer', $request);
    }

    /**
     *  Получить черный список для статистики.<br/><a href="/docs/IOtapiService.html#M:GetBlackListContents">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiContentListListAnswer

     */
    public static function GetBlackListContents($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetBlackListContents', $params, 'OtapiContentListListAnswer', $request);
    }

    /**
     *  Получение информации о бренде<br/><a href="/docs/IOtapiService.html#M:GetBrandInfo">Документация</a> 
     *
     * @param string $language    
     * @param string $brandId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBrandInfoAnswer

     */
    public static function GetBrandInfo($language, $brandId, &$request) {
        $params = array(
            'language' => $language,
            'brandId' => $brandId,
        );
        self::registerRequest('GetBrandInfo', $params, 'OtapiBrandInfoAnswer', $request);
    }

    /**
     *  Получение полного списка брендов, включая скрытые<br/><a href="/docs/IOtapiService.html#M:GetBrandInfoFullList">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBrandInfoListAnswer

     */
    public static function GetBrandInfoFullList($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetBrandInfoFullList', $params, 'OtapiBrandInfoListAnswer', $request);
    }

    /**
     *  Получение списка брендов<br/><a href="/docs/IOtapiService.html#M:GetBrandInfoList">Документация</a> 
     *
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBrandInfoListAnswer

     */
    public static function GetBrandInfoList($language, &$request) {
        $params = array(
            'language' => $language,
        );
        self::registerRequest('GetBrandInfoList', $params, 'OtapiBrandInfoListAnswer', $request);
    }

    /**
     *  Получение частичного списка брендов<br/><a href="/docs/IOtapiService.html#M:GetBrandInfoListFrame">Документация</a> 
     *
     * @param string $language    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBrandInfoListFrameAnswer

     */
    public static function GetBrandInfoListFrame($language, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('GetBrandInfoListFrame', $params, 'OtapiBrandInfoListFrameAnswer', $request);
    }

    /**
     *  Получение подборки брендов<br/><a href="/docs/IOtapiService.html#M:GetBrandRatingList">Документация</a> 
     *
     * @param string $language    
     * @param string $itemRatingTypeId    
     * @param string $numberItem    
     * @param string $categoryId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBrandInfoListAnswer

     */
    public static function GetBrandRatingList($language, $itemRatingTypeId, $numberItem, $categoryId, &$request) {
        $params = array(
            'language' => $language,
            'itemRatingTypeId' => $itemRatingTypeId,
            'numberItem' => $numberItem,
            'categoryId' => $categoryId,
        );
        self::registerRequest('GetBrandRatingList', $params, 'OtapiBrandInfoListAnswer', $request);
    }

    /**
     *  Получение частичной подборки брендов<br/><a href="/docs/IOtapiService.html#M:GetBrandRatingListFrame">Документация</a> 
     *
     * @param string $language    
     * @param string $itemRatingTypeId    
     * @param string $categoryId    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBrandInfoListFrameAnswer

     */
    public static function GetBrandRatingListFrame($language, $itemRatingTypeId, $categoryId, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'itemRatingTypeId' => $itemRatingTypeId,
            'categoryId' => $categoryId,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('GetBrandRatingListFrame', $params, 'OtapiBrandInfoListFrameAnswer', $request);
    }

    /**
     *  Получение архивных данных по числу вызовов<br/><a href="/docs/IOtapiService.html#M:GetCallArchives">Документация</a> 
     *
     * @param string $xmlSearchParameters    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCallArchiveAnswer

     */
    public static function GetCallArchives($xmlSearchParameters, &$request) {
        $params = array(
            'xmlSearchParameters' => $xmlSearchParameters,
        );
        self::registerRequest('GetCallArchives', $params, 'OtapiCallArchiveAnswer', $request);
    }

    /**
     *  Получение статистики вызовов по приложению<br/><a href="/docs/IOtapiService.html#M:GetCallStatistics">Документация</a> 
     *
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCallStatisticsAnswer

     */
    public static function GetCallStatistics(&$request) {
        $params = array(
        );
        self::registerRequest('GetCallStatistics', $params, 'OtapiCallStatisticsAnswer', $request);
    }

    /**
     *  Получение списка категорий, привязанных к группе ценообразования<br/><a href="/docs/IOtapiService.html#M:GetCategoriesOfPriceFormationGroup">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $priceFormationGroupId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCategoryListAnswer

     */
    public static function GetCategoriesOfPriceFormationGroup($sessionId, $priceFormationGroupId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'priceFormationGroupId' => $priceFormationGroupId,
        );
        self::registerRequest('GetCategoriesOfPriceFormationGroup', $params, 'OtapiCategoryListAnswer', $request);
    }

    /**
     *  Получение информации о категории<br/><a href="/docs/IOtapiService.html#M:GetCategoryInfo">Документация</a> 
     *
     * @param string $language    
     * @param string $categoryId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCategoryAnswer

     */
    public static function GetCategoryInfo($language, $categoryId, &$request) {
        $params = array(
            'language' => $language,
            'categoryId' => $categoryId,
        );
        self::registerRequest('GetCategoryInfo', $params, 'OtapiCategoryAnswer', $request);
    }

    /**
     *  Получение информации о списке категорий<br/><a href="/docs/IOtapiService.html#M:GetCategoryInfoList">Документация</a> 
     *
     * @param string $language    
     * @param string $categoryIds    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCategoryListAnswer

     */
    public static function GetCategoryInfoList($language, $categoryIds, &$request) {
        $params = array(
            'language' => $language,
            'categoryIds' => $categoryIds,
        );
        self::registerRequest('GetCategoryInfoList', $params, 'OtapiCategoryListAnswer', $request);
    }

    /**
     *  Получение частичного списка товаров категории<br/><a href="/docs/IOtapiService.html#M:GetCategoryItemInfoListFrame">Документация</a> 
     *
     * @param string $language    
     * @param string $categoryId    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemInfoListFrameAnswer

     */
    public static function GetCategoryItemInfoListFrame($language, $categoryId, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'categoryId' => $categoryId,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('GetCategoryItemInfoListFrame', $params, 'OtapiItemInfoListFrameAnswer', $request);
    }

    /**
     *  Получение упрощенного частичного списка товаров категории<br/><a href="/docs/IOtapiService.html#M:GetCategoryItemSimpleInfoListFrame">Документация</a> 
     *
     * @param string $language    
     * @param string $categoryId    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemInfoListFrameAnswer

     */
    public static function GetCategoryItemSimpleInfoListFrame($language, $categoryId, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'categoryId' => $categoryId,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('GetCategoryItemSimpleInfoListFrame', $params, 'OtapiItemInfoListFrameAnswer', $request);
    }

    /**
     *  Получение подборки категорий<br/><a href="/docs/IOtapiService.html#M:GetCategoryRatingList">Документация</a> 
     *
     * @param string $language    
     * @param string $itemRatingTypeId    
     * @param string $numberItem    
     * @param string $categoryId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCategoryListAnswer

     */
    public static function GetCategoryRatingList($language, $itemRatingTypeId, $numberItem, $categoryId, &$request) {
        $params = array(
            'language' => $language,
            'itemRatingTypeId' => $itemRatingTypeId,
            'numberItem' => $numberItem,
            'categoryId' => $categoryId,
        );
        self::registerRequest('GetCategoryRatingList', $params, 'OtapiCategoryListAnswer', $request);
    }

    /**
     *  Получение частичной подборки категорий<br/><a href="/docs/IOtapiService.html#M:GetCategoryRatingListFrame">Документация</a> 
     *
     * @param string $language    
     * @param string $itemRatingTypeId    
     * @param string $categoryId    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCategoryListFrameAnswer

     */
    public static function GetCategoryRatingListFrame($language, $itemRatingTypeId, $categoryId, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'itemRatingTypeId' => $itemRatingTypeId,
            'categoryId' => $categoryId,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('GetCategoryRatingListFrame', $params, 'OtapiCategoryListFrameAnswer', $request);
    }

    /**
     *  Получение списка родительских категорий (путь к корню)<br/><a href="/docs/IOtapiService.html#M:GetCategoryRootPath">Документация</a> 
     *
     * @param string $language    
     * @param string $categoryId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCategoryListAnswer

     */
    public static function GetCategoryRootPath($language, $categoryId, &$request) {
        $params = array(
            'language' => $language,
            'categoryId' => $categoryId,
        );
        self::registerRequest('GetCategoryRootPath', $params, 'OtapiCategoryListAnswer', $request);
    }

    /**
     *  Получение списка свойств для поиска товаров в категории<br/><a href="/docs/IOtapiService.html#M:GetCategorySearchProperties">Документация</a> 
     *
     * @param string $language    
     * @param string $categoryId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSearchPropertyListAnswer

     */
    public static function GetCategorySearchProperties($language, $categoryId, &$request) {
        $params = array(
            'language' => $language,
            'categoryId' => $categoryId,
        );
        self::registerRequest('GetCategorySearchProperties', $params, 'OtapiSearchPropertyListAnswer', $request);
    }

    /**
     *  Получение списка подкатегорий указанной категории<br/><a href="/docs/IOtapiService.html#M:GetCategorySubcategoryInfoList">Документация</a> 
     *
     * @param string $language    
     * @param string $parentCategoryId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCategoryListAnswer

     */
    public static function GetCategorySubcategoryInfoList($language, $parentCategoryId, &$request) {
        $params = array(
            'language' => $language,
            'parentCategoryId' => $parentCategoryId,
        );
        self::registerRequest('GetCategorySubcategoryInfoList', $params, 'OtapiCategoryListAnswer', $request);
    }

    /**
     *  Получить общие настройки инстанса<br/><a href="/docs/IOtapiService.html#M:GetCommonInstanceOptionsInfo">Документация</a> 
     *
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCommonInstanceOptionsInfoAnswer

     */
    public static function GetCommonInstanceOptionsInfo($language, &$request) {
        $params = array(
            'language' => $language,
        );
        self::registerRequest('GetCommonInstanceOptionsInfo', $params, 'OtapiCommonInstanceOptionsInfoAnswer', $request);
    }

    /**
     *  [NotAvailable] Получение списка сконфигурированных товаров<br/><a href="/docs/IOtapiService.html#M:GetConfiguredItemInfoList">Документация</a> 
     *
     * @param string $language    
     * @param string $itemId    
     * @param string $configurators    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiConfiguredItemListAnswer

     */
    public static function GetConfiguredItemInfoList($language, $itemId, $configurators, &$request) {
        $params = array(
            'language' => $language,
            'itemId' => $itemId,
            'configurators' => $configurators,
        );
        self::registerRequest('GetConfiguredItemInfoList', $params, 'OtapiConfiguredItemListAnswer', $request);
    }

    /**
     *  Получить допустимые виды контента статистики.<br/><a href="/docs/IOtapiService.html#M:GetContentTypes">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiContentTypesAnswer

     */
    public static function GetContentTypes($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetContentTypes', $params, 'OtapiContentTypesAnswer', $request);
    }

    /**
     *  Получить список доступных стран<br/><a href="/docs/IOtapiService.html#M:GetCountryInfoList">Документация</a> 
     *
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCountryInfoListAnswer

     */
    public static function GetCountryInfoList($language, &$request) {
        $params = array(
            'language' => $language,
        );
        self::registerRequest('GetCountryInfoList', $params, 'OtapiCountryInfoListAnswer', $request);
    }

    /**
     *  Получение используемой валюты приложения<br/><a href="/docs/IOtapiService.html#M:GetCurrency">Документация</a> 
     *
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCurrencyInfoAnswer

     */
    public static function GetCurrency($language, &$request) {
        $params = array(
            'language' => $language,
        );
        self::registerRequest('GetCurrency', $params, 'OtapiCurrencyInfoAnswer', $request);
    }

    /**
     *  Используемые валюты<br/><a href="/docs/IOtapiService.html#M:GetCurrencyInstanceList">Документация</a> 
     *
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiInstanceListOfCurrencyInfoAnswer

     */
    public static function GetCurrencyInstanceList(&$request) {
        $params = array(
        );
        self::registerRequest('GetCurrencyInstanceList', $params, 'OtapiInstanceListOfCurrencyInfoAnswer', $request);
    }

    /**
     *  Получение списка доступных валют.<br/><a href="/docs/IOtapiService.html#M:GetCurrencyList">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCurrencyInfoListAnswer

     */
    public static function GetCurrencyList($sessionId, $language, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'language' => $language,
        );
        self::registerRequest('GetCurrencyList', $params, 'OtapiCurrencyInfoListAnswer', $request);
    }

    /**
     *  Получение списка доступных способов синхронизации валют.<br/><a href="/docs/IOtapiService.html#M:GetCurrencySynchronizationModeList">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCurrencySynchronizationModeListAnswer

     */
    public static function GetCurrencySynchronizationModeList($sessionId, $language, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'language' => $language,
        );
        self::registerRequest('GetCurrencySynchronizationModeList', $params, 'OtapiCurrencySynchronizationModeListAnswer', $request);
    }

    /**
     *  <a href="documentation?name=GetCurrentBoxStatistics">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $xmlParameters
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBoxStatisticsAnswer

     */
    public static function GetCurrentBoxStatistics($language, $sessionId, $xmlParameters, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlParameters' => $xmlParameters,
        );
        self::registerRequest('GetCurrentBoxStatistics', $params, 'OtapiBoxStatisticsAnswer', $request);
    }

    /**
     *  <a href="documentation?name=GetArchiveBoxStatistics">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $xmlParameters
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBoxStatisticsListAnswer

     */
    public static function GetArchiveBoxStatistics($language, $sessionId, $xmlParameters, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlParameters' => $xmlParameters,
        );
        self::registerRequest('GetArchiveBoxStatistics', $params, 'OtapiBoxStatisticsListAnswer', $request);
    }

    /**
     *  Получить историю обработки заказа<br/><a href="/docs/IOtapiService.html#M:GetCustomerSalesProcessLog">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $salesId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSalesProcLogInfoListAnswer

     */
    public static function GetCustomerSalesProcessLog($sessionId, $salesId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'salesId' => $salesId,
        );
        self::registerRequest('GetCustomerSalesProcessLog', $params, 'OtapiSalesProcLogInfoListAnswer', $request);
    }

    /**
     *  Получение списка стран, в которые возможна доставка<br/><a href="/docs/IOtapiService.html#M:GetDeliveryCountryInfoList">Документация</a> 
     *
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCountryInfoListAnswer

     */
    public static function GetDeliveryCountryInfoList($language, &$request) {
        $params = array(
            'language' => $language,
        );
        self::registerRequest('GetDeliveryCountryInfoList', $params, 'OtapiCountryInfoListAnswer', $request);
    }

    /**
     *  Получение списка с данными о способах доставки систем служб доставки<br/><a href="documentation?name=GetDeliveryModesByServiceSystem">Документация</a>
     *
     * @param string $language
     * @param string $serviceSystem
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiDeliveryModeServiceSystemInfoListAnswer

     */
    public static function GetDeliveryModesByServiceSystem($language, $serviceSystem, &$request) {
        $params = array(
            'language' => $language,
            'serviceSystem' => $serviceSystem,
        );
        self::registerRequest('GetDeliveryModesByServiceSystem', $params, 'OtapiDeliveryModeServiceSystemInfoListAnswer', $request);
    }

    /**
     *  Получение способов доставки<br/><a href="/docs/IOtapiService.html#M:GetDeliveryModesWithPrice">Документация</a> 
     *
     * @param string $language    
     * @param string $countryId    
     * @param string $weight    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiDeliveryModeListAnswer

     */
    public static function GetDeliveryModesWithPrice($language, $countryId, $weight, &$request) {
        $params = array(
            'language' => $language,
            'countryId' => $countryId,
            'weight' => $weight,
        );
        self::registerRequest('GetDeliveryModesWithPrice', $params, 'OtapiDeliveryModeListAnswer', $request);
    }

    /**
     *  Получение списка с данными о системах служб доставки<br/><a href="/docs/IOtapiService.html#M:GetDeliveryServiceSystemInfoList">Документация</a> 
     *
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiDeliveryServiceSystemInfoListAnswer

     */
    public static function GetDeliveryServiceSystemInfoList($language, &$request) {
        $params = array(
            'language' => $language,
        );
        self::registerRequest('GetDeliveryServiceSystemInfoList', $params, 'OtapiDeliveryServiceSystemInfoListAnswer', $request);
    }

    /**
     *  Получение группы скидок для конкретного пользователя<br/><a href="/docs/IOtapiService.html#M:GetDiscountGroup">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiDiscountGroupInfoAnswer

     */
    public static function GetDiscountGroup($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetDiscountGroup', $params, 'OtapiDiscountGroupInfoAnswer', $request);
    }

    /**
     *  Получение списка доступных групп скидок<br/><a href="/docs/IOtapiService.html#M:GetDiscountGroupList">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiDiscountGroupInfoListAnswer

     */
    public static function GetDiscountGroupList($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetDiscountGroupList', $params, 'OtapiDiscountGroupInfoListAnswer', $request);
    }

    /**
     *  Получение списка категории для админки<br/><a href="/docs/IOtapiService.html#M:GetEditableCategorySubcategories">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $language    
     * @param string $parentCategoryId    
     * @param string $needHighlightParentsOfDeletedCategories    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return EditableOtapiCategoryListAnswer

     */
    public static function GetEditableCategorySubcategories($sessionId, $language, $parentCategoryId, $needHighlightParentsOfDeletedCategories, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'language' => $language,
            'parentCategoryId' => $parentCategoryId,
            'needHighlightParentsOfDeletedCategories' => $needHighlightParentsOfDeletedCategories,
        );
        self::registerRequest('GetEditableCategorySubcategories', $params, 'EditableOtapiCategoryListAnswer', $request);
    }

    /**
     *  Получение списка включенных возможностей приложения<br/><a href="/docs/IOtapiService.html#M:GetEnabledFeatures">Документация</a> 
     *
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiEnabledFeaturesAnswer

     */
    public static function GetEnabledFeatures(&$request) {
        $params = array(
        );
        self::registerRequest('GetEnabledFeatures', $params, 'OtapiEnabledFeaturesAnswer', $request);
    }

    /**
     *  Получение тарифа внешней доставки<br/><a href="/docs/IOtapiService.html#M:GetExternalDeliveryRate">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $externalDeliveryRateId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiExternalDeliveryRateAnswer

     */
    public static function GetExternalDeliveryRate($sessionId, $externalDeliveryRateId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'externalDeliveryRateId' => $externalDeliveryRateId,
        );
        self::registerRequest('GetExternalDeliveryRate', $params, 'OtapiExternalDeliveryRateAnswer', $request);
    }

    /**
     *  Получение списка тарифов внешней доставки<br/><a href="/docs/IOtapiService.html#M:GetExternalDeliveryRateList">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiExternalDeliveryRateListAnswer

     */
    public static function GetExternalDeliveryRateList($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetExternalDeliveryRateList', $params, 'OtapiExternalDeliveryRateListAnswer', $request);
    }

    /**
     *  Получение способа внешней доставки<br/><a href="/docs/IOtapiService.html#M:GetExternalDeliveryType">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $externalDeliveryTypeId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiExternalDeliveryTypeAnswer

     */
    public static function GetExternalDeliveryType($sessionId, $externalDeliveryTypeId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'externalDeliveryTypeId' => $externalDeliveryTypeId,
        );
        self::registerRequest('GetExternalDeliveryType', $params, 'OtapiExternalDeliveryTypeAnswer', $request);
    }

    /**
     *  Получение cписок способов внешней доставки<br/><a href="/docs/IOtapiService.html#M:GetExternalDeliveryTypeList">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiExternalDeliveryTypeListAnswer

     */
    public static function GetExternalDeliveryTypeList($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetExternalDeliveryTypeList', $params, 'OtapiExternalDeliveryTypeListAnswer', $request);
    }

    /**
     *  Получение списка избранных продавцов<br/><a href="/docs/IOtapiService.html#M:GetFavoriteVendors">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCollectionInfoAnswer

     */
    public static function GetFavoriteVendors($language, $sessionId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetFavoriteVendors', $params, 'OtapiCollectionInfoAnswer', $request);
    }

    /**
     *  Получение глобального списка брендов<br/><a href="/docs/IOtapiService.html#M:GetGlobalBrandInfoList">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBrandInfoListAnswer

     */
    public static function GetGlobalBrandInfoList($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetGlobalBrandInfoList', $params, 'OtapiBrandInfoListAnswer', $request);
    }

    /**
     *  Получение глобальной статистики вызовов<br/><a href="/docs/IOtapiService.html#M:GetGlobalCallStatistics">Документация</a> 
     *
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCallStatisticsAnswer

     */
    public static function GetGlobalCallStatistics(&$request) {
        $params = array(
        );
        self::registerRequest('GetGlobalCallStatistics', $params, 'OtapiCallStatisticsAnswer', $request);
    }

    /**
     *  Получение настроек валюты приложения.<br/><a href="/docs/IOtapiService.html#M:GetInstanceCurrenciesSettings">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCurrencySettingsAnswer

     */
    public static function GetInstanceCurrenciesSettings($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetInstanceCurrenciesSettings', $params, 'OtapiCurrencySettingsAnswer', $request);
    }

    /**
     *  Получение отображаемых на витрине валют приложения.<br/><a href="/docs/IOtapiService.html#M:GetInstanceCurrencyInfoList">Документация</a> 
     *
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiInstanceListOfCurrencyInfoAnswer

     */
    public static function GetInstanceCurrencyInfoList($language, &$request) {
        $params = array(
            'language' => $language,
        );
        self::registerRequest('GetInstanceCurrencyInfoList', $params, 'OtapiInstanceListOfCurrencyInfoAnswer', $request);
    }

    /**
     *  Текущие курсы валют<br/><a href="/docs/IOtapiService.html#M:GetInstanceCurrencyRateList">Документация</a> 
     *
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCurrencyRateListAnswer

     */
    public static function GetInstanceCurrencyRateList(&$request) {
        $params = array(
        );
        self::registerRequest('GetInstanceCurrencyRateList', $params, 'OtapiCurrencyRateListAnswer', $request);
    }

    /**
     *  Получить настройки инстанса<br/><a href="documentation?name=GetInstanceOptionsInfo">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiInstanceOptionsInfoAnswer

     */
    public static function GetInstanceOptionsInfo($language, $sessionId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetInstanceOptionsInfo', $params, 'OtapiInstanceOptionsInfoAnswer', $request);
    }

    /**
     *  Получить список действий пользователя<br/><a href="/docs/IOtapiService.html#M:GetInstanceUserActionList">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiActionInfoListAnswer

     */
    public static function GetInstanceUserActionList($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetInstanceUserActionList', $params, 'OtapiActionInfoListAnswer', $request);
    }

    /**
     *  Получить список пользователей приложения<br/><a href="/docs/IOtapiService.html#M:GetInstanceUserList">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiInstanceUserInfoListAnswer

     */
    public static function GetInstanceUserList($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetInstanceUserList', $params, 'OtapiInstanceUserInfoListAnswer', $request);
    }

    /**
     *  Получение списка ролей пользователя приложения<br/><a href="/docs/IOtapiService.html#M:GetInstanceUserRoleList">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiInstanceUserRoleInfoListAnswer

     */
    public static function GetInstanceUserRoleList($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetInstanceUserRoleList', $params, 'OtapiInstanceUserRoleInfoListAnswer', $request);
    }

    /**
     *  Получение списка ролей пользователя приложения<br/><a href="/docs/IOtapiService.html#M:GetInstanceUserRoleListByLogin">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $userLogin    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiInstanceUserRoleInfoListAnswer

     */
    public static function GetInstanceUserRoleListByLogin($sessionId, $userLogin, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'userLogin' => $userLogin,
        );
        self::registerRequest('GetInstanceUserRoleListByLogin', $params, 'OtapiInstanceUserRoleInfoListAnswer', $request);
    }

    /**
     *  Получение полного списка доставок товара (во все регионы)<br/><a href="/docs/IOtapiService.html#M:GetItemDeliveryCosts">Документация</a> 
     *
     * @param string $language    
     * @param string $itemId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiDeliveryCostListAnswer

     */
    public static function GetItemDeliveryCosts($language, $itemId, &$request) {
        $params = array(
            'language' => $language,
            'itemId' => $itemId,
        );
        self::registerRequest('GetItemDeliveryCosts', $params, 'OtapiDeliveryCostListAnswer', $request);
    }

    /**
     *  Получение описания товара<br/><a href="/docs/IOtapiService.html#M:GetItemDescription">Документация</a> 
     *
     * @param string $language    
     * @param string $itemId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemDescriptionAnswer

     */
    public static function GetItemDescription($language, $itemId, &$request) {
        $params = array(
            'language' => $language,
            'itemId' => $itemId,
        );
        self::registerRequest('GetItemDescription', $params, 'OtapiItemDescriptionAnswer', $request);
    }

    /**
     *  Получение полной информации о товаре<br/><a href="/docs/IOtapiService.html#M:GetItemFullInfo">Документация</a> 
     *
     * @param string $language    
     * @param string $itemId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemFullInfoAnswer

     */
    public static function GetItemFullInfo($language, $itemId, &$request) {
        $params = array(
            'language' => $language,
            'itemId' => $itemId,
        );
        self::registerRequest('GetItemFullInfo', $params, 'OtapiItemFullInfoAnswer', $request);
    }

    /**
     *  Получение полной информации о товаре вместе с информацией о доставке<br/><a href="/docs/IOtapiService.html#M:GetItemFullInfoWithDeliveryCosts">Документация</a> 
     *
     * @param string $language    
     * @param string $itemId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemFullInfoAnswer

     */
    public static function GetItemFullInfoWithDeliveryCosts($language, $itemId, &$request) {
        $params = array(
            'language' => $language,
            'itemId' => $itemId,
        );
        self::registerRequest('GetItemFullInfoWithDeliveryCosts', $params, 'OtapiItemFullInfoAnswer', $request);
    }

    /**
     *  Получение полной информации о товаре вместе с информацией о промоакциях<br/><a href="/docs/IOtapiService.html#M:GetItemFullInfoWithPromotions">Документация</a> 
     *
     * @param string $language    
     * @param string $itemId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemFullInfoAnswer

     */
    public static function GetItemFullInfoWithPromotions($language, $itemId, &$request) {
        $params = array(
            'language' => $language,
            'itemId' => $itemId,
        );
        self::registerRequest('GetItemFullInfoWithPromotions', $params, 'OtapiItemFullInfoAnswer', $request);
    }

    /**
     *  Получение информация о товаре<br/><a href="/docs/IOtapiService.html#M:GetItemInfo">Документация</a> 
     *
     * @param string $language    
     * @param string $itemId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemInfoAnswer

     */
    public static function GetItemInfo($language, $itemId, &$request) {
        $params = array(
            'language' => $language,
            'itemId' => $itemId,
        );
        self::registerRequest('GetItemInfo', $params, 'OtapiItemInfoAnswer', $request);
    }

    /**
     *  Получение информация о списке товаров<br/><a href="/docs/IOtapiService.html#M:GetItemInfoList">Документация</a> 
     *
     * @param string $language    
     * @param string $idsList    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemInfoListAnswer

     */
    public static function GetItemInfoList($language, $idsList, &$request) {
        $params = array(
            'language' => $language,
            'idsList' => $idsList,
        );
        self::registerRequest('GetItemInfoList', $params, 'OtapiItemInfoListAnswer', $request);
    }

    /**
     *  Получение оригинального описания товара, без перевода<br/><a href="/docs/IOtapiService.html#M:GetItemOriginalDescription">Документация</a> 
     *
     * @param string $itemId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemDescriptionAnswer

     */
    public static function GetItemOriginalDescription($itemId, &$request) {
        $params = array(
            'itemId' => $itemId,
        );
        self::registerRequest('GetItemOriginalDescription', $params, 'OtapiItemDescriptionAnswer', $request);
    }

    /**
     *  Расчет цены товара на основе механизма ЦО приложения<br/><a href="/docs/IOtapiService.html#M:GetItemPrice">Документация</a> 
     *
     * @param string $quantity    
     * @param string $itemId    
     * @param string $promotionId    
     * @param string $configurationId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiPriceAnswer

     */
    public static function GetItemPrice($quantity, $itemId, $promotionId, $configurationId, &$request) {
        $params = array(
            'quantity' => $quantity,
            'itemId' => $itemId,
            'promotionId' => $promotionId,
            'configurationId' => $configurationId,
        );
        self::registerRequest('GetItemPrice', $params, 'OtapiPriceAnswer', $request);
    }

    /**
     *  Получение списка скидок на товар<br/><a href="/docs/IOtapiService.html#M:GetItemPromotions">Документация</a> 
     *
     * @param string $language    
     * @param string $itemId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemPromotionListAnswer

     */
    public static function GetItemPromotions($language, $itemId, &$request) {
        $params = array(
            'language' => $language,
            'itemId' => $itemId,
        );
        self::registerRequest('GetItemPromotions', $params, 'OtapiItemPromotionListAnswer', $request);
    }

    /**
     *  Получение списка скидок на товар по попыткам<br/><a href="/docs/IOtapiService.html#M:GetItemPromotionsWithAttempts">Документация</a> 
     *
     * @param string $language    
     * @param string $itemId    
     * @param string $attemptCount    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemPromotionListAnswer

     */
    public static function GetItemPromotionsWithAttempts($language, $itemId, $attemptCount, &$request) {
        $params = array(
            'language' => $language,
            'itemId' => $itemId,
            'attemptCount' => $attemptCount,
        );
        self::registerRequest('GetItemPromotionsWithAttempts', $params, 'OtapiItemPromotionListAnswer', $request);
    }

    /**
     *  Получение подборки товаров<br/><a href="/docs/IOtapiService.html#M:GetItemRatingList">Документация</a> 
     *
     * @param string $language    
     * @param string $itemRatingTypeId    
     * @param string $numberItem    
     * @param string $categoryId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemInfoRatingListAnswer

     */
    public static function GetItemRatingList($language, $itemRatingTypeId, $numberItem, $categoryId, &$request) {
        $params = array(
            'language' => $language,
            'itemRatingTypeId' => $itemRatingTypeId,
            'numberItem' => $numberItem,
            'categoryId' => $categoryId,
        );
        self::registerRequest('GetItemRatingList', $params, 'OtapiItemInfoRatingListAnswer', $request);
    }

    /**
     *  Поиск подборки брендов<br/><a href="documentation?name=SearchRatingListBrands">Документация</a> 
     *
     * @param string $language    
     * @param string $xmlSearchParameters    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBrandInfoListFrameAnswer

     */
    public static function SearchRatingListBrands($language, $xmlSearchParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'xmlSearchParameters' => $xmlSearchParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchRatingListBrands', $params, 'OtapiBrandInfoListFrameAnswer', $request);
    }


    /**
     *  Поиск подборки категорий<br/><a href="documentation?name=SearchRatingListCategories">Документация</a> 
     *
     * @param string $language    
     * @param string $xmlSearchParameters    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCategoryListFrameAnswer

     */
    public static function SearchRatingListCategories($language, $xmlSearchParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'xmlSearchParameters' => $xmlSearchParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchRatingListCategories', $params, 'OtapiCategoryListFrameAnswer', $request);
    }


    /**
     *  Поиск подборки продавцов<br/><a href="documentation?name=SearchRatingListVendors">Документация</a> 
     *
     * @param string $language    
     * @param string $xmlSearchParameters    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiVendorInfoListFrameAnswer

     */
    public static function SearchRatingListVendors($language, $xmlSearchParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'xmlSearchParameters' => $xmlSearchParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchRatingListVendors', $params, 'OtapiVendorInfoListFrameAnswer', $request);
    }



    /**
     *  Получение подборки поисковых строк<br/><a href="documentation?name=SearchRatingListSearchStrings">Документация</a> 
     *
     * @param string $language    
     * @param string $xmlSearchParameters    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSearchRatingListSearchStringsAnswer

     */
    public static function SearchRatingListSearchStrings($language, $xmlSearchParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'xmlSearchParameters' => $xmlSearchParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchRatingListSearchStrings', $params, 'OtapiSearchRatingListSearchStringsAnswer', $request);
    }




    /**
     *  Получение частичной подборки товаров<br/><a href="/docs/IOtapiService.html#M:GetItemRatingListFrame">Документация</a> 
     *
     * @param string $language    
     * @param string $itemRatingTypeId    
     * @param string $categoryId    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemInfoListFrameAnswer

     */
    public static function GetItemRatingListFrame($language, $itemRatingTypeId, $categoryId, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'itemRatingTypeId' => $itemRatingTypeId,
            'categoryId' => $categoryId,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('GetItemRatingListFrame', $params, 'OtapiItemInfoListFrameAnswer', $request);
    }

    /**
     *  Получение списка родительских категорий товара (путь к корню)<br/><a href="/docs/IOtapiService.html#M:GetItemRootPath">Документация</a> 
     *
     * @param string $language    
     * @param string $itemId    
     * @param string $taoBaoCategoryId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCategoryListAnswer

     */
    public static function GetItemRootPath($language, $itemId, $taoBaoCategoryId, &$request) {
        $params = array(
            'language' => $language,
            'itemId' => $itemId,
            'taoBaoCategoryId' => $taoBaoCategoryId,
        );
        self::registerRequest('GetItemRootPath', $params, 'OtapiCategoryListAnswer', $request);
    }

    /**
     *  Расчет общей стоимости заданного количества товаров на основе механизма ЦО приложения<br/><a href="/docs/IOtapiService.html#M:GetItemTotalCost">Документация</a> 
     *
     * @param string $quantity    
     * @param string $itemId    
     * @param string $promotionId    
     * @param string $configurationId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiPriceAnswer

     */
    public static function GetItemTotalCost($quantity, $itemId, $promotionId, $configurationId, &$request) {
        $params = array(
            'quantity' => $quantity,
            'itemId' => $itemId,
            'promotionId' => $promotionId,
            'configurationId' => $configurationId,
        );
        self::registerRequest('GetItemTotalCost', $params, 'OtapiPriceAnswer', $request);
    }

    /**
     *  Получить список доступных языков<br/><a href="/docs/IOtapiService.html#M:GetLanguageInfoList">Документация</a> 
     *
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiNamedPropertyListAnswer

     */
    public static function GetLanguageInfoList(&$request) {
        $params = array(
        );
        self::registerRequest('GetLanguageInfoList', $params, 'OtapiNamedPropertyListAnswer', $request);
    }

    /**
     *  Получить доступные статусы строки заказа<br/><a href="/docs/IOtapiService.html#M:GetLineAvailableStatusList">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $salesId    
     * @param string $salesLineId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSalesLineStatusInfoListAnswer

     */
    public static function GetLineAvailableStatusList($sessionId, $salesId, $salesLineId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'salesId' => $salesId,
            'salesLineId' => $salesLineId,
        );
        self::registerRequest('GetLineAvailableStatusList', $params, 'OtapiSalesLineStatusInfoListAnswer', $request);
    }

    /**
     *  Получение содержимого блокнота<br/><a href="/docs/IOtapiService.html#M:GetNote">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCollectionInfoAnswer

     */
    public static function GetNote($language, $sessionId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetNote', $params, 'OtapiCollectionInfoAnswer', $request);
    }

    /**
     *  Получение списка логируемых операций<br/><a href="/docs/IOtapiService.html#M:GetOperationTypes">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiOperationTypeInfoListAnswer

     */
    public static function GetOperationTypes($sessionId, $language, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'language' => $language,
        );
        self::registerRequest('GetOperationTypes', $params, 'OtapiOperationTypeInfoListAnswer', $request);
    }

    /**
     *  Получение информации об операторе<br/><a href="/docs/IOtapiService.html#M:GetOperatorInfo">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiOperatorInfoAnswer

     */
    public static function GetOperatorInfo($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetOperatorInfo', $params, 'OtapiOperatorInfoAnswer', $request);
    }

    /**
     *  Получить дерево прав по сессии оператора<br/><a href="/docs/IOtapiService.html#M:GetOperatorRightTree">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiInstanceRoleRightInfoListAnswer

     */
    public static function GetOperatorRightTree($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetOperatorRightTree', $params, 'OtapiInstanceRoleRightInfoListAnswer', $request);
    }

    /**
     *  Получить список статусов строк заказа<br/><a href="/docs/IOtapiService.html#M:GetOrderLineStatusList">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSalesLineStatusInfoListAnswer

     */
    public static function GetOrderLineStatusList($sessionId, $language, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'language' => $language,
        );
        self::registerRequest('GetOrderLineStatusList', $params, 'OtapiSalesLineStatusInfoListAnswer', $request);
    }

    /**
     *  Получение настроек заказа<br/><a href="documentation?name=GetOrderSettings">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $includeMetaInfo
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiOrderSettingsAnswer

     */
    public static function GetOrderSettings($language, $sessionId, $includeMetaInfo, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'includeMetaInfo' => $includeMetaInfo,
        );
        self::registerRequest('GetOrderSettings', $params, 'OtapiOrderSettingsAnswer', $request);
    }

    /**
     *  Получение настроек заказа<br/><a href="/docs/IOtapiService.html#M:GetOrderSettingsInfo">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiOrderSettingsInfoAnswer

     */
    public static function GetOrderSettingsInfo($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetOrderSettingsInfo', $params, 'OtapiOrderSettingsInfoAnswer', $request);
    }

    /**
     *  Получение истории заказа<br/><a href="/docs/IOtapiService.html#M:GetOrdersHistory">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiOrdersHistoryAnswer

     */
    public static function GetOrdersHistory($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetOrdersHistory', $params, 'OtapiOrdersHistoryAnswer', $request);
    }

    /**
     *  Получить статусы заказа<br/><a href="/docs/IOtapiService.html#M:GetOrderStatusList">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSalesStatusInfoListAnswer

     */
    public static function GetOrderStatusList($sessionId, $language, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'language' => $language,
        );
        self::registerRequest('GetOrderStatusList', $params, 'OtapiSalesStatusInfoListAnswer', $request);
    }

    /**
     *  Информация о лицевом счете пользователя, которому принадлежит заказ<br/><a href="/docs/IOtapiService.html#M:GetOrderUserAccountInfoForOperator">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $orderId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiAccountAdministrationInfoAnswer

     */
    public static function GetOrderUserAccountInfoForOperator($sessionId, $orderId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'orderId' => $orderId,
        );
        self::registerRequest('GetOrderUserAccountInfoForOperator', $params, 'OtapiAccountAdministrationInfoAnswer', $request);
    }

    /**
     *  Получение информации о пользователе заказа<br/><a href="/docs/IOtapiService.html#M:GetOrderUserInfoForOperator">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $orderId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiUserInfoAnswer

     */
    public static function GetOrderUserInfoForOperator($sessionId, $orderId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'orderId' => $orderId,
        );
        self::registerRequest('GetOrderUserInfoForOperator', $params, 'OtapiUserInfoAnswer', $request);
    }

    /**
     *  Список операций по лицевому счету пользователя, которому принадлежит заказ, за период<br/><a href="/docs/IOtapiService.html#M:GetOrderUserStatementForOperator">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $orderId    
     * @param string $dateFrom    
     * @param string $dateTo    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiAccountStatementAdministrationAnswer

     */
    public static function GetOrderUserStatementForOperator($sessionId, $orderId, $dateFrom, $dateTo, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'orderId' => $orderId,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        );
        self::registerRequest('GetOrderUserStatementForOperator', $params, 'OtapiAccountStatementAdministrationAnswer', $request);
    }

    /**
     *  Получение статистики обращений к Отапи<br/><a href="/docs/IOtapiService.html#M:GetOtapiCallStatistic">Документация</a> 
     *
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCallStatisticAnswer

     */
    public static function GetOtapiCallStatistic(&$request) {
        $params = array(
        );
        self::registerRequest('GetOtapiCallStatistic', $params, 'OtapiCallStatisticAnswer', $request);
    }

    /**
     *  Получить данные по посылке<br/><a href="/docs/IOtapiService.html#M:GetPackage">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $packageId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiPackageAdminInfoAnswer

     */
    public static function GetPackage($sessionId, $packageId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'packageId' => $packageId,
        );
        self::registerRequest('GetPackage', $params, 'OtapiPackageAdminInfoAnswer', $request);
    }

    /**
     *  Получить список доступных статусов посылки<br/><a href="/docs/IOtapiService.html#M:GetPackageAvailableStatusList">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $packageId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiPackageStatusInfoListAnswer

     */
    public static function GetPackageAvailableStatusList($sessionId, $packageId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'packageId' => $packageId,
        );
        self::registerRequest('GetPackageAvailableStatusList', $params, 'OtapiPackageStatusInfoListAnswer', $request);
    }

    /**
     *  Получение способов оплаты<br/><a href="/docs/IOtapiService.html#M:GetPaymentModes">Документация</a> 
     *
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiPaymentModeListAnswer

     */
    public static function GetPaymentModes($language, &$request) {
        $params = array(
            'language' => $language,
        );
        self::registerRequest('GetPaymentModes', $params, 'OtapiPaymentModeListAnswer', $request);
    }

    /**
     *  Получить форму для создания платежа<br/><a href="/docs/IOtapiService.html#M:GetPaymentParameters">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $paymentRequestXml    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiPaymentFormAnswer

     */
    public static function GetPaymentParameters($language, $sessionId, $paymentRequestXml, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'paymentRequestXml' => $paymentRequestXml,
        );
        self::registerRequest('GetPaymentParameters', $params, 'OtapiPaymentFormAnswer', $request);
    }

    /**
     *  Получение группы ценообразования<br/><a href="/docs/IOtapiService.html#M:GetPriceFormationGroup">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $priceFormationGroupId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiPriceFormationGroupInfoAnswer

     */
    public static function GetPriceFormationGroup($sessionId, $priceFormationGroupId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'priceFormationGroupId' => $priceFormationGroupId,
        );
        self::registerRequest('GetPriceFormationGroup', $params, 'OtapiPriceFormationGroupInfoAnswer', $request);
    }

    /**
     *  Получение списка доступных групп ценообразования<br/><a href="/docs/IOtapiService.html#M:GetPriceFormationGroupList">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiPriceFormationGroupInfoListAnswer

     */
    public static function GetPriceFormationGroupList($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetPriceFormationGroupList', $params, 'OtapiPriceFormationGroupInfoListAnswer', $request);
    }

    /**
     *  Получение настроек ценообразования инстанса.<br/><a href="/docs/IOtapiService.html#M:GetPriceFormationSettings">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiEditablePriceFormationSettingsAnswer

     */
    public static function GetPriceFormationSettings($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetPriceFormationSettings', $params, 'OtapiEditablePriceFormationSettingsAnswer', $request);
    }

    /**
     *  Получение стратегии ценообразования<br/><a href="/docs/IOtapiService.html#M:GetPriceFormationStrategy">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $language    
     * @param string $strategyId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiStrategyInfoAnswer

     */
    public static function GetPriceFormationStrategy($sessionId, $language, $strategyId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'language' => $language,
            'strategyId' => $strategyId,
        );
        self::registerRequest('GetPriceFormationStrategy', $params, 'OtapiStrategyInfoAnswer', $request);
    }

    /**
     *  Получение списка доступных стратегий ценообразования<br/><a href="/docs/IOtapiService.html#M:GetPriceFormationStrategyList">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiStrategyInfoListAnswer

     */
    public static function GetPriceFormationStrategyList($sessionId, $language, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'language' => $language,
        );
        self::registerRequest('GetPriceFormationStrategyList', $params, 'OtapiStrategyInfoListAnswer', $request);
    }

    /**
     *  Получение рекламируемых товаров по списку идентификатор<br/><a href="/docs/IOtapiService.html#M:GetPromoteItems">Документация</a> 
     *
     * @param string $language    
     * @param string $promotedId    
     * @param string $itemIds    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiPromoItemInfoListAnswer

     */
    public static function GetPromoteItems($language, $promotedId, $itemIds, &$request) {
        $params = array(
            'language' => $language,
            'promotedId' => $promotedId,
            'itemIds' => $itemIds,
        );
        self::registerRequest('GetPromoteItems', $params, 'OtapiPromoItemInfoListAnswer', $request);
    }

    /**
     *  Получение информации о внешней категории<br/><a href="/docs/IOtapiService.html#M:GetProviderCategory">Документация</a> 
     *
     * @param string $language    
     * @param string $categoryId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCategoryAnswer

     */
    public static function GetProviderCategory($language, $categoryId, &$request) {
        $params = array(
            'language' => $language,
            'categoryId' => $categoryId,
        );
        self::registerRequest('GetProviderCategory', $params, 'OtapiCategoryAnswer', $request);
    }

    /**
     *  Получение списка родительских категорий (путь к корню) для внешней категории<br/><a href="/docs/IOtapiService.html#M:GetProviderCategoryRootPath">Документация</a> 
     *
     * @param string $language    
     * @param string $categoryId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCategoryListAnswer

     */
    public static function GetProviderCategoryRootPath($language, $categoryId, &$request) {
        $params = array(
            'language' => $language,
            'categoryId' => $categoryId,
        );
        self::registerRequest('GetProviderCategoryRootPath', $params, 'OtapiCategoryListAnswer', $request);
    }

    /**
     *  Получение списка подкатегорий внешней категории<br/><a href="/docs/IOtapiService.html#M:GetProviderCategorySubcategories">Документация</a> 
     *
     * @param string $language    
     * @param string $categoryId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCategoryListAnswer

     */
    public static function GetProviderCategorySubcategories($language, $categoryId, &$request) {
        $params = array(
            'language' => $language,
            'categoryId' => $categoryId,
        );
        self::registerRequest('GetProviderCategorySubcategories', $params, 'OtapiCategoryListAnswer', $request);
    }

    /**
     *  Получение информации о провайдере<br/><a href="/docs/IOtapiService.html#M:GetProviderInfo">Документация</a> 
     *
     * @param string $providerType    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiProviderInfoAnswer

     */
    public static function GetProviderInfo($providerType, &$request) {
        $params = array(
            'providerType' => $providerType,
        );
        self::registerRequest('GetProviderInfo', $params, 'OtapiProviderInfoAnswer', $request);
    }

    /**
     *  Получение информации о провайдере<br/><a href="/docs/IOtapiService.html#M:GetProviderInfoList">Документация</a> 
     *
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiProviderInfoListAnswer

     */
    public static function GetProviderInfoList($language, &$request) {
        $params = array(
            'language' => $language,
        );
        self::registerRequest('GetProviderInfoList', $params, 'OtapiProviderInfoListAnswer', $request);
    }

    /**
     *  Получение информации о доступных способах поиска<br/><a href="/docs/IOtapiService.html#M:GetProviderSearchMethodInfoList">Документация</a> 
     *
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiProviderSearchMethodInfoListAnswer

     */
    public static function GetProviderSearchMethodInfoList($language, &$request) {
        $params = array(
            'language' => $language,
        );
        self::registerRequest('GetProviderSearchMethodInfoList', $params, 'OtapiProviderSearchMethodInfoListAnswer', $request);
    }

    /**
     *  Получение всех подборок заданного типа контента<br/><a href="/docs/IOtapiService.html#M:GetRatingCollectionsByContent">Документация</a> 
     *
     * @param string $contentType    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemRatingCollectionListAnswer

     */
    public static function GetRatingCollectionsByContent($contentType, &$request) {
        $params = array(
            'contentType' => $contentType,
        );
        self::registerRequest('GetRatingCollectionsByContent', $params, 'OtapiItemRatingCollectionListAnswer', $request);
    }

    /**
     *  Поиск подборок для данного товара<br/><a href="documentation?name=GetRatingListsByElement">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $contentType
     * @param string $itemId
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemRatingCollectionListAnswer

     */
    public static function GetRatingListsByElement($language, $sessionId, $contentType, $itemId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'contentType' => $contentType,
            'itemId' => $itemId,
        );
        self::registerRequest('GetRatingListsByElement', $params, 'OtapiItemRatingCollectionListAnswer', $request);
    }

    /**
     *  Получить дерево прав по роли<br/><a href="/docs/IOtapiService.html#M:GetRightTree">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $roleName    
     * @param string $isTemplate    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiInstanceRoleRightInfoListAnswer

     */
    public static function GetRightTree($sessionId, $roleName, $isTemplate, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'roleName' => $roleName,
            'isTemplate' => $isTemplate,
        );
        self::registerRequest('GetRightTree', $params, 'OtapiInstanceRoleRightInfoListAnswer', $request);
    }

    /**
     *  Получить список действий, привязанных к роли<br/><a href="/docs/IOtapiService.html#M:GetRoleActionList">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $roleName    
     * @param string $isTemplate    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiActionInfoListAnswer

     */
    public static function GetRoleActionList($sessionId, $roleName, $isTemplate, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'roleName' => $roleName,
            'isTemplate' => $isTemplate,
        );
        self::registerRequest('GetRoleActionList', $params, 'OtapiActionInfoListAnswer', $request);
    }

    /**
     *  Получить список корневых округов/административный делений КНР<br/><a href="/docs/IOtapiService.html#M:GetRootAreaList">Документация</a> 
     *
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiAreaListAnswer

     */
    public static function GetRootAreaList($language, &$request) {
        $params = array(
            'language' => $language,
        );
        self::registerRequest('GetRootAreaList', $params, 'OtapiAreaListAnswer', $request);
    }

    /**
     *  Получение списка корневых категорий<br/><a href="/docs/IOtapiService.html#M:GetRootCategoryInfoList">Документация</a> 
     *
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCategoryListAnswer

     */
    public static function GetRootCategoryInfoList($language, &$request) {
        $params = array(
            'language' => $language,
        );
        self::registerRequest('GetRootCategoryInfoList', $params, 'OtapiCategoryListAnswer', $request);
    }

    /**
     *  Получить историю обработки строки заказа<br/><a href="/docs/IOtapiService.html#M:GetSaleLineProcessLog">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $salesId    
     * @param string $salesLineId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSalesLineProcLogInfoListAnswer

     */
    public static function GetSaleLineProcessLog($sessionId, $salesId, $salesLineId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'salesId' => $salesId,
            'salesLineId' => $salesLineId,
        );
        self::registerRequest('GetSaleLineProcessLog', $params, 'OtapiSalesLineProcLogInfoListAnswer', $request);
    }

    /**
     *  Получить список операторов заказов<br/><a href="/docs/IOtapiService.html#M:GetSalesOperatorInfoList">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiOperatorInfoListAnswer

     */
    public static function GetSalesOperatorInfoList($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetSalesOperatorInfoList', $params, 'OtapiOperatorInfoListAnswer', $request);
    }

    /**
     *  Получение информации о заказе<br/><a href="/docs/IOtapiService.html#M:GetSalesOrderDetails">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $salesId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSalesOrderDetailsAnswer

     */
    public static function GetSalesOrderDetails($language, $sessionId, $salesId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'salesId' => $salesId,
        );
        self::registerRequest('GetSalesOrderDetails', $params, 'OtapiSalesOrderDetailsAnswer', $request);
    }

    /**
     *  Подробная информация о заказе<br/><a href="/docs/IOtapiService.html#M:GetSalesOrderDetailsForOperator">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $salesId    
     * @param string $filter    
     * @param string $queryType    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSalesOrderDetailsAnswer

     */
    public static function GetSalesOrderDetailsForOperator($sessionId, $salesId, $filter, $queryType, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'salesId' => $salesId,
            'filter' => $filter,
            'queryType' => $queryType,
        );
        self::registerRequest('GetSalesOrderDetailsForOperator', $params, 'OtapiSalesOrderDetailsAnswer', $request);
    }

    /**
     *  Получить список посылок (номеров трекинга) по заказу<br/><a href="/docs/IOtapiService.html#M:GetSalesOrderShippings">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $salesId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSalesShippingInfoListAnswer

     */
    public static function GetSalesOrderShippings($language, $sessionId, $salesId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'salesId' => $salesId,
        );
        self::registerRequest('GetSalesOrderShippings', $params, 'OtapiSalesShippingInfoListAnswer', $request);
    }

    /**
     *  Получение списка заказов<br/><a href="/docs/IOtapiService.html#M:GetSalesOrdersList">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $filter    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSalesOrderInfoListAnswer

     */
    public static function GetSalesOrdersList($language, $sessionId, $filter, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'filter' => $filter,
        );
        self::registerRequest('GetSalesOrdersList', $params, 'OtapiSalesOrderInfoListAnswer', $request);
    }

    /**
     *  [Obsolete] Список заказов для оператора сайта. Возвращает 100 последних заказов<br/><a href="/docs/IOtapiService.html#M:GetSalesOrdersListForOperator">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $xmlOrderFilter    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSalesOrderInfoListAnswer

     */
    public static function GetSalesOrdersListForOperator($sessionId, $xmlOrderFilter, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'xmlOrderFilter' => $xmlOrderFilter,
        );
        self::registerRequest('GetSalesOrdersListForOperator', $params, 'OtapiSalesOrderInfoListAnswer', $request);
    }

    /**
     *  Получить список посылок по заказу<br/><a href="/docs/IOtapiService.html#M:GetSalesPackageList">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $salesId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiPackageAdminInfoListAnswer

     */
    public static function GetSalesPackageList($sessionId, $salesId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'salesId' => $salesId,
        );
        self::registerRequest('GetSalesPackageList', $params, 'OtapiPackageAdminInfoListAnswer', $request);
    }

    /**
     *  Запрос информации для резервирования денег<br/><a href="/docs/IOtapiService.html#M:GetSalesPaymentInfo">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $salesId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSalesPaymentInfoAnswer

     */
    public static function GetSalesPaymentInfo($sessionId, $salesId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'salesId' => $salesId,
        );
        self::registerRequest('GetSalesPaymentInfo', $params, 'OtapiSalesPaymentInfoAnswer', $request);
    }

    /**
     *  Получить историю обработки заказа<br/><a href="/docs/IOtapiService.html#M:GetSalesProcessLog">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $salesId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSalesProcLogInfoListAnswer

     */
    public static function GetSalesProcessLog($sessionId, $salesId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'salesId' => $salesId,
        );
        self::registerRequest('GetSalesProcessLog', $params, 'OtapiSalesProcLogInfoListAnswer', $request);
    }

    /**
     *  Получение списка поисковых категорий для выбора<br/><a href="/docs/IOtapiService.html#M:GetSearchCategoryInfoList">Документация</a> 
     *
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCategoryListAnswer

     */
    public static function GetSearchCategoryInfoList($language, &$request) {
        $params = array(
            'language' => $language,
        );
        self::registerRequest('GetSearchCategoryInfoList', $params, 'OtapiCategoryListAnswer', $request);
    }

    /**
     *  Получение подборки поисковых строк<br/><a href="/docs/IOtapiService.html#M:GetSearchStringRatingList">Документация</a> 
     *
     * @param string $language    
     * @param string $itemRatingTypeId    
     * @param string $numberItem    
     * @param string $categoryId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSearchStringRatingListAnswer

     */
    public static function GetSearchStringRatingList($language, $itemRatingTypeId, $numberItem, $categoryId, &$request) {
        $params = array(
            'language' => $language,
            'itemRatingTypeId' => $itemRatingTypeId,
            'numberItem' => $numberItem,
            'categoryId' => $categoryId,
        );
        self::registerRequest('GetSearchStringRatingList', $params, 'OtapiSearchStringRatingListAnswer', $request);
    }

    /**
     *  Получение настроек фичи Витрина<br/><a href="/docs/IOtapiService.html#M:GetShowcase">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiShowcaseSettingsAnswer

     */
    public static function GetShowcase($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetShowcase', $params, 'OtapiShowcaseSettingsAnswer', $request);
    }

    /**
     *  Формирование выписки по счету<br/><a href="/docs/IOtapiService.html#M:GetStatement">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $fromDate    
     * @param string $toDate    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiAccountStatementAnswer

     */
    public static function GetStatement($language, $sessionId, $fromDate, $toDate, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
        );
        self::registerRequest('GetStatement', $params, 'OtapiAccountStatementAnswer', $request);
    }

    /**
     *  Список операций по лицевому счету за период<br/><a href="/docs/IOtapiService.html#M:GetStatementForOperator">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $customerId    
     * @param string $fromDate    
     * @param string $toDate    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiAccountStatementAdministrationAnswer

     */
    public static function GetStatementForOperator($sessionId, $customerId, $fromDate, $toDate, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'customerId' => $customerId,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
        );
        self::registerRequest('GetStatementForOperator', $params, 'OtapiAccountStatementAdministrationAnswer', $request);
    }

    /**
     *  Установка настроек статистики<br/><a href="/docs/IOtapiService.html#M:GetStatisticsSettings">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiStatisticsSettingsInfoAnswer

     */
    public static function GetStatisticsSettings($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetStatisticsSettings', $params, 'OtapiStatisticsSettingsInfoAnswer', $request);
    }

    /**
     *  Получить список округов/административный делений КНР по родителю<br/><a href="/docs/IOtapiService.html#M:GetSubAreaList">Документация</a> 
     *
     * @param string $language    
     * @param string $parentId    
     * @param string $depthLevel    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiAreaListAnswer

     */
    public static function GetSubAreaList($language, $parentId, $depthLevel, &$request) {
        $params = array(
            'language' => $language,
            'parentId' => $parentId,
            'depthLevel' => $depthLevel,
        );
        self::registerRequest('GetSubAreaList', $params, 'OtapiAreaListAnswer', $request);
    }

    /**
     *  Получить историю смены тарифа инстанса<br/><a href="documentation?name=GetTariffChangeHistory">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiTariffChangeHistoryAnswer

     */
    public static function GetTariffChangeHistory($language, $sessionId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetTariffChangeHistory', $params, 'OtapiTariffChangeHistoryAnswer', $request);
    }
    /**
     *  Получить список шаблонных ролей<br/><a href="/docs/IOtapiService.html#M:GetTemplateRoleList">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiInstanceUserRoleInfoListAnswer

     */
    public static function GetTemplateRoleList($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetTemplateRoleList', $params, 'OtapiInstanceUserRoleInfoListAnswer', $request);
    }

    /**
     *  Получение списка трех уровней корневых категорий<br/><a href="/docs/IOtapiService.html#M:GetThreeLevelRootCategoryInfoList">Документация</a> 
     *
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCategoryListAnswer

     */
    public static function GetThreeLevelRootCategoryInfoList($language, &$request) {
        $params = array(
            'language' => $language,
        );
        self::registerRequest('GetThreeLevelRootCategoryInfoList', $params, 'OtapiCategoryListAnswer', $request);
    }

    /**
     *  Получить список категорий для обращения<br/><a href="/docs/IOtapiService.html#M:GetTicketCatogories">Документация</a> 
     *
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiTicketCategoryInfoListAnswer

     */
    public static function GetTicketCatogories($language, &$request) {
        $params = array(
            'language' => $language,
        );
        self::registerRequest('GetTicketCatogories', $params, 'OtapiTicketCategoryInfoListAnswer', $request);
    }

    /**
     *  Получить список обращений клиентов в поддержку<br/><a href="/docs/IOtapiService.html#M:GetTicketInfoList">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $direction    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiTicketInfoListAnswer

     */
    public static function GetTicketInfoList($language, $sessionId, $direction, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'direction' => $direction,
        );
        self::registerRequest('GetTicketInfoList', $params, 'OtapiTicketInfoListAnswer', $request);
    }

    /**
     *  Получение частичного списка отзывов (комментариев) о товаре<br/><a href="/docs/IOtapiService.html#M:GetTradeRateInfoListFrame">Документация</a> 
     *
     * @param string $language    
     * @param string $itemId    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemReviewListFrameAnswer

     */
    public static function GetTradeRateInfoListFrame($language, $itemId, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'itemId' => $itemId,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('GetTradeRateInfoListFrame', $params, 'OtapiItemReviewListFrameAnswer', $request);
    }

    /**
     *  Возращает список пар текст-язык по ключу<br/><a href="/docs/IOtapiService.html#M:GetTranslatedListByKey">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $key    
     * @param string $idInContext    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiTextByLangListAnswer

     */
    public static function GetTranslatedListByKey($sessionId, $key, $idInContext, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'key' => $key,
            'idInContext' => $idInContext,
        );
        self::registerRequest('GetTranslatedListByKey', $params, 'OtapiTextByLangListAnswer', $request);
    }

    /**
     *  Получение списка двух уровней корневых категорий<br/><a href="/docs/IOtapiService.html#M:GetTwoLevelRootCategoryInfoList">Документация</a> 
     *
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCategoryListAnswer

     */
    public static function GetTwoLevelRootCategoryInfoList($language, &$request) {
        $params = array(
            'language' => $language,
        );
        self::registerRequest('GetTwoLevelRootCategoryInfoList', $params, 'OtapiCategoryListAnswer', $request);
    }

    /**
     *  Получение информации о пользователе<br/><a href="/docs/IOtapiService.html#M:GetUserInfo">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiUserInfoAnswer

     */
    public static function GetUserInfo($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetUserInfo', $params, 'OtapiUserInfoAnswer', $request);
    }

    /**
     *  Получение информации о пользователе<br/><a href="/docs/IOtapiService.html#M:GetUserInfoForOperator">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $userId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiUserInfoAnswer

     */
    public static function GetUserInfoForOperator($sessionId, $userId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'userId' => $userId,
        );
        self::registerRequest('GetUserInfoForOperator', $params, 'OtapiUserInfoAnswer', $request);
    }

    /**
     *  Получение списка профилей пользователя<br/><a href="/docs/IOtapiService.html#M:GetUserProfileInfoList">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiUserProfileInfoListAnswer

     */
    public static function GetUserProfileInfoList($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetUserProfileInfoList', $params, 'OtapiUserProfileInfoListAnswer', $request);
    }

    /**
     *  Получение списка профилей пользователя<br/><a href="/docs/IOtapiService.html#M:GetUserProfileInfoListForOperator">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $userId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiUserProfileInfoListAnswer

     */
    public static function GetUserProfileInfoListForOperator($sessionId, $userId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'userId' => $userId,
        );
        self::registerRequest('GetUserProfileInfoListForOperator', $params, 'OtapiUserProfileInfoListAnswer', $request);
    }

    /**
     *  Получить настройки для полей профиля<br/><a href="documentation?name=GetUserProfileSettings">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $inputLanguage
     * @param string $includeMetaInfo
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiUserProfileSettingsAnswer

     */
    public static function GetUserProfileSettings($language, $sessionId, $inputLanguage, $includeMetaInfo, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'inputLanguage' => $inputLanguage,
            'includeMetaInfo' => $includeMetaInfo,
        );
        self::registerRequest('GetUserProfileSettings', $params, 'OtapiUserProfileSettingsAnswer', $request);
    }

    /**
     *  Получение списка пользователей группы скидок<br/><a href="/docs/IOtapiService.html#M:GetUsersOfDiscountGroup">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $discountGroupId    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiDiscountGroupUserInfoListFrameAnswer

     */
    public static function GetUsersOfDiscountGroup($sessionId, $discountGroupId, $framePosition, $frameSize, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'discountGroupId' => $discountGroupId,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('GetUsersOfDiscountGroup', $params, 'OtapiDiscountGroupUserInfoListFrameAnswer', $request);
    }

    /**
     *  Получение информации о  статусе пользователя<br/><a href="documentation?name=GetUserStatusInfo">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiUserStatusInfoAnswer

     */
    public static function GetUserStatusInfo($language, $sessionId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetUserStatusInfo', $params, 'OtapiUserStatusInfoAnswer', $request);
    }

    /**
     *  Получение информации о поставщике<br/><a href="/docs/IOtapiService.html#M:GetVendorInfo">Документация</a> 
     *
     * @param string $language    
     * @param string $vendorId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiVendorInfoAnswer

     */
    public static function GetVendorInfo($language, $vendorId, &$request) {
        $params = array(
            'language' => $language,
            'vendorId' => $vendorId,
        );
        self::registerRequest('GetVendorInfo', $params, 'OtapiVendorInfoAnswer', $request);
    }

    /**
     *  Получение частичного списка товаров поставщика<br/><a href="/docs/IOtapiService.html#M:GetVendorItemInfoSortedListFrame">Документация</a> 
     *
     * @param string $language    
     * @param string $vendorId    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param string $sortingParameters    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemInfoListFrameAnswer

     */
    public static function GetVendorItemInfoSortedListFrame($language, $vendorId, $framePosition, $frameSize, $sortingParameters, &$request) {
        $params = array(
            'language' => $language,
            'vendorId' => $vendorId,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
            'sortingParameters' => $sortingParameters,
        );
        self::registerRequest('GetVendorItemInfoSortedListFrame', $params, 'OtapiItemInfoListFrameAnswer', $request);
    }

    /**
     *  Получение частичного списка товаров поставщика с выдачей упрощенной информации (без названия)<br/><a href="/docs/IOtapiService.html#M:GetVendorItemSimpleInfoSortedListFrame">Документация</a> 
     *
     * @param string $language    
     * @param string $vendorId    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param string $sortingParameters    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemInfoListFrameAnswer

     */
    public static function GetVendorItemSimpleInfoSortedListFrame($language, $vendorId, $framePosition, $frameSize, $sortingParameters, &$request) {
        $params = array(
            'language' => $language,
            'vendorId' => $vendorId,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
            'sortingParameters' => $sortingParameters,
        );
        self::registerRequest('GetVendorItemSimpleInfoSortedListFrame', $params, 'OtapiItemInfoListFrameAnswer', $request);
    }

    /**
     *  Получение подборки продавцов<br/><a href="/docs/IOtapiService.html#M:GetVendorRatingList">Документация</a> 
     *
     * @param string $language    
     * @param string $itemRatingTypeId    
     * @param string $numberItem    
     * @param string $categoryId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiVendorInfoListAnswer

     */
    public static function GetVendorRatingList($language, $itemRatingTypeId, $numberItem, $categoryId, &$request) {
        $params = array(
            'language' => $language,
            'itemRatingTypeId' => $itemRatingTypeId,
            'numberItem' => $numberItem,
            'categoryId' => $categoryId,
        );
        self::registerRequest('GetVendorRatingList', $params, 'OtapiVendorInfoListAnswer', $request);
    }

    /**
     *  Получение частичной подборки продавцов<br/><a href="/docs/IOtapiService.html#M:GetVendorRatingListFrame">Документация</a> 
     *
     * @param string $language    
     * @param string $itemRatingTypeId    
     * @param string $categoryId    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiVendorInfoListFrameAnswer

     */
    public static function GetVendorRatingListFrame($language, $itemRatingTypeId, $categoryId, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'itemRatingTypeId' => $itemRatingTypeId,
            'categoryId' => $categoryId,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('GetVendorRatingListFrame', $params, 'OtapiVendorInfoListFrameAnswer', $request);
    }

    /**
     *  Получить информацию о категории товаров на складе<br/><a href="/docs/IOtapiService.html#M:GetWarehouseCategoryInfo">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $categoryId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiWarehouseCategoryInfoAnswer

     */
    public static function GetWarehouseCategoryInfo($sessionId, $categoryId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'categoryId' => $categoryId,
        );
        self::registerRequest('GetWarehouseCategoryInfo', $params, 'OtapiWarehouseCategoryInfoAnswer', $request);
    }

    /**
     *  Получить информацию о товаре на складе<br/><a href="/docs/IOtapiService.html#M:GetWarehouseItemInfo">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $itemId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiWarehouseItemInfoAnswer

     */
    public static function GetWarehouseItemInfo($sessionId, $itemId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'itemId' => $itemId,
        );
        self::registerRequest('GetWarehouseItemInfo', $params, 'OtapiWarehouseItemInfoAnswer', $request);
    }

    /**
     *  Получение настроек фичи WebUI<br/><a href="documentation?name=GetWebUISettings">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiWebUISettingsAnswer

     */
    public static function GetWebUISettings($language, $sessionId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetWebUISettings', $params, 'OtapiWebUISettingsAnswer', $request);
    }

    /**
     *  Скрытие удаленных с таобао категорий без детей<br/><a href="/docs/IOtapiService.html#M:HideDeletedCategoriesWithoutChildren">Документация</a> 
     *
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function HideDeletedCategoriesWithoutChildren($sessionId, &$request) {
        $params = array(
            'sessionId' => $sessionId,
        );
        self::registerRequest('HideDeletedCategoriesWithoutChildren', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Импорт заранее заготовленного пакета каталога<br/><a href="/docs/IOtapiService.html#M:ImportCatalog">Документация</a> 
     *
     * @param string $sessionId    
     * @param string $language    
     * @param string $xmlCatalog    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function ImportCatalog($sessionId, $language, $xmlCatalog, &$request) {
        $params = array(
            'sessionId' => $sessionId,
            'language' => $language,
            'xmlCatalog' => $xmlCatalog,
        );
        self::registerRequest('ImportCatalog', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Получение частичного содержимого корзины<br/><a href="documentation?name=GetPartialBasket">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $elements    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCollectionInfoAnswer

     */
    public static function GetPartialBasket($language, $sessionId, $elements, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'elements' => $elements,
        );
        self::registerRequest('GetPartialBasket', $params, 'OtapiCollectionInfoAnswer', $request);
    }
    
    /**
     *  Получить список сессий провайдера<br/><a href="/docs/IOtapiService.html#M:GetProviderOrdersIntegrationSessionInfoList">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $providerType
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiProviderSessionInfoListAnswer
    
     */
    public static function GetProviderOrdersIntegrationSessionInfoList($language, $sessionId, $providerType, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'providerType' => $providerType,
        );
        self::registerRequest('GetProviderOrdersIntegrationSessionInfoList', $params, 'OtapiProviderSessionInfoListAnswer', $request);
    }
    
    /**
     *  Получить информацию для получения новой сессии провайдера<br/><a href="/docs/IOtapiService.html#M:GetProviderOrdersIntegrationSessionAuthenticationInfo">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $providerType
     * @param string $returnUrl
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiProviderAuthenticationInfoAnswer
    
     */
    public static function GetProviderOrdersIntegrationSessionAuthenticationInfo($language, $sessionId, $providerType, $returnUrl, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'providerType' => $providerType,
            'returnUrl' => $returnUrl,
        );
        self::registerRequest('GetProviderOrdersIntegrationSessionAuthenticationInfo', $params, 'OtapiProviderAuthenticationInfoAnswer', $request);
    }
    
    /**
     *  Запустить экспорт заказа в провайдера<br/><a href="/docs/IOtapiService.html#M:RunOrderExportingToProvider">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $providerType
     * @param string $providerSessionId
     * @param string $xmlParameters
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBackgroundActivityIdentificationInfoAnswer
    
     */
    public static function RunOrderExportingToProvider($language, $sessionId, $providerType, $providerSessionId, $xmlParameters, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'providerType' => $providerType,
            'providerSessionId' => $providerSessionId,
            'xmlParameters' => $xmlParameters,
        );
        self::registerRequest('RunOrderExportingToProvider', $params, 'OtapiBackgroundActivityIdentificationInfoAnswer', $request);
    }
    
    /**
     *  Найти фоновые активности<br/><a href="/docs/IOtapiService.html#M:SearchBackgroundActivities">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $xmlSearchParameters    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBackgroundActivityInfoListAnswer

     */
    public static function SearchBackgroundActivities($language, $sessionId, $xmlSearchParameters, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlSearchParameters' => $xmlSearchParameters,
        );
        self::registerRequest('SearchBackgroundActivities', $params, 'OtapiBackgroundActivityInfoListAnswer', $request);
    }    

    /**
     *  Получить информацию о фоновой активности<br/><a href="/docs/IOtapiService.html#M:GetBackgroundActivityInfo">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $activityType
     * @param string $activityId
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBackgroundActivityFullInfoAnswer
    
     */
    public static function GetBackgroundActivityInfo($language, $sessionId, $activityType, $activityId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'activityType' => $activityType,
            'activityId' => $activityId,
        );
        self::registerRequest('GetBackgroundActivityInfo', $params, 'OtapiBackgroundActivityFullInfoAnswer', $request);
    }

    /**
     *  Выполнить действие для фоновой активности<br/><a href="/docs/IOtapiService.html#M:DoActionForBackgroundActivity">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $activityType
     * @param string $activityId
     * @param string $actionId
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer
    
     */
    public static function DoActionForBackgroundActivity($language, $sessionId, $activityType, $activityId, $actionId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'activityType' => $activityType,
            'activityId' => $activityId,
            'actionId' => $actionId,
        );
        self::registerRequest('DoActionForBackgroundActivity', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Получение информации о доступных способах поиска<br/><a href="/docs/IOtapiService.html#M:GetAvailableProviderSearchMethodInfoListForSearchParameters">Документация</a> 
     *
     * @param string $language    
     * @param string $xmlSearchParameters    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiProviderSearchMethodInfoListAnswer

     */
    public static function GetAvailableProviderSearchMethodInfoListForSearchParameters($language, $xmlSearchParameters, &$request) {
        $params = array(
            'language' => $language,
            'xmlSearchParameters' => $xmlSearchParameters,
        );
        self::registerRequest('GetAvailableProviderSearchMethodInfoListForSearchParameters', $params, 'OtapiProviderSearchMethodInfoListAnswer', $request);
    }

    /**
     *  Запустить синхронизацию заказов с провайдером<br/><a href="/docs/IOtapiService.html#M:RunOrdersSynchronizingWithProvider">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $providerType    
     * @param string $providerSessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBackgroundActivityIdentificationInfoAnswer

     */
    public static function RunOrdersSynchronizingWithProvider($language, $sessionId, $providerType, $providerSessionId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'providerType' => $providerType,
            'providerSessionId' => $providerSessionId,
        );
        self::registerRequest('RunOrdersSynchronizingWithProvider', $params, 'OtapiBackgroundActivityIdentificationInfoAnswer', $request);
    }

    /**
     *  Запустить отвязывание заказа от провайдера<br/><a href="/docs/IOtapiService.html#M:RunOrderUnlinkingFromProvider">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $providerType
     * @param string $providerSessionId
     * @param string $xmlParameters
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBackgroundActivityIdentificationInfoAnswer
    
     */
    public static function RunOrderUnlinkingFromProvider($language, $sessionId, $providerType, $providerSessionId, $xmlParameters, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'providerType' => $providerType,
            'providerSessionId' => $providerSessionId,
            'xmlParameters' => $xmlParameters,
        );
        self::registerRequest('RunOrderUnlinkingFromProvider', $params, 'OtapiBackgroundActivityIdentificationInfoAnswer', $request);
    }
    
    /**
     *  Запустить связывание заказа с имеющимся провайдерскими заказами<br/><a href="/docs/IOtapiService.html#M:RunOrderLinkingWithProvider">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $providerType
     * @param string $providerSessionId
     * @param string $xmlParameters
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBackgroundActivityIdentificationInfoAnswer
    
     */
    public static function RunOrderLinkingWithProvider($language, $sessionId, $providerType, $providerSessionId, $xmlParameters, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'providerType' => $providerType,
            'providerSessionId' => $providerSessionId,
            'xmlParameters' => $xmlParameters,
        );
        self::registerRequest('RunOrderLinkingWithProvider', $params, 'OtapiBackgroundActivityIdentificationInfoAnswer', $request);
    }

    /**
     *  Поиск подборки товаров<br/><a href="documentation?name=SearchRatingListItems">Документация</a> 
     *
     * @param string $language    
     * @param string $xmlSearchParameters    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemInfoListFrameAnswer

     */
    public static function SearchRatingListItems($language, $xmlSearchParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'xmlSearchParameters' => $xmlSearchParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchRatingListItems', $params, 'OtapiItemInfoListFrameAnswer', $request);
    }    
    
    /**
     *  Поиск подборок<br/><a href="documentation?name=SearchRatingLists">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $xmlSearchParameters    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemRatingCollectionListFrameAnswer

     */
    public static function SearchRatingLists($language, $sessionId, $xmlSearchParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlSearchParameters' => $xmlSearchParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchRatingLists', $params, 'OtapiItemRatingCollectionListFrameAnswer', $request);
    }

    /**
     *  Получить настройки интеграции заказов с провайдером (с мета-информацией по настройкам)<br/><a href="documentation?name=GetProviderOrdersIntegrationSettings">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $providerType    
     * @param string $includeMetaInfo    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiProviderOrdersIntegrationSettingsAnswer

     */
    public static function GetProviderOrdersIntegrationSettings($language, $sessionId, $providerType, $includeMetaInfo, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'providerType' => $providerType,
            'includeMetaInfo' => $includeMetaInfo,
        );
        self::registerRequest('GetProviderOrdersIntegrationSettings', $params, 'OtapiProviderOrdersIntegrationSettingsAnswer', $request);
    }

    /**
     *  Обновить настройки интеграции заказов с провайдером<br/><a href="documentation?name=UpdateProviderOrdersIntegrationSettings">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $providerType
     * @param string $xmlUpdateData
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer
    
     */
    public static function UpdateProviderOrdersIntegrationSettings($language, $sessionId, $providerType, $xmlUpdateData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'providerType' => $providerType,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateProviderOrdersIntegrationSettings', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Добавить подборки любых элементов<br/><a href="documentation?name=InsertElementsSetToRatingList">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $xmlInsertData
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer
    
     */
    public static function InsertElementsSetToRatingList($language, $sessionId, $xmlInsertData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlInsertData' => $xmlInsertData,
        );
        self::registerRequest('InsertElementsSetToRatingList', $params, 'VoidOtapiAnswer', $request);
    }    
    
    /**
     *  Получить адрес для загузки файла YML<br/><a href="documentation?name=GetYandexMarketDataUrl">Документация</a> 
     *
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiYandexMarketDataUrlAnswer

     */
    public static function GetYandexMarketDataUrl($language, &$request) {
        $params = array(
            'language' => $language,
        );
        self::registerRequest('GetYandexMarketDataUrl', $params, 'OtapiYandexMarketDataUrlAnswer', $request);
    }

    /**
     *  Получить настройки магазина ЯМИ<br/><a href="documentation?name=GetYandexMarketIntegrationSettings">Документация</a> 
     *
     * @param string $language    
     * @param string $includeMetaInfo    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiYandexMarketIntegrationSettingsAnswer

     */
    public static function GetYandexMarketIntegrationSettings($language, $includeMetaInfo, &$request) {
        $params = array(
            'language' => $language,
            'includeMetaInfo' => $includeMetaInfo,
        );
        self::registerRequest('GetYandexMarketIntegrationSettings', $params, 'OtapiYandexMarketIntegrationSettingsAnswer', $request);
    }

    /**
     *  Обновить настройки магазина ЯМИ<br/><a href="documentation?name=UpdateYandexMarketIntegrationSettings">Документация</a> 
     *
     * @param string $language    
     * @param string $xmlUpdateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateYandexMarketIntegrationSettings($language, $xmlUpdateData, &$request) {
        $params = array(
            'language' => $language,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateYandexMarketIntegrationSettings', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Получить информацию для старта внешней аутентификации<br/><a href="documentation?name=GetExternalAuthenticationInfo">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $authenticationSystem    
     * @param string $returnUrl    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiAuthenticationInfoAnswer

     */
    public static function GetExternalAuthenticationInfo($language, $sessionId, $authenticationSystem, $returnUrl, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'authenticationSystem' => $authenticationSystem,
            'returnUrl' => $returnUrl,
        );
        self::registerRequest('GetExternalAuthenticationInfo', $params, 'OtapiAuthenticationInfoAnswer', $request);
    }

    /**
     *  Получить список внешних систем аутентификации для пользователя<br/><a href="documentation?name=GetExternalAuthenticationSystemInfoList">Документация</a> 
     *
     * @param string $language    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiAuthenticationSystemInfoListAnswer

     */
    public static function GetExternalAuthenticationSystemInfoList($language, &$request) {
        $params = array(
            'language' => $language,
        );
        self::registerRequest('GetExternalAuthenticationSystemInfoList', $params, 'OtapiAuthenticationSystemInfoListAnswer', $request);
    }

    /**
     *  Получить настройки провайдера<br/><a href="documentation?name=GetProviderSettings">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $providerType
     * @param string $includeMetaInfo
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiProviderSettingsAnswer

     */
    public static function GetProviderSettings($language, $sessionId, $providerType, $includeMetaInfo, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'providerType' => $providerType,
            'includeMetaInfo' => $includeMetaInfo,
        );
        self::registerRequest('GetProviderSettings', $params, 'OtapiProviderSettingsAnswer', $request);
    }

    /**
     *  Обновить настройки провайдера<br/><a href="documentation?name=UpdateProviderSettings">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $providerType
     * @param string $xmlUpdateData
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateProviderSettings($language, $sessionId, $providerType, $xmlUpdateData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'providerType' => $providerType,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateProviderSettings', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Получить список email-серверов<br/><a href="documentation?name=GetEmailServerInfoList">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiEmailServerInfoListAnswer

     */
    public static function GetEmailServerInfoList($language, $sessionId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetEmailServerInfoList', $params, 'OtapiEmailServerInfoListAnswer', $request);
    }

    /**
     *  Получить настройки email-сервера<br/><a href="documentation?name=GetEmailServerInfo">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $serverId    
     * @param string $includeMetaInfo    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiEmailServerInfoAnswer

     */
    public static function GetEmailServerInfo($language, $sessionId, $serverId, $includeMetaInfo, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'serverId' => $serverId,
            'includeMetaInfo' => $includeMetaInfo,
        );
        self::registerRequest('GetEmailServerInfo', $params, 'OtapiEmailServerInfoAnswer', $request);
    }

    /**
     *  Обновить настройки email-сервера<br/><a href="documentation?name=UpdateEmailServerInfo">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $serverId    
     * @param string $xmlUpdateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateEmailServerInfo($language, $sessionId, $serverId, $xmlUpdateData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'serverId' => $serverId,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateEmailServerInfo', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Проверить настройки email-сервера отправкой тестового письма<br/><a href="documentation?name=TestEmailServer">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $serverId    
     * @param string $recipientEmail    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function TestEmailServer($language, $sessionId, $serverId, $recipientEmail, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'serverId' => $serverId,
            'recipientEmail' => $recipientEmail,
        );
        self::registerRequest('TestEmailServer', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Обновить финансовую информацию<br/><a href="documentation?name=UpdateFinancialCalculationData">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $providerType
     * @param string $date
     * @param string $xmlUpdateData
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer
    
     */
    public static function UpdateFinancialCalculationData($language, $sessionId, $providerType, $date, $xmlUpdateData, &$request) {
    	$params = array(
    			'language' => $language,
    			'sessionId' => $sessionId,
    			'providerType' => $providerType,
    			'date' => $date,
    			'xmlUpdateData' => $xmlUpdateData,
    	);
    	self::registerRequest('UpdateFinancialCalculationData', $params, 'VoidOtapiAnswer', $request);
    }    
    
    /**
     *  Получить настройки способов поиска<br/><a href="documentation?name=GetProviderSearchMethodSettings">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $includeMetaInfo    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiProviderSearchMethodSettingsAnswer

     */
    public static function GetProviderSearchMethodSettings($language, $sessionId, $includeMetaInfo, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'includeMetaInfo' => $includeMetaInfo,
        );
        self::registerRequest('GetProviderSearchMethodSettings', $params, 'OtapiProviderSearchMethodSettingsAnswer', $request);
    }

    /**
     *  Обновить настройки способов поиска<br/><a href="documentation?name=UpdateProviderSearchMethodSettings">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $xmlUpdateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateProviderSearchMethodSettings($language, $sessionId, $xmlUpdateData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateProviderSearchMethodSettings', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Подтвердить внешнюю аутентификацию с указанными адресом почты и паролем<br/><a href="documentation?name=ConfirmExternalAuthentication">Документация</a> 
     *
     * @param string $language    
     * @param string $contextId    
     * @param string $userEmail    
     * @param string $userPassword    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSessionIdAnswer

     */
    public static function ConfirmExternalAuthentication($language, $contextId, $userEmail, $userPassword, &$request) {
        $params = array(
            'language' => $language,
            'contextId' => $contextId,
            'userEmail' => $userEmail,
            'userPassword' => $userPassword,
        );
        self::registerRequest('ConfirmExternalAuthentication', $params, 'OtapiSessionIdAnswer', $request);
    }

    /**
     *  Подтвердить новый пароль<br/><a href="documentation?name=ConfirmNewPassword">Документация</a>
     *
     * @param string $language
     * @param string $confirmationCode
     * @param string $newPassword
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSessionIdAnswer

     */
    public static function ConfirmNewPassword($language, $confirmationCode, $newPassword, &$request) {
        $params = array(
            'language' => $language,
            'confirmationCode' => $confirmationCode,
            'newPassword' => $newPassword,
        );
        self::registerRequest('ConfirmNewPassword', $params, 'OtapiSessionIdAnswer', $request);
    }
    
    /**
     *  Получить список настраиваемых способов поиска<br/><a href="documentation?name=GetProviderSearchMethods">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $inputLanguage
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiProviderSearchMethodListAnswer
    
     */
    public static function GetProviderSearchMethods($language, $sessionId, $inputLanguage, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'inputLanguage' => $inputLanguage,
        );
        self::registerRequest('GetProviderSearchMethods', $params, 'OtapiProviderSearchMethodListAnswer', $request);
    }

    /**
     *  Получить настройки указанного способа поиска<br/><a href="documentation?name=GetProviderSearchMethod">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $inputLanguage
     * @param string $searchMethodId
     * @param string $includeMetaInfo
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiProviderSearchMethodAnswer
    
     */
    public static function GetProviderSearchMethod($language, $sessionId, $inputLanguage, $searchMethodId, $includeMetaInfo, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'inputLanguage' => $inputLanguage,
            'searchMethodId' => $searchMethodId,
            'includeMetaInfo' => $includeMetaInfo,
        );
        self::registerRequest('GetProviderSearchMethod', $params, 'OtapiProviderSearchMethodAnswer', $request);
    }
    
    /**
     *  Обновить настройки указанного способа поиска<br/><a href="documentation?name=UpdateProviderSearchMethod">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $inputLanguage
     * @param string $searchMethodId
     * @param string $xmlUpdateData
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer
    
     */
    public static function UpdateProviderSearchMethod($language, $sessionId, $inputLanguage, $searchMethodId, $xmlUpdateData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'inputLanguage' => $inputLanguage,
            'searchMethodId' => $searchMethodId,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateProviderSearchMethod', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Получить настройки корзины, избранного<br/><a href="documentation?name=GetCollectionsSettings">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $includeMetaInfo    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCollectionsSettingsAnswer

     */
    public static function GetCollectionsSettings($language, $sessionId, $includeMetaInfo, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'includeMetaInfo' => $includeMetaInfo,
        );
        self::registerRequest('GetCollectionsSettings', $params, 'OtapiCollectionsSettingsAnswer', $request);
    }

    /**
     *  Обновить настройки корзины, избранного<br/><a href="documentation?name=UpdateCollectionsSettings">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $xmlUpdateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateCollectionsSettings($language, $sessionId, $xmlUpdateData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateCollectionsSettings', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Получение списка переводимого контента<br/><a href="documentation?name=GetTranslatableContentList">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiTranslatableContentInfoListAnswer

     */
    public static function GetTranslatableContentList($language, $sessionId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetTranslatableContentList', $params, 'OtapiTranslatableContentInfoListAnswer', $request);
    }

    /**
     *  Поиск переводов<br/><a href="documentation?name=SearchTranslations">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $xmlSearchParameters    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiTranslationInfoListFrameAnswer

     */
    public static function SearchTranslations($language, $sessionId, $xmlSearchParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlSearchParameters' => $xmlSearchParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchTranslations', $params, 'OtapiTranslationInfoListFrameAnswer', $request);
    }

    /**
     *  Обновление переводов<br/><a href="documentation?name=UpdateTranslation">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $inputLanguage    
     * @param string $translatableContent    
     * @param string $translationId    
     * @param string $updateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateTranslation($language, $sessionId, $inputLanguage, $translatableContent, $translationId, $updateData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'inputLanguage' => $inputLanguage,
            'translatableContent' => $translatableContent,
            'translationId' => $translationId,
            'updateData' => $updateData,
        );
        self::registerRequest('UpdateTranslation', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Найти транзакции по лицевым счетам<br/><a href="documentation?name=SearchTransactions">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $xmlSearchParameters    
     * @param string $framePosition    
     * @param string $frameSize    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiTransactionInfoListFrameAnswer

     */
    public static function SearchTransactions($language, $sessionId, $xmlSearchParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlSearchParameters' => $xmlSearchParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchTransactions', $params, 'OtapiTransactionInfoListFrameAnswer', $request);
    }

    /**
     *  Расчитать финансовый отчет<br/><a href="documentation?name=GetFinancialCalculation">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $xmlParameters    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiFinancialCalculationReportAnswer

     */
    public static function GetFinancialCalculation($language, $sessionId, $xmlParameters, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlParameters' => $xmlParameters,
        );
        self::registerRequest('GetFinancialCalculation', $params, 'OtapiFinancialCalculationReportAnswer', $request);
    }
    
    /**
     *  Получить финансовый отчет<br/><a href="documentation?name=GetFinancialReport">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $includeMetaInfo    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiFinancialReportAnswer

     */
    public static function GetFinancialReport($language, $sessionId, $includeMetaInfo, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'includeMetaInfo' => $includeMetaInfo,
        );
        self::registerRequest('GetFinancialReport', $params, 'OtapiFinancialReportAnswer', $request);
    }

    /**
     *  Выполнить действие шага для фоновой активности<br/><a href="documentation?name=DoStepActionForBackgroundActivity">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $activityType    
     * @param string $activityId    
     * @param string $xmlParameters    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function DoStepActionForBackgroundActivity($language, $sessionId, $activityType, $activityId, $xmlParameters, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'activityType' => $activityType,
            'activityId' => $activityId,
            'xmlParameters' => $xmlParameters,
        );
        self::registerRequest('DoStepActionForBackgroundActivity', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Получить настройки пользователя<br/><a href="documentation?name=GetUserPreferences">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiUserPreferencesAnswer

     */
    public static function GetUserPreferences($language, $sessionId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetUserPreferences', $params, 'OtapiUserPreferencesAnswer', $request);
    }

    /**
     *  Обновить настройки пользователя<br/><a href="documentation?name=UpdateUserPreferences">Документация</a> 
     *
     * @param string $language    
     * @param string $sessionId    
     * @param string $xmlUpdateData    
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateUserPreferences($language, $sessionId, $xmlUpdateData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateUserPreferences', $params, 'VoidOtapiAnswer', $request);
    }
    
    /**
     *  Получить события<br/><a href="documentation?name=GetMessageEventList">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiEventInfoListAnswer
    
     */
    public static function GetMessageEventList($language, $sessionId, &$request) {
    	$params = array(
    			'language' => $language,
    			'sessionId' => $sessionId,
    	);
    	self::registerRequest('GetMessageEventList', $params, 'OtapiEventInfoListAnswer', $request);
    }
    
    /**
     *  Получить шаблон сообщения<br/><a href="documentation?name=GetMessageTemplate">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $inputLanguage
     * @param string $eventType
     * @param string $includeMetaInfo
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiMessageTemplateInfoAnswer
    
     */
    public static function GetMessageTemplate($language, $sessionId, $inputLanguage, $eventType, $includeMetaInfo, &$request) {
    	$params = array(
    			'language' => $language,
    			'sessionId' => $sessionId,
    			'inputLanguage' => $inputLanguage,
    			'eventType' => $eventType,
    			'includeMetaInfo' => $includeMetaInfo,
    	);
    	self::registerRequest('GetMessageTemplate', $params, 'OtapiMessageTemplateInfoAnswer', $request);
    }
    
    /**
     *  Обновить шаблон сообщения<br/><a href="documentation?name=UpdateMessageTemplate">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $inputLanguage
     * @param string $eventType
     * @param string $xmlUpdateData
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer
    
     */
    public static function UpdateMessageTemplate($language, $sessionId, $inputLanguage, $eventType, $xmlUpdateData, &$request) {
    	$params = array(
    			'language' => $language,
    			'sessionId' => $sessionId,
    			'inputLanguage' => $inputLanguage,
    			'eventType' => $eventType,
    			'xmlUpdateData' => $xmlUpdateData,
    	);
    	self::registerRequest('UpdateMessageTemplate', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  <a href="documentation?name=TestMessageTemplate">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $inputLanguage
     * @param string $eventType
     * @param string $recipientInfo
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function TestMessageTemplate($language, $sessionId, $inputLanguage, $eventType, $recipientInfo, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'inputLanguage' => $inputLanguage,
            'eventType' => $eventType,
            'recipientInfo' => $recipientInfo,
        );
        self::registerRequest('TestMessageTemplate', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Сохранить результат группы вызовов Otapi<br/><a href="documentation?name=StoreRequestGroup">Документация</a>
     *
     * @param string $language
     * @param string $xmlRequestGroup
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function StoreRequestGroup($language, $xmlRequestGroup, &$request) {
        $params = array(
            'language' => $language,
            'xmlRequestGroup' => $xmlRequestGroup,
        );
        self::registerRequest('StoreRequestGroup', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Получить настройки экспорта подборок<br/><a href="documentation?name=GetSelectorExportingSettings">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $includeMetaInfo
     * @param        $request Переменная, куда будет записан ответ от сервисов
     *
     * @return OtapiSelectorExportersSettingsAnswer
     */
    public static function GetSelectorExportingSettings($language, $sessionId, $includeMetaInfo, &$request)
    {
        $params = array(
            'language'        => $language,
            'sessionId'       => $sessionId,
            'includeMetaInfo' => $includeMetaInfo,
        );
        self::registerRequest('GetSelectorExportingSettings', $params, 'OtapiSelectorExportersSettingsAnswer', $request);
    }

    /**
     *  Обновить настройки экспорта подборок<br/><a href="documentation?name=UpdateSelectorExportingSettings">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $xmlUpdateData
     * @param        $request Переменная, куда будет записан ответ от сервисов
     *
     * @return VoidOtapiAnswer
     */
    public static function UpdateSelectorExportingSettings($language, $sessionId, $xmlUpdateData, &$request)
    {
        $params = array(
            'language'      => $language,
            'sessionId'     => $sessionId,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateSelectorExportingSettings', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Получить список интеграций для экспорта.<br/><a href="documentation?name=GetSelectorExportingTargets">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param        $request Переменная, куда будет записан ответ от сервисов
     *
     * @return OtapiExportingTargetInfoListAnswer
     */
    public static function GetSelectorExportingTargets($language, $sessionId, &$request)
    {
        $params = array(
            'language'  => $language,
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetSelectorExportingTargets', $params, 'OtapiExportingTargetInfoListAnswer', $request);
    }

    /**
     *  Запустить экспорт выбранных подборок на указанный сайт.<br/><a href="documentation?name=RunSelectorExporting">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $exportingTarget
     * @param        $request Переменная, куда будет записан ответ от сервисов
     *
     * @return OtapiBackgroundActivityIdentificationInfoAnswer
     */
    public static function RunSelectorExporting($language, $sessionId, $exportingTarget, &$request)
    {
        $params = array(
            'language'        => $language,
            'sessionId'       => $sessionId,
            'exportingTarget' => $exportingTarget,
        );
        self::registerRequest('RunSelectorExporting', $params, 'OtapiBackgroundActivityIdentificationInfoAnswer', $request);
    }

    /**
     *  Запустить импорт товаров на складе<br/><a href="documentation?name=RunWarehouseImportItems">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $xmlImportData
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBackgroundActivityIdentificationInfoAnswer

     */
    public static function RunWarehouseImportItems($language, $sessionId, $xmlImportData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlImportData' => $xmlImportData,
        );
        self::registerRequest('RunWarehouseImportItems', $params, 'OtapiBackgroundActivityIdentificationInfoAnswer', $request);
    }

    /**
     *  Получить демо-объект для Мета-ЮИ<br/><a href="documentation?name=GetMetaDemoObject">Документация</a>
     *
     * @param string $language
     * @param string $includeMetaInfo
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiDemoObjectAnswer

     */
    public static function GetMetaDemoObject($language, $includeMetaInfo, &$request) {
        $params = array(
            'language' => $language,
            'includeMetaInfo' => $includeMetaInfo,
        );
        self::registerRequest('GetMetaDemoObject', $params, 'OtapiDemoObjectAnswer', $request);
    }

    /**
     *  Получить список сущностей, управляемых с помощью Мета-ЮИ<br/><a href="documentation?name=GetMetaEntities">Документация</a>
     *
     * @param string $language
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiMetaEntityInfoListAnswer

     */
    public static function GetMetaEntities($language, &$request) {
        $params = array(
            'language' => $language,
        );
        self::registerRequest('GetMetaEntities', $params, 'OtapiMetaEntityInfoListAnswer', $request);
    }

    /**
     *  Обновить демо-объект для Мета-ЮИ<br/><a href="documentation?name=UpdateMetaDemoObject">Документация</a>
     *
     * @param string $language
     * @param string $xmlUpdateData
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateMetaDemoObject($language, $xmlUpdateData, &$request) {
        $params = array(
            'language' => $language,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateMetaDemoObject', $params, 'VoidOtapiAnswer', $request);
    }



    /**
     *  Импорт отзывов пользователей на товар<br/><a href="documentation?name=ImportExternalItemReviews">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $xmlImportData
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiImportItemReviewsInfoAnswer

     */
    public static function ImportExternalItemReviews($language, $sessionId, $xmlImportData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlImportData' => $xmlImportData,
        );
        self::registerRequest('ImportExternalItemReviews', $params, 'OtapiImportItemReviewsInfoAnswer', $request);
    }

    /**
     *  Получение отзыва пользователя по идентификатору<br/><a href="documentation?name=GetItemReview">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $itemReviewId
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemReviewInfoAnswer

     */
    public static function GetItemReview($language, $sessionId, $itemReviewId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'itemReviewId' => $itemReviewId,
        );
        self::registerRequest('GetItemReview', $params, 'OtapiItemReviewInfoAnswer', $request);
    }

    /**
     *  Добавление отзыва пользователя на товар<br/><a href="documentation?name=AddItemReview">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $xmlAddData
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemReviewIdAnswer

     */
    public static function AddItemReview($language, $sessionId, $xmlAddData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlAddData' => $xmlAddData,
        );
        self::registerRequest('AddItemReview', $params, 'OtapiItemReviewIdAnswer', $request);
    }

    /**
     *  Завершить строку заказа<br/><a href="documentation?name=CloseLineSalesOrder">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $salesId
     * @param string $salesLineId
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function CloseLineSalesOrder($language, $sessionId, $salesId, $salesLineId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'salesId' => $salesId,
            'salesLineId' => $salesLineId,
        );
        self::registerRequest('CloseLineSalesOrder', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Поиск отзывов пользователей на товар<br/><a href="documentation?name=SearchItemReviews">Документация</a>
     *
     * @param string $language
     * @param string $xmlSearchParameters
     * @param string $framePosition
     * @param string $frameSize
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemReviewInfoListFrameAnswer

     */
    public static function SearchItemReviews($language, $sessionId, $xmlSearchParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlSearchParameters' => $xmlSearchParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchItemReviews', $params, 'OtapiItemReviewInfoListFrameAnswer', $request);
    }

    /**
     *  Поиск отзывов пользователей на товар<br/><a href="documentation?name=SearchItemReviewsForOperator">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $xmlSearchParameters
     * @param string $framePosition
     * @param string $frameSize
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemReviewInfoListFrameAnswer

     */
    public static function SearchItemReviewsForOperator($language, $sessionId, $xmlSearchParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlSearchParameters' => $xmlSearchParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchItemReviewsForOperator', $params, 'OtapiItemReviewInfoListFrameAnswer', $request);
    }

    /**
     *  Подтверждение отзывов пользователей на товар<br/><a href="documentation?name=ApproveItemReviews">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $itemReviewIds
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function ApproveItemReviews($language, $sessionId, $itemReviewIds, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'itemReviewIds' => $itemReviewIds,
        );
        self::registerRequest('ApproveItemReviews', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Удаление отзывов пользователей на товар<br/><a href="documentation?name=DeleteItemReviews">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $itemReviewIds
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function DeleteItemReviews($language, $sessionId, $itemReviewIds, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'itemReviewIds' => $itemReviewIds,
        );
        self::registerRequest('DeleteItemReviews', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Поиск товаров с отзывами пользователей<br/><a href="documentation?name=SearchReviewedItems">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $xmlSearchParameters
     * @param string $framePosition
     * @param string $frameSize
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemInfoListFrameAnswer

     */
    public static function SearchReviewedItems($language, $sessionId, $xmlSearchParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlSearchParameters' => $xmlSearchParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchReviewedItems', $params, 'OtapiItemInfoListFrameAnswer', $request);
    }

    /**
     *  Получить настройки отзывов<br/><a href="documentation?name=GetItemReviewSettings">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $includeMetaInfo
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemReviewSettingsAnswer

     */
    public static function GetItemReviewSettings($language, $sessionId, $includeMetaInfo, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'includeMetaInfo' => $includeMetaInfo,
        );
        self::registerRequest('GetItemReviewSettings', $params, 'OtapiItemReviewSettingsAnswer', $request);
    }

    /**
     *  Обновить настройки отзывов<br/><a href="documentation?name=UpdateItemReviewSettings">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $xmlUpdateData
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateItemReviewSettings($language, $sessionId, $xmlUpdateData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateItemReviewSettings', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Добавить ответ на отзыв<br/><a href="documentation?name=AddAnswerToItemReview">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $itemReviewId
     * @param string $xmlAddData
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function AddAnswerToItemReview($language, $sessionId, $itemReviewId, $xmlAddData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'itemReviewId' => $itemReviewId,
            'xmlAddData' => $xmlAddData,
        );
        self::registerRequest('AddAnswerToItemReview', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Добавить оценку на отзыв<br/><a href="documentation?name=GradeItemReview">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $itemReviewId
     * @param string $isPositive
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiItemReviewGradeInfoAnswer

     */
    public static function GradeItemReview($language, $sessionId, $itemReviewId, $isPositive, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'itemReviewId' => $itemReviewId,
            'isPositive' => $isPositive,
        );
        self::registerRequest('GradeItemReview', $params, 'OtapiItemReviewGradeInfoAnswer', $request);
    }

    /**
     *  Получить настройки уведомлений<br/><a href="documentation?name=GetMessageSettings">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $includeMetaInfo
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiMessengerSettingsAnswer

     */
    public static function GetMessageSettings($language, $sessionId, $includeMetaInfo, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'includeMetaInfo' => $includeMetaInfo,
        );
        self::registerRequest('GetMessageSettings', $params, 'OtapiMessengerSettingsAnswer', $request);
    }

    /**
     *  Обновить настройки для уведомлений<br/><a href="documentation?name=UpdateMessageSettings">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $xmlUpdateData
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateMessageSettings($language, $sessionId, $xmlUpdateData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateMessageSettings', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Добавить ответ на отзыв как оператор<br/><a href="documentation?name=AddAnswerToItemReviewForOperator">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $itemReviewId
     * @param string $xmlAddData
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function AddAnswerToItemReviewForOperator($language, $sessionId, $itemReviewId, $xmlAddData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'itemReviewId' => $itemReviewId,
            'xmlAddData' => $xmlAddData,
        );
        self::registerRequest('AddAnswerToItemReviewForOperator', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Получить дополнительную информацию о товаре<br/><a href="documentation?name=GetAdditionalItemInfo">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $inputLanguage
     * @param string $itemId
     * @param string $includeMetaInfo
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiAdditionalItemInfoAnswer

     */
    public static function GetAdditionalItemInfo($language, $sessionId, $inputLanguage, $itemId, $includeMetaInfo, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'inputLanguage' => $inputLanguage,
            'itemId' => $itemId,
            'includeMetaInfo' => $includeMetaInfo,
        );
        self::registerRequest('GetAdditionalItemInfo', $params, 'OtapiAdditionalItemInfoAnswer', $request);
    }

    /**
     *  Обновить дополнительную информацию о товаре<br/><a href="documentation?name=UpdateAdditionalItemInfo">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $inputLanguage
     * @param string $itemId
     * @param string $xmlUpdateData
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateAdditionalItemInfo($language, $sessionId, $inputLanguage, $itemId, $xmlUpdateData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'inputLanguage' => $inputLanguage,
            'itemId' => $itemId,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateAdditionalItemInfo', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Получить информацию о файле<br/><a href="documentation?name=GetFileInfo">Документация</a>
     *
     * @param string $language
     * @param string $fileId
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiFileInfoAnswer

     */
    public static function GetFileInfo($language, $fileId, &$request) {
        $params = array(
            'language' => $language,
            'fileId' => $fileId,
        );
        self::registerRequest('GetFileInfo', $params, 'OtapiFileInfoAnswer', $request);
    }

    /**
     *  Получить адрес загрузки файла<br/><a href="documentation?name=GetFileUploadUrl">Документация</a>
     *
     * @param string $language
     * @param string $fileName
     * @param string $fileType
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiUploadUrlInfoAnswer

     */
    public static function GetFileUploadUrl($language, $fileName, $fileType, &$request) {
        $params = array(
            'language' => $language,
            'fileName' => $fileName,
            'fileType' => $fileType,
        );
        self::registerRequest('GetFileUploadUrl', $params, 'OtapiUploadUrlInfoAnswer', $request);
    }

    /**
     *  Получение списка языков каталога<br/><a href="documentation?name=GetCatalogLanguageInfoList">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiLanguageInfoListAnswer

     */
    public static function GetCatalogLanguageInfoList($language, $sessionId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetCatalogLanguageInfoList', $params, 'OtapiLanguageInfoListAnswer', $request);
    }

    /**
     *  Искать информацию о городах<br/><a href="documentation?name=SearchCities">Документация</a>
     *
     * @param string $language
     * @param string $xmlSearchParameters
     * @param string $framePosition
     * @param string $frameSize
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCityInfoListFrameAnswer

     */
    public static function SearchCities($language, $xmlSearchParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'xmlSearchParameters' => $xmlSearchParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchCities', $params, 'OtapiCityInfoListFrameAnswer', $request);
    }

    /**
     *  Поиск пунктов выдачи заказов<br/><a href="documentation?name=SearchDeliveryPickupPoints">Документация</a>
     *
     * @param string $language
     * @param string $xmlSearchParameters
     * @param string $framePosition
     * @param string $frameSize
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiPickupPointInfoListFrameAnswer

     */
    public static function SearchDeliveryPickupPoints($language, $xmlSearchParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'xmlSearchParameters' => $xmlSearchParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchDeliveryPickupPoints', $params, 'OtapiPickupPointInfoListFrameAnswer', $request);
    }

    /**
     *  <a href="documentation?name=SearchDirectPayments">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $xmlParameters
     * @param string $framePosition
     * @param string $frameSize
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiPaymentInfoListFrameAnswer

     */
    public static function SearchDirectPayments($language, $sessionId, $xmlParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlParameters' => $xmlParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchDirectPayments', $params, 'OtapiPaymentInfoListFrameAnswer', $request);
    }

    /**
     *  <a href="documentation?name=SearchUserPayments">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $xmlParameters
     * @param string $framePosition
     * @param string $frameSize
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiPaymentInfoListFrameAnswer

     */
    public static function SearchUserPayments($language, $sessionId, $xmlParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlParameters' => $xmlParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchUserPayments', $params, 'OtapiPaymentInfoListFrameAnswer', $request);
    }

    /**
     *  Получить настройки службы доставки<br/><a href="documentation?name=GetDeliveryServiceSystemSettings">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $serviceSystem
     * @param string $includeMetaInfo
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiDeliveryServiceSystemSettingsAnswer

     */
    public static function GetDeliveryServiceSystemSettings($language, $sessionId, $serviceSystem, $includeMetaInfo, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'serviceSystem' => $serviceSystem,
            'includeMetaInfo' => $includeMetaInfo,
        );
        self::registerRequest('GetDeliveryServiceSystemSettings', $params, 'OtapiDeliveryServiceSystemSettingsAnswer', $request);
    }

    /**
     *  Обновить настройки службы доставки<br/><a href="documentation?name=UpdateDeliveryServiceSystemSettings">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $serviceSystem
     * @param string $xmlUpdateData
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateDeliveryServiceSystemSettings($language, $sessionId, $serviceSystem, $xmlUpdateData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'serviceSystem' => $serviceSystem,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateDeliveryServiceSystemSettings', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Запустить подтверждение выгруженных в службу доставки посылок<br/><a href="documentation?name=RunExportedPackagesConfirming">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $serviceSystem
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBackgroundActivityIdentificationInfoAnswer

     */
    public static function RunExportedPackagesConfirming($language, $sessionId, $serviceSystem, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'serviceSystem' => $serviceSystem,
        );
        self::registerRequest('RunExportedPackagesConfirming', $params, 'OtapiBackgroundActivityIdentificationInfoAnswer', $request);
    }

    /**
     *  Найти перечень любых подборок<br/><a href="documentation?name=BatchSearchRatingLists">Документация</a>
     *
     * @param string $language
     * @param string $xmlSearchParameters
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBatchRatingListsSearchResultAnswer

     */
    public static function BatchSearchRatingLists($language, $xmlSearchParameters, &$request) {
        $params = array(
            'language' => $language,
            'xmlSearchParameters' => $xmlSearchParameters,
        );
        self::registerRequest('BatchSearchRatingLists', $params, 'OtapiBatchRatingListsSearchResultAnswer', $request);
    }

    /**
     *  Массовое добавление товаров в корзину<br/><a href="documentation?name=BatchSimplifiedAddItemsToBasket">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $xmlRequest
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiElementIdListAnswer

     */
    public static function BatchSimplifiedAddItemsToBasket($language, $sessionId, $xmlRequest, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlRequest' => $xmlRequest,
        );
        self::registerRequest('BatchSimplifiedAddItemsToBasket', $params, 'OtapiElementIdListAnswer', $request);
    }

    /**
     *  Отменить строки заказа<br/><a href="documentation?name=CancelLinesOrder">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $orderId
     * @param string $orderLineIds
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer
     */
    public static function CancelLinesOrder($language, $sessionId, $orderId, $orderLineIds, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'orderId' => $orderId,
            'orderLineIds' => $orderLineIds,
        );
        self::registerRequest('CancelLinesOrder', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Подтвердить цену в строках заказа<br/><a href="documentation?name=ConfirmPriceLinesOrder">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $orderId
     * @param string $orderLineIds
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer
     */
    public static function ConfirmPriceLinesOrder($language, $sessionId, $orderId, $orderLineIds, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'orderId' => $orderId,
            'orderLineIds' => $orderLineIds,
        );
        self::registerRequest('ConfirmPriceLinesOrder', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Перемещение списка категорий и изменение порядка<br/><a href="documentation?name=MoveCategories">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $categoryIds
     * @param string $parentCategoryId
     * @param string $index
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function MoveCategories($language, $sessionId, $categoryIds, $parentCategoryId, $index, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'categoryIds' => $categoryIds,
            'parentCategoryId' => $parentCategoryId,
            'index' => $index,
        );
        self::registerRequest('MoveCategories', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Добавить заказ<br/><a href="documentation?name=AddOrder">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $xmlAddData
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiOrderInfoAnswer

     */
    public static function AddOrder($language, $sessionId, $xmlAddData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlAddData' => $xmlAddData,
        );
        self::registerRequest('AddOrder', $params, 'OtapiOrderInfoAnswer', $request);
    }

    /**
     *  Получить платежи ПС Direct в статусе ожидания<br/><a href="documentation?name=GetWaitingDirectPayments">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $framePosition
     * @param string $frameSize
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiDirectPaymentInfoListFrameAnswer

     */
    public static function GetWaitingDirectPayments($language, $sessionId, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('GetWaitingDirectPayments', $params, 'OtapiDirectPaymentInfoListFrameAnswer', $request);
    }

    /**
     *  Получить настройки ПС Direct<br/><a href="documentation?name=GetDirectPaymentSettings">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $inputLanguage
     * @param string $includeMetaInfo
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiDirectPaymentSettingsAnswer

     */
    public static function GetDirectPaymentSettings($language, $sessionId, $inputLanguage, $includeMetaInfo, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'inputLanguage' => $inputLanguage,
            'includeMetaInfo' => $includeMetaInfo,
        );
        self::registerRequest('GetDirectPaymentSettings', $params, 'OtapiDirectPaymentSettingsAnswer', $request);
    }

    /**
     *  Обновить настройки ПС Direct<br/><a href="documentation?name=UpdateDirectPaymentSettings">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $xmlUpdateData
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateDirectPaymentSettings($language, $sessionId, $xmlUpdateData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateDirectPaymentSettings', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Установить статус платежа для ПС Direct<br/><a href="documentation?name=SetDirectPaymentStatus">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $paymentId
     * @param string $isPaid
     * @param string $adminInfo
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function SetDirectPaymentStatus($language, $sessionId, $paymentId, $isPaid, $adminInfo, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'paymentId' => $paymentId,
            'isPaid' => $isPaid,
            'adminInfo' => $adminInfo,
        );
        self::registerRequest('SetDirectPaymentStatus', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Поиск способов доставки<br/><a href="documentation?name=SearchDeliveryModes">Документация</a>
     *
     * @param string $language
     * @param string $xmlSearchParameters
     * @param string $framePosition
     * @param string $frameSize
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiDeliveryModeListFrameAnswer

     */
    public static function SearchDeliveryModes($language, $xmlSearchParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'xmlSearchParameters' => $xmlSearchParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchDeliveryModes', $params, 'OtapiDeliveryModeListFrameAnswer', $request);
    }

    /**
     *  Запуск проверки содержимого корзины<br/><a href="documentation?name=RunBasketChecking">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $elements
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBackgroundActivityIdentificationInfoAnswer

     */
    public static function RunBasketChecking($language, $sessionId, $elements, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'elements' => $elements,
        );
        self::registerRequest('RunBasketChecking', $params, 'OtapiBackgroundActivityIdentificationInfoAnswer', $request);
    }

    /**
     *  Получение результата проверки содержимого корзины<br/><a href="documentation?name=GetBasketCheckingResult">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $activityId
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBasketCheckingResultAnswer

     */
    public static function GetBasketCheckingResult($language, $sessionId, $activityId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'activityId' => $activityId,
        );
        self::registerRequest('GetBasketCheckingResult', $params, 'OtapiBasketCheckingResultAnswer', $request);
    }

    /**
     *  Получить список доступных доставок по содержимому корзины<br/><a href="documentation?name=GetDeliveryModesByBasket">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $elements
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiDeliveryModeListAnswer

     */
    public static function GetDeliveryModesByBasket($language, $sessionId, $elements, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'elements' => $elements,
        );
        self::registerRequest('GetDeliveryModesByBasket', $params, 'OtapiDeliveryModeListAnswer', $request);
    }

    /**
     *  Получить данные по ослеживанию посылки<br/><a href="documentation?name=GetPackageTracking">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $packageId
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiPackageTrackingInfoAnswer

     */
    public static function GetPackageTracking($language, $sessionId, $packageId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'packageId' => $packageId,
        );
        self::registerRequest('GetPackageTracking', $params, 'OtapiPackageTrackingInfoAnswer', $request);
    }

    /**
     *  Получить данные по ослеживанию посылки<br/><a href="documentation?name=GetPackageTrackingForOperator">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $packageId
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiPackageTrackingInfoAnswer

     */
    public static function GetPackageTrackingForOperator($language, $sessionId, $packageId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'packageId' => $packageId,
        );
        self::registerRequest('GetPackageTrackingForOperator', $params, 'OtapiPackageTrackingInfoAnswer', $request);
    }

    /**
     *  Получение списка групп скидок для текущего пользователя<br/><a href="documentation?name=GetDiscountGroups">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiDiscountGroupInfoListAnswer

     */
    public static function GetDiscountGroups($language, $sessionId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
        );
        self::registerRequest('GetDiscountGroups', $params, 'OtapiDiscountGroupInfoListAnswer', $request);
    }

    /**
     *  Получить информацию о счете<br/><a href="documentation?name=GetBill">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $billId
     * @param string $includeDetails
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiBillInfoAnswer

     */
    public static function GetBill($language, $sessionId, $billId, $includeDetails, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'billId' => $billId,
            'includeDetails' => $includeDetails,
        );
        self::registerRequest('GetBill', $params, 'OtapiBillInfoAnswer', $request);
    }

    /**
     *  Получить дополнительную информацию о продавце<br/><a href="documentation?name=GetAdditionalVendorInfo">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $inputLanguage
     * @param string $vendorId
     * @param string $includeMetaInfo
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiAdditionalVendorInfoAnswer

     */
    public static function GetAdditionalVendorInfo($language, $sessionId, $inputLanguage, $vendorId, $includeMetaInfo, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'inputLanguage' => $inputLanguage,
            'vendorId' => $vendorId,
            'includeMetaInfo' => $includeMetaInfo,
        );
        self::registerRequest('GetAdditionalVendorInfo', $params, 'OtapiAdditionalVendorInfoAnswer', $request);
    }

    /**
     *  Обновить дополнительную информацию о продавце<br/><a href="documentation?name=UpdateAdditionalVendorInfo">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $inputLanguage
     * @param string $vendorId
     * @param string $xmlUpdateData
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateAdditionalVendorInfo($language, $sessionId, $inputLanguage, $vendorId, $xmlUpdateData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'inputLanguage' => $inputLanguage,
            'vendorId' => $vendorId,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateAdditionalVendorInfo', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  Найти контентные страницы<br/><a href="documentation?name=SearchContentMenuItems">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $inputLanguage
     * @param string $xmlSearchParameters
     * @param string $framePosition
     * @param string $frameSize
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiContentMenuItemInfoListFrameAnswer

     */
    public static function SearchContentMenuItems($language, $sessionId, $inputLanguage, $xmlSearchParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'inputLanguage' => $inputLanguage,
            'xmlSearchParameters' => $xmlSearchParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchContentMenuItems', $params, 'OtapiContentMenuItemInfoListFrameAnswer', $request);
    }

    /**
     *  Создать контентную страницу<br/><a href="documentation?name=CreateContentMenuItem">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $inputLanguage
     * @param string $xmlCreateData
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiCreateContentMenuItemAnswer

     */
    public static function CreateContentMenuItem($language, $sessionId, $inputLanguage, $xmlCreateData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'inputLanguage' => $inputLanguage,
            'xmlCreateData' => $xmlCreateData,
        );
        self::registerRequest('CreateContentMenuItem', $params, 'OtapiCreateContentMenuItemAnswer', $request);
    }

    /**
     *  Удалить контентную страницу<br/><a href="documentation?name=DeleteContentMenuItem">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $menuItemId
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function DeleteContentMenuItem($language, $sessionId, $menuItemId, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'menuItemId' => $menuItemId,
        );
        self::registerRequest('DeleteContentMenuItem', $params, 'VoidOtapiAnswer', $request);
    }


    /**
     *  Получить упрощенный вариант списка избранных продавцов<br/><a href="documentation?name=SearchSimplifiedFavoriteVendors">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $xmlParameters
     * @param string $framePosition
     * @param string $frameSize
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSimplifiedVendorInfoListFrameAnswer

     */
    public static function SearchSimplifiedFavoriteVendors($language, $sessionId, $xmlParameters, $framePosition, $frameSize, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlParameters' => $xmlParameters,
            'framePosition' => $framePosition,
            'frameSize' => $frameSize,
        );
        self::registerRequest('SearchSimplifiedFavoriteVendors', $params, 'OtapiSimplifiedVendorInfoListFrameAnswer', $request);
    }
    /**
     *  <a href="documentation?name=GetSmsServiceSettings">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $includeMetaInfo
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSmsServiceSettingsAnswer

     */
    public static function GetSmsServiceSettings($language, $sessionId, $includeMetaInfo, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'includeMetaInfo' => $includeMetaInfo,
        );
        self::registerRequest('GetSmsServiceSettings', $params, 'OtapiSmsServiceSettingsAnswer', $request);
    }

    /**
     *  <a href="documentation?name=UpdateSmsServiceSettings">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $xmlUpdateData
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateSmsServiceSettings($language, $sessionId, $xmlUpdateData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateSmsServiceSettings', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  <a href="documentation?name=GetUserSettings">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $includeMetaInfo
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiUserSettingsAnswer

     */
    public static function GetUserSettings($language, $sessionId, $includeMetaInfo, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'includeMetaInfo' => $includeMetaInfo,
        );
        self::registerRequest('GetUserSettings', $params, 'OtapiUserSettingsAnswer', $request);
    }

    /**
     *  <a href="documentation?name=UpdateUserSettings">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $xmlUpdateData
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return VoidOtapiAnswer

     */
    public static function UpdateUserSettings($language, $sessionId, $xmlUpdateData, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'xmlUpdateData' => $xmlUpdateData,
        );
        self::registerRequest('UpdateUserSettings', $params, 'VoidOtapiAnswer', $request);
    }

    /**
     *  <a href="documentation?name=ChangePhone">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $currentPassword
     * @param string $newPhone
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiConfirmationInfoAnswer

     */
    public static function ChangePhone($language, $sessionId, $currentPassword, $newPhone, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'currentPassword' => $currentPassword,
            'newPhone' => $newPhone,
        );
        self::registerRequest('ChangePhone', $params, 'OtapiConfirmationInfoAnswer', $request);
    }

    /**
     *  <a href="documentation?name=ConfirmPhone">Документация</a>
     *
     * @param string $language
     * @param string $sessionId
     * @param string $confirmationCode
     * @param $request Переменная, куда будет записан ответ от сервисов
     * @return OtapiSessionIdAnswer

     */
    public static function ConfirmPhone($language, $sessionId, $confirmationCode, &$request) {
        $params = array(
            'language' => $language,
            'sessionId' => $sessionId,
            'confirmationCode' => $confirmationCode,
        );
        self::registerRequest('ConfirmPhone', $params, 'OtapiSessionIdAnswer', $request);
    }

}
