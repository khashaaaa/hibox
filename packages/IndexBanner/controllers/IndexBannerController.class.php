<?php

class IndexBannerController extends IndexController
{
    public function renderIndexBannerAction()
    {
        $banners = $this->getBanners();
        return General::viewFetch('view/renderBanner', array(
            'path' => dirname(dirname(__FILE__)),
            'vars' => array(
                'banners' => $banners
            )));
    }
    
    /**
     * @return array
     */
    public function getBanners()
    {
        try {
            $sql = 'SELECT * from index_banner';

            $result = $this->cms->queryMakeArray($sql);

            return $result;
        } catch (ServiceException $e) {
            print_r($e);
        }
    }
}