<?php

class ContentRepository extends Repository
{

    public function checkIsServicePage($alias)
    {
        $this->cms->checkTable('pages');
        $this->checkSingleServicePage($alias, Session::getActiveLang());
    }

    public function checkServicePagesExists($language)
    {
        $this->cms->checkTable('pages');
        $this->createDefaultPages(false, $language);
    }

    // страницы контента автоматически создаются только при инсталляции, а служебные автоматически создаются всегда
    public function GetPages($createNonServiceContentPages = false, $language = null)
    {
        $issetPagesWithLangCode = $this->cms->querySingleValue('
            SELECT count(`p`.`id`)
            FROM `pages` `p`
            LEFT JOIN `site_pages_langs` `pl`
                ON `p`.`id`=`pl`.`page_id`
            LEFT JOIN `site_langs` `l`
                ON `pl`.`lang_id` = `l`.`id`
            WHERE `l`.`lang_code` IS NOT NULL
            ORDER BY `p`.`id` ASC
        ');

        $r = $this->cms->query('
            SELECT DISTINCT `p`.*, `l`.`lang_name`, `l`.`lang_code`
            FROM `pages` `p`
            LEFT JOIN `site_pages_langs` `pl`
                ON `p`.`id`=`pl`.`page_id`
            LEFT JOIN `site_langs` `l`
                ON `pl`.`lang_id` = `l`.`id`
            ORDER BY `p`.`id` ASC
        ');
        $pages = array();
        if ($issetPagesWithLangCode && $r && @mysqli_num_rows($r)) {
            while ($row = mysqli_fetch_assoc($r)) {
                $pages[] = $row;
            }
        } else {
            $this->cms->checkTable('pages');
            $this->cms->checkTable('site_langs');
            $this->cms->checkTable('site_pages_langs');
            $this->cms->checkLanguage('ru', 'Russian (Русский)');
            $this->cms->checkLanguage('en', 'English (English)');

            $this->createDefaultPages($createNonServiceContentPages, $language);

            $pages = -1;
        }
        return $pages;
    }

    public function createDefaultPages($createNonServiceContentPages = false, $language = null)
    {
        // Получаем страницы из БД
        $pages = array();

        $r = $this->cms->query('
            SELECT *
            FROM `pages` `p`
            LEFT JOIN `site_pages_langs` `pl`
                ON `p`.`id`=`pl`.`page_id`
            LEFT JOIN `site_langs` `l`
                ON `pl`.`lang_id` = `l`.`id`
            WHERE `l`.`lang_code` IS NOT NULL
            ORDER BY `p`.`id` ASC
        ');

        while ($row = mysqli_fetch_assoc($r)) {
            $pages[$row['alias']][$row['lang_code']] = $row;
        }


        // Отсутствующих в БД загружаем из xml
        $xml = simplexml_load_file(CFG_APP_ROOT . '/config/defaultpages.xml');

        foreach ($xml->alias as $page) {
            $b = array();

            foreach ($page->data as $data) {
                foreach ($data->attributes() as $k => $v) {
                    $b[$v->__toString()] = $data->__toString();
                }
            }

            if (empty($b['is_service']) && !$createNonServiceContentPages) {
                continue;
            }

            $lang = $b['lang'];
            if (
                $language &&
                !in_array($language, array('ru', 'en')) &&
                !empty($b['is_service']) &&
                $b['lang'] == 'en'
            ) {
                // для языка агента создаем сервисные страницы из en варианта
                $lang = $language;
            }

            if (!isset($pages[$b['alias']][$lang])) {
                $pid = $this->CreatePage(array(
                    'lang' => $lang,
                    'alias' => $b['alias'],
                    'title' => $b['title'],
                    'is_index' => isset($b['is_index']) ? $b['is_index'] : 0,
                    'is_service' => isset($b['is_service']) ? $b['is_service'] : 0
                ));

                if ($pid && isset($b['text'])) {
                    $pInfo = $this->GetFullPageById($pid);
                    $this->UpdateBlockByID($pInfo['block_id'], '<p>' . $b['text'] . '</p>');
                }
            }
        }
    }

    public function CreatePage($data)
    {
        $langid = $this->cms->_getLangCodeId($data['lang']);
        if (!$langid) {
            return false;
        }
        $this->cms->checkTable('site_pages_langs');
        $this->cms->checkTable('site_pages_langs_data');

        $this->cms->query('INSERT INTO `pages` (`alias`, `title`, `is_index`, `is_service`) VALUES ("' . $this->cms->escape($data['alias']) . '", "' . $this->cms->escape($data['title']) . '", "' . $this->cms->escape($data['is_index']) . '", "' . $this->cms->escape($data['is_service']) . '")');
        $pid = $this->cms->insertedId();
        $sql = 'INSERT INTO `site_pages_langs` (`lang_id`, `page_id` ) VALUES ( "' . $langid . '", "' . $pid . '" )';
        $this->cms->query($sql);

        $pagetitle = isset($data['pagetitle']) ? $this->cms->escape($data['pagetitle']) : '';
        $seo_keywords = isset($data['seo_keywords']) ? $this->cms->escape($data['seo_keywords']) : '';
        $seo_description = isset($data['seo_description']) ? $this->cms->escape($data['seo_description']) : '';
        $sql = 'INSERT INTO `site_pages_langs_data` (`lang_id`, `p`, `pagetitle`, `seo_keywords`, `seo_description`, `type`) VALUES ("' . $langid . '", "' . $this->cms->escape($data['alias']) . '", "' . $pagetitle . '", "' . $seo_keywords . '", "' . $seo_description . '", "content")';
        $this->cms->query($sql);
        return $pid;
    }

    public function GetFullPageById($id)
    {
        $r = $this->cms->queryMakeArray('SELECT * FROM `pages` WHERE `id` = "' . (int)$id . '"');
        $page = false;
        if ($r) {
            $page = $r[0];
        }
        if ($page) {
            $block = $this->GetBlocksByPageID($page['id']);
            if ($block === -1) $block = $this->GetBlocksByPageID($page['id']);
            $page['text'] = $block[0]['text'];
            $page['text'] = str_replace(array("\'", '\"', '\\\\'), array("'", '"', '\\'), $page['text']);
            $page['block_id'] = $block[0]['id'];
        }
        return $page;
    }

    public function UpdateBlockByID($id, $text)
    {
        $this->cms->query('UPDATE `blocks` SET `text` = "' . $this->cms->escape($text) . '" WHERE `id` = "' . (int)$id . '"');
    }

    public function GetBlocksByPageID($id)
    {
        $block = $this->cms->queryMakeArray('SELECT * FROM `blocks` WHERE `page_id` = "' . (int)$id . '"');
        if ($block) {
            foreach ($block as &$tmp) {
                $tmp['text'] = str_replace(array("\'", '\"', '\\\\'), array("'", '"', '\\'), $tmp['text']);
            }
        } else {
            $this->cms->checkTable('blocks');
            $this->cms->query("INSERT INTO `blocks` (`page_id`, `text` ) VALUES ('" . (int)$id . "', '');");
            $block = -1;
        }
        return $block;
    }

    public function GetPageByAlias($alias, $lang = '')
    {
        $alias = rawurldecode($alias);
        $lang = ! empty($lang) ? $lang : Session::getActiveLang();
        $this->cms->checkTable('pages');
        $this->cms->checkTable('site_pages_langs');
        $r = $this->cms->query(
            '
            SELECT `p`.*, `l`.`id` as `lang_id` FROM `pages` `p`
            LEFT JOIN `site_pages_langs` `pl`
                ON `p`.`id`=`pl`.`page_id`
            LEFT JOIN `site_langs` `l`
                ON `pl`.`lang_id` = `l`.`id`
            WHERE `p`.`alias` = "' . $this->cms->escape($alias) . '" AND (`l`.`lang_code`="' . $this->cms->escape($lang) . '"  OR `l`.`lang_code` IS NULL)
            ORDER BY `l`.`lang_code` DESC
            '
        );
        $page = false;
        if ($r) if ($row = mysqli_fetch_assoc($r)) {
            $page = $row;
            $r = $this->cms->query("
                SELECT * FROM `site_pages_langs_data`
                WHERE `p`='" . $this->cms->escape($page['alias']) . "'
                AND `lang_id`='" . $page['lang_id'] . "'
                ");
            if ($r && mysqli_num_rows($r)) {
                $row = mysqli_fetch_assoc($r);
                $page['pagetitle'] = $row['pagetitle'];
                $page['seo_keywords'] = $row['seo_keywords'];
                $page['seo_description'] = $row['seo_description'];
            }
        }
        if ($page) {
            $block = $this->GetBlocksByPageID($page['id']);
            $page['text'] = $block[0]['text'];
            $page['text'] = str_replace(array("\'", '\"', '\\\\'), array("'", '"', '\\'), $page['text']);
            $page['block_id'] = $block[0]['id'];
        }
        return $page;
    }

    public function DeletePageByID($id) {
        $this->cms->checkTable('site_pages_parents');
        $id = (int)$id;
        $this->cms->query("DELETE FROM `site_pages_parents` WHERE `parent_id` = '" . $id . "'");
        $this->cms->query("DELETE FROM `site_pages_parents` WHERE `page_id` = '" . $id . "'");
        $alias = $this->cms->querySingleValue('SELECT `alias` FROM `pages` WHERE `id` = "' . $id . '"');
        $this->cms->checkTable('site_pages_langs_data');
        $this->cms->query('DELETE FROM `site_pages_langs_data` WHERE `p` = "' . $this->cms->escape($alias) . '"');
        $this->cms->checkTable('site_pages_langs');
        $this->cms->query('DELETE FROM `site_pages_langs` WHERE `page_id` = "' . $id . '"');
        $this->cms->query('DELETE FROM `pages` WHERE `id` = "' . $id . '"');
    }



    public function GetPageByID($id) {
        $r = $this->cms->query(
            '
            SELECT `p`.*, `l`.`lang_code`, `l`.`id` as `lang_id` FROM `pages` `p`
            LEFT JOIN `site_pages_langs` `pl`
                ON `p`.`id`=`pl`.`page_id`
            LEFT JOIN `site_langs` `l`
                ON `pl`.`lang_id` = `l`.`id`
            WHERE `p`.`id` = "' . (int)$id . '"
            '
        );
        $page = false;
        if ($r) if ($row = mysqli_fetch_assoc($r)) {
            $page = $row;
            $page['title'] = str_replace(array("\'", '\"', '\\\\'), array("'", '"', '\\'), $page['title']);
            $r = $this->cms->query("
                SELECT * FROM `site_pages_langs_data`
                WHERE `p`='" . $this->cms->escape($page['alias']) . "'
                AND `lang_id`='" . $page['lang_id'] . "'
                ");
            if ($r && mysqli_num_rows($r)) {
                $row = mysqli_fetch_assoc($r);
                $page['pagetitle'] = $row['pagetitle'];
                $page['seo_keywords'] = $row['seo_keywords'];
                $page['seo_description'] = $row['seo_description'];                
            }
        }
        return $page;
    }

    private function checkSingleServicePage($page, $language) {
        $result = $this->cms->query('
            SELECT `p`.*, `l`.`id` as `lang_id` FROM `pages` `p`
            LEFT JOIN `site_pages_langs` `pl`
                ON `p`.`id`=`pl`.`page_id`
            LEFT JOIN `site_langs` `l`
                ON `pl`.`lang_id` = `l`.`id`
            WHERE `p`.`alias` = "' . $this->cms->escape($page) . '" AND `l`.`lang_code`="' . $language . '"
            ORDER BY `l`.`lang_code` DESC
        ');
        $result = $result ? mysqli_num_rows($result) : 0;
        if($result == 0){
            $this->createDefaultPages(false, $language);
        }
    }

    public function getPageParentId($pageId)
    {
        $result = $this->cms->querySingleValue('SELECT `parent_id` FROM `site_pages_parents` WHERE `page_id` = "' . $pageId . '"  ORDER BY `menu_order`', array('site_pages_parents'));
        if ($result) {
            return $result;
        }
        return false;
    }

    public function getChildrenPages($parentId)
    {
        $result = $this->cms->query('SELECT * FROM `site_pages_parents` WHERE `parent_id`="' . $parentId . '" ORDER BY `page_id`', ['site_pages_parents']);
        $children = [];
        if($result && mysqli_num_rows($result)){
            while($row = mysqli_fetch_assoc($result)){
                $children[] = $this->GetPageByID($row['page_id']);
            }
        }

        return array_filter($children);
    }

    public function hasContentPage($alias)
    {
        return (bool)$this->GetPageByAlias($alias);
    }
}
