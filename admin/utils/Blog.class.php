<?php

OTBase::import('system.uploader.php.UploadHandler');
OTBase::import('system.lib.Validation.*');
OTBase::import('system.lib.Validation.Rules.*');


class Blog extends GeneralUtil
{
    protected $_template = 'blog';
    protected $_template_path = 'blog/';
    protected $contentsProvider;

    public function __construct()
    {
        parent::__construct(true);
        $this->contentsProvider = new ContentsProvider($this->cms, $this->getOtapilib());
    }

    public function defaultAction($request)
    {
        $this->_template = 'blog';
        $sid = Session::get('sid');
        $posts = array('count' => 0, 'rows' => array());
        try {
            $page = $this->getPageDisplayParams($request);
            
            $perpage = $page['limit'];
            $pageNum = $page['number'];
            $from = $page['offset'];
            
            //get selected language
            $current_lang = Session::get('active_lang_' . strtolower($request->get('cmd')));
            
            //get posts
            $posts = $this->contentsProvider->getBlogPosts($current_lang, $from, $perpage);
            foreach ($posts['rows'] as $key => &$value) {
                $value['category'] = $this->contentsProvider->GetBlogCategoryById($value['category_id']);
                if (is_array($value['category']) && count($value['category']) > 0) {
                    $value['category'] = $value['category'][0];
                } else {
                    $value['category'] = '';
                }
            }
        } catch (Exception $e) {
            ErrorHandler::registerError($e);
        }

        $this->tpl->assign('posts', $posts);
        $this->tpl->assign('paginator', new Paginator($posts['count'], $pageNum, $perpage));
        print $this->fetchTemplate();
    }

    function addBlogPostAction($request)
    {
        $this->_template_path = 'blog/blog/';
        $this->_template = 'crud';
        $pages = array();
        $languages = array();
        $categories = array();
        $language = Session::get('active_lang_' . strtolower($request->get('cmd')));
        if (empty($language)) {
            $language = Session::getActiveLang();
            Session::set('active_lang_contents', $language);
        }
        
        $sid = Session::get('sid');
        try {
            $languages = $this->contentsProvider->GetLanguageInfoList();
            $categories = $this->contentsProvider->GetAllBlogCategories($language);
        } catch (Exception $e) {
            ErrorHandler::registerError($e);
        }

        $this->tpl->assign('categories', $categories);
        $this->tpl->assign('languages', $languages);
        $this->tpl->assign('language', $language);

        print $this->fetchTemplate();
    }
    
    public function saveBlogPostAction($request) {
        $sid = Session::get('sid');
        try {
            $id = intval($request->getValue('id'));
            $title = $request->getValue('post-title');
            $content = $request->getValue('post-content');
            $preview = $request->getValue('post-preview');
            $alias = $request->getValue('alias');
            
            $pageTitle = $request->getValue('page-title');
            $pageKeywords = $request->getValue('page-keywords');
            $pageDescription = $request->getValue('page-description');
            
            $lang = $request->getValue('post-language');
            $created = $request->getValue('post-date-display');
            $image = $request->getValue('image');
            $categoryId = $request->getValue('post-category');
             
            //validate
            $validator = new Validator(array(
                'post-title' => trim($title),
                'post-content' => trim($content),
                'alias' => trim($alias),
            ));
    
            $validator->addRule(new NotEmptyString(), 'post-content', LangAdmin::get('contents::Content_cannot_be_empty'));
            $validator->addRule(new NotEmptyString(), 'post-title', LangAdmin::get('Title_cannot_be_empty'));
            $validator->addRule(new NotEmptyString(), 'alias', LangAdmin::get('contents::Alias_cannot_be_empty'));
            $validator->addRule(new BlogPostAliasString($this->contentsProvider, $lang, $id), 'alias', LangAdmin::get('contents::Alias_already_in_use'));
            $validator->addAliasStringValidator('alias', LangAdmin::get('Alias_is_invalid'));

            if (! $validator->validate()) {
                $this->respondAjaxError($validator->getErrors());
            }
                    
            //add new if need
            if ( !$id) {
                $id = $this->contentsProvider->CreatePost($title, $categoryId, '', $content, $created, $lang, $preview, $alias);
            }
    
            //file
            $newImage = $this->getNameUploadBlogImage($id);
            if ($newImage) {
                $image = $newImage;
            }
    
            //update
            settype($id, 'int');
            $this->contentsProvider->UpdatePostByID($id, $title, $categoryId, $image, $content, $created, $lang, $preview, $alias);
            
            if (! empty($alias)) {
                $langId = $this->cms->_getLangCodeId($lang);
                $this->contentsProvider->setPageData($langId, $alias, $pageTitle, $pageKeywords, $pageDescription, 'post');
            }
            
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array('result' => 'ok'), true);
    }
    

    function editBlogPostAction($request)
    {
        $this->_template_path = 'blog/blog/';
        $this->_template = 'crud';
        $pages = array();
        $languages = array();
        $categories = array();
        $sid = Session::get('sid');
        $id = 0;
        $language = Session::get('active_lang_' . strtolower($request->get('cmd')));
        if (empty($language)) {
            $language = Session::getActiveLang();
            Session::set('active_lang_contents', $language);
        }

        $post = array('lang_code' => $language );
        try {
            $id = intval($request->getValue('id'));
            $post = $this->contentsProvider->GetPostByID($id);
            $post['created'] = date('j.m.Y', $post['created']->getTimestamp());
            if (isset($post['brief'])) {
                $post['brief'] = $this->contentsProvider->tinyMceEntityFix($post['brief']);
            }
            $post['content'] = $this->contentsProvider->tinyMceEntityFix($post['content']);
            $languages = $this->contentsProvider->GetLanguageInfoList();
            $categories = $this->contentsProvider->GetAllBlogCategories($language);
        } catch (Exception $e) {
            ErrorHandler::registerError($e);
        }

        $this->tpl->assign('categories', $categories);
        $this->tpl->assign('languages', $languages);
        $this->tpl->assign('post', $post);
        $this->tpl->assign('id', $id);

        print $this->fetchTemplate();
    }

