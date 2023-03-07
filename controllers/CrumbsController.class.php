<?php

class CrumbsController extends GeneralContoller
{
    private static $crumbs = array();

    public function defaultAction()
    {
        $crumbs = $this->getCrumbs();

        return $this->renderPartial('controllers/crumbs/crumbs', [
            'crumbs' => $crumbs,
        ]);
    }

    private function getCrumbs()
    {
        if (! empty(self::$crumbs)) {
            return self::$crumbs;
        }

        $contentRepository = new ContentRepository($this->cms);
        $crumbs = array();

        $curentAlias = General::getScriptName();
        // TODO: проверяя страницу в списке сервисных мы делаем лишних sql запрос, можно избежать дублирования
        $contentRepository->checkIsServicePage($curentAlias);
        $page = $contentRepository->GetPageByAlias($curentAlias);
        if ($page === false) {
            return self::$crumbs;
        }

        // добавляем ссылку на страницу
        array_unshift($crumbs, array('title' => $page['title']));

        // добавляем к крошки родителей страницы
        while (($parentId = $contentRepository->getPageParentId($page['id'])) !== false) {
            $page = $contentRepository->GetPageByID($parentId);
            array_unshift($crumbs, array('title' => $page['title'], 'url' => UrlGenerator::toRoute($page['alias'])));
        }

        // добавляем ссылку на главную
        array_unshift($crumbs, array('title' => Lang::get('home'), 'url' => UrlGenerator::getHomeUrl()));

        self::$crumbs = $crumbs;
        return self::$crumbs;
    }

    public static function setCrumbs($crumbs)
    {
        self::$crumbs = $crumbs;
    }
    
    public static function generateCrumbsByRootPath($rootPath, $addLinkToEndCrumb = false)
    {
        $crumbs = array();
    	UrlGenerator::addCategoriesForWarmup($rootPath->GetItem());
    	foreach ($rootPath->GetItem() as $p => $path) {
            $crumbs[] = array(
                'title' => $path->GetName(), 
                'url' => UrlGenerator::generateSearchUrlByParams(array('cid' => $path->GetId()))
            );
    	}
    	$crumbs[] = array('title' => Lang::get('home'), 'url' => UrlGenerator::getHomeUrl());
        
        if (!$addLinkToEndCrumb && isset($crumbs[0]['url'])) { 
            unset($crumbs[0]['url']); 
        }
                
        $crumbs = array_reverse($crumbs);
        
    	return $crumbs; 
    }
}
