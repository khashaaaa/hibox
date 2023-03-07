<?php

class DigestRepository extends Repository
{
    public function GetPostsByCat($cid,$from,$num)
    {
        $q = 'SELECT DISTINCT `p` . * , `pl`.`lang_id`, `l`.`lang_name`, `c`.`title` as `cat_title`
                    FROM `digest` `p`
                    LEFT JOIN `site_digest_langs` `pl` ON `p`.`id` = `pl`.`post_id`
                    LEFT JOIN `site_langs` `l` ON `pl`.`lang_id` = `l`.`id`
                    LEFT JOIN `site_digest_categories` `c` ON `p`.`category_id` = `c`.`id`
                    WHERE `p`.`category_id` = "'.$this->cms->escape($cid).'"
                    ORDER BY `p`.`created` DESC
                    LIMIT '.$this->cms->escape($from).' , '.$this->cms->escape($num);
        $r = $this->cms->query($q,array('digest','site_digest_langs'));
        $posts = array();
        if ($r && @mysqli_num_rows($r)) {
            while ($row = mysqli_fetch_assoc($r)) {
                $row['title'] = strip_tags($row['title']);
                $row['content'] = strip_tags($row['content']);
                $row['created'] = new DateTime($row['created']);
                $posts[] = $row;
            }
        } else {
            $posts = -1;
        }
        return $posts;

    }


    public function GetCountPostsByCat($cid)
    {
        $q = 'SELECT DISTINCT `p` . * , `pl`.`lang_id`, `l`.`lang_name`, `c`.`title` as `cat_title`
                    FROM `digest` `p`
                    LEFT JOIN `site_digest_langs` `pl` ON `p`.`id` = `pl`.`post_id`
                    LEFT JOIN `site_langs` `l` ON `pl`.`lang_id` = `l`.`id`
                    LEFT JOIN `site_digest_categories` `c` ON `p`.`category_id` = `c`.`id`
                    WHERE `p`.`category_id` = "'.$this->cms->escape($cid).'"
                    ORDER BY `p`.`created` DESC';
        $r = $this->cms->query($q,array('digest','site_digest_langs'));
        return @mysqli_num_rows($r);
    }

    public function GetAllPosts($from,$num)
    {
        $q = "SELECT DISTINCT `p` . * , `pl`.`lang_id`, `l`.`lang_name`, `c`.`title` as `cat_title`
                    FROM `digest` `p`
                    LEFT JOIN `site_digest_langs` `pl` ON `p`.`id` = `pl`.`post_id`
                    LEFT JOIN `site_langs` `l` ON `pl`.`lang_id` = `l`.`id`
                    LEFT JOIN `site_digest_categories` `c` ON `p`.`category_id` = `c`.`id`
                    ORDER BY `p`.`created` DESC
                    LIMIT ".$this->cms->escape($from)." , ".$this->cms->escape($num);
        $r = $this->cms->query($q,array('digest','site_digest_langs', 'site_digest_categories', 'site_langs'));
        $posts = array();
        if ($r && @mysqli_num_rows($r)) {
            while ($row = mysqli_fetch_assoc($r)) {
                $row['title'] = strip_tags($row['title']);
                $row['content'] = strip_tags($row['content']);
                $row['created'] = new DateTime($row['created']);
                $posts[] = $row;
            }
        } else {
            $posts = -1;
        }
        return $posts;
    }

