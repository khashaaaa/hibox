<?php

class NewsController extends GeneralContoller
{

    public function defaultAction()
    {
        if (! CMS::IsFeatureEnabled('News')) {
            return $this->renderNotFoundPage();
        }

        $news = array();
        try {
            $crumbs = array();
            array_unshift($crumbs, array('title' => Lang::get('Service_page_news_title'), 'url' => UrlGenerator::toRoute('allnews')));
            array_unshift($crumbs, array('title' => Lang::get('home'), 'url' => UrlGenerator::getHomeUrl()));
            CrumbsController::setCrumbs($crumbs);
            
            $news = $this->cms->GetAllNews();
            $GLOBALS['pagetitle'] = Lang::get('news');
        } catch (Exception $e) {
            $this->errorHandler->registerError($e);
        }

        return $this->render('controllers/news/list', [
            'news' => $news
        ]);
    }
    
    public function showNewAction()
    {
        if (! CMS::IsFeatureEnabled('News')) {
            return $this->renderNotFoundPage();
        }

        $new = array();
        $lastNews = array();
        $newsRepository = new NewsRepository($this->cms);
        try {
            $new = $this->cms->GetNewsById($this->request->getValue('id'));
            $lastNews = $newsRepository->getNews(Session::getActiveLang(), 0, General::getConfigValue('news_count_print', 3));
            General::$_page['title'] = Lang::get('news') . ' - ' . $new['title'];
            $this->definePageOg($new);
            $crumbs = array();
            array_unshift($crumbs, array('title' => $new['title']));
            array_unshift($crumbs, array('title' => Lang::get('Service_page_news_title'), 'url' => UrlGenerator::toRoute('allnews')));
            array_unshift($crumbs, array('title' => Lang::get('home'), 'url' => UrlGenerator::getHomeUrl()));
            CrumbsController::setCrumbs($crumbs);
        } catch (Exception $e) {
            $this->errorHandler->registerError($e);
        }
        return $this->render('controllers/news/view', [
            'new' => $new,
            'lastNews' => $lastNews
        ]);
    }
    
    public function renderLastNewsAction()
    {
        if (! CMS::IsFeatureEnabled('News')) {
            return '';
        }
        $news = array();
        try {
            $newsRepository = new NewsRepository($this->cms);
            $news = $newsRepository->getNews(Session::getActiveLang(), 0, General::getConfigValue('news_count_print', 3));
        } catch (Exception $e) {
            $this->errorHandler->registerError($e);
        }
        return $this->renderPartial('controllers/news/last', [
            'news' => $news,
        ]);
    }
    
    private function definePageOg($new)
    {
        if (!empty($new['title'])){
            General::$_page['og']['title'] = $new['title'];
        }
        if (!empty($new['brief'])){
            General::$_page['og']['description'] = substr(strip_tags($new['brief']), 0, 160);
        }
        if (!empty($new['image'])){
            General::$_page['og']['image'] = UrlGenerator::getHomeUrl() . $new['image'];
        }
    }
}
