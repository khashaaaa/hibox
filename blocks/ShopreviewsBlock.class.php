<?php

class ShopreviewsBlock extends GenerateBlock
{
    protected $_cache = false;
    protected $_life_time = 3600;
    protected $_template = 'shopreviewsblocknew';
    protected $_template_path = '/main/';

    private $ShopReviews;

    public function __construct()
    {
        parent::__construct(true);
        $this->ShopReviews = new ShopReviewsRepository(new CMS());

    }

    protected function setVars()
    {
        $limit = General::getConfigValue('shopreviews_main', 5);
        $allPosts = $this->ShopReviews->GetReviews(0, $limit);
        $this->tpl->assign('allPosts', is_array($allPosts) && isset($allPosts['rows']) ? $allPosts['rows'] : array());
    }
}
