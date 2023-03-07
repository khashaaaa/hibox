<?php

class SeoCategoryRepository extends Repository
{
    const CHUNK_SIZE = 100;

    private $persistedAliases = array();

    public function __construct($cms)
    {
        parent::__construct($cms);
        $this->cms->checkTable('site_categories');
    }

    /*
     * @return string|false
     */
    public function getCategoryAlias($cid, $cname = '', $createNew = true)
    {
        // проблема с алиасами 'undefined' на старых платформах
        if ($cname == 'undefined') {
            return false;
        }

        $q = 'SELECT `alias` FROM `site_categories` WHERE `category_id`="' . $this->cms->escape($cid) . '" ORDER BY `id` LIMIT 1';
        $alias = $this->cms->querySingleValue($q);
        if (! $alias && $createNew) {
            $categoryName = strtolower(TextHelper::translitСonverter($cname));
            $categoryName = $categoryName ? $categoryName.'-' : $categoryName;
            $alias =  $categoryName . $cid;
            $this->setCategoryAlias($cid, $alias);
        }
        return $alias;
    }
    
    public function getCategoriesAliases()
    {
        $data = $this->cms->queryMakeArray('SELECT `category_id`, `alias` FROM `site_categories`');
        $result = array();
        foreach ($data as $d) {
            $result[$d['category_id']] = $d['alias'];
        }
        return $result;
    }

    public function persistCategoryAlias($categoryId, $categoryName)
    {
        $alias = strtolower(TextHelper::translitСonverter($categoryName)).'-'.$categoryId;
        $this->setCategoryAlias($categoryId, $alias);

        /*$existedAlias = $this->cms->querySingleValue('
            SELECT `alias`
            FROM `site_categories`
            WHERE `category_id`="'.$this->cms->escape($categoryId).'"');

        if (!$existedAlias) {
            $this->persistedAliases[] = array(
                'alias' => $this->cms->escape($alias),
                'category_id' => $this->cms->escape($categoryId),
            );
            return $alias;
        }

        return $existedAlias;*/
    }

    public function updateCategoryAliases($categories, &$aliases)
    {
        $sqlStart = 'INSERT IGNORE INTO `site_categories` (`alias`, `category_id`) VALUES ';
        $values = array();
        foreach ($categories as $item) {
            if (! array_key_exists($item['id'], $aliases)) {
                $alias = strtolower(TextHelper::translitСonverter($item['name'])) . '-' . $item['id'];
                $values[] = '"' . $this->cms->escape($alias) . '", "' . $this->cms->escape($item['id']) . '"';
                if (count($values) >= self::CHUNK_SIZE) {
                    $sql = $sqlStart . '(' . implode('), (', $values) . ')';
                    $values = array();
                    $res = $this->cms->query($sql);
                    if ($res) {
                        $aliases[$item['id']] = array('category_id' => $item['id'], 'alias' => $alias);
                    }
                }
            }
        }
        if (! empty($values)) {
            $sql = $sqlStart . '(' . implode('), (', $values) . ')';
            $this->cms->query($sql);
        }
        return $categories;
    }

    public function getCategoryAliases($categoriesIds)
    {
        $categoriesIds = ! is_array($categoriesIds) ? array($categoriesIds) : $categoriesIds;
        $categoriesIds = array_map(array($this->cms, 'escape'), $categoriesIds);

        $i = 0;
        $result = array();
        $count = count($categoriesIds);
        while ($i * self::CHUNK_SIZE < $count) {
            $ids = array_slice($categoriesIds, $i * self::CHUNK_SIZE, self::CHUNK_SIZE);
            if (! empty($ids)) {
                $sql = 'SELECT * FROM `site_categories` WHERE `category_id` IN ("' . implode('", "', $ids) . '")';
                $result = array_merge($result, $this->cms->queryMakeArray($sql, array(), 'category_id'));
            }
            $i++;
        }
        return $result;
    }

    public function getVendorAliases($vendorIds)
    {
        $vendorIds = ! is_array($vendorIds) ? array($vendorIds) : $vendorIds;
        $vendorIds = array_map(array($this->cms, 'escape'), $vendorIds);

        $i = 0;
        $result = array();
        $count = count($vendorIds);
        while ($i * self::CHUNK_SIZE < $count) {
            $ids = array_slice($vendorIds, $i * self::CHUNK_SIZE, self::CHUNK_SIZE);
            if (! empty($ids)) {
                $sql = 'SELECT * FROM `site_vendors_images` WHERE `vendor_id` IN ("' . implode('", "', $ids) . '")';
                $result = array_merge($result, $this->cms->queryMakeArray($sql, array(), 'vendor_id'));
            }
            $i++;
        }
        return $result;
    }

