<?php


class CategoriesMenuUpdater
{
    CONST DEFAULT_SETS_TTL = 86400; // 1 день (60*60*24);
    CONST INCREASE_SETS_TTL = 604800; // 7 дней (60*60*24*7);

    /** @var CMS */
    private $cms;

    /** @var FileAndMysqlMemoryCache */
    private $fileMysqlMemoryCache;

    /**
     * @var SetsUpdater
     */
    protected static $_instance;

    private function __construct() {
        $this->cms = General::getCms();
        $this->fileMysqlMemoryCache = new FileAndMysqlMemoryCache($this->cms);
    }

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    private function __clone() {
    }

    private function __wakeup() {
    }

    public function generateData($language)
    {
        Otapilib2::GetThreeLevelRootCategoryInfoList($language, $rootCats);
        Otapilib2::makeRequests();

        $rootCats = $rootCats->GetCategoryInfoList()->GetContent()->GetItem()->toArray();
        UrlGenerator::addCategoriesForWarmup($rootCats);
        UrlGenerator::warmupCategoryAlias();

        $rootCats = $this->generateCategoryArray($rootCats);
        $treeCats = $this->generateCategoriesTree($rootCats);

        $cacheKey = $this->getCacheKey($language);
        $this->fileMysqlMemoryCache->AddCacheEl($cacheKey, self::INCREASE_SETS_TTL, json_encode($treeCats)); /* время жизни 7 дней */
    }

    public function getData($language)
    {
        $cacheKey = $this->getCacheKey($language);

        if (! $this->fileMysqlMemoryCache->Exists($cacheKey)) {
            throw new TemporarilyUnavailableException();
        }

        return json_decode($this->fileMysqlMemoryCache->GetCacheEl($cacheKey), true);
    }

    private function getCacheKey($lang)
    {
        return 'Menu:categoriesMenu_' . $lang;
    }

    private function generateCategoryArray($rootCats)
    {
        $categories = [];

        if (! empty($rootCats)) {
            foreach ($rootCats as $id => $cat) {
                $category = [];
                $category['Id'] = $cat->GetId();
                $category['Name'] = $cat->GetName();
                $category['Image'] = (string) $cat->GetIconImageUrl();
                $category['ParentId'] = (string) $cat->GetParentId() ? $cat->GetParentId() : 0;
                $category['Url'] = UrlGenerator::generateSearchUrlByParams([
                    'cid' => $cat->GetId(),
                    'OtapiCategory' => $cat
                ]);
                foreach ($cat->GetMetaData()->GetItem() as $dataItem) {
                    $category[$dataItem->GetNameAttribute()] = $dataItem->asString();
                }

                $categories[] = $category;
            }
        }

        return $categories;
    }

    private function generateCategoriesTree($rootCats)
    {
        $tree = [];
        $sub = [0 => &$tree];

        foreach ($rootCats as $cat) {
            $id = $cat['Id'];
            $parent = $cat['ParentId'];

            $branch = &$sub[$parent];
            if (! empty($cat['ParentId'])) {
                $branch['children'][$id] = $cat;
                $sub[$id] = &$branch['children'][$id];
            } else {
                $branch[$id] = $cat;
                $sub[$id] = &$branch[$id];
            }
        }

        return $tree;
    }
}