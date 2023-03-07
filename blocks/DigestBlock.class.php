<?php

class DigestBlock extends GenerateBlock
{
    protected $_cache = false;
    protected $_life_time = 3600;
    protected $_template = 'digestblocknew';
    protected $_template_path = '/main/';
    
    /**
     * @var digest
     */
    protected $digest;
    
    public function __construct()
    {
        parent::__construct(true);
        $this->digest = new DigestRepository(new CMS());    
    }

    protected function setVars()    {
        $allPosts = -1;

        try {
            if (CMS::IsFeatureEnabled('Digest')) {
                $count = General::getConfigValue('blog_posts_index', 3);
                if ($count) {
                    $allPosts = $this->digest->GetPostsByLang(Session::get('active_lang'), 0, $count);
                }
            }
        } catch (DBException $e) {
            Session::setError($e->getMessage(), 'DBError');                
        }
        
        $this->tpl->assign('digestBlock', $allPosts);        
    }
    
}

?>
