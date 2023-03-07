<?php

class SubCategoryNew extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'subcategorylistnew'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/main/';

    protected function setVars()
    {
        $cid = RequestWrapper::getRequestValueSafe('cid');
        //Получаем флаг AvailableItemRatingListContentTypes
        $batch = $this->otapilib->BatchSearchItemsFrame(
            session_id(),
            $this->getBatchXML($cid),
            0,//Заглушка на скорость вызова
            1,//Заглушка на скорость вызова
            'Category,RootPath,SubCategories'
        );
        $this->defineGlobalRootPath($batch);
        // определяем картинку категории для старого шаблона
        if (! empty($batch['Category']['IconImageUrl'])) {
            $GLOBALS['CategoryImage'][$batch['Category']['Id']] = $batch['Category']['IconImageUrl'];
        }

        // в зависимости от наличия подборки отображаем или виртуальную категорию или категорию с подборкой (селектор)
        if ((! empty($batch['Category']['AvailableItemRatingListContentTypes']))
        && (! empty($batch['Category']['AvailableItemRatingListContentTypes']['ContentType']))
        && ($batch['Category']['AvailableItemRatingListContentTypes']['ContentType'] == 'Item')) {
            $this->showItems($batch);            
        } else {
            $this->showCategories($batch);            
        }
    }

    private function showItems($batch)
    {
        $cid = RequestWrapper::getRequestValueSafe('cid');
        $from = intval($this->request->get('from'));
        if (RequestWrapper::post('per_page', false)) {
            $from = 0;
        }
        $perpage = $this->getPerPageByItems();
        
        $this->_template = 'itemlistnew';
        $foundItems = $this->otapilib->SearchRatingListItems(
            $this->getRatingListXML($cid),
            $from,
            $perpage
        );

        $baseUrl = new UrlWrapper();
        $baseUrl->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
        $baseUrl->DeleteKey('from');
        $GLOBALS['categoryInfo'] = end($GLOBALS['rootpath']);

        $subCategories = isset($batch['SubCategories']) ? $batch['SubCategories'] : array();
        if($this->cms->IsFeatureEnabled('Seo2') && is_array($subCategories)){
            try {
                $SeoCatsRepository = new SeoCategoryRepository(new CMS());
                foreach($subCategories as &$c){
                    $c['alias'] = $SeoCatsRepository->getCategoryAlias(@$c['Id'], @$c['Name']);
                }
            } catch (DBException $e) {
                Session::setError($e->getMessage(), 'DBError');
            }
        }

        // добавляем картинки для товаров, выбраннные админом
        $setsRepository = new SetsRepository($this->cms);
        $foundItems['content'] = $setsRepository->addCutomImageForItems($foundItems['content'], Session::getActiveLang());
        $foundItems['content'] = $this->prepareQuantityRanges($foundItems['content']);

        $this->tpl->assign('categoryId', $cid);
        //Заглушки для шаблона itemlistnew
        $this->tpl->assign('SearchTypes', array());
        $this->tpl->assign('allFeatures', array());
        $this->tpl->assign('CurrentSearchType', false);
        $this->tpl->assign('IsProvider', true); //Чтобы вывести сосотояния - показаны от "бла бла бла" до "бла бла бла"
        $this->tpl->assign('SearchProp', '');
        $this->tpl->assign('checkMultiSearch', false);
        $this->tpl->assign('availableSearchInner', false);
        //Необходимые параметры для шаблона itemlistnew
        $this->tpl->assign('baseUrl', $baseUrl);
        $this->tpl->assign('perpage', $perpage);
        $this->tpl->assign('subCategories', $subCategories);
        $this->tpl->assign('itemlist', $foundItems['content']);
        $this->tpl->assign('totalcount', $foundItems['totalcount']);
        $this->tpl->assign('count', $foundItems['totalcount']);
        $this->tpl->assign('maximumPageCount', ceil($foundItems['totalcount'] / $perpage)); //Кол-во страниц для пагинации
        $this->tpl->assign('from', $from);
    }

    private function defineGlobalRootPath($batch)
    {
        $GLOBALS['rootpath'] = isset($batch['RootPath']) && is_array($batch['RootPath']) ? array_reverse($batch['RootPath']) : array();
    }

    private function getPerPageByItems()
    {
        $cookieKey = 'per_page';
        $perpage = Cookie::get($cookieKey, General::getNumConfigValue('default_perpage', 20));

        if ($this->request->get('per_page')) {
            Cookie::set($cookieKey, $this->request->get('per_page'));
            $perpage = $this->request->get('per_page');
        }

        return $perpage;
    }

    private function showCategories($batch)
    {
        //Выводим просто категории
        try {
            $cid = RequestWrapper::getRequestValueSafe('cid');
            $this->tpl->assign('cid', $cid);

            // 3 столбец - то что внутри текущей категории
            $subcats = isset($batch['SubCategories']) ? $batch['SubCategories'] : array();
            if(in_array('Seo2', General::$enabledFeatures)){
                $SeoCatsRepository = new SeoCategoryRepository(new CMS());
                if (is_array($subcats))
                    foreach($subcats as &$c){
                        $c['alias'] = $SeoCatsRepository->getCategoryAlias($c['Id'], $c['Name']);
                    }
            }
            $this->tpl->assign('subcats', $subcats);
            if (count($subcats)>0) $GLOBALS['no_search_props'] = true;


            // 1 столбец - самый первый уровень каталога = корень
            $lang = Session::getActiveLang() ? Session::getActiveLang() : 'en';
            $cacheKey = 'GetRootCategoryInfoList:' . $lang;

            if ($this->fileMysqlMemoryCache->Exists($cacheKey)) {
                $rootcats = $this->fileMysqlMemoryCache->GetCacheEl($cacheKey);
            } else {
                $this->otapilib->setResultInXMLOn();
                $rootcats = $this->otapilib->GetRootCategoryInfoList();
                $this->otapilib->setResultInXMLOff();

                if (empty($rootcats)) {
                    show_error($this->otapilib->error_message);
                } else {
                    $rootcats = $rootcats->asXML();
                    $this->fileMysqlMemoryCache->AddCacheEl($cacheKey, 24 * 60 * 60, $rootcats);
                }
            }
            $rootcats = $this->otapilib->GetRootCategoryInfoList($rootcats);

            if(in_array('Seo2', General::$enabledFeatures)) {
                if(is_array($rootcats))
                foreach($rootcats as &$c){
                    $c['alias'] = $SeoCatsRepository->getCategoryAlias($c['Id'], $c['Name']);
                }
            }
            $this->tpl->assign('rootcats', $rootcats);


            if (isset($GLOBALS['rootpath'])) {
                $catpath = array_reverse($GLOBALS['rootpath']);
                $catpath = array_slice($catpath, 1, 1);
                $ids_path = array();
                if (isset($catpath[0])) {
                    // 2 столбец - категории рядом с текущей
                    $subcats_prev = $this->otapilib->GetCategorySubcategoryInfoList($catpath[0]['id']);
                    if(in_array('Seo2', General::$enabledFeatures)){
                        if(is_array($subcats_prev))
                        foreach($subcats_prev as &$c){
                            $c['alias'] = $SeoCatsRepository->getCategoryAlias($c['Id'], $c['Name']);
                        }
                    }
                    $this->tpl->assign('subcats_prev', $subcats_prev);
                        //
                    $catpath = array_reverse($GLOBALS['rootpath']);
                    $catpath = array_slice($catpath, 0, 1);

                    $ids_path = is_array(@$GLOBALS['rootpath']) ? $GLOBALS['rootpath'] : array();
                    array_walk($ids_path, array(&$this, 'perparePath'));

                    $this->tpl->assign('cid', $catpath[0]['id']);
                }
            }
            $this->tpl->assign('ids_path', $ids_path); // id категорий которые необходимо отметить как active
        } catch (DBException $e) {
            Session::setError($e->getMessage(), 'DBError');
        }
    }
    
    private function perparePath(&$item, $key)
    {
        $item = $item['id'];
    }

    private function getBatchXML($cid)
    {
        $xmlParams = new SimpleXMLElement('<SearchItemsParameters></SearchItemsParameters>');
        $xmlParams->addChild('CategoryId', $cid);
        return $xmlParams->asXML();
    }

    private function getRatingListXML($cid)
    {
        $xmlParams = new SimpleXMLElement('<RatingListItemSearchParameters></RatingListItemSearchParameters>');
        $xmlParams->addChild('CategoryId', $cid);
        $xmlParams->addChild('ItemRatingType', 'Category');
        return $xmlParams->asXML();
    }

    private function prepareQuantityRanges($items)
    {
        if (!empty($items)) {
            foreach ($items as &$item) {
                if (! empty($item['QuantityRanges'])) {
                    $quantityRanges = $item['QuantityRanges'];
                    $tmpRanges = array();

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

                        $minQuantity = $range['MinQuantity'];
                        $tmpRange['MinRange'] = $minQuantity;

                        if (isset($quantityRanges[$idx + 1]) && !empty($quantityRanges[$idx + 1])) {
                            $maxQuantity = $quantityRanges[$idx + 1]['MinQuantity'] - 1;

                            if ($minQuantity != $maxQuantity) {
                                $tmpRange['DisplayRange'] = $minQuantity . ' - ' . $maxQuantity;
                            } else {
                                $tmpRange['DisplayRange'] = $minQuantity;
                            }
                        } else {
                            $tmpRange['DisplayRange'] = '&ge; ' . $minQuantity;
                        }
                        $tmpRange['DisplayRange'] .= ' ' . Lang::get('pcs');

                        $tmpRange['Price'] = TextHelper::formatPrice($range['Price']['ConvertedPriceWithoutSign']) . ' ' . $range['Price']['CurrencySign'];

                        $tmpRanges[] = $tmpRange;
                    }

                    $item['QuantityRanges'] = $tmpRanges;
                }
            }
        }

        return $items;
    }
}
