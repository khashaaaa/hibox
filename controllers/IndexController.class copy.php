<?php

class IndexController extends GeneralContoller
{
    public function defaultAction()
    {
        $this->layout = 'main';

        $this->definePageOg();
        return $this->render('controllers/index/content');
    }

    public function renderSections1Action()
    {
        // определяем массив элементов в левой колонке
        $blocks = array(
            'slider' => array('controller' => 'Index', 'action' => 'renderSlider')
        );

        return $this->renderPartial('controllers/index/render-sections-1', [
            'sections' => $this->getSections($blocks)
        ]);
    }

    public function renderSections2Action()
    {
        // определяем массив элементов в нижней колонке
        $blocks = array(
            //'social-network' => array('controller' => 'Index', 'action' => 'renderSocialBlock'),
            'news' => array('controller' => 'News', 'action' => 'renderLastNews'),
            'blog' => array('controller' => 'Blog', 'action' => 'renderLastBlog'),         
            'shop-comments' => array('controller' => 'ShopComments', 'action' => 'renderLastComments')
        );

        return $this->renderPartial('controllers/index/render-sections-2', [
            'sections' => $this->getSections($blocks)
        ]);
    }

    private function getSections(array $blocks)
    {
        $sections = array();
        foreach ($blocks as $key => $value) {
            $sections[$key] = General::runController($value['controller'], $value['action']);
        }

        return $sections;
    }

    public function renderSliderAction()
    {
        $banners = array();
        try {
            $BannersRepository = new BannerRepository($this->cms);
            $banners = $BannersRepository->GetBanners(Session::getActiveLang());
        } catch (Exception $e) {
            $this->errorHandler->registerError($e);
        }

        return $this->renderPartial('controllers/index/banners', [
            'banners' => $banners,
        ]);
    }

    public function renderSocialBlockAction()
    {
        return $this->renderPartial('controllers/index/social-block');
    }
    
    private function definePageOg()
    {
        if (General::isSellFree()) {
            $defaultLogo = UrlGenerator::getHomeUrl() . '/i/sellfree-logo.png';
        } else {
            $defaultLogo = UrlGenerator::getHomeUrl() . '/i/logo.png';
        }
        
        General::$_page['og']['title'] = UrlGenerator::getHomeUrl();
        if (!empty(General::$_page['seo_description'])){
            General::$_page['og']['description'] = General::$_page['seo_description'];
        }
        General::$_page['og']['image'] = General::getConfigValue('logo', $defaultLogo, false);
    }
}
