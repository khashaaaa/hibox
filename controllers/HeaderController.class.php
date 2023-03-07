<?php

class HeaderController extends GeneralContoller
{

    public function __construct()
    {
        parent::__construct(true);
    }

    public function defaultAction()
    {
        if (!Session::getActiveLang()) {
            Session::setActiveLang('ru');
        }

        $isIndexPage = false;
        if ($this->request->get('p', 'index') == 'index') {
            $isIndexPage = true;
        }

        return $this->renderPartial('main/header', [
            'controller' => $this->request->get('p', 'index'),
            'topMenu' => $this->getTopMenu(),
            'langs' => @$GLOBALS['langs'],
            'isIndexPage' => $isIndexPage
        ]);
    }

    private function getTopMenu()
    {
        $lang = Session::getActiveLang();
        $cacheKey = "Menu:top_menu_" . $lang;
        // проверяем наличие кеша
        if ($this->fileMysqlMemoryCache->Exists($cacheKey)) {
            $menu = json_decode($this->fileMysqlMemoryCache->GetCacheEl($cacheKey), true);
        } else {
            $menu = $this->getMenu('top_menu', $lang, array(
                'defaultItem' => array('how_to_order'),
            ));

            // Добавить в начало пункт "Главная"
            array_unshift($menu, [
                'alias' => 'index',
                'title' => Lang::get('home'),
                'url' => '/',
            ]);

            // сохраняем значение в кеше
            $this->fileMysqlMemoryCache->AddCacheEl($cacheKey, 86400, json_encode($menu));
        }

        return $menu;
    }

    public function renderHeadAction()
    {
        list($title, $description, $keywords) = $this->getSeoData($this->request);

        return $this->renderPartial('main/head', [
            'controller' => $this->request->get('p', 'index'),
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
        ]);
    }

