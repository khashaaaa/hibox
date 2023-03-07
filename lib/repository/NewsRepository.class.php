<?php

class NewsRepository extends Repository
{
    public function getNews($language, $from, $count)
    {
        $this->cms->checkTable('news');
        $this->cms->checkTable('site_news_langs');
        $news = $this->cms->queryMakeArray('
            SELECT DISTINCT `p`.*, `l`.`lang_name`,`l`.`lang_code`
            FROM `news` `p`
            LEFT JOIN `site_news_langs` `pl`
                ON `p`.`id`=`pl`.`news_id`
            LEFT JOIN `site_langs` `l`
                        ON `pl`.`lang_id` = `l`.`id` WHERE `l`.`lang_code` = "' . $language . '"
                        ORDER BY `p`.`created` DESC LIMIT ' . $from . ' , ' . $count . '
            ');
        return $news;
    }
}
