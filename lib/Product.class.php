<?php

OTBase::import('system.lib.FileAndMysqlMemoryCache');

/*
 * Класс для работы с сущностью товара.
 * 
 * Пример вызова:
 * 
 * OTAPILib2::BatchGetItemFullInfo(Session::getActiveLang(), User::getObject()->getSid(), 15451662868, '', $answer);
 * OTAPILib2::makeRequests();
 * if ($answer && $answer->GetResult()) {
 *     $item = Product::getObject($itemId, $answer->GetResult()->GetItem());
 *     $result = 'Title: ' . $item->getTitle();
 *     $item = Product::getObject(15451662868);
 *     $result = 'Title: ' . $item->getTitle();
 * }
 */
class Product
{
    const DEFAULT_IMAGE = '/i/noimg.png';

    private static $_objects = array();

    /**
     * @var Array
     */
    private $registry;

    /**
     * @var string
     */
    private $id = null;

    /**
     * @var string
     */
    private $errorCode = '';

    /**
     * @var string
     */
    private $provider = '';

    /**
     * @var string
     */
    private $notAvailableReason  = '';

    /**
     * @var string
     */
    private $notAvailableId  = '';
    
    /**
     * @var string
     */
    private $title = '';

    /**
     * @var string
     */
    private $externalItemUrl = '';

    /**
     * @var integer
     */
    private $code = 0;

    /**
     * @var OtapiBasePrice|OtapiPrice|null
     */
    private $fullTotalCost = null;

    /**
     * @var float|OtapiPrice
     */
    private $price = null;

    /**
     * @var string
     */
    private $displayPrice = null;

    /**
     * @var float|OtapiBasePrice
     */
    private $promotionPrice = null;

    /**
     * @var string
     */
    private $displayPromoPrice = null;

    /**
     * @var int
     */
    private $promotionPricePercent = null;

    /**
     * @var string/null
     */
    private $promotionGroupName = null;

    /**
     * @var array
     */
    private $quantityRanges = array();

    /**
     * @var array
     */
    private $pictures = array();

    /**
     * @var array
     */
    private $properties = array();

    /**
     * @var array
     */
    public $features = array();

    /**
     * @var array
     */
    private $mainPicture = array();

    /**
     * @var array
     */
    private $videos = array();

    /**
     * @var array
     */
    private $vendor = array();

    /**
     * @var string
     */
    private $vendorId = '';

    /**
     * @var string
     */
    private $vendorName = '';

    /**
     * @var float
     */
    private $vendorScore = 0;

    /**
     * @var bool
     */
    private $isSellAllowed = true;

    /**
     * @var string
     */
    private $sellDisallowReason = '';

    /**
     * @var string
     */
    private $configurationName = '';
    
    /**
     * @var string
     */
    private $configurationId = '';

    /**
     * @var array
     */
    private $configuration = [];
    
    /**
     * @var string
     */
    private $promoId = '';
    
    /**
     * @var string
     */
    private $categoryId = '';
    
    /**
     * @var string
     */
    private $categoryName = '';
    
    /**
     * @var float
     */
    private $weight = 0;

    /**
     * @var string
     */
    private $weightLabel = '';
    
    /**
     * @var int
     */
    private $quantity = 0; 
    
    /**
     * @var string
     */
    private $comment = null;

    /**
     * @var string
     */
    private $url = '';

    /**
     * @var int
     */
    private $totalSales = null;

    /**
     * @var int
     */
    private $salesInLast30Days = null;

    /**
     * @var float
     */
    private $itemReviewRating = null;

    /**
     * @var string
     */
    private $currencyName = '';

    /**
     * @var string
     */
    private $currencyCode = '';

    /**
     * @var OtapiSimplifiedItemProperty[]
     */
    private $сonfigurators = [];

    /**
     * @var null|OtapiSimplifiedItemProperty[]
     */
    private $сonfiguratorMultiInput = null;

    /**
     * @var string|null
     */
    private $externalDeliveryId = null;

    /**
     * @var string|null
     */
    private $externalDeliveryName = null;

    /**
     * @var string|null
     */
    private $externalDeliveryDescription = null;

    /**
     * @var array|null
     */
    private $deliveryModes = null;

    public static function getObject($id, $data = null, $options = array())
    {
        $id = strval($id);
        if ($data === null) {
            $blockList = '';
            $answer = null;
            OTAPILib2::BatchGetItemFullInfo(Session::getActiveLang(), User::getObject()->getSid(), $id, $blockList, $answer);
            OTAPILib2::makeRequests();
            if ($answer && $answer->GetResult()) {
                $data = $answer->GetResult()->GetItem();
            }
        }

        // создаем новый экземпляр
        // кешировать объект не имеет смысла, т.к. чаще всего цена зависит от конфигурации
        return new self($id, $data, $options);
    }