    private function uploadBlogImage($id)
    {
        $uploader = new UploadHandler(array(
            'param_name' => 'new_image',
            'image_versions' => array(
                'large' => array(
                    'max_width' => 1200,
                    'max_height' => 800,
                    'jpeg_quality' => 95,
                    'name' => 'large'
                ),
                'big' => array(
                    'max_width' => 300,
                    'max_height' => 200,
                    'jpeg_quality' => 95,
                    'name' => 'big'
                ),
                'thumb' => array(
                    'max_width' => 400,
                    'max_height' => 400,
                    'jpeg_quality' => 90,
                    'name' => 'thumb'
                )
            ),
        ), false, null, '/uploaded/blogs/' . $id . '/');
        return $uploader->post(false);
    }
    
    private function getNameUploadBlogImage($id)
    {
        $imageUrl = '';
        if (! empty($_FILES['new_image']['tmp_name'])) {
            $uploadResult = $this->uploadBlogImage($id);
            if (isset($uploadResult['new_image'][0])) {
                if (isset($uploadResult['new_image'][0]->url)) {
                    $path_info = pathinfo($uploadResult['new_image'][0]->url);
                    $imageUrl = '/uploaded/blogs/' . $id . '/thumb.' . $path_info['extension'];
                } else if (isset($uploadResult['new_image'][0]->error)) {
                    $this->respondAjaxError($uploadResult['new_image'][0]->error);
                }
            } else {
                $this->respondAjaxError('Unknown error occured while uploading image. Try again.');
            }
        } else {
            $imageUrl = '';
        }
        return $imageUrl;
    }
    
    public function deleteBlogPostAction($request)
    {
        try {
            $blogId = intval($request->getValue('id'));
            $this->contentsProvider->DeletePostByID($blogId);
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array('result' => 'ok'), true);
    }
    
    public function deleteCategoryAction($request)
    {
        try {
            $id = intval($request->getValue('id'));
            $this->contentsProvider->DeleteBlogCategory($id);
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array('result' => 'ok'), true);
    }

    public function saveCategoryAction($request)
    {
        try {
            $id = intval($request->getValue('id'), 0);
            $name = $request->getValue('name');
            $description = $request->getValue('description');
            $language = $request->getValue('language');
            
            if ($id) {
                $this->contentsProvider->UpdateBlogCategory($name, $description, $language, $id);
            } else {
                $id = $this->contentsProvider->CreateBlogCategory($name, $description, $language);
            }
            
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array('result' => 'ok', 'id' => $id), true);
    }

    public function getProductAction($request)
    {
        $html = '';
        try {            
            $confId = false;
            $id = $this->prepareItemId($request, $confId);
            $language = $request->getValue('language');
            $fulliteminfo = $this->contentsProvider->GetItemFullInfo($id, $language);            
            if ($fulliteminfo) {
                $price = $fulliteminfo['ConvertedPrice'];
                if ($confId) {
                    if (array_key_exists('Promotions', $fulliteminfo) && array_key_exists('ConfiguredItems', $fulliteminfo['Promotions'])) {
                        foreach ($fulliteminfo['Promotions']['ConfiguredItems'] as $key => $value) {
                            if ($value['Id'] == $confId) {
                                $price = $value['Price']['ConvertedPrice'];
                            }
                        }    
                    } else {
                        if (array_key_exists('item_with_config', $fulliteminfo) && array_key_exists($confId, $fulliteminfo['item_with_config'])) {
                            $price = $fulliteminfo['item_with_config'][$confId]['convertedprice'];
                        }
                    }
                } 
                
                $html = '<table align="center" border="0" cellpadding="0" cellspacing="0" style="width:100%; " width="624"><tr>
                <td><p align="center"><a href="index.php?p=item&id=' . $fulliteminfo['id'] . '">' . $fulliteminfo['title'] . '</a></p></td>
                </tr><tr>
                <td><p align="center"><a href="index.php?p=item&id=' . $fulliteminfo['id'] . '"><img src="' . $fulliteminfo['mainpictureurl'] . '" width="310px" height="310px"></a></p></td>
                </tr><tr>
                <td><p align="center"><strong>' . $price . '</strong></p></td>
                </tr></table>';
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e->getMessage());
        }
        $this->sendAjaxResponse(array(
            'content' => $html
        ));
    } 
    
    
    private function prepareItemId($request, &$confId)
    {
        $confId = false;
        $item = $request->getValue('id');
        if (preg_match( '/('.UrlGenerator::getProtocol().')/i', $item )) {
            $url = parse_url($item);                    
            $params = array();
            if (array_key_exists('fragment', $url)) {
                $confId = htmlspecialchars($url['fragment']);
            }
            if (isset($url['query'])) {
                parse_str($url['query'], $params);
                if (isset($params['id'])) {
                    $return = htmlspecialchars($params['id']);
                } else {
                    throw new Exception(LangAdmin::get('Item_url_is_invalid'));
                }
            } else {
                throw new Exception(LangAdmin::get('Item_url_is_invalid'));
            }
        } else {
            $return = htmlspecialchars($item);
        }

        $validator = new Validator(array(
            'itemId' => $return
        )); 
        $validator->addRule(new NotEmptyString(), 'itemId', LangAdmin::get('Item_id_is_invalid'));
        if (! $validator->validate()) {
            $this->respondAjaxError($validator->getErrors());
        }
        return $return;
    }
}