    public function GetPostsByLang($lang, $from, $num)
    {
        $lang = $this->cms->_getLangCodeId($lang);
        $sql = "SELECT DISTINCT `p` . * , `pl`.`lang_id`, `l`.`lang_name`, `c`.`title` as `cat_title`"
                . " FROM `digest` `p`"
                . " LEFT JOIN `site_digest_langs` `pl` ON `p`.`id` = `pl`.`post_id`"
                . " LEFT JOIN `site_langs` `l` ON `pl`.`lang_id` = `l`.`id`"
                . " LEFT JOIN `site_digest_categories` `c` ON `p`.`category_id` = `c`.`id`"
                . " WHERE `pl`.`lang_id` = '" . $lang . "'"
                . " ORDER BY `p`.`created` DESC"
                . " LIMIT ".$this->cms->escape($from)." , ".$this->cms->escape($num);
        $res = $this->cms->query($sql, array('digest','site_digest_langs', 'site_digest_categories', 'site_langs'));

        $posts = array();
        if ($res && @mysqli_num_rows($res)) {
            while ($row = mysqli_fetch_assoc($res)) {
                $row['title'] = strip_tags($row['title']);
                $row['content'] = strip_tags($row['content']);
                $row['created'] = new DateTime($row['created']);
                $posts[] = $row;
            }
        } else {
            $posts = -1;
        }
        return $posts;
    }

    public function GetCountPosts()
    {
        $q = "SELECT DISTINCT `p` . * , `pl`.`lang_id`, `l`.`lang_name`, `c`.`title` as `cat_title`
                    FROM `digest` `p`
                    LEFT JOIN `site_digest_langs` `pl` ON `p`.`id` = `pl`.`post_id`
                    LEFT JOIN `site_langs` `l` ON `pl`.`lang_id` = `l`.`id`
                    LEFT JOIN `site_digest_categories` `c` ON `p`.`category_id` = `c`.`id`
                    ORDER BY `p`.`created` DESC";

        $r = $this->cms->query($q,array('digest','site_digest_langs'));
        return @mysqli_num_rows($r);
    }

    public function GetCountPostsByLang($lang)
    {
        $lang = $this->cms->_getLangCodeId($lang);
        $sql = "SELECT DISTINCT `p` . * , `pl`.`lang_id`, `l`.`lang_name`, `c`.`title` as `cat_title`"
                . " FROM `digest` `p`"
                . " LEFT JOIN `site_digest_langs` `pl` ON `p`.`id` = `pl`.`post_id`"
                . " LEFT JOIN `site_langs` `l` ON `pl`.`lang_id` = `l`.`id`"
                . " LEFT JOIN `site_digest_categories` `c` ON `p`.`category_id` = `c`.`id`"
                . " WHERE `pl`.`lang_id` = '" . $lang . "'"
                . " ORDER BY `p`.`created` DESC";
        $res = $this->cms->query($sql, array('digest','site_digest_langs'));

        return @mysqli_num_rows($res);
    }

    public function GetAllDigestCategories($lang)
    {
        $lang = $this->cms->_getLangCodeId($lang);
        $categories = array();
        $q = 'SELECT `id`,`alias`,`title`,`description` FROM `site_digest_categories` WHERE `lang_id` = "'.$lang.'"';
        $r = $this->cms->query($q,array('site_digest_categories'));
        while ($row = mysqli_fetch_assoc($r)) {
            $categories[$row['alias']]['id'] = $row['id'];
            $categories[$row['alias']]['cid'] = $row['alias'];
            $categories[$row['alias']]['title'] = strip_tags($row['title']);
            $categories[$row['alias']]['description'] = strip_tags($row['description']);
        }
        foreach ($categories as $key => &$category) {
            $q = 'SELECT count(*) FROM `digest` where `category_id`="' . $category['id'] .'";';
            $category['count'] = $this->cms->querySingleValue($q);
        }

        return $categories;
    }

    public function GetCategoryById($cid)
    {
        $category = array();
        $q = "SELECT * FROM `site_digest_categories` WHERE `id`='".$this->cms->escape($cid)."'";
        $result = $this->cms->queryMakeArray($q,array('site_digest_categories'));
        return $result;
    }

    public function GetCategoryByAlias($alias)
    {
        $category = array();
        $q = "SELECT * FROM `site_digest_categories` WHERE `alias`='".$this->cms->escape($alias)."'";
        $result = $this->cms->queryMakeArray($q,array('site_digest_categories'));
        return $result;
    }