    private function __clone()
    {
    }

    private function __construct($id, $data, $options = array())
    {
        $this->id = $id;

        // BatchGetItemFullInfo
        if ($data instanceof OtapiItemFullInfo) {
            // Not implemented
        } elseif ($data instanceof OtapiItemInfo) {
            $this->id = $data->GetId();
            $this->errorCode = $data->GetErrorCode();
            $this->provider = InstanceProvider::getObject()->GetAliasByProviderName(
                Session::getActiveLang(),
                $data->GetProviderType()
            );
            $this->title = $data->GetTitle();
            $this->vendorId = $data->GetVendorId();

            $this->vendorName = $data->GetVendorName();
            $this->vendorScore = (float) $data->GetVendorScore();

            $features  = array();
            foreach ($data->GetFeatures()->GetFeature() as $feature) {
                $features[] = $feature;
            }
            $this->features = $features;

            $this->price = $data->GetPrice();
            $this->promotionPrice = $data->GetPromotionPrice();

            foreach ($data->GetPromotionPricePercent()->GetPercent() as $pricePercent) {
                if (! Session::get('currency') || $pricePercent->GetCurrencyCodeAttribute() == Session::get('currency')) {
                    $this->promotionPricePercent = (int)$pricePercent->asString();
                    break;
                }
            }

            $mainPictureUrl = $data->GetMainPictureUrl();
            $this->mainPicture = array(
                'isMain' => true,
                'url' => $mainPictureUrl,
                'small' => $mainPictureUrl,
                'medium' => $mainPictureUrl,
                'large' => $mainPictureUrl,
            );

            if ($data->GetPictures()->GetItemPicture()) {
                foreach ($data->GetPictures()->GetItemPicture() as $value) {
                    $picture = array();
                    $picture['isMain'] = $value->IsMain();
                    $picture['url'] = $value->GetUrl();
                    $picture['small'] = $value->GetSmall()->asString();
                    $picture['medium'] = $value->GetMedium()->asString();
                    $picture['large'] = $value->GetLarge()->asString();
                    $this->pictures[] = $picture;

                    if ($value->IsMain()) {
                        $this->mainPicture = $picture;
                    }
                }
            }

            $this->isSellAllowed = $data->IsSellAllowed();
            $this->sellDisallowReason = $data->GetSellDisallowReason();

            $this->defineFeaturesValues($data->GetFeaturedValues()->GetValue());

            if ($data->GetQuantityRanges()->GetRange()) {
                $i = 0;
                foreach ($data->GetQuantityRanges()->GetRange() as $range) {
                    $this->quantityRanges[$i] = [
                        'Price' => $range->GetPrice()->GetConvertedPriceList(),
                        'Min' => $range->GetMinQuantity(),
                        'Max' => null,
                    ];
                    if (isset($this->quantityRanges[$i - 1])) {
                        $this->quantityRanges[$i - 1]['Max'] = $range->GetMinQuantity() - 1;
                    }
                    $i++;
                }
            }
        } elseif ($data instanceof OtapiElementInfo) {
            $this->id = $data->GetItemId();
            $this->provider = InstanceProvider::getObject()->GetAliasByProviderName(
                Session::getActiveLang(),
                $data->GetProviderType()
            );

            $this->configurationId = $data->GetConfigurationId();
            $this->categoryId = $data->GetCategoryId();
            $this->categoryName = $data->GetCategoryName();

            if ($data->GetConfiguration()) {
                foreach ($data->GetConfiguration()->GetConfigurator() as $config) {
                    $this->configuration[] = [
                        'name' => $config->GetNameAttribute(),
                        'value' => $config->GetValueAttribute(),
                        'originalName' => $config->GetOriginalNameAttribute(),
                        'originalValue' => $config->GetOriginalValueAttribute()
                    ];
                }
            }

            foreach ($data->GetFields()->GetFieldInfo() as $field) {
                switch ($field->GetNameAttribute()) {
                    case 'ItemTitle':
                        $this->title = $field->GetValueAttribute();
                        break;
                    case 'VendorId':
                        $this->vendorId = $field->GetValueAttribute();
                        $this->vendorName = $field->GetValueAttribute();
                        break;
                    case 'ItemConfiguration':
                        $this->configurationName = $field->GetValueAttribute();
                        break;
                    case 'PromoId':
                        $this->promoId = $field->GetValueAttribute();
                        break;
                    case 'PictureURL':
                        $this->mainPicture = array(
                            'url' => $field->GetValueAttribute(),
                            'small' => $field->GetValueAttribute(),
                            'medium' => $field->GetValueAttribute(),
                            'large' => $field->GetValueAttribute(),
                        );
                        break;
                    case 'Weight':
                        $this->weight = $field->GetValueAttribute();
                        break;
                    case 'Comment':
                        $this->comment = $field->GetValueAttribute();
                        break;
                    case 'ExternalDeliveryId':
                        $this->externalDeliveryId = $field->GetValueAttribute();
                        break;
                    case 'ExternalDeliveryName':
                        $this->externalDeliveryName = $field->GetValueAttribute();
                        break;
                    case 'ExternalDeliveryDescription':
                        $this->externalDeliveryDescription = $field->GetValueAttribute();
                        break;
                }
            }

            $currency = User::getObject()->getCurrencyCode();
            foreach ($data->GetDeliveryModes()->GetMode() as $mode) {
                $tmpMode = array();

                $id = $mode->GetId();
                $tmpMode['Id'] = $id;
                $tmpMode['Name'] = $mode->GetName();
                $tmpMode['Description'] = $mode->GetDescription();

                foreach ($mode->GetFullPrice()->GetConvertedPriceList()->GetDisplayedMoneys()->GetMoney() as $money) {
                    if ($money->GetCodeAttribute() === $currency) {
                        $deliveryCost = array();
                        $deliveryCost['Code'] = $money->GetCodeAttribute();
                        $deliveryCost['Sign'] = $money->GetSignAttribute();
                        $deliveryCost['Val'] = $money->GetValue();
                        $tmpMode['Cost'] = $deliveryCost;
                        break;
                    }
                }

                $this->deliveryModes[$id] = $tmpMode;
            }

            $this->fullTotalCost = $data->GetFullTotalCost();
            $this->promotionPrice = $data->GetFullPrice();
            $this->price = $data->GetFullPriceWithoutDiscount();
            $this->quantity = $data->GetQuantity();
            if ($data->GetDiscount()->GetRawData()) {
                $this->promotionPricePercent = $data->GetDiscount()->GetValueAttribute();
                $this->promotionGroupName = $data->GetDiscount()->GetNameAttribute();
            }

            $features  = array();
            foreach ($data->GetFeatures()->GetFeature() as $feature) {
                $features[] = (string) $feature;
            }
            $this->features = $features;
        } elseif ($data instanceof OtapiBatchSimplifiedItemFullInfo) {
            $this->id = $data->GetItem()->GetId();
            $this->title = $data->GetItem()->GetTitle();
            $this->provider = InstanceProvider::getObject()->GetAliasByProviderName(
                Session::getActiveLang(),
                $data->GetItem()->GetProviderType()
            );
            foreach ($data->GetItem()->GetPictures()->GetPicture() as $key => $picture){
                if ($key === 0) {
                    $this->mainPicture = array(
                        'url' => $picture->getUrl(),
                        'small' => $picture->getSmall()->asString(),
                        'medium' => $picture->GetMedium()->asString(),
                        'large' =>$picture->GetLarge()->asString(),
                    );
                }

                $this->pictures[] = array(
                    'url' => $picture->getUrl(),
                    'small' => $picture->GetSmall()->asString(),
                    'medium' => $picture->GetMedium()->asString(),
                    'large' =>$picture->GetLarge()->asString(),
                );
            }
            $this->weight = (float)$data->GetItem()->GetWeight()->asString();
            $this->weightLabel = $data->GetItem()->GetWeight()->GetDisplayNameAttribute();
            $this->code = $data->GetItem()->GetId();
            if ($data->GetItem()->GetAvailability()->asString() === "false") {
                $this->notAvailableReason = $data->GetItem()->GetAvailability()->GetDisplayNameAttribute();
                $this->notAvailableId = $data->GetItem()->GetAvailability()->GetIdAttribute();
            }
            $features  = array();
            foreach ($data->GetItem()->GetFeatures()->GetFeature() as $feature) {
                $features[] = $feature;
            }
            $this->features = $features;

            $this->vendorId = $data->GetItem()->GetVendor()->asString();
            $this->vendorName = $data->GetItem()->GetVendor()->GetDisplayNameAttribute();
            foreach ($data->GetItem()->GetVideos()->GetVideo() as $video) {
                $this->videos[] = $video;
            }
            $this->externalItemUrl = $data->GetItem()->GetExternalItemUrl();

            foreach ($data->GetItem()->GetProperties()->GetProperty() as $property) {
                $this->properties[$property->GetIdAttribute()] = [
                    'DisplayName' => $property->GetDisplayNameAttribute(),
                    'Values' => [],
                ];

                foreach ($property->GetValue() as $value) {
                    $this->properties[$property->GetIdAttribute()]['Values'][$value->GetIdAttribute()] = $value->GetDisplayNameAttribute();
                }
            }

            foreach ($data->GetItem()->GetConfigurators()->GetProperty() as $property) {
                if ($property->GetIdAttribute() === $data->GetItem()->GetConfigurators()->GetMultiInputPropertyIdAttribute()) {
                    $this->сonfiguratorMultiInput = $property;
                } else {
                    $this->сonfigurators[] = $property;
                }
            }
        } elseif ($data instanceof OtapiSimplifiedItemInfo) {
            $this->id = $data->GetId();
            $this->errorCode = $data->GetErrorCode() ? $data->GetErrorCode() : 'Ok';
            $this->title = $data->GetTitle();
            foreach ($data->GetPictures()->GetPicture() as $key => $picture){
                if ($key === 0) {
                    $this->mainPicture = array(
                        'url' => $picture->getUrl(),
                        'small' => $picture->getSmall()->asString(),
                        'medium' => $picture->GetMedium()->asString(),
                        'large' =>$picture->GetLarge()->asString(),
                    );
                }

                $this->pictures[] = array(
                    'url' => $picture->getUrl(),
                    'small' => $picture->GetSmall()->asString(),
                    'medium' => $picture->GetMedium()->asString(),
                    'large' =>$picture->GetLarge()->asString(),
                );
            }

            $features  = array();
            foreach ($data->GetFeatures()->GetFeature() as $feature) {
                $features[] = $feature;
            }
            $this->features = $features;

            $this->vendorId = $data->GetVendor()->asString();

            $this->vendorName = $data->GetVendor()->GetDisplayNameAttribute();

            $this->externalItemUrl = $data->GetExternalItemUrl();
            $this->price = $data->GetPrice()->GetOldPrice() ? $data->GetPrice()->GetOldPrice()->asString() : $data->GetPrice()->GetPrice()->asString();
            $this->promotionPrice = $data->GetPrice()->GetPrice()->asString();

            if ($data->GetQuantityRanges()->GetRange()) {
                foreach ($data->GetQuantityRanges()->GetRange() as $range) {
                    $this->quantityRanges[] = [
                        'Price' => (float)$range->GetPrice()->asString(),
                        'Min' => $range->GetMinQuantityAttribute(),
                        'Max' => $range->GetMaxQuantityAttribute(),
                    ];
                }
            }
        }

        $this->url = $this->generateUrl($options);
    }

