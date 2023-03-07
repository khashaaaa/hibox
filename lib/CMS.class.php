<?php

/**
 * CMS
 */

class CMS
{
    private static $link;

    /**
     * @var Array
     */
    private static $registry = array();

    /**
     * @param $sql
     * @param array $checkTablesExist
     * @return resource
     * @throws DBException
     */
    public function query($sql, $checkTablesExist = array())
    {
        $this->Check();
        if (mysqli_connect_error()) {
            throw new DBException('There is no DB connection.');
        }
        foreach ($checkTablesExist as $table) {
            $this->checkTable($table);
        }

        /* инициализируем таймер */
        $debugger = new Debugger();
        $debugger->start(Debugger::LOG_MYSQL_TYPE, [
            'startText' => $sql,
        ]);

        $query = mysqli_query(self::$link, $sql);
        if (! $query) {
            throw new DBException('Error: ' . mysqli_error(self::$link) . '. SQL: ' . $sql);
        }

        $debugger->end('');

        return $query;
    }

    /**
     * @param $sql
     * @param array $checkTablesExist
     * @return resource
     */
    public function querySingleValue($sql, $checkTablesExist = array()){
        $query = $this->query($sql, $checkTablesExist);

        // В случае оператора DELETE получаем объект bool
        if (is_bool($query)) {
            return array();
        }
        
        $value = false;
        if (mysqli_num_rows($query)) {
            $value = mysqli_fetch_array($query);
            if (is_array($value)) {
                $value = $value[0];
            }
        }

        return $value;
    }

    /**
     * @param $sql
     * @param array $checkTablesExist
     * @return array
     */

    public function queryMakeArray($sql, $checkTablesExist = array(), $idField = null, $options = array())
    {
        $query = $this->query($sql, $checkTablesExist);
        $result = array();

        if (is_bool($query)){
            return $result;
        }

        while ($row = mysqli_fetch_assoc($query)) {
            if ($idField && isset($row[$idField])) {
                $result[$row[$idField]] = $row;
            } else {
                $result[] = $row;
            }
        }
        
        if (isset($options['free_result']) && $options['free_result'] == true) {
        	mysqli_free_result($query);
        } 

        return $result;
    }

    /**
     * @param $value
     * @return string
     */
    public function escape($value)
    {
        return mysqli_real_escape_string(self::$link, $value);
    }

    /**
     * @return int
     */
    public function insertedId()
    {
        return mysqli_insert_id(self::$link);
    }

    public function getFoundRows()
    {
        $res = @mysqli_fetch_row(@mysqli_query(self::$link, 'SELECT FOUND_ROWS()'));
        return isset($res[0]) ? (int)$res[0] : 0;
    }
    
    public function checkTable($tableName)
    {
        //заполняем реестр таблиц если пуст
        if (empty(self::$registry['show_tables_isset'])) {
            $result = $this->query("SHOW TABLES");
            while ($row = mysqli_fetch_assoc($result)) {
                self::$registry['show_tables_isset'][array_shift($row)] = true; 
            }
        }
        
        // если таблица есть в реестре
        if (isset(self::$registry['show_tables_isset'][$tableName])) {
            return true;
        }
    
        //создаем если таблицы ее нет
        $tableNameSafe = $this->escape($tableName);
        $f = dirname(dirname(__FILE__)) . '/admin/sql/' . $tableNameSafe . '.sql';
        if(file_exists($f)){
            $this->query(file_get_contents($f));
        }
        else{
            throw new DBException('Table schema file not found', DBException::CANNOT_CREATE_TABLE);
        }
    
        self::$registry['show_tables_isset'][$tableName] = true;
        return true;
    }
    

    public function tableExists($tableName){
        $tableNameSafe = $this->escape($tableName);
        $result = $this->query("SHOW TABLES LIKE '$tableNameSafe'");

        return mysqli_num_rows($result);
    }

    static public function IsFeatureEnabled($feature)
    {
        return General::IsFeatureEnabled($feature);
    }

    //
    public function sendEmail($email, $subject, $body)
    {
        General::mail_utf8(
            $email,
            General::getConfigValue('notification_send_from_name'),
            General::getConfigValue('notification_send_from'),
            $subject,
            $body
        );
    }