    public function DeletePostByID($id)
    {
        $q = 'DELETE FROM `digest` WHERE `id` = "'.$this->cms->escape($id).'"';
        $r = $this->cms->query($q,array('digest'));
        $q = 'DELETE FROM `site_dgest_langs` WHERE `post_id` = "'.$this->cms->escape($id).'"';
        $r = $this->cms->query($q,array('site_digest_langs'));
    }

    public function CreatePost($data)
    {
        $fullDate = '';
        if (! empty($data['date'])) {
            $fullDate = strtotime($data['date']) ? date('Y-m-d', strtotime($data['date'])) : date('Y-m-d');
        }
        $fullDate = $fullDate ? $fullDate : date('Y-m-d');
        $langid = $this->cms->_getLangCodeId($data['lang']);
        $this->cms->checkTable('site_digest_langs');
        $this->cms->query('INSERT INTO `digest` (`title`, `category_id`, `image`, `content`, `created`) VALUES ( "' . $this->cms->escape($data['title']) . '", "' . $this->cms->escape($data['category']) . '", "' . $data['image'] . '", "' . $this->cms->escape($data['content']) . '", "' . $fullDate . '" )');
        $pid = $this->cms->insertedId();
        $this->cms->query('INSERT INTO `site_digest_langs` (`lang_id`, `post_id` ) VALUES ( "'.$this->cms->escape($langid).'", "'.$this->cms->escape($pid).'" )');

        return $pid;
    }

    public function UpdatePostByID($id, $data)
    {
        $fullDate = '';
        if (! empty($data['date'])) {
            $fullDate = strtotime($data['date']) ? date('Y-m-d', strtotime($data['date'])) : date('Y-m-d');
        }
        $fullDate = $fullDate ? $fullDate : date('Y-m-d');
        $langid = $this->cms->_getLangCodeId($data['lang']);
        $this->cms->query('UPDATE `digest` SET `title`= "'.$this->cms->escape($data['title']).'", `category_id`= "'.$this->cms->escape($data['category']).'", `image` = "'.$this->cms->escape($data['image']). '",`content` = "'.$this->cms->escape($data['content']).'" ,`created` = "' . $fullDate . '" WHERE `id` = "'.$this->cms->escape($id).'"',array('site_digest_langs'));

        $q = 'SELECT COUNT(*) FROM site_digest_langs WHERE `lang_id`="'.$this->cms->escape($langid).'" AND `post_id`="'.$this->cms->escape($id).'"';
        $c = $this->cms->querySingleValue($q);
        if ($c) {
            $this->cms->query('UPDATE `site_digest_langs` SET `lang_id` = "'.$this->cms->escape($langid).'" WHERE `post_id` = "'.$this->cms->escape($id).'"');
        } else {
            $this->cms->query('INSERT INTO `site_digest_langs` (`lang_id`, `post_id` ) VALUES ( "'.$this->cms->escape($langid).'", "'.$this->cms->escape($id).'" )');
        }
    }

    public function GetPagesByLang($lang)
    {
        $q = 'SELECT DISTINCT `p`.* FROM `pages` `p`
            LEFT JOIN `site_pages_langs` `pl`
                ON `p`.`id`=`pl`.`page_id`
            LEFT JOIN `site_langs` `l`
                ON `pl`.`lang_id` = `l`.`id`
            WHERE `l`.`lang_code`="'.$this->cms->escape($lang).'"';
        $r = $this->cms->query($q,array('site_pages_langs'));
        $pages = array();
        while ($p = mysqli_fetch_assoc($r)) {
            if (isset($p['title'])) {
                $p['title'] = strip_tags($p['title']);
            }
            if (isset($p['content'])) {
                $p['content'] = strip_tags($p['content']);
            }
            if (isset($p['created'])) {
                $p['created'] = new DateTime($p['created']);
            }
            $pages[] = $p;
        }
        return $pages;
    }