    public function __get($name)
    {
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter) ) {
            return $this->$getter();
        } elseif(property_exists(__CLASS__, $name)) {
            return $this->$name;
        }

        throw new \Exception('Not found getter for property - "' . $name . '"');
    }

    public function getDisplayPrice()
    {
        if (! is_null($this->displayPrice)) {
            return $this->displayPrice;
        }

        if ($this->price instanceof OtapiBasePrice) {
            $this->displayPrice = $this->getPriceOfConvertedPriceList($this->price->GetConvertedPriceList());
        } else {
            $this->displayPrice = $this->getPriceOfFloat($this->price);
        }

        return $this->displayPrice;
    }

    public function getDisplayPromoPrice()
    {
        if (! is_null($this->displayPromoPrice)) {
            return $this->displayPromoPrice;
        }

        if ($this->promotionPrice instanceof OtapiBasePrice) {
            $this->displayPromoPrice = $this->getPriceOfConvertedPriceList($this->promotionPrice->GetConvertedPriceList());
        } else {
            $this->displayPromoPrice = $this->getPriceOfFloat($this->promotionPrice);
        }

        return $this->displayPromoPrice != $this->getDisplayPrice() ? $this->displayPromoPrice : false;
    }

    private function getPriceOfConvertedPriceList($convertedPriceList)
    {
        $data = array();
        $currencyCode = User::getObject()->getCurrencyCode();
        foreach ($convertedPriceList->GetDisplayedMoneys()->GetMoney() as $k => $v) {
            if (empty($currencyCode)) {
                $data = array(
                    'Val' => $v->GetValue(),
                    'Sign' => $v->GetSignAttribute(),
                    'Code' => $v->GetCodeAttribute(),
                );
                break;
            }

            if ($v->GetCodeAttribute() == $currencyCode) {
                $data = array(
                    'Val' => $v->GetValue(),
                    'Sign' => $v->GetSignAttribute(),
                    'Code' => $v->GetCodeAttribute(),
                );
                break;
            }
        }

        return General::getHtmlPrice($data, array('addItemprop' => true));
    }

    private function getPriceOfFloat($price)
    {
        if (!$price) {
            return '';
        }

        $data = array(
            'Val' => $price,
            'Sign' => $this->currencyName,
            'Code' => $this->currencyCode,
        );

        return General::getHtmlPrice($data, array('addItemprop' => true));
    }

    public function getQuantityRanges()
    {
        $quantityRanges = $this->quantityRanges;
        $tmpRanges = array();

        if (!empty($quantityRanges)) {
            foreach ($quantityRanges as $idx => $range) {
                $tmpRange = array();

                if (count($quantityRanges) > 5 && 1 < $idx && $idx < count($quantityRanges) - 2) {
                    if ($idx == 2) {
                        $tmpRange['DisplayRange'] = '...';
                        $tmpRange['Price'] = '...';
                        $tmpRanges[] = $tmpRange;
                    }
                    continue;
                }

                $minQuantity = $range['Min'];
                $tmpRange['MinRange'] = $minQuantity;

                if (!empty($range['Max'])) {
                    $maxQuantity = $range['Max'];

                    if ($minQuantity != $maxQuantity) {
                        $tmpRange['DisplayRange'] = $minQuantity . '&nbsp-&nbsp' . $maxQuantity;
                    } else {
                        $tmpRange['DisplayRange'] = $minQuantity;
                    }
                } else {
                    $tmpRange['DisplayRange'] = '&ge;&nbsp' . $minQuantity;
                }
                $tmpRange['DisplayRange'] .= '&nbsp' . Lang::get('pcs');

                if (!is_float($range['Price'])) {
                    $tmpRange['Price'] = $this->getPriceOfConvertedPriceList($range['Price']);
                } else {
                    $tmpRange['Price'] = $this->getPriceOfFloat($range['Price']);
                }

                $tmpRanges[] = $tmpRange;
            }
        }

        return $tmpRanges;
    }

    public function getSmallImage()
    {
        return $this->getItemImage('small');
    }

    public function getMediumImage()
    {
        return $this->getItemImage('medium');
    }

    public function getLargeImage()
    {
        return $this->getItemImage('large');
    }

    //sizes - small, medium, large
    private function getItemImage($size)
    {
        // Проверим кастомные картинки

        // TODO: реализовать позднюю загрузку картинки
        /*if (! empty($item['image_path'])) {
            return $item['image_path'];
        }*/
        if (empty($this->mainPicture[$size])) {
            return self::DEFAULT_IMAGE;
        }

        return $this->mainPicture[$size];
    }

    private function generateUrl($options = array())
    {
        $data = array();

        $category = isset($options['category']) ? $options['category'] : null;
        if ($category instanceof OtapiCategory && $category->IsSearch()) {
            $data['cid'] = $category->GetId();
        }

        return UrlGenerator::generateItemUrl($this->id, $data);
    }

    private function defineFeaturesValues($values = array())
    {
        foreach ($values as $val) {
            switch ($val->GetNameAttribute()) {
                case 'TotalSales':
                    $this->totalSales = $val->asString();
                    break;
                case 'SalesInLast30Days':
                    $this->salesInLast30Days = $val->asString();
                    break;
                case 'ItemReviewRating':
                    $this->itemReviewRating = $val->asString();
                    break;
            }
        }
    }

    public function setCurrencyName($displayName) {
        $this->currencyName = $displayName;
    }

    public function setCurrencyCode($code) {
        $this->currencyCode = $code;
    }

    public function displayBlock()
    {
        return General::viewFetch('other/product/display-block', array('vars' => array('item' => $this)));
    }

    public function displayList()
    {
        return General::viewFetch('other/product/display-list', array('vars' => array('item' => $this)));
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getConfigurationName()
    {
        return $this->configurationName;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function getFullTotalCost()
    {
        if (is_null($this->fullTotalCost)) {
            return $this->fullTotalCost;
        } else {
            return $this->getPriceOfConvertedPriceList($this->fullTotalCost->GetConvertedPriceList());
        }
    }

    public function getTotalWeight()
    {
        return $this->weight * $this->quantity;
    }

    public function getDisplayTotalWeight()
    {
        return number_format($this->getTotalWeight(), 2, '.', ' ');
    }

    public function getDisplayWeight()
    {
        return number_format($this->weight, 2, '.', ' ');
    }

    /**
     * @return bool
     */
    public function isIncomplete() {
        foreach ($this->features as $feature) {
            if ('Incomplete' === $feature->GetIdAttribute()) {
                return true;
            }
        }

        return false;
    }
}
