<?php

/**
 * Check page alias string
**/
class PageAliasString extends NotEmptyString implements IRule 
{
    protected $message;
    private $contentsProvider;
    private $language;
    private $pageId;
    
    public function __construct($contentsProvider, $language, $pageId)
    {
        $this->contentsProvider = $contentsProvider;
        $this->language = $language;
        $this->pageId = $pageId;
    }

    public function test($value)
    {
        if (! parent::test($value)) {
            return false;
        }
    
        $found = $this->contentsProvider->getPageIdByAlias($value, $this->language);
        if (($found && $found == $this->pageId) || ! $found) {
            return true;
        }
        return false;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
