<?php

OTBase::import('system.lib.CMS');
OTBase::import('system.lib.General');
OTBase::import('system.lib.repository.SiteConfigurationRepository');
OTBase::import('system.config.config');

abstract class ThemeDesignPlugin
{
    protected $designThemeName;
    protected $designThemeFilesPath;
    protected $designThemeZipPath;
    protected $pluginFilesPath;
    protected $pluginZipPath;
    protected $configFilePath;
    protected $cms;
    protected $siteConfig;
    protected $defaultContentLanguages = ['ru', 'en'];

    abstract public function __construct();

    public function installPlugin()
    {
        return $this->extractTheme();
    }

    public function updatePlugin()
    {
        return $this->extractTheme();
    }

    public function deletePlugin()
    {
        $this->deactivatePlugin();
        General::rrmdir($this->designThemeFilesPath);
        return true;
    }

    public function activatePlugin()
    {
        $this->cms = new CMS();
        $this->siteConfig = new SiteConfigurationRepository($this->cms);
        $this->siteConfig->Set('design_theme', $this->designThemeName);
        return true;
    }

    public function deactivatePlugin()
    {
        if (General::getConfigValue('design_theme') === $this->designThemeName) {
            $this->cms = new CMS();
            $this->siteConfig = new SiteConfigurationRepository($this->cms);
            $this->siteConfig->Set('design_theme', General::$baseTheme);
        }

        return true;
    }

    private function createDefaultContent()
    {
        global $otapilib;
        $CMS = new CMS();
        $contentsProvider = new ContentsProvider($CMS, $otapilib);

        $this->createDefaultPages($contentsProvider, $CMS);
        $this->createDefaultNews($contentsProvider, $CMS);
        $this->checkCalculatorPage($contentsProvider, $this->defaultContentLanguages);

        return true;
    }

    private function createDefaultNews(ContentsProvider $contentsProvider, CMS $CMS)
    {
        $news = $this->requireDataFile('/defaultNews.php');

        if (!$news || empty($news)) {
            return false;
        }

        $ruCreatedNews = $contentsProvider->getAllNews('ru');
        $enCreatedNews = $contentsProvider->getAllNews('en');

        if ($ruCreatedNews['count'] && $enCreatedNews['count']) {
            return false;
        }

        $daySeconds = 86400; // 60 * 60 * 24 (1 day);
        $timestamp = time() - (count($news) * $daySeconds);
        foreach ($news as $newsPost) {
            if (${$newsPost['language'] . 'CreatedNews'}['count']) {
                continue;
            }

            $id = $contentsProvider->createNews(
                $newsPost['title'],
                $newsPost['preview'],
                $newsPost['content'],
                $newsPost['image'],
                $newsPost['language']
            );

            $date = date('Y-m-d H:m:s', $timestamp);
            $timestamp += $daySeconds;
            $sql = "UPDATE `news` SET `created`='$date' WHERE `id`='$id';";
            $CMS->query($sql);
        }

        return true;
    }

    private function createDefaultPages(ContentsProvider $contentsProvider, CMS $CMS)
    {
        $pages = $this->requireDataFile('/defaultPages.php');

        if (!$pages || empty($pages)) {
            return false;
        }

        $pagesWithParent = array();
        $contentRepository = new ContentRepository($CMS);
        foreach ($pages as $page) {
            $existingPage = $contentRepository->GetPageByAlias($page['alias'], $page['lang']);
            $pageId = $existingPage['id'] ?: $contentsProvider->addPage($page['alias'], $page['title'], 0, $page['title_h1'], 1);

            $contentsProvider->setPageContent($pageId, $page['content']);
            $langId = $contentsProvider->setPageLang($pageId, $page['lang']);
            $contentsProvider->setPageData($langId, $page['alias'], $page['title'], '', '');

            // родителей страниц проставляем отдельным циклом
            if (! empty($page['parent'])) {
                $page['idInDB'] = $pageId;
                $pagesWithParent[] = $page;
            }

            // добавление страницы в меню
            if (! empty($page['menu_type'])) {
                $contentsProvider->addPageToMenu($pageId, $page['menu_type']);
            }
        }

        // добавление страниц в родительскую
        foreach ($pagesWithParent as $page) {
            $parentPageId = $contentsProvider->getPageIdByAlias($page['parent'], $page['lang']);
            $contentsProvider->setPageParent($page['idInDB'], $parentPageId);
        }

        return true;
    }

    private function checkCalculatorPage(ContentsProvider $contentsProvider, $languages)
    {
        if (! is_array($languages) || empty($languages)) {
            return false;
        }

        foreach ($languages as $language) {
            // создаем несозданные сервисные страницы, что бы добавить калькулятор в меню
            $contentsProvider->checkServicePages($language);
            $calculatorPage = $contentsProvider->getPageIdByAlias('calculator', $language);

            if (! empty($calculatorPage)) {
                $contentsProvider->addPageToMenu($calculatorPage, 'top_menu_' . $language);
            }
        }
    }

    private function extractTheme()
    {
        $zipPath = $this->designThemeZipPath;
        $zip = new ZipArchive();
        if ($zip->open($zipPath) === true) {
            $zip->extractTo($this->designThemeFilesPath);
            $zip->close();
            unlink($zipPath);
            return true;
        }
        return false;
    }

    private function requireDataFile($path)
    {
        $path = $this->pluginFilesPath . $path;
        if (! file_exists($path)) {
            return false;
        }

        return require_once $path;
    }

    public function renderPluginPage()
    {
        return General::viewFetch('views/index', array('path' => $this->pluginFilesPath));
    }

    public function requestAdmin()
    {
        return $answer = $this->createDefaultContent();
    }
}