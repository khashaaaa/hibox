<?php

/**
 * Check page alias string
**/
class BlogPostAliasString extends NotEmptyString implements IRule 
{
    protected $message;
    private $contentsProvider;
    private $language;
    private $postId;
    
    
    public function __construct($contentsProvider, $language, $postId)
    {
        $this->contentsProvider = $contentsProvider;
        $this->language = $language;
        $this->postId = $postId;
    }

    public function test($value)
    {
        if (! parent::test($value)) {
            return false;
        }
    
        $found = $this->contentsProvider->getBlogPostIdByLangAlias($value, $this->language);
        if (($found && $found == $this->postId) || ! $found) {
            return true;
        }
        return false;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