    public function getBlogPostIdByAlias($alias) {
        $q = 'SELECT `p`.`id` FROM `digest` `p`
        LEFT JOIN `site_digest_langs` `pl`
        ON `p`.`id`=`pl`.`post_id`
        LEFT JOIN `site_langs` `l`
        ON `pl`.`lang_id` = `l`.`id`
        WHERE `p`.`alias` = "' . $this->escape($alias) . '" AND (`l`.`lang_code`="' . $_SESSION['active_lang'] . '"  OR `l`.`lang_code` IS NULL)
        ORDER BY `l`.`lang_code` DESC;';

        $r = $this->querySingleValue($q);
        return $r ? $r : '';
    }
    //Устарело
    public function GetPageByAlias($alias)
    {
        $this->checkTable('site_pages_langs');

        $_SESSION['active_lang'];
        $r = mysqli_query(self::$link,
            '
            SELECT `p`.*, `l`.`id` as `lang_id` FROM `pages` `p`
            LEFT JOIN `site_pages_langs` `pl`
                ON `p`.`id`=`pl`.`page_id`
            LEFT JOIN `site_langs` `l`
                ON `pl`.`lang_id` = `l`.`id`
            WHERE `p`.`alias` = "' . $this->escape($alias) . '" AND (`l`.`lang_code`="' . $_SESSION['active_lang'] . '"  OR `l`.`lang_code` IS NULL)
            ORDER BY `l`.`lang_code` DESC
            '
        );
        $page = false;
        if ($r) if ($row = mysqli_fetch_assoc($r)) {
            $page = $row;
            $r = mysqli_query(self::$link, "SELECT * FROM `site_pages_langs_data`
                WHERE `p`='" . $this->escape($page['alias']) . "'
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
            if ($block === -1) {
                $block = $this->GetBlocksByPageID($page['id']);
            }
            $page['text'] = $block[0]['text'];
            $page['text'] = str_replace(array("\'", '\"', '\\\\'), array("'", '"', '\\'), $page['text']);
            // TODO
            $page['block_id'] = $block[0]['id'];
        }
        return $page;
    }

    public static function GetQuittanceMethod($currency)
    {
        if ($_SESSION['active_lang'] !== 'ru') return array();
        if (!self::IsFeatureEnabled('SberbankInvoice')) return array();
        if ($currency['Code'] != 'RUB') return array();
        if (!self::CheckStatic()) return array();
        if (!General::getConfigValue('ot_sberbank_pay_active', 1)) return array();
        $checkFields = array(
            'name_of_payee',
            'INN_of_payee',
            'account_number_of_payee',
            'bank_name_of_payee',
            'bank_identification_code',
            'correspondent_bank_account',
            'description_of_payment',
        );
        $data = self::getSiteConfigStatic();
        if (!$data[0] || CMS::QuittanceDataHasErrors($data[1], $checkFields))
            return array();
        return array(
            'Id' => 'sberbank',
            'id' => 'sberbank',
            'Name' => Lang::get('quittance'),
            'name' => Lang::get('quittance'),
            'Description' => Lang::get('quittance_desc'),
            'description' => Lang::get('quittance_desc'),
            'PaymSortCode' => 'quittance',
            'paymsortcode' => 'quittance',
            'PaymSortText' => Lang::get('quittance'),
            'paymsorttext' => Lang::get('quittance'),
            'ImageURL' => 'sberbank.png',
            'imageurl' => 'sberbank.png',
            'AbsoluteImageUrl' => null,
            'absoluteimageurl' => null,
            'PaymentSystem' => 'quittance',
            'paymentsystem' => 'quittance',
            'CustomField' => 'None',
            'customfield' => 'None',
        );
    }

    public static function QuittanceDataHasErrors($data, $fields)
    {
        $arResult = array();
        foreach ($fields as $fieldName) {
            if (isset($data[$fieldName])) $data[$fieldName] = trim($data[$fieldName]);
            if (!isset($data[$fieldName]) || empty($data[$fieldName])) {
                $arResult[$fieldName] = '';
                continue;
            }
            if ($fieldName == 'INN_of_payee') {
                if (!is_numeric($data[$fieldName]) || strlen($data[$fieldName]) < 10 || strlen($data[$fieldName]) > 12)
                    $arResult[$fieldName] = $data[$fieldName];
            } elseif ($fieldName == 'account_number_of_payee' || $fieldName == 'correspondent_bank_account') {
                if (!is_numeric($data[$fieldName]) || strlen($data[$fieldName]) != 20)
                    $arResult[$fieldName] = $data[$fieldName];
            } elseif ($fieldName == 'bank_identification_code') {
                if (!is_numeric($data[$fieldName]) || strlen($data[$fieldName]) < 8 || strlen($data[$fieldName]) > 9)
                    $arResult[$fieldName] = $data[$fieldName];
            }
        }
        if (!empty($arResult))
            return $arResult;
        else
            return false;

    }
    
    //Устарело
    public function GetFullPageById($id)
    {
        $r = mysqli_query(self::$link, 'SELECT * FROM `pages` WHERE `id` = "' . (int)$id . '"');
        $page = false;
        if ($r) if ($row = mysqli_fetch_assoc($r)) {
            $page = $row;
        }
        if ($page) {
            $block = $this->GetBlocksByPageID($page['id']);
            if ($block === -1) $block = $this->GetBlocksByPageID($page['id']);
            $page['text'] = $block[0]['text'];
            $page['text'] = str_replace(array("\'", '\"', '\\\\'), array("'", '"', '\\'), $page['text']);
            // TODO
            $page['block_id'] = $block[0]['id'];
        }
        return $page;
    }

    //Устарело
    public function GetBlocksByPageID($id)
    {
        $r = mysqli_query(self::$link, 'SELECT * FROM `blocks` WHERE `page_id` = "' . (int)$id . '"');
        $block = array();
        if ($r && mysqli_num_rows($r) > 0) {
            while ($row = mysqli_fetch_assoc($r)) {
                $row['text'] = str_replace(array("\'", '\"', '\\\\'), array("'", '"', '\\'), $row['text']);
                $block[] = $row;
            }
        } else {
            $this->checkTable('blocks');
            mysqli_query(self::$link, "INSERT INTO `blocks` (`page_id`, `text` ) VALUES ('" . (int)$id . "', '');");
            $block = -1;
        }
        return $block;
    }
    
    public function UpdatePageByID($id, $data)
    {
        $langid = $this->_getLangCodeId($data['lang']);
        $this->checkTable('site_pages_langs');
        $this->checkTable('site_pages_langs_data');

        $isService = (isset($data['is_service'])) ? '1' : '0';
        mysqli_query(self::$link, 'UPDATE `pages` SET `alias` = "' . $data['alias'] . '", `title`= "' . $this->escape($data['title']) . '", `is_service` = "' . $isService . '" WHERE `id` = "' . (int)$id . '"');

        $c = $this->querySingleValue('SELECT COUNT(*) FROM site_pages_langs WHERE `lang_id`="' . $langid . '" AND `page_id`="' . (int)$id . '"');
        if ($c) {
            mysqli_query(self::$link, 'UPDATE `site_pages_langs` SET `lang_id` = "' . $langid . '" WHERE `page_id` = "' . (int)$id . '"');
        } else {
            mysqli_query(self::$link, 'INSERT INTO `site_pages_langs` (`lang_id`, `page_id` ) VALUES ( "' . $langid . '", "' . (int)$id . '" )');
        }

        $r = mysqli_query(self::$link, 'DELETE FROM site_pages_langs_data WHERE `lang_id`="' . $langid . '" AND `p`="' . $this->escape($data['alias']) . '"');
        mysqli_query(self::$link, 'INSERT INTO `site_pages_langs_data` (`lang_id`, `p`, `pagetitle`, `seo_keywords`, `seo_description`, `type` ) VALUES ( "' . $langid . '", "' . $this->escape($data['alias']) . '", "' . $this->escape($data['pagetitle']) . '", "' . $this->escape($data['seo_keywords']) . '", "' . $this->escape($data['seo_description']) . '", "content" )');
    }
    
    //Устарело
    public function UpdateBlockByID($id, $text)
    {
        $r = mysqli_query(self::$link, 'UPDATE `blocks` SET `text` = "' . $this->escape($text) . '" WHERE `id` = "' . (int)$id . '"');
    }

    public function add_site_pages_parents($page_id, $parent_id)
    {
        $this->checkTable('site_pages_parents');
        mysqli_query(self::$link, 'INSERT INTO `site_pages_parents` SET `page_id` = "' . (int)$page_id . '", '
            . ' `parent_id` = "' . (int)$parent_id . '"');
        return mysqli_insert_id(self::$link);
    }

    public function del_site_pages_parents_page_id($page_id)
    {
        $this->checkTable('site_pages_parents');
        mysqli_query(self::$link, 'DELETE FROM `site_pages_parents` WHERE `page_id`= "' . (int)$page_id . '"');
        return mysqli_affected_rows(self::$link);
    }

    public function get_parent_id_site_pages_parents_page_id($page_id)
    {
        $this->checkTable('site_pages_parents');
        return $this->querySingleValue('SELECT `parent_id` FROM `site_pages_parents` WHERE `page_id` = "' . (int)$page_id . '"  ORDER BY `menu_order`');
    }
    
    //Устарело
    public function DeletePageByID($id)
    {
        $this->checkTable('site_pages_parents');
        $id = (int)$id;
        // СѓРґР°Р»СЏРµРј РїРѕС‚РѕРјРєРѕРІ РµСЃР»Рё СЃС‚СЂР°РЅРёС†Р° СЂРѕРґРёС‚РµР»СЊ
        mysqli_query(self::$link, "DELETE FROM `site_pages_parents` WHERE `parent_id` = '" . $id . "'");
        // СѓРґР°Р»СЏРµРј СЃС‚СЂР°РЅРёС†Сѓ РµСЃР»Рё РѕРЅР° РїРѕС‚РѕРјРѕРє
        mysqli_query(self::$link, "DELETE FROM `site_pages_parents` WHERE `page_id` = '" . $id . "'");

        // РґР»СЏ РѕС‡РёСЃС‚РєРё С‚Р°Р±Р»РёС†С‹ `site_pages_langs_data`
        $alias = $this->querySingleValue($query);
        
        $this->checkTable('site_pages_langs_data');
        $r = mysqli_query(self::$link, 'DELETE FROM `site_pages_langs_data` WHERE `p` = "' . $this->escape($alias) . '"');

        $this->checkTable('site_pages_langs');
        $r = mysqli_query(self::$link, 'DELETE FROM `site_pages_langs` WHERE `page_id` = "' . $id . '"');

        // СѓРґР°Р»СЏРµРј СЃС‚СЂР°РЅРёС†Сѓ
        $r = mysqli_query(self::$link, 'DELETE FROM `pages` WHERE `id` = "' . $id . '"');
    }
    
    //Устарело
    public function CreatePage($data)
    {
        $langid = $this->_getLangCodeId($data['lang']);
        $this->checkTable('site_pages_langs');
        $this->checkTable('site_pages_langs_data');

        mysqli_query(self::$link, 'INSERT INTO `pages` (`alias`, `title`) VALUES ("' . $this->escape($data['alias']) . '", "' . $this->escape($data['title']) . '")');
        $pid = mysqli_insert_id(self::$link);
        $sql = 'INSERT INTO `site_pages_langs` (`lang_id`, `page_id` ) VALUES ( "' . $langid . '", "' . $pid . '" )';
        mysqli_query(self::$link, $sql);

        $pagetitle = isset($data['pagetitle']) ? $this->escape($data['pagetitle']) : '';
        $seo_keywords = isset($data['seo_keywords']) ? $this->escape($data['seo_keywords']) : '';
        $seo_description = isset($data['seo_description']) ? $this->escape($data['seo_description']) : '';
        $sql = 'INSERT INTO `site_pages_langs_data` (`lang_id`, `p`, `pagetitle`, `seo_keywords`, `seo_description`, `type`) VALUES ("' . $langid . '", "' . $this->escape($data['alias']) . '", "' . $pagetitle . '", "' . $seo_keywords . '", "' . $seo_description . '", "content")';
        mysqli_query(self::$link, $sql);

        return $pid;
    }

    //Устарело
    public function GetPageByID($id)
    {
        $r = mysqli_query(self::$link,
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
            $r = mysqli_query(self::$link, "
                SELECT * FROM `site_pages_langs_data`
                WHERE `p`='" . $this->escape($page['alias']) . "'
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

    public function CreateNews($data)
    {
        $langid = $this->_getLangCodeId($data['lang']);
        $this->checkTable('site_news_langs');

        mysqli_query(self::$link, 'INSERT INTO `news` (`title`, `brief`, `image` ) VALUES ( "' . $this->escape($data['title']) . '", "' . $this->escape($data['brief']) . '", "' . $this->escape($data['image']) . '" )');
        $pid = mysqli_insert_id(self::$link);
        mysqli_query(self::$link, 'INSERT INTO `site_news_langs` (`lang_id`, `news_id` ) VALUES ( "' . $langid . '", "' . $pid . '" )');

        return $pid;
    }

    public function UpdateNewsByID($id, $data)
    {
        $langid = $this->_getLangCodeId($data['lang']);
        $this->checkTable('site_news_langs');

        mysqli_query(self::$link, 'UPDATE `news` SET `title`= "' . $this->escape($data['title']) . '", `brief`= "' . $this->escape($data['brief']) . '", `image` = "' . $this->escape($data['image']) . '" WHERE `id` = "' . (int)$id . '"');

        $c = $this->querySingleValue('SELECT COUNT(*) FROM site_news_langs WHERE `lang_id`="' . $langid . '" AND `news_id`="' . $id . '"');
        if ($c) {
            mysqli_query(self::$link, 'UPDATE `site_news_langs` SET `lang_id` = "' . $langid . '" WHERE `news_id` = "' . (int)$id . '"');
        } else {
            mysqli_query(self::$link, 'INSERT INTO `site_news_langs` (`lang_id`, `news_id` ) VALUES ( "' . $langid . '", "' . (int)$id . '" )');
        }
    }

    function UpdateNewsText($id, $text)
    {
        mysqli_query(self::$link, 'UPDATE `news` SET `text`= "' . $this->escape($text) . '" WHERE `id` = "' . (int)$id . '"');
    }

    public function DeleteNewsByID($id)
    {
        $r = mysqli_query(self::$link, 'DELETE FROM `news` WHERE `id` = "' . (int)$id . '"');

        $this->checkTable('site_news_langs');
        $r = mysqli_query(self::$link, 'DELETE FROM `site_news_langs` WHERE `news_id` = "' . (int)$id . '"');
    }

    public function GetNewsByID($id)
    {
        $r = mysqli_query(self::$link,
            '
            SELECT `p`.*, `l`.`lang_code`, `l`.`id` as `lang_id` FROM `news` `p`
            LEFT JOIN `site_news_langs` `pl`
                ON `p`.`id`=`pl`.`news_id`
            LEFT JOIN `site_langs` `l`
                ON `pl`.`lang_id` = `l`.`id`
            WHERE `p`.`id` = "' . (int)$id . '"
            '
        );

        $news = false;
        if ($r) if ($row = mysqli_fetch_assoc($r)) {
            $news = $row;
            $news['title'] = str_replace(array("\'", '\"', '\\\\'), array("'", '"', '\\'), $news['title']);
            $news['brief'] = str_replace(array("\'", '\"', '\\\\'), array("'", '"', '\\'), $news['brief']);
            $news['image'] = str_replace(array("\'", '\"', '\\\\'), array("'", '"', '\\'), $news['image']);
            $news['text'] = str_replace(array("\'", '\"', '\\\\'), array("'", '"', '\\'), $news['text']);
        }
        return $news;
    }

    public function GetAllNews()
    {
        $this->checkTable('news');
        $this->checkTable('site_news_langs');
        $r = mysqli_query(self::$link, '
            SELECT DISTINCT `p`.*, `l`.`lang_name`,`l`.`lang_code`
            FROM `news` `p`
            LEFT JOIN `site_news_langs` `pl`
                ON `p`.`id`=`pl`.`news_id`
            LEFT JOIN `site_langs` `l`
                        ON `pl`.`lang_id` = `l`.`id`
                        ORDER BY `p`.`created` DESC
            ');
        //Не осилил SQL запрос, так бы в нем прописал where
        $news = array();
        if ($r && @mysqli_num_rows($r)) {
            while ($row = mysqli_fetch_assoc($r)) {
                if ($row['lang_code']==$_SESSION['active_lang'])
                    $news[] = $row;
            }
        } else
            $news = -1;

        if (count($news)==0)
            $news = -1;
        return $news;
    }

    //Устарело
    private function createPaymentPages()
    {
        $pid = $this->CreatePage(array('lang' => 'ru', 'alias' => 'paymentsuccess', 'title' => 'Оплата успешно произведена'));
        $pInfo = $this->GetFullPageById($pid);
        $this->UpdateBlockByID($pInfo['block_id'], '<p>Оплата успешно произведена</p>');

        $pid = $this->CreatePage(array('lang' => 'ru', 'alias' => 'robo_success', 'title' => 'Оплата успешно произведена'));
        $pInfo = $this->GetFullPageById($pid);
        $this->UpdateBlockByID($pInfo['block_id'], '<p>Оплата успешно произведена</p>');

        $pid = $this->CreatePage(array('lang' => 'ru', 'alias' => 'paymentfail', 'title' => 'Произошла ошибка в процессе оплаты'));
        $pInfo = $this->GetFullPageById($pid);
        $this->UpdateBlockByID($pInfo['block_id'], '<p>Произошла ошибка в процессе оплаты</p>');

        $pid = $this->CreatePage(array('lang' => 'ru', 'alias' => 'robo_fail', 'title' => 'Произошла ошибка в процессе оплаты'));
        $pInfo = $this->GetFullPageById($pid);
        $this->UpdateBlockByID($pInfo['block_id'], '<p>Произошла ошибка в процессе оплаты</p>');

        $pid = $this->CreatePage(array('lang' => 'ru', 'alias' => 'depositsuccess', 'title' => 'Оплата успешно произведена'));
        $pInfo = $this->GetFullPageById($pid);
        $this->UpdateBlockByID($pInfo['block_id'], '<p>Ваш счет успешно пополнен</p>');

        $pid = $this->CreatePage(array('lang' => 'ru', 'alias' => 'depositfail', 'title' => 'Произошла ошибка в процессе оплаты'));
        $pInfo = $this->GetFullPageById($pid);
        $this->UpdateBlockByID($pInfo['block_id'], '<p>Произошла ошибка в процессе оплаты</p>');


        $pid = $this->CreatePage(array('lang' => 'en', 'alias' => 'paymentsuccess', 'title' => 'Payment is completed successfully'));
        $pInfo = $this->GetFullPageById($pid);
        $this->UpdateBlockByID($pInfo['block_id'], '<p>Payment is completed successfully</p>');

        $pid = $this->CreatePage(array('lang' => 'en', 'alias' => 'robo_success', 'title' => 'Payment is completed successfully'));
        $pInfo = $this->GetFullPageById($pid);
        $this->UpdateBlockByID($pInfo['block_id'], '<p>Payment is completed successfully</p>');

        $pid = $this->CreatePage(array('lang' => 'en', 'alias' => 'paymentfail', 'title' => 'The errors in the payment process'));
        $pInfo = $this->GetFullPageById($pid);
        $this->UpdateBlockByID($pInfo['block_id'], '<p>In the process of payment errors were encountered. Please try again.</p>');

        $pid = $this->CreatePage(array('lang' => 'en', 'alias' => 'robo_fail', 'title' => 'The errors in the payment process'));
        $pInfo = $this->GetFullPageById($pid);
        $this->UpdateBlockByID($pInfo['block_id'], '<p>In the process of payment errors were encountered. Please try again.</p>');

        $pid = $this->CreatePage(array('lang' => 'en', 'alias' => 'depositsuccess', 'title' => 'Deposit into an account was successfull'));
        $pInfo = $this->GetFullPageById($pid);
        $this->UpdateBlockByID($pInfo['block_id'], '<p>Payment is completed successfully</p>');

        $pid = $this->CreatePage(array('lang' => 'en', 'alias' => 'depositfail', 'title' => 'The errors in the payment process'));
        $pInfo = $this->GetFullPageById($pid);
        $this->UpdateBlockByID($pInfo['block_id'], '<p>In the process of payment errors were encountered. Please try again.</p>');
    }

    public function Check()
    {
        if (self::$link) return true;
        if (!defined('DB_HOST')) return false;
        if (!defined('DB_USER')) return false;
        if (!defined('DB_PASS')) return false;
        if (!defined('DB_BASE')) return false;

        $host = strtolower(DB_HOST);

        if($host == 'localhost') {
            $host = '127.0.0.1';
        }
        if (defined('DB_HOST_USE_LOCALHOST') && DB_HOST_USE_LOCALHOST) {
            $host = 'localhost';
        }

        self::$link = @mysqli_connect($host, DB_USER, DB_PASS);
        if (!self::$link) return false;
        $res = @mysqli_select_db(self::$link, DB_BASE);
        mysqli_query(self::$link, 'SET NAMES utf8');
        if (!$res) return false;

        return true;
    }

    public static function CheckStatic()
    {
        if (!defined('DB_HOST')) return false;
        if (!defined('DB_USER')) return false;
        if (!defined('DB_PASS')) return false;
        if (!defined('DB_BASE')) return false;
        $link = @mysqli_connect(DB_HOST, DB_USER, DB_PASS);
        if (!$link) return false;
        $res = @mysqli_select_db($link, DB_BASE);
        mysqli_query($link, 'SET NAMES utf8');
        if (!$res) return false;

        return true;
    }

    public function checkLanguage($lang_name, $lang_descr)
    {
        $this->checkTable('site_langs');

        $lang_name = $this->escape($lang_name);
        $lang_descr = $this->escape($lang_descr);
        
        $result = $this->querySingleValue('SELECT COUNT(*) FROM `site_langs` WHERE `lang_code`="' . $lang_name . '"');
        
        if (! $result) {
            mysqli_query(self::$link, 'INSERT INTO `site_langs` SET `lang_code`="' . $lang_name . '", `lang_name`="' . $lang_descr . '"');
        }
/*        $q = mysqli_query(self::$link, 'SELECT COUNT(*) FROM `site_langs` WHERE `lang_code`="' . $lang_name . '"');

        if ($result = mysqli_use_result(self::$link)) {
            $rows = mysqli_fetch_row($result);
            if (is_array($rows)) {
                $rows = $rows[0];
            }

            if (!$rows) {
                mysqli_query(self::$link, 'INSERT INTO `site_langs` SET `lang_code`="' . $lang_name . '", `lang_name`="' . $lang_descr . '"');
            }

            mysqli_free_result($result);
        }*/
    }

    public function getLanguages()
    {
        $this->checkTable('site_langs');

        $q = mysqli_query(self::$link, 'SELECT * FROM `site_langs`');
        $langs = array();

        if ($result = mysqli_use_result(self::$link)) {
            while($lang = mysqli_fetch_array($result)) {
                $langs[] = $lang;
            }

            mysqli_free_result($result);
        }
        return $langs;
    }

    public function getTranslations($id = '', $lang_code = '', $key = '')
    {
        $this->checkTable('site_langs');
        $this->checkTable('site_translations');
        $this->checkTable('site_translation_keys');

        $where = array();
        if ($id) {
            $id = (int)$id;
            $where[] = '`st`.`id` = "' . $id . '"';
        }
        if ($lang_code) {
            $lang_code = $this->escape($lang_code);
            $where[] = '`l`.`lang_code` = "' . $lang_code . '"';
        }
        if ($key) {
            $key = $this->escape($key);
            $where[] = 'BINARY `k`.`name` = "' . $key . '"';
        }
        if ($where) $where = ' WHERE ' . implode(' AND ', $where); else $where = '';
        $query = '
            SELECT `st`.`id`, `st`.`translation`, `k`.`name` as `key`, `l`.`lang_name`, `l`.`lang_code`
            FROM `site_translations` `st`
            INNER JOIN `site_translation_keys` `k`
            ON `st`.`key`=`k`.`id`
            INNER JOIN `site_langs` `l`
            ON `st`.`langid`=`l`.`id`
            ' . $where . ' ORDER BY `key` ASC';
        
        $translations = $this->queryMakeArray($query, array(), false, array('free_result' => true) );

        return $translations;
    }

    public function existTranslations($lang = null)
    {
        $this->checkTable('site_langs');
        $this->checkTable('site_translations');
        $this->checkTable('site_translation_keys');

        $lang = $lang ? $lang : Session::getActiveLang();
        $lang_code = $this->escape($lang);
        $where = 'WHERE `l`.`lang_code` = "' . $lang_code . '"';

        $query = '
            SELECT COUNT(*)
            FROM `site_translations` `st`
            INNER JOIN `site_translation_keys` `k`
            ON `st`.`key`=`k`.`id`
            INNER JOIN `site_langs` `l`
            ON `st`.`langid`=`l`.`id`
            ' . $where;

        return $this->querySingleValue($query);

    }

    public function SetCustomCalculator()
    {
        $files = array('countries.sql', 'delivery.sql', 'countries_for_delivery.sql');
        foreach ($files as $file) {
            $f = dirname(dirname(__FILE__)) . '/admin/sql/calculator/' . $file;
            $fileExists = file_exists($f);
            $r = $fileExists ? mysqli_query(self::$link, file_get_contents($f)) : false;
            echo ' ' . $file . ' : ';
            var_dump($r);
        }
    }

    public function ClearCalculator()
    {
        $tables = array('countries', 'delivery', 'countries_for_delivery');
        foreach ($tables as $table) {
            mysqli_query(self::$link, 'drop table if exists ' . $table);
            $f = dirname(dirname(__FILE__)) . '/admin/sql/' . $table . '.sql';
            $fileExists = file_exists($f);
            $r = $fileExists ? mysqli_query(self::$link, file_get_contents($f)) : false;
            echo ' ' . $table . ' : '; var_dump($r);
        }
    }

    public function checkTranslations()
    {
        $trans = $this->getTranslations('', $_SESSION['translate_lang']);
        if (count($trans)) {
            return false;
        }

        $xml = @simplexml_load_file(dirname(dirname(__FILE__)) . '/langs/' . $_SESSION['translate_lang'] . '.xml');
        if (!$xml) {
            return false;
        }

        foreach ($xml->key as $k) {
            $keyid = $this->_addKey((string)$k['name']);
            $this->_addTranslation($keyid, $_SESSION['translate_lang'], (string)$k[0]);
        }
    }

    public function getBlock($type)
    {
        $this->checkTable('site_blocks');
        return $this->querySingleValue('SELECT `properties` FROM `site_blocks` WHERE `type`="' . $this->escape($type) . '"');        
    }

    public function getSiteConfig()
    {
        $q = mysqli_query(self::$link, 'SELECT * FROM `site_config`');
        if (!$q) {
            return array(false, mysqli_error(self::$link));
        }
        $conf = array();
        while ($r = mysqli_fetch_assoc($q)) {
            $r['value'] = str_replace(array('\\"', '\\\\', "\\'"), array('"', '\\', "'"), $r['value']);
            $conf[$r['key']] = $r['value'];
        }
        return array(true, $conf);
    }

    public static function getSiteConfigStatic()
    {
        $q = mysqli_query(self::$link, 'SELECT * FROM `site_config`');
        if (!$q) {
            return array(false, mysqli_error(self::$link));
        }
        $conf = array();
        while ($r = mysqli_fetch_assoc($q)) {
            $r['value'] = str_replace(array('\\"', '\\\\', "\\'"), array('"', '\\', "'"), $r['value']);
            $conf[$r['key']] = $r['value'];
        }
        return array(true, $conf);
    }

    public function getSiteConfigMultipleLanguages($name)
    {
        // если есть переменная для текущего языка
        $result = $this->querySingleValue("SELECT `value` FROM `site_config` WHERE `key` = '" . $this->escape($name) . "_" . @$_SESSION['active_lang'] . "' LIMIT 1");

        if (!$result) {
            $result = $this->querySingleValue("SELECT `value` FROM `site_config` WHERE `key` = '" . $this->escape($name) . "' LIMIT 1");
        }
        
        return $result;
    }

    public function saveSiteConfig($params)
    {
        foreach ($params as $k => $v) {
            if ($v !== '') {
                $exists =  $this->querySingleValue('SELECT COUNT(*) FROM `site_config` WHERE `key`="' . $this->escape($k) . '"');
                if ($exists) {
                    $r = mysqli_query(self::$link, 'UPDATE `site_config` SET `value`="' . $this->escape($v) . '" WHERE `key`="' . $this->escape($k) . '"');
                } else {
                    $r = mysqli_query(self::$link, 'INSERT INTO `site_config` SET `value`="' . $this->escape($v) . '", `key`="' . $this->escape($k) . '"');
                }
            } else {
                $r = mysqli_query(self::$link, 'DELETE FROM `site_config` WHERE `key`="' . $this->escape($k) . '"');
            }
        }

        return $r;
    }

    public function getCategoryIdByAlias($alias)
    {
        $this->checkTable('site_categories');
        $result = $this->querySingleValue('SELECT `category_id` FROM `site_categories` WHERE `alias`="' . $this->escape($alias) . '"');
        return $result ? $result : ''; 
    }

    private function _addKey($key)
    {
        $key = $this->escape($key);
        
        $result = $this->querySingleValue("SELECT `id` FROM `site_translation_keys` WHERE `name`='$key'");
        if ($result) {
                return $result;
        }

        mysqli_query(self::$link, "INSERT INTO `site_translation_keys` SET `name`='$key'");
        return mysqli_insert_id(self::$link);
    }

    private function _addTranslation($keyid, $lang, $trans)
    {
        if (!$trans)
            return array(true);

        $lang = $this->escape($lang);
        
        $langid = $this->querySingleValue("SELECT `id` FROM `site_langs` WHERE `lang_code`='$lang'");
        if (! $langid )
            return array(false, 'РЇР·С‹Рє РЅРµ Р±С‹Р» РЅР°Р№РґРµРЅ');

        $trans = $this->escape($trans);
        $this->checkTable('site_translations');
        mysqli_query(self::$link, "DELETE FROM `site_translations` WHERE `langid`='$langid' AND `key`='$keyid'");
        mysqli_query(self::$link, "INSERT INTO `site_translations` SET `langid`='$langid', `key`='$keyid', `translation`='$trans'");
        return array(true);
    }

    public function _getLangCodeId($lang)
    {
        $lang = $this->escape($lang);
        return $this->querySingleValue("SELECT `id` FROM `site_langs` WHERE `lang_code`='$lang'");
    }

    public function getLangDescrByCode($code)
    {
        $lang = $this->escape($code);
        return $this->querySingleValue("SELECT `lang_name` FROM `site_langs` WHERE `lang_code`='$lang'", array('site_langs'));
    }

    public function GetDelivery()
    {
        $delivery = array();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : NULL;
        $sql = 'SELECT * FROM `delivery` WHERE (1=1)';
        if ($id)
            $sql .= " AND id=$id";
        $query = mysqli_query(self::$link, $sql);
        while ($row = mysqli_fetch_assoc($query)) {
            $delivery[] = $row;
        }
        return $delivery;
    }

    public function AddOrUpdateDelivery()
    {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : NULL;
        $formula = isset($_POST['formula']) ? trim($_POST['formula']) : '';
        $name = trim($_POST['kind_of_delivery']);

        if (!$id) {
            return mysqli_query(self::$link, "INSERT INTO `delivery` (`name`, `formula`) VALUES('$name', '$formula')");
        } else {
            return mysqli_query(self::$link, "UPDATE `delivery` SET `name`='$name', `formula`='$formula' WHERE id=$id");
        }
    }

    public function AddOrUpdateCountry()
    {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : NULL;
        $name = trim($_POST['name']);

        if (!$id) {
            return mysqli_query(self::$link, "INSERT INTO `countries` (`name`) VALUES('$name')");
        } else {
            return mysqli_query(self::$link, "UPDATE `countries` SET `name`='$name' WHERE id=$id");
        }
    }

    public function GetCountries()
    {
        $countries = array();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : NULL;
        $sql = 'SELECT * FROM `countries` WHERE (1=1)';
        if ($id)
            $sql .= " AND id=$id";
        $query = mysqli_query(self::$link, $sql);
        while ($row = mysqli_fetch_array($query)) {
            $countries[] = $row;
        }
        return $countries;
    }

    public function GetCountriesByDelivery()
    {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $result = array();
        $sql = "SELECT `countries_for_delivery`.*, `countries`.name country_name FROM `countries_for_delivery`
                INNER JOIN `countries` ON (`countries_for_delivery`.country_id = `countries`.id)
                WHERE (1=1)";
        if ($id)
            $sql .= " AND delivery_id=$id";
        $query = mysqli_query(self::$link, $sql);
        while ($row = mysqli_fetch_assoc($query)) {
            $result[] = $row;
        }
        return $result;
    }

    public function SetCountriesByDelivery()
    {
        $delivery = isset($_POST['delivery']) ? (int)$_POST['delivery'] : 0;
        if (!$delivery)
            return false;
        $countries = $_POST['countries'];
        foreach ($countries as $country) {
            $country_id = $country['id'];
            $is_active = isset($country['is_active']) ? 1 : 0;
            $ret = mysqli_query(self::$link, "INSERT INTO `countries_for_delivery` (`delivery_id`,`country_id`,`is_active`) VALUES ($delivery, $country_id, $is_active)
                                ON DUPLICATE KEY UPDATE is_active = $is_active");
        }
        return true;
    }

    public function DeleteRow($table, $id)
    {
        if ((empty($id)) || (empty ($table)))
            return false;
        else {
            return mysqli_query(self::$link, "DELETE FROM `$table` WHERE id=$id");
        }
    }

    public function DeleteDelivery($delivery_id)
    {
        return mysqli_query(self::$link, 'DELETE FROM `countries_for_delivery` WHERE delivery_id=' . $delivery_id);
    }

    public function GetDataByCountryAndDelivery()
    {
        $country_id = isset($_GET['country_id']) ? (int)$_GET['country_id'] : 0;
        $delivery_id = isset($_GET['delivery_id']) ? (int)$_GET['delivery_id'] : 0;
        if ($country_id && $delivery_id) {
            $result = mysqli_query(self::$link, "SELECT countries_for_delivery.*, countries.name country_name, delivery.name delivery_name, delivery.formula
                                    FROM `countries_for_delivery`
                                        INNER JOIN countries ON (countries_for_delivery.country_id = countries.id)
                                        INNER JOIN delivery ON (countries_for_delivery.delivery_id = delivery.id)
                                    WHERE `delivery_id`=$delivery_id AND `country_id`=$country_id");
            return mysqli_fetch_array($result);
        } else
            return false;
    }

    public function UpdateParamsForDelivery()
    {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : NULL;
        $step = isset($_POST['param_step']) ? (int)$_POST['param_step'] : 0;
        $start = isset($_POST['param_start']) ? (float)$_POST['param_start'] : 0;
        if ($id) {
            return mysqli_query(self::$link, "UPDATE `countries_for_delivery` SET `param_step`=$step, `param_start`=$start WHERE id=$id");
        } else {
            return false;
        }
    }

    public function GetUserByLogin($login)
    {
        $login = $this->escape($login);
        $query = mysqli_query(self::$link, "SELECT * FROM `site_referrals` WHERE `login`='$login'");
        return mysqli_fetch_assoc($query);
    }

    public function GetUserById($id)
    {
        $id = (int)$id;
        $query = mysqli_query(self::$link, "SELECT * FROM `site_referrals` WHERE `user_id`='$id'");
        return mysqli_fetch_assoc($query);
    }

    public function GetReferralUsers($id=0)
    {
        $this->checkTable('site_orders');
        $result = array();
        $sql = 'SELECT *, (SELECT EXISTS( SELECT * from site_orders where site_orders.referral_id = site_referrals.id )) as in_system FROM `site_referrals`';
        $condition = array();
        if ($id)
        $condition[] = 'id=' . (int)$id;

        if ($condition)
            $sql .= ' WHERE ' . implode (' AND ', $condition);
        $query = mysqli_query(self::$link, $sql);
        while ($row = mysqli_fetch_assoc($query)) {
            $result[] = $row;
        }
        return $result;
    }

    public function ExistsOrderByUser($id)
    {
        return $this->querySingleValue('SELECT EXISTS( SELECT * from site_orders where site_orders.referral_id = ' . (int)$id . ' limit 1 )');
    }

    public function GetReferralsInfo($array = array())
    {
        $result = array();
        $sql = '
            SELECT
                site_referrals.id AS idx, parent_id, login, email, `status`, comission, balance
            FROM
              site_referrals';
        if ($array)
            $sql .= ' WHERE id IN (' . implode(',', $array).')';
        $query = mysqli_query(self::$link, $sql);
        while ($row = mysqli_fetch_assoc($query)) {
            $result[] = $row;
        }
        return $result;
    }

    public function GetChildrenReferralsInfo($array = array())
    {
        $result = array();
        $sql = '
            SELECT
                site_referrals.id AS idx, parent_id, login, email, `status`, comission, balance
            FROM
              site_referrals';
        if ($array)
            $sql .= ' WHERE parent_id IN (' . implode(',', $array).')';
        $query = mysqli_query(self::$link, $sql);
        while ($row = mysqli_fetch_assoc($query)) {
            $result[] = $row;
        }
        return $result;
    }

    public function updateOrderForPresentByOrderId($id)
    {
        return mysqli_query(self::$link, "UPDATE `site_orders` SET `send_present`=1 WHERE order_id=$id");
    }

    public function AddUser($user, $email, $parent_id)
    {
        $user_name = $this->escape($user['Login']);
        $user_id = trim($user['Id']);
        return mysqli_query(self::$link, "INSERT INTO `site_referrals` (`parent_id`, `login`, `user_id`, `email`) VALUES($parent_id, '$user_name', '$user_id', '$email')");
    }

    public function UpdateReferral($user_id, $status, $comission, $balance)
    {
        return mysqli_query(self::$link, "UPDATE `site_referrals` SET `status`=$status, `comission`=$comission, `balance`=$balance WHERE id=$user_id");
    }

    public function AddOrder($referral_id, $order_id, $purchase)
    {
        return mysqli_query(self::$link, "INSERT INTO `site_orders` (`referral_id`, `order_id`, `purchase`) VALUES($referral_id, '$order_id', $purchase)");
    }

    public function getChildrenByStatuses($user_id)
    {
        $result = array();
        $query = mysqli_query(self::$link,
            'SELECT `status`, COUNT(`status`) AS cnt FROM `site_referrals`
                    WHERE parent_id = ' . $user_id . ' GROUP BY `status`;');
        while ($row = mysqli_fetch_assoc($query)) {
            $result[$row['status']] = (int)$row['cnt'];
        }
        return $result;
    }

    public function getChildrenByParentId($parent_id)
    {
        $this->Check();
        $result = array();
        $query = mysqli_query(self::$link, '
            SELECT r.*, sum(o.purchase) as purchase
            FROM `site_referrals` AS r
            LEFT JOIN site_orders AS o ON (r.id = o.referral_id)
            WHERE r.parent_id = ' . $parent_id . '
            GROUP BY r.id');
        while ($row = mysqli_fetch_assoc($query)) {
            $result[] = $row;
        }
        return $result;
    }

    public function getReferralBalance($user_id)
    {
        return $this->querySingleValue('SELECT SUM(`purchase`) FROM `site_orders` WHERE `send_present`=1 AND `referral_id`=' . $user_id);
    }

    public function updateOrderPurchase($order_id, $purchase)
    {
        return mysqli_query(self::$link, "UPDATE `site_orders` SET `purchase`=$purchase, `send_present`=1 WHERE order_id=$order_id");
    }

    public function AddOrUpdateMyCategory()
    {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : NULL;
        $pid = isset($_POST['pid']) ? (int)$_POST['pid'] : 0;
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        if (!$id) {
            return mysqli_query(self::$link, "INSERT INTO `my_categories` (`parent_id`,`name`,`description`) VALUES($pid, '$name', '" . $this->escape($description) . "');");
        } else {
            return mysqli_query(self::$link, "UPDATE `my_categories` SET `name`='$name', `description`='" . $this->escape($description) . "' WHERE id=$id");
        }
    }

    public function GetCategoryById($id = 0)
    {
        $result = array();
        $sql = 'SELECT * FROM `my_categories` WHERE (1=1)';
        if ($id)
            $sql .= ' AND id=' . (int)$id;
        $query = mysqli_query(self::$link, $sql);
        if ($query) {
            while ($row = mysqli_fetch_assoc($query)) {
                $result[] = $row;
            }
        }
        return $result;
    }

    public function GetGoodsByCatId($cat_id)
    {
        $result = array();
        $sql = 'SELECT * FROM `my_goods`';
        $sql .= ' WHERE `my_category_id`=' . (int)$cat_id;
        $sql .= $this->_setWhere();
        $sql .= $this->_setLimit();
        $query = mysqli_query(self::$link, $sql);
        while ($row = mysqli_fetch_assoc($query)) {
            $result[] = $row;
        }
        return $result;
    }

    private function _setWhere()
    {
        $where = '';
        $cost_from = isset($_GET['cost']['from']) ? (int)$_GET['cost']['from'] : 0;
        $cost_to = isset($_GET['cost']['to']) ? (int)$_GET['cost']['to'] : 0;
        $where .= $cost_from ? ' AND `price`>' . $cost_from : '';
        $where .= $cost_to ? ' AND `price`<' . $cost_to : '';
        return $where;
    }

    private function _setLimit()
    {
        $limit = ' LIMIT ';
        $per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 16;
        $from = isset($_GET['from']) ? (int)$_GET['from'] : 0;
        $limit .= $from ? $from : '';
        $limit .= ' ' . $per_page;
        return $limit;
    }

    public function GetGoodsById($id)
    {
        $result = array();
        $query = mysqli_query(self::$link, 'SELECT * FROM `my_goods` WHERE `id`=' . (int)$id);
        while ($row = mysqli_fetch_assoc($query)) {
            $result[] = $row;
        }

        return $result;
    }

    public function AddOrUpdateMyGoods()
    {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : NULL;
        $cat = isset($_POST['cat']) ? (int)$_POST['cat'] : 0;
        $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
        $amount = isset($_POST['amount']) ? (int)$_POST['amount'] : 0;
        $image = isset($_POST['PictureUrl']) ? $_POST['PictureUrl'] : '';
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $properties = $this->_setAttributes();
        if (!$id) {
            return mysqli_query(self::$link, "INSERT INTO `my_goods` (`my_category_id`,`name`,`description`,`image`,`price`,`amount`,`properties`) VALUES($cat, '$name', '" . $this->escape($description) . "', '$image', $price, $amount, '$properties');");
        } else {
            $sql = "UPDATE `my_goods` SET `name`='$name', `description`='" . $this->escape($description) . "', `price`=$price,`amount`=" . $amount . ",`properties`='" . $this->escape($properties) . "'";
            $sql .= $image ? ",`image`='$image'" : '';
            $sql .= ' WHERE id=' . $id;
            return mysqli_query(self::$link, $sql);
        }
    }

    private function _setAttributes()
    {
        $result = array();
        if (isset($_POST['atr_name'])) {
            foreach ($_POST['atr_name'] as $key => $name) {
                $value = isset($_POST['atr_val'][$key]) ? $_POST['atr_val'][$key] : NULL;
                if ($name && !is_null($value))
                    $result[$name] = $value;
            }
        }
        return serialize($result);
    }


    //Устарело
    public function checkSiteUnavailablePageExists()
    {
        $this->checkTable('pages');
        $result = mysqli_query(self::$link, 'SELECT * FROM `pages` WHERE `alias`="site_unavailable"');
        $result = mysqli_num_rows($result);
        //$page = $this->GetPageByAlias('site_unavailable');
        if($result==0){
            $pageId = $this->CreatePage(
                array(
                    'alias' => 'site_unavailable',
                    'title' => Lang::get('Site_is_under_construction'),
                    'lang'  => 'ru',
                    'pagetitle' => '',
                    'seo_keywords' => '',
                    'seo_description' => '',
                )
            );
            $pageInfo = $this->GetFullPageById($pageId);
            $this->UpdateBlockByID($pageInfo['block_id'],
                '<p>На сайте ведутся технические работы. Приносим извинения за временные неудобства</p>');

            $pageId = $this->CreatePage(
                array(
                    'alias' => 'site_unavailable',
                    'title' => 'On site maintenance work',
                    'lang'  => Session::getActiveLang(),
                    'pagetitle' => '',
                    'seo_keywords' => '',
                    'seo_description' => '',
                )
            );
            $pageInfo = $this->GetFullPageById($pageId);
            $this->UpdateBlockByID($pageInfo['block_id'],
                '<p>On site maintenance work. We apologize for any inconvenience</p>');
        }
    }

    public function sendReferralMessage($subject, $message, $direction, $parent, $userId)
    {
        $this->checkTable('site_referrals_messages');
        $added = time();
        $subject = $this->escape($subject);
        $message = $this->escape($message);
        $direction = $this->escape($direction);
        $parent = (int)$parent;
        $userId = $this->escape($userId);
        $result = mysqli_query(self::$link, "
            INSERT INTO `site_referrals_messages`
            SET
              `subject` = '$subject'
              , `message` = '$message'
              , `direction` = '$direction'
              , `parent` = '$parent'
              , `login` = '$userId'
              , `added` = $added
        ");
        if(!$result)
            throw new DBException(mysqli_error(self::$link), mysqli_errno(self::$link));
    }

    public function getCategoryAlias($cid, $cName = '', $createNew = true)
    {
        $seoCatRepository = new SeoCategoryRepository($this);
        return $seoCatRepository->getCategoryAlias($cid, $cName, $createNew);
    }

    public static function removeNotAvailableMenuItems($data)
    {
        $data = (array)$data;
        $newData = [];
        $preDefinedMenu = array(
            'digest' => 'Digest',
            'pristroy' => 'FleaMarket',
            'shopreviews' => 'ShopComments'
        );
        foreach ($data as $item) {
            if ($key = array_key_exists($item, $preDefinedMenu)) {
                if(! CMS::IsFeatureEnabled($preDefinedMenu[$item])) {
                    continue;
                }
            }
            $newData[] = $item;
        }
        return $newData;
    }

    public function getLink()
    {
        return self::$link;
    }
}
