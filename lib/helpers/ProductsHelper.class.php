<?php

class ProductsHelper
{
    const YAHOO_SEARCH = 'yahoo';
    const YAHOO_ITEM_PREFFIX = 'yjp';
    const DEFAULT_IMAGE = '/i/noimg.png';
    
    public static function getSmallImage($item)
    {
        return self::getItemImage($item, 'small');
    }
    
    public static function getMediumImage($item)
    {
        return self::getItemImage($item, 'medium');
    }
    
    public static function getLargeImage($item)
    {
        return self::getItemImage($item, 'large');
    }
    
    //sizes - small, medium, large
    private static function getItemImage($item, $size)
    {
        // Проверим кастомные картинки
        if (! empty($item['image_path'])) {
            return $item['image_path'];
        }
        if (empty($item['pictures'])) {
            // Возьмём дефолтную картинку
            if (! empty($item['MainPictureUrl'])) {
                return $item['MainPictureUrl'];
            } elseif (! empty($item['MainImageUrl'])) {
                return $item['MainImageUrl'];
            } elseif (! empty($item['PictureURL'])) {
                return $item['PictureURL'];
            } elseif (! empty($item['PictureUrl'])) {
                return $item['PictureUrl'];
            } elseif (! empty($item['ItemImageURL'])) {
                return $item['ItemImageURL'];
            } elseif (! empty($item['url'])) {
                return $item['url'];
            }
            return self::DEFAULT_IMAGE;
        }
        $mainPicture = false;
        foreach ($item['pictures'] as $i => $picture) {
            if ($picture['ismain'] == 'true') {
                $mainPicture = $i;
                break;
            }
        }
        return $mainPicture ? $item['pictures'][$mainPicture][$size] : $item['pictures'][0][$size];
    }
    
    
    public static function isWarehouseProduct($item, $asObject = false)
    {
        if (! $asObject) {
            $providerType = !empty($item['ProviderType']) ? $item['ProviderType'] : false;
            $id = !empty($item['id']) ? $item['id'] : false;
            $itemId = !empty($item['ItemId']) ? $item['ItemId'] : false;
            $itemIdLower = !empty($item['itemid']) ? $item['itemid'] : false;
        } else {
            $providerType = !empty($item->ProviderType) ? $item->ProviderType : '';
            $id = !empty($item->id) ? $item->id : false;
            $itemId = !empty($item->ItemId) ? $item->ItemId : false;
            $itemIdLower = !empty($item->itemid) ? $item->itemid : false;
        }

        if ($providerType) {
            return (string)$providerType == 'Warehouse';
        }

        $whId = $id ? strpos($id, 'wh-') === 0 : false;
        $whItemId = $itemId ? strpos($itemId, 'wh-') === 0 : false;
        $whItemIdLower = $itemIdLower ? strpos($itemIdLower, 'wh-') === 0 : false;

        return $whId || $whItemId || $whItemIdLower;
    }

    public static function isYahooProduct($item, $asObject = false)
    {
        if (! $asObject) {
            $providerType = !empty($item['ProviderType']) ? $item['ProviderType'] : false;
            if (! $providerType && isset($item['ItemTaobaoId'])) {
                if (strpos($item['ItemTaobaoId'], self::YAHOO_ITEM_PREFFIX) !== false) {
                    $providerType = self::YAHOO_SEARCH;
                }
            }
            /** Для последних открытых товаров в ЛК */
            if (! $providerType && isset($item['id'])) {
                if (strpos($item['id'], self::YAHOO_ITEM_PREFFIX) !== false) {
                    $providerType = self::YAHOO_SEARCH;
                }
            }
        } else {
            $providerType = !empty($item->ProviderType) ? $item->ProviderType : '';
            if (! $providerType && isset($item->ItemTaobaoId)) {
                if (strpos($item->ItemTaobaoId, self::YAHOO_ITEM_PREFFIX) !== false) {
                    $providerType = self::YAHOO_SEARCH;
                }
            }
        }

        if ($providerType) {
            if (strpos(strtolower($providerType), self::YAHOO_SEARCH) === false) {
                return false;
            } else {
                return true;
            }
        }

        return false;
    }

    public static function getWarehouseProductUrl($item)
    {
        $itemid = false;

        if (isset($item->itemid)) {
            $itemid = str_replace('wh-', '', $item->itemid);
        } elseif (isset($item->ItemId)) {
            $itemid = str_replace('wh-', '', $item->ItemId);
        } elseif (isset($item['itemid'])) {
            $itemid = str_replace('wh-', '', $item['itemid']);
        } elseif (isset($item['ItemId'])) {
            $itemid = str_replace('wh-', '', $item['ItemId']);
        }

        return $itemid ? UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]" . UrlGenerator::generateItemUrl($itemid) : '5555';
    }

    /**
     * Форматирование html меток для списка Features
    **/
     public static function getHtmlFeatureListForLabel($features = array())
    {
        $html = '';
        if (is_array($features) && count($features)) {
            $list = array();
            foreach ($features as &$feature) {
                // TODO: вернуть проверку && $feature->GetDisplayTypeAttribute() === 'Label'
                if ($feature instanceof OtapiSimplifiedValueWithId) {
                    $feature = $feature->GetIdAttribute();
                }
                if (is_string($feature)) {
                    $list[] = strtolower($feature);
                }
            }

            $html = General::viewFetch('other/item/features', ['vars' => [
                'features' => $list
            ]]);
        }

        return $html;
    }
    
    /**
     * Форматирование списка Features для вывода в css классах
    **/
    public static function formatFeatureListForCss($features = array())
    {
        $css = array();
        if (is_array($features) && count($features)) {
            foreach ($features as $feature) {
                if ($feature instanceof OtapiSimplifiedValueWithId && $feature->GetDisplayTypeAttribute() === 'Label') {
                    $feature = $feature->GetIdAttribute();
                }
                if (is_string($feature)) {
                    $css[] = 'product-feature-' . strtolower($feature);
                }
            }
        }

        return strtolower(implode(' ', $css));
    }

    public static function getImage($item, $size = null)
    {
        // Проверим кастомные картинки
        if (! empty($item['image_path'])) {
            return $item['image_path'];
        }
        if (empty($item['pictures'])) {
            // Возьмём дефолтную картинку
            if (!empty($item['DisplayPictureUrl'])) {
                $imageUrl = $item['DisplayPictureUrl'];
            } elseif (! empty($item['MainPictureUrl'])) {
                $imageUrl = $item['MainPictureUrl'];
            } elseif (! empty($item['MainImageUrl'])) {
                $imageUrl = $item['MainImageUrl'];
            } elseif (! empty($item['DisplayPictureUrl'])) {
                $imageUrl = $item['DisplayPictureUrl'];
            } elseif (! empty($item['PictureURL'])) {
                $imageUrl = $item['PictureURL'];
            } elseif (! empty($item['PictureUrl'])) {
                $imageUrl = $item['PictureUrl'];
            } elseif (! empty($item['ItemImageURL'])) {
                $imageUrl = $item['ItemImageURL'];
            } elseif (! empty($item['url'])) {
                $imageUrl = $item['url'];
            }

            if (empty($imageUrl)) {
                return self::DEFAULT_IMAGE;
            }

            if (! $size) {
                return $imageUrl;
            }
            switch ($size) {
                case 'small':
                    $size = 100;
                    break;
                case 'medium':
                    $size = 310;
                    break;
                case 'large':
                    $size = 600;
                    break;
            }
            $imageUrl = preg_replace('/\?.*/', '', $imageUrl);
            if ((self::isWarehouseProduct($item)) && strpos($imageUrl, 'taobaocdn.com') === false) {
                $suffix = sprintf('_%d_%d', $size, $size);
                if (strpos($imageUrl, 'uploaded/warehouse') !== false) {
                    $image = str_replace('uploaded/warehouse', 'uploaded/warehouse/thumbnail' . $suffix, $imageUrl);
                } else {
                    $image = str_replace('uploaded', 'uploaded/thumbnail' . $suffix, $imageUrl);
                }
            } else if (strpos($imageUrl, 'tbcdn.cn') !== false) {
                $vendorAvailableSizes = array(100, 120, 160);
                if (in_array($size, $vendorAvailableSizes) && preg_match('#avatar\-(\d+)#si', $imageUrl, $m) && !empty($m[1])) {
                    $image = str_replace($m[1], $size, $imageUrl);
                } else {
                    $image = $imageUrl;
                }
            } elseif (self::isYahooProduct($item)) {
                $image = $imageUrl;
            } else {
                $pos = strpos($imageUrl, '.jpg');
                if ($pos != FALSE) {
                    $image = substr($imageUrl, 0, $pos);
                    $image = $image . sprintf('.jpg_%dx%d.jpg', $size, $size);
                } else {
                    $image = $imageUrl;
                }
            }
            return $image;
        }
        switch ($size) {
           case 100:
              $size = 'small';
              break;
           case 310:
              $size = 'medium';
              break;
           case 600:
              $size = 'large';
              break;
        }
        $mainPicture = false;
        foreach ($item['pictures'] as $i => $picture) {
            if ($picture['ismain'] == 'true') {
                $mainPicture = $i;
                break;
            }
        }
        if (! $size) {
            return $mainPicture ? $item['pictures'][$mainPicture]['url'] : $item['pictures'][0]['url'];
        }
        return $mainPicture ? $item['pictures'][$mainPicture][$size] : $item['pictures'][0][$size];
    }

    public static function getPristroyImage(array $item, $size = null, $uploadedImagePriority = true)
    {
        if (empty($item['images'])) {
            return false;
        }

        $defaultImage = !empty($item['images'][0]) ? $item['images'][0] : '';
        $uploadedImage = !empty($item['images'][1]) ? $item['images'][1] : '';
        if ($uploadedImagePriority) {
            $imageUrl = self::imageUrlExists($uploadedImage) ? $uploadedImage : $defaultImage;
        } else {
            $imageUrl = $defaultImage;
        }

        if (! $size) {
            return $imageUrl;
        }

        return self::getPristroyImageSize($imageUrl, $size);
    }

    public static function getPristroyImageSize($imageUrl, $size)
    {
        if (strpos($imageUrl, 'taobaocdn.com') === false) {
            // товар с пристроя, загруженный вручную с компа
            $suffix = sprintf('_%d_%d/', $size, $size);
            $image = str_replace('uploaded/pristroy/', 'uploaded/pristroy/thumbnail' . $suffix, $imageUrl);
        } else {
            $image = $imageUrl . sprintf('_%dx%d.jpg', $size, $size);
        }

        return $image;
    }

    public static function getPrice($item, $params = array())
    {
        return General::getCurrencyPrice($item, $params);
    }

    public static function getPromoPrice($item, $params = array())
    {
        return General::getCurrencyPromoPrice($item, $params);
    }

    public static function getPromoPriceDiscount($item)
    {
        $percent = General::getPriceOfConvertedPriceList($item['PromotionPricePercent']);
        return empty($percent) ? '' : $percent['Val'];
    }
    
    public static function imageUrlExists($url) { 
        $hdrs = @get_headers($url); 
        return is_array($hdrs) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/',$hdrs[0]) : false; 
    }

    /**
     * Валидация html строки с описанием
     * товара, путем закрытия не открытых
     * тегов
     *
     * @param string $htmlDescription html с описанием товара
     * @return string Валидный html с описанием товара
     */
    public static function prepareDescription($htmlDescription)
    {
        $htmlDescription = str_replace('&amp;', '&', $htmlDescription);
        return TextHelper::closeTags($htmlDescription);
    }

    public static function getGroupDisplayName($item)
    {
        if (!empty($item['GroupDisplayName'])) {
            return $item['GroupDisplayName'];
        } elseif($item['AdditionalPriceInfoType'] == 'InternalDeliveryPerVendor') {
            return Lang::get('vendor_is', array('vendor' => $item['VendorName']));
        }

        return Lang::get('additional_fee') . ':';
    }
}