    public function getAliasById($cid)
    {        
        $q = 'SELECT `alias` FROM `site_categories` WHERE `category_id` = "' . $cid . '" ORDER BY `id` LIMIT 1';
        return $this->cms->querySingleValue($q);
    }

    public function flushAliases()
    {
        /*$inserts = array();
        foreach ($this->persistedAliases as $alias) {
            $inserts[] =
                "('{$alias['category_id']}','{$alias['alias']}')";
        }
        $this->persistedAliases = array();
        $query = "INSERT INTO site_categories(category_id,alias) VALUES " . implode(",\n", $inserts);
        $this->cms->query($query);*/
    }

    public function getCategorySEO($cid, $language = 'ru')
    {
        $this->cms->checkTable('site_pages_langs_data');
        $langId = $this->cms->_getLangCodeId($language);
        $q = 'SELECT * FROM `site_pages_langs_data` WHERE `p`="' . $cid . '" AND `lang_id`="' . $langId . '"';
        $r = $this->cms->query($q);
        return @mysqli_fetch_assoc($r);
    }

    public function getBrandSEO($id, $language = 'ru')
    {
        $this->cms->checkTable('site_pages_langs_data');
        $langId = $this->cms->_getLangCodeId($language);
        $q = 'SELECT * FROM `site_pages_langs_data` WHERE `p`="' . $id . '" AND `lang_id`="' . $langId . '"';
        $r = $this->cms->query($q);
        return @mysqli_fetch_assoc($r);
    }


    public function getVendorSEO($sellerId, $language = 'ru')
    {
        $this->cms->checkTable('site_pages_langs_data');
        $langId = $this->cms->_getLangCodeId($language);
        $q = 'SELECT * FROM `site_pages_langs_data` WHERE `p`="' . $sellerId . '" AND `lang_id`="' . $langId . '"';
        $r = $this->cms->query($q);
        return @mysqli_fetch_assoc($r);
    }

    public function getLangsCategorySEO()
    {
        $this->cms->checkTable('site_pages_langs_data');
        $this->cms->checkTable('site_langs');
        $q = 'SELECT `s1`.*,`s2`.`lang_code` FROM `site_pages_langs_data` as `s1`  LEFT OUTER JOIN `site_langs` `s2` ON `s1`.`lang_id` = `s2`.`id`WHERE `s1`.`type` = "category"';
        $data = $this->cms->queryMakeArray($q);
        $result = array();
        foreach ($data as $d) {
            if (empty($result[$d['p']])) $result[$d['p']] = array();
            if (! empty($d['lang_code'])) $result[$d['p']][$d['lang_code']] = $d;
        }
        return $result;
    }

    public function deleteCategoryAlias($categoryId = '', $language = '')
    {
        $this->cms->Check();
        $categoryId = $this->cms->escape($categoryId);
        $language = $this->cms->escape($language);

        if (empty($categoryId)) {
            // очистить всю таблицу
            $sql = 'TRUNCATE site_categories';
        } else {
            if (empty($lang)) {
                // очистить алиас категирии для всех языковых версий
                $sql = 'DELETE FROM `site_categories` WHERE category_id = "' . $categoryId . '"';
            } else {
                // очистить алиас категории для выбранного языка
                $sql = 'DELETE FROM `site_categories` WHERE category_id = "' . $categoryId . '" AND lang_id = "' . $language . '"';
            }
        }

        return $this->cms->query($sql, array('site_categories'));
    }

    public function deleteCategoryAliasFrames(array $categoriesFrames, $language = '')
    {
        $this->cms->Check();
        $language = $this->cms->escape($language);

        foreach ($categoriesFrames as $categoriesFrame) {
            if (is_array($categoriesFrame)) {
                $categoriesFrame = '"' . implode('","', $categoriesFrame) . '"';
                if (!empty($categoriesFrame)) {
                    if (empty($lang)) {
                        // очистить алиас категирий для всех языковых версий
                        $sql = 'DELETE FROM `site_categories` WHERE category_id IN (' . $categoriesFrame . ')';
                    } else {
                        // очистить алиас категорий для выбранного языка
                        $sql = 'DELETE FROM `site_categories` WHERE category_id IN (' . $categoriesFrame . ') AND lang_id = "' . $language . '"';
                    }
                    $this->cms->query($sql, array('site_categories'));
                }
            }
        }
    }

