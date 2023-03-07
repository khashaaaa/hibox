<?php

class ShopReviewsRepository extends Repository
{
    public function GetReviews($from, $perpage = 20, $isActive = true)
    {
        $reviews = array('count' => 0, 'rows' => array());

        $activreCondition = $isActive ? " WHERE `accepted` = '1'" : "";
        $this->cms->checkTable('shop_reviews');

        $reviews['count'] = $this->cms->querySingleValue("SELECT COUNT(*) FROM `shop_reviews`");

        if($reviews['count'] == 0){
            return $reviews;
        }

        $from = (int)$from;
        $perpage = (int)$perpage;

        $result = $this->cms->queryMakeArray("SELECT * FROM `shop_reviews` " . $activreCondition . " ORDER BY `created` DESC LIMIT ".$from." , ".$perpage."");
        foreach ($result as &$item) {
            $preparedDate = explode(' ', $item['created']);
            $item['created'] = $preparedDate[0];
            $preparedDate = explode(' ', $item['answer_date']);
            $item['answer_date'] = $preparedDate[0];
        }
        $reviews['rows'] = $result;
        return $reviews;
    }

    public function GetCount()
    {
        $result = $this->cms->querySingleValue("SELECT COUNT(*) FROM `shop_reviews`");
        return $result;
    }

    public function GetModeratedCount()
    {
        $result = $this->cms->querySingleValue("SELECT COUNT(*) FROM `shop_reviews` WHERE `accepted` = '1'");
        return $result;
    }

    public function AddReview($data)
    {
        if (User::getObject()->getLastName() || User::getObject()->getFirstName()) {
            $name = trim(User::getObject()->getLastName().' '.User::getObject()->getFirstName());
        } else {
            $name = User::getObject()->getLogin();
        }
        if (strlen($data['txt']) > 0) {
            $this->cms->query("INSERT INTO `shop_reviews` (`review_id`, `name`, `text`, `answer`, `accepted`, `created`,`rating`) VALUES (NULL, '".$this->cms->escape($name)."', '".$this->cms->escape($data['txt'])."', '', '0', CURRENT_TIMESTAMP, '0')");
            return true;
        } else {
            return false;
        }

    }

    public function activateComment($id)
    {
        $this->cms->query("UPDATE  `shop_reviews` SET  `accepted` = 1  WHERE  `review_id` =".$this->cms->escape($id)."");
    }

    public function removeComment($id)
    {
        $this->cms->query("DELETE FROM `shop_reviews` WHERE  `review_id` =".$this->cms->escape($id)."");
    }

    public function SetAnswerReview($id, $text)
    {
        $this->cms->query("UPDATE  `shop_reviews` SET  `answer_date` = CURRENT_TIMESTAMP, `answer` =  '".$this->cms->escape($text)."' WHERE  `review_id` =".$this->cms->escape($id)."");
    }

    public function SetRatingReview($mark, $id)
    {
        $result = $this->cms->queryMakeArray("SELECT * FROM `shop_reviews` WHERE `review_id` = ".$this->cms->escape($id)."");
        $mark=='minus' ? $tmp=-1 : $tmp=1;
        $new_rate = $result[0]['rating'] +  $tmp;
        $res = $this->cms->query("UPDATE  `shop_reviews` SET  `rating` =  '".$new_rate."' WHERE  `review_id` =".$this->cms->escape($id)."");
        $shopRating = Session::get('shop_rating', []);
        $shopRating[] = $id;
        Session::set('shop_rating', $shopRating);
    }



}
