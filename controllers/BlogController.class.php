<?php

class BlogController extends GeneralContoller
{

    public function __construct()
    {
        parent::__construct();
        $this->layout = 'twoColumn';
    }

    public function defaultAction()
    {
        if (! CMS::IsFeatureEnabled('Digest')) {
            return $this->renderNotFoundPage();
        }

        $blog = array();
        $blogCategories = array();

        try {
            $page = $this->request->getValue('page', 1) - 1;
            $from = $page * General::getConfigValue('blog_posts', 10);
            $blogRepository = new DigestRepository($this->cms);
            $blogCategories = $blogRepository->GetAllDigestCategories(Session::getActiveLang());
            $currentCategory = array();
            if ($this->request->getValue('cat')) {
                $currentCategory = $blogRepository->GetCategoryByAlias($this->request->getValue('cat'));
                $blog = $blogRepository->GetPostsByCat($currentCategory[0]['id'], $from, General::getConfigValue('blog_posts', 10));
		$countPosts = $blogRepository->GetCountPostsByCat($currentCategory[0]['id']);
                $this->setSeoData($currentCategory[0]['title'], '', '');
            } else {
                $blog = $blogRepository->GetPostsByLang(Session::getActiveLang(), $from, General::getConfigValue('blog_posts', 10));
                $countPosts = $blogRepository->GetCountPostsByLang(Session::get('active_lang'));
                $this->setSeoData(Lang::get('digest'), '', '');
            }
            
            $crumbs = array();
            if (! empty($currentCategory[0])) {
                array_unshift($crumbs, array('title' => $currentCategory[0]['title'], 'url' => UrlGenerator::generateDigestUrl('digest', $currentCategory[0]['alias'])));
            }
            
            array_unshift($crumbs, array('title' => Lang::get('Service_page_digest_title'), 'url' => UrlGenerator::toRoute('digest')));
            array_unshift($crumbs, array('title' => Lang::get('home'), 'url' => UrlGenerator::getHomeUrl()));
            CrumbsController::setCrumbs($crumbs);
        } catch (Exception $e) {
            $this->errorHandler->registerError($e);
        }
        return $this->render('controllers/blog/list', [
            'currentCategory' => ! empty($currentCategory[0]) ? $currentCategory[0] : false,
            'blog' => $blog,
            'blogCategories' => $blogCategories,
            'paginator' => new Paginator($countPosts, $page+1, General::getConfigValue('blog_posts', 10))
        ]);
    }

    public function showPostAction($alias = null)
    {
        if (! CMS::IsFeatureEnabled('Digest')) {
            return $this->renderNotFoundPage();
        }
        if ($alias == null) {
            $alias = $this->request->request('id');
        } else {
            $alias = urldecode($alias);
        }

        if (!CMS::IsFeatureEnabled('Seo2')) {
            $id = $this->request->request('id');
        } else {
            $blogRepository = new DigestRepository($this->cms);
            list($id, $needRedirect) = $blogRepository->parseIdFromAlias($alias);
            if ($needRedirect) {
                RequestWrapper::LocationRedirect($id);
            }
        }

        $post = array();
        $blogCategories = array();
        $lastBlogs = array();

        try {
            $blogRepository = new DigestRepository($this->cms);
            $post = $blogRepository->GetPostById($id);
            $category = $blogRepository->GetCategoryById($post['category_id']);
            $category = isset($category[0]) ? $category[0] : null;

            if(!empty($category['title'])) {
                $post['category_name'] = $category['title'];
                $post['category_alias'] = $category['alias'];
            }
            $blogCategories = $blogRepository->GetAllDigestCategories(Session::getActiveLang());
            $lastBlogs = $blogRepository->GetPostsByLang(Session::getActiveLang(), 0, General::getConfigValue('blog_posts_index', 3));
            $this->setSeoData(
                empty($post['pagetitle']) ? Lang::get('post') . ' - ' . $post['title'] : $post['pagetitle'],
                empty($post['seo_keywords']) ? '' : $post['seo_keywords'],
                empty($post['seo_description']) ? '' : $post['seo_description']
            );

            $crumbs = array();
            array_unshift($crumbs, array('title' => $post['title']));
            if(!empty($category['title'])) {
                array_unshift($crumbs, array('title' => $post['category_name'], 'url' => UrlGenerator::toRoute('digest', ['cat' => $category['alias']])));
            }
            array_unshift($crumbs, array('title' => Lang::get('Service_page_digest_title'), 'url' => UrlGenerator::toRoute('digest')));
            array_unshift($crumbs, array('title' => Lang::get('home'), 'url' => UrlGenerator::getHomeUrl()));
            CrumbsController::setCrumbs($crumbs);
        } catch (Exception $e) {
            $this->errorHandler->registerError($e);
        }
        $this->definePageOg($post);
        return $this->render('controllers/blog/view', [
            'post' => $post,
            'blogCategories' => $blogCategories,
            'lastBlogs' => $lastBlogs
        ]);
    }
    
    public function renderLastBlogAction()
    {
        if (! CMS::IsFeatureEnabled('Digest')) {
            return '';
        }

        $blog = array();
        try {
            $blogRepository = new DigestRepository($this->cms);
            $blog = $blogRepository->GetPostsByLang(Session::getActiveLang(), 0, General::getConfigValue('blog_posts_index', 3));
        } catch (Exception $e) {
            $this->errorHandler->registerError($e);
        }
        return $this->renderPartial('controllers/blog/last', [
            'blog' => $blog,
        ]);
    }
    
    private function setSeoData($title, $keywords, $description)
    {
        if ($title) General::$_page['title'] = $title;
        if ($keywords) General::$_page['seo_keywords'] = $keywords;
        if ($description) General::$_page['seo_description'] = $description;
    }
    
    private function definePageOg($post)
    {
        if (!empty($post['title'])) {
            General::$_page['og']['title'] = $post['title'];
        }
        if (!empty($post['brief'])) {
            General::$_page['og']['description'] = substr(strip_tags($post['brief']), 0, 160);
        }
        if (!empty($post['image'])) {
            General::$_page['og']['image'] = UrlGenerator::getHomeUrl() . $post['image'];
        }
    }
}