    public function setCategoryAlias($cid, $alias, $showError = false, $defineIsset = null)
    {
        $this->cms->checkTable('site_categories');
        if (is_null($defineIsset)) {
            $isset = $this->cms->querySingleValue('SELECT COUNT(*) FROM `site_categories` WHERE `category_id`="' . $this->cms->escape($cid) . '"'); //
        } else {
            $isset = $defineIsset;
        }

        if ($isset) {
        	if ($showError) {
        		$result = $this->cms->query('UPDATE `site_categories` SET `alias`="' . $this->cms->escape($alias) . '" WHERE `category_id`="' . $this->cms->escape($cid) . '"');
        	} else {
            	$result = $this->cms->query('UPDATE IGNORE `site_categories` SET `alias`="' . $this->cms->escape($alias) . '" WHERE `category_id`="' . $this->cms->escape($cid) . '"');
        	}
        } else {
            $result = $this->cms->query('INSERT INTO `site_categories` (`category_id`, `alias`, `lang_id`) VALUES ( "' . $this->cms->escape($cid) . '", "' . $this->cms->escape($alias) . '", "0")');
        }
        return $result;
    }

    public function checkCategoryAlias($alias)
    {
        $this->cms->checkTable('site_categories');

        $isset = $this->cms->querySingleValue('SELECT COUNT(*) FROM `site_categories` WHERE `alias`="' . $this->cms->escape($alias) . '"');
        return $isset;
    }