    /**
     * @param RequestWrapper $request
     */
    private function getSeoData($request)
    {
        $title = '';
        $description = '';
        $keywords = '';

        if (General::isSellFree()) {
            $defaultLogo = UrlGenerator::getHomeUrl() . '/i/sellfree-logo.png';
        } else {
            $defaultLogo = UrlGenerator::getHomeUrl() . '/i/logo.png';
        }

        $controller = $request->get('p', 'index');
        if (
            ! empty(General::$_page['title']) ||
            ! empty(General::$_page['seo_description']) ||
            ! empty(General::$_page['seo_keywords'])
        ) {
            if (! empty(General::$_page['pagetitle'])) {
                $title = General::$_page['pagetitle'];
            } elseif (! empty(General::$_page['title'])) {
                $title = General::$_page['title'];
            } else {
                $title = General::getConfigValue('site_name', CFG_SITE_NAME);
            }
            $description = (!empty(General::$_page['seo_description'])) ? General::$_page['seo_description'] : '';
            $keywords = (!empty(General::$_page['seo_keywords'])) ? General::$_page['seo_keywords'] : '';
        } elseif ($controller == 'index') {
            $titleItems = array(Lang::get('home'), General::getConfigValue('site_name', CFG_SITE_NAME));
            $title = General::getConfigValue('title_for_home',  $this->generatePageTitle($titleItems));
            $description = General::getConfigValue('description_for_home', @$GLOBALS['page']['seo_description']);
            $keywords = General::getConfigValue('keywords_for_home', @$GLOBALS['page']['seo_keywords']);
        } elseif (! empty($GLOBALS['page']) && isset($GLOBALS['page']['pagetitle'])) {
            $titleItems = array($GLOBALS['page']['pagetitle'], General::getConfigValue('site_name', CFG_SITE_NAME));
            $title = $this->generatePageTitle($titleItems);
            $description = $this->escape($GLOBALS['page']['seo_description']);
            $keywords = $this->escape($GLOBALS['page']['seo_keywords']);
        } elseif(@$GLOBALS['category']) {
            $prefix = General::getConfigValue('category_prefix');
            $suffix = General::getConfigValue('category_suffix');
            if(! empty($GLOBALS['category']['seo_title']) && ($GLOBALS['category']['seo_title'] != '||')) {
                list($prefix, $suffix) = explode('||', $GLOBALS['category']['seo_title']);
            }
            if (! empty($GLOBALS['category']['pagetitle'])) {
                $title = $GLOBALS['category']['pagetitle'];
            } else {
                $title = @$GLOBALS['pagetitle'];
            }
            $title = $prefix . ' ' . $title . ' ' . $suffix;

            $description = @$GLOBALS['category']['seo_description']?$GLOBALS['category']['seo_description']:@$GLOBALS['pagetitle'];
            $keywords = @$GLOBALS['category']['seo_keywords']?$GLOBALS['category']['seo_keywords']:@$GLOBALS['pagetitle'];
        } elseif (! empty($GLOBALS['brands_seo'])) {
            $prefix = General::getConfigValue('brand_prefix');
            $suffix = General::getConfigValue('brand_suffix');
            if (@$GLOBALS['brands_seo']['seo_title']) {
                list($prefix, $suffix) = explode('||', $GLOBALS['brands_seo']['seo_title']);
            } elseif(General::getConfigValue('site_name')) {
                $prefix = General::getConfigValue('site_name');
            }
            $title = @$GLOBALS['brands_seo']['pagetitle']?@$GLOBALS['brands_seo']['pagetitle']:@$GLOBALS['pagetitle'];
            $title = $prefix . ' ' . $title . ' ' . $suffix;

            $description = @$GLOBALS['brands_seo']['seo_description']?$GLOBALS['brands_seo']['seo_description']:@$GLOBALS['pagetitle'];
            $keywords = @$GLOBALS['brands_seo']['seo_keywords']?$GLOBALS['brands_seo']['seo_keywords']:@$GLOBALS['pagetitle'];
        } elseif (! empty($GLOBALS['recom_seo'])) {
            $prefix = General::getConfigValue('category_prefix');
            $suffix = General::getConfigValue('category_suffix');
            if(@$GLOBALS['recom_seo']['title']) {
                $title = $this->escape($prefix." ".@$GLOBALS['recom_seo']['title']." ".$suffix);
            } else {
                $title = Lang::get('recommendations');
            }

            $description = @$GLOBALS['recom_seo']['meta_description'];
            $keywords = @$GLOBALS['recom_seo']['meta_keywords'];
        } elseif(@$_GET['cid']) {
            $prefix = General::getConfigValue('category_prefix');
            $suffix = General::getConfigValue('category_suffix');
            $title = $prefix . ' ' . $GLOBALS['pagetitle'] . ' ' . $suffix;

            $description = $this->escape(@$GLOBALS['pagetitle']);
            $keywords = $this->escape(@$GLOBALS['pagetitle']);
        } elseif(@$_GET['p']=='news' || @$_GET['p']=='allnews') {
            $titleItems = array(Lang::get('news'), General::getConfigValue('site_name', CFG_SITE_NAME));
            $title = $this->generatePageTitle($titleItems);
        } elseif(in_array(@$_GET['p'], array('calculator', 'sitemap', 'brands'))) {
            $titleItems = array(Lang::get(@$_GET['p']), General::getConfigValue('site_name', CFG_SITE_NAME));
            $title = $this->escape($this->generatePageTitle($titleItems));
        } elseif(@$_GET['p'] == 'vendor') {
            $titleItems = array(General::getConfigValue('site_name', CFG_SITE_NAME) , @$_GET['id']);
            $title = $this->escape($this->generatePageTitle($titleItems));
        } else {
            $titleItems = array(@$GLOBALS['pagetitle'], General::getConfigValue('site_name', CFG_SITE_NAME));
            $title = $this->escape($this->generatePageTitle($titleItems));

            $description = (empty($GLOBALS['seo_description'])) ? $this->escape(@$GLOBALS['pagetitle']) : $GLOBALS['seo_description'];
            $keywords = (empty($GLOBALS['seo_keywords'])) ? $this->escape(@$GLOBALS['pagetitle']) : $GLOBALS['seo_keywords'];
        }
        if (empty(General::$_page['og']['image'])) {
            General::$_page['og']['image'] = General::getConfigValue('logo', $defaultLogo, false);
        }

        $title = !empty($title) ? $title : General::getConfigValue('site_name');

        return [$title, $description, $keywords];
    }

    private function generatePageTitle($items)
    {
        $title = array();
        foreach ($items as $item) {
            if ($item) {
                $title[] = $item;
            }
        }

        return implode (' - ', $title);
    }

    public function renderSearchBarAction()
    {
        $isIndexPage = false;
        if ($this->request->get('p', 'index') == 'index') {
            $isIndexPage = true;
        }

        // получаем список провайдеров доступных для поиска
        $instanceProvider = InstanceProvider::getObject();
        try {
            $providers = $instanceProvider->getAvailableProviders(Session::getActiveLang());
            $isLimitItemsByCatalog = $instanceProvider->isLimitItemsByCatalog();
        } catch (Exception $e) {
            $providers = array();
            $isLimitItemsByCatalog = false;
            $this->errorHandler->registerError($e);
        }

        return $this->renderPartial('main/header/search-bar', [
            'isIndexPage' => $isIndexPage,
            'providers' => $providers,
            'isLimitItemsByCatalog' => $isLimitItemsByCatalog
        ]);
    }
}
