<?php

class ItemInfoRepository extends Repository
{
    public function saveComment($text, $itemCid, $username, $id)
    {
        $text = trim($text);
        if (empty($text)) {
            return false;
        }
        $sql = 'INSERT INTO `reviews`
                SET `name`="' . $this->cms->escape($username) . '",
                    `item_id`="' . $this->cms->escape($id) . '",
                    `category_id`="' . $this->cms->escape($itemCid) . '",
                    `text`="' . $this->cms->escape($text) . '"';
        $this->cms->query($sql);

        return true;
    }

    public function getItemComments($itemId, $categoryId = null)
    {
        $categoryCondition = '';
        if (! is_null($categoryId)) {
            $categoryCondition = " AND `category_id` = '" . $this->cms->escape($categoryId) . "'";
        }

        $query = 'SELECT * FROM `reviews` WHERE `item_id` = "' . $this->cms->escape($itemId) . '" ' . $categoryCondition;
        $reviews = $this->cms->queryMakeArray($query, array('reviews'));
        foreach ($reviews as &$review) {
            $review['created'] = strtotime($review['created']);
            $review['created'] = date('d.m.Y', $review['created']);
        }

        return $reviews;
    }


    public function getNotAcceptedComments($from = 0, $perpage = 16)
    {
        $this->cms->checkTable('reviews');
        $query = 'SELECT * FROM `reviews` WHERE `accepted`=0 order by created desc limit ' . $this->cms->escape($from) . ',' . $this->cms->escape($perpage);
        $comments = $this->cms->queryMakeArray($query);

        return $comments;
    }

    public function getNumberOfComments()
    {
        $this->cms->checkTable('reviews');
        $query = 'SELECT count(review_id) as COUNT FROM `reviews` WHERE `accepted`=0';
        $r = $this->cms->queryMakeArray($query);
        $result = isset($r[0]['COUNT']) ? $r[0]['COUNT'] : 0;

        return $result;
    }

    public function acceptComment($id)
    {
        if (! is_int($id)) {
            return false;
        }
        $query = 'UPDATE `reviews` SET accepted=1 WHERE `review_id`="' . $this->cms->escape($id) . '"';
        $this->cms->queryMakeArray($query);
    }

    public function deleteComment($id)
    {
        if (! is_int($id)) {
            return false;
        }
        $query = 'DELETE FROM `reviews` WHERE `review_id`="' . $this->cms->escape($id) . '"';
        $this->cms->queryMakeArray($query);
    }

    public function getItemsComments(array $itemsIds)
    {
        $itemsIds = array_map(
            array($this, 'escape'),
            array_filter($itemsIds, function ($a) {
                return is_numeric($a);
            })
        );

        if (empty($itemsIds)) {
            return array();
        }
        $query = 'SELECT * FROM `reviews` WHERE `item_id` IN ("' . implode('", "', $itemsIds) . '") ORDER BY `created` DESC';
        $comments = $this->cms->queryMakeArray($query, array('reviews'));

        return $comments;
    }
}