    public function checkVendorName($url) {
        $this->cms->checkTable('site_vendors_images');
        $isset = $this->cms->querySingleValue('SELECT COUNT(*) FROM `site_vendors_images` 
        WHERE `vendor_id`="'.$this->cms->escape($url).'"');
        return $isset;
    }

    public function checkVendorAlias($alias, $sellerId) {
        $this->cms->checkTable('site_vendors_images');
        $isset = $this->cms->querySingleValue('SELECT COUNT(*) FROM `site_vendors_images` 
        WHERE `alias`="' . $this->cms->escape($alias) . '" AND `vendor_id` != "' . $sellerId . '"');
        return $isset;
    }

    public function deleteCategorySEO($categoryId = '', $language = '')
    {
        $this->cms->Check();
        $categoryId = $this->cms->escape($categoryId);
        $language = $this->cms->escape($language);

        if (empty($categoryId)) {
            // очистить всю таблицу с информацией о СЕО категориях
            $sql = 'DELETE FROM `site_pages_langs_data` WHERE type = "category"';
        } else {
            if (empty($lang)) {
                // очистить СЕО категирии для всех языковых версий
                $sql = 'DELETE FROM `site_pages_langs_data` WHERE p = "' . $categoryId . '" AND type = "category"';
            } else {
                // очистить СЕО категории для выбранного языка
                $sql = 'DELETE FROM `site_pages_langs_data` WHERE p = "' . $categoryId . '" AND lang_id = "' . $language . '" AND type = "category"';
            }
        }

        return $this->cms->query($sql, array('site_pages_langs_data'));
    }

    public function deleteCategorySEOFrames(array $categoriesFrames, $language = '')
    {
        $this->cms->Check();
        $language = $this->cms->escape($language);

        foreach ($categoriesFrames as $categoriesFrame) {
            if (is_array($categoriesFrame)) {
                $categoriesFrame = '"' . implode('","', $categoriesFrame) . '"';
                if (!empty($categoriesFrame)) {
                    if (empty($lang)) {
                        // очистить СЕО категирий для всех языковых версий
                        $sql = 'DELETE FROM `site_pages_langs_data` WHERE p IN (' . $categoriesFrame . ') AND type = "category"';
                    } else {
                        // очистить СЕО категорий для выбранного языка
                        $sql = 'DELETE FROM `site_pages_langs_data` WHERE p IN (' . $categoriesFrame . ') AND lang_id = "' . $language . '" AND type = "category"';
                    }
                    $this->cms->query($sql, array('site_pages_langs_data'));
                }
            }
        }
    }

    public function setCategorySEO($data, $defineIsset = null)
    {
        if (is_null($defineIsset)) {
            $this->cms->checkTable('site_langs');
            if (! empty($data['language'])) {
                $langId = $this->cms->_getLangCodeId($data['language']);
            } else {
                $langId = $this->cms->_getLangCodeId('ru');
                $data['language'] = 'ru';
            }
            $isset = $this->getCategorySEO($data['cid'], $data['language']);
        } else {
            $langId = $data['language'];
            $isset = $defineIsset;
        }

        if (! isset($data['meta_title'])) {
        	$data['meta_title'] = '';
        }
        if (! isset($data['meta_description'])) {
        	$data['meta_description'] = '';
        }
        if (! isset($data['meta_keywords'])) {
        	$data['meta_keywords'] = '';
        }        
        
        if (! empty($isset)) {
            $updateData = '';
            
            if (isset($data['seo_title'])) $updateData .= ', `seo_title` = "' . $this->cms->escape($data['seo_title']) . '"';
            if (isset($data['meta_title'])) $updateData .= ', `pagetitle` = "' . $this->cms->escape($data['meta_title']) . '"';
            if (isset($data['meta_keywords'])) $updateData .= ', `seo_keywords` = "' . $this->cms->escape($data['meta_keywords']) . '"';
            if (isset($data['meta_description'])) $updateData .= ', `seo_description` = "' . $this->cms->escape($data['meta_description']) . '"';
            if (! empty($updateData)) $q = 'UPDATE `site_pages_langs_data` SET `p` = "' . $data['cid'] . '" '.$updateData.' WHERE `p` = "' . $data['cid'] . '" AND `lang_id` = "' . $langId . '"';
        } else {
            $q = 'INSERT INTO `site_pages_langs_data` (`p`, `seo_title`, `pagetitle`, `seo_keywords`, `seo_description`, `type`, `lang_id` ) VALUES ( "' . $data['cid'] . '", "' . $this->cms->escape($data['seo_title']) . '", "' . $this->cms->escape($data['meta_title']) . '", "' . $this->cms->escape($data['meta_keywords']) . '", "' . $this->cms->escape($data['meta_description']) . '", "category", "' . $langId . '")';
        }
        if (! empty($q)) $this->cms->query($q);
    }

    public function setBrandSEO($data, $defineIsset = null)
    {
        if (is_null($defineIsset)) {
            $this->cms->checkTable('site_langs');
            if (! empty($data['seolanguage'])) {
                $langId = $this->cms->_getLangCodeId($data['seolanguage']);
            } else {
                $langId = $this->cms->_getLangCodeId('ru');
                $data['seolanguage'] = 'ru';
            }
            $isset = $this->getBrandSEO($data['id'], $data['seolanguage']);
        } else {
            $langId = $data['seolanguage'];
            $isset = $defineIsset;
        }

        if (! isset($data['meta_title'])) {
            $data['meta_title'] = '';
        }
        if (! isset($data['meta_description'])) {
            $data['meta_description'] = '';
        }
        if (! isset($data['meta_keywords'])) {
            $data['meta_keywords'] = '';
        }

        if (! empty($isset)) {
            $updateData = '';

            if (isset($data['seo_title'])) $updateData .= ', `seo_title` = "' . $this->cms->escape($data['seo_title']) . '"';
            if (isset($data['meta_title'])) $updateData .= ', `pagetitle` = "' . $this->cms->escape($data['meta_title']) . '"';
            if (isset($data['meta_keywords'])) $updateData .= ', `seo_keywords` = "' . $this->cms->escape($data['meta_keywords']) . '"';
            if (isset($data['meta_description'])) $updateData .= ', `seo_description` = "' . $this->cms->escape($data['meta_description']) . '"';
            if (! empty($updateData)) $q = 'UPDATE `site_pages_langs_data` SET `p` = "' . $data['id'] . '" '.$updateData.' WHERE `p` = "' . $data['id'] . '" AND `lang_id` = "' . $langId . '"';
        } else {
            $q = 'INSERT INTO `site_pages_langs_data` (`p`, `seo_title`, `pagetitle`, `seo_keywords`, `seo_description`, `type`, `lang_id` ) VALUES ( "' . $data['id'] . '", "' . $this->cms->escape($data['seo_title']) . '", "' . $this->cms->escape($data['meta_title']) . '", "' . $this->cms->escape($data['meta_keywords']) . '", "' . $this->cms->escape($data['meta_description']) . '", "brand", "' . $langId . '")';
        }
        if (! empty($q)) $this->cms->query($q);
    }

    public function setVendorSEO($data, $defineIsset = null)
    {
        if (is_null($defineIsset)) {
            $this->cms->checkTable('site_langs');
            if (! empty($data['seolanguage'])) {
                $langId = $this->cms->_getLangCodeId($data['seolanguage']);
            } else {
                $langId = $this->cms->_getLangCodeId('ru');
                $data['seolanguage'] = 'ru';
            }
            $isset = $this->getCategorySEO($data['sellerId'], $data['seolanguage']);
        } else {
            $langId = $data['seolanguage'];
            $isset = $defineIsset;
        }

        if (! isset($data['meta_title'])) {
        	$data['meta_title'] = '';
        }
        if (! isset($data['meta_description'])) {
        	$data['meta_description'] = '';
        }
        if (! isset($data['meta_keywords'])) {
        	$data['meta_keywords'] = '';
        }

        if (! empty($isset)) {
            $updateData = '';

            if (isset($data['meta_title'])) $updateData .= ', `pagetitle` = "' . $this->cms->escape($data['meta_title']) . '"';
            if (isset($data['meta_keywords'])) $updateData .= ', `seo_keywords` = "' . $this->cms->escape($data['meta_keywords']) . '"';
            if (isset($data['meta_description'])) $updateData .= ', `seo_description` = "' . $this->cms->escape($data['meta_description']) . '"';
            if (! empty($updateData)) $q = 'UPDATE `site_pages_langs_data` SET `p` = "' . $data['sellerId'] . '" '.$updateData.' WHERE `p` = "' . $data['sellerId'] . '" AND `lang_id` = "' . $langId . '"';
        } else {
            $q = 'INSERT INTO `site_pages_langs_data` (`p`, `pagetitle`, `seo_keywords`, `seo_description`, `type`, `lang_id` ) VALUES ( "' . $data['sellerId'] . '", "' . $this->cms->escape($data['meta_title']) . '", "' . $this->cms->escape($data['meta_keywords']) . '", "' . $this->cms->escape($data['meta_description']) . '", "vendor", "' . $langId . '")';
        }
        if (! empty($q)) $this->cms->query($q);
    }

    public function removeCategoryByCategoryId($categoryId)
    {
        $categoryId = $this->cms->escape($categoryId);
        $this->cms->checkTable('site_categories');
        $this->cms->query('DELETE FROM `site_categories` WHERE `category_id`="' . $categoryId . '"');
    }

    /**
     * Вернет массив [$cid, $needRedirect], если $needRedirect = true,
     * то $cid будет содержать Url для редиректа
     *
     * @param $alias
     * @return array
     */
    public function parseCategoryIdFromAlias($alias)
    {
        if (!General::IsFeatureEnabled('Seo2')) {
            return [$alias, false];
        }

        $alias = rawurldecode($alias);
        $cid = $this->cms->getCategoryIdByAlias($alias);
        // обратная совместимость для старых ссылок - пытаемся найти по категории нужный алиас
        if (empty($cid)) {
            $redirectUrl = UrlGenerator::generateContentUrl('404');

            if (preg_match("/([a-z]+-[0-9]+)$/", $alias, $matches)) {
                $foundAlias = $this->cms->getCategoryAlias($matches[0], '', false);
                if ($foundAlias) {
                    $redirectUrl = UrlGenerator::generateSubcategoryUrl(array('alias' => $foundAlias), true);
                }
            } elseif (preg_match("/([0-9]+)$/", $alias, $matches)) {
                $foundAlias = $this->cms->getCategoryAlias($matches[0], '', false);
                if ($foundAlias) {
                    $redirectUrl = UrlGenerator::generateSubcategoryUrl(array('alias' => $foundAlias), true);
                } else {
                    $redirectUrl = UrlGenerator::generateSearchUrlByParams(array('cid' => $matches[0]), array('isAbsolute' => true));
                }
            } else {
                // выдаем 404 страницу, т.к. не нашлось подходящего алиаса
            }

            return [$redirectUrl, true];
        }
        return [$cid, false];
    }
}