    public function CreateDigestCategory($title, $description, $lang)
    {
        $lang_id = $this->cms->_getLangCodeId($lang);
        $q = 'INSERT INTO `site_digest_categories` SET
            `title`="'.$this->cms->escape($title).'",
            `alias`="'.$this->cms->escape(TextHelper::translitСonverter($title)).'",
            `description`="'.$this->cms->escape($description).'",
            `lang_id`="'.$lang_id.'"';
        $r = $this->cms->query($q,array('site_digest_categories'));
        return $this->cms->insertedId();
    }

    public function UpdateDigestCategory($title, $description, $lang, $id)
    {
        $lang_id = $this->cms->_getLangCodeId($lang);
        $q = 'UPDATE `site_digest_categories` SET
            `title`="'.$this->cms->escape($title).'",
            `alias`="'.$this->cms->escape(TextHelper::translitСonverter($title)).'",
            `description`="'.$this->cms->escape($description).'",
            `lang_id`="'.$lang_id.'"
            WHERE `id`="'.$this->cms->escape($id).'"';
        $r = $this->cms->query($q,array('site_digest_categories'));
    }

    public function DeleteDigestCategory($id)
    {
        $q = 'DELETE FROM `site_digest_categories` WHERE `id`="'.$this->cms->escape($id).'"';
        $r = $this->cms->query($q,array('site_digest_categories'));
    }

    public function GetPostByID($id)
    {
        $q = 'SELECT `p`.*, `l`.`lang_code`, `l`.`id` as `lang_id` FROM `digest` `p`
            LEFT JOIN `site_digest_langs` `pl`
                ON `p`.`id`=`pl`.`post_id`
            LEFT JOIN `site_langs` `l`
                ON `pl`.`lang_id` = `l`.`id`
            WHERE `p`.`id` = "'.$this->cms->escape($id).'"
            ';
        $r = $this->cms->query($q);
        $post = false;
        if ($r)
            if ($row = mysqli_fetch_assoc($r)) {
                $post = $row;
                $post['title'] = str_replace(array("\'", '\"', '\\\\'), array("'", '"', '\\'), $post['title']);
                $post['category_id'] = str_replace(array("\'", '\"', '\\\\'), array("'", '"', '\\'), $post['category_id']);
                $post['image'] = str_replace(array("\'", '\"', '\\\\'), array("'", '"', '\\'), $post['image']);
                $post['content'] = str_replace(array("\'", '\"', '\\\\'), array("'", '"', '\\'), $post['content']);
                $post['created'] = new DateTime($post['created']);
            }
            
        if ($post && ! empty($post['alias'])) {
            $r = $this->cms->query("
                SELECT * FROM `site_pages_langs_data`
                WHERE `p`='" . $post['alias'] . "'
                AND `lang_id`='" . $post['lang_id'] . "' AND type='post'");
            if ($r && mysqli_num_rows($r)) {
                $row = mysqli_fetch_assoc($r);
                $post['pagetitle'] = $row['pagetitle'];
                $post['seo_title'] = $row['seo_title'];
                $post['seo_keywords'] = $row['seo_keywords'];
                $post['seo_description'] = $row['seo_description'];
            }
        }            
        return $post;
    }

    public function getLangCode($lang)
    {
        $q = 'SELECT `lang_code` FROM `site_langs` WHERE `id`="'.$this->cms->escape($lang).'"';
        return $this->cms->querySingleValue($q,array('site_digest_categories'));
    }
    
    public static function getImage($image, $size) {
		return str_replace("thumb", $size, $image);
	}

    /**
     * Вернет массив [$id, $needRedirect], если $needRedirect = true,
     * то $id будет содержать Url для редиректа
     *
     * @param $alias
     * @return array
     */
	public function parseIdFromAlias($alias)
    {
        if (!General::IsFeatureEnabled('Seo2')) {
            return [$alias, false];
        }
        $id = $this->cms->getBlogPostIdByAlias($alias);
        if (empty($id)) {
            $redirectUrl = UrlGenerator::getUrl('digest');
            return [$redirectUrl, true];
        }

        return [$id, false];
    }
}
