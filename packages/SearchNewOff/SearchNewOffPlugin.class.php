<?php

OTBase::import('system.lib.GeneralPlugin');

class SearchNewOffPlugin extends GeneralPlugin
{
    /**
     * @param RequestWrapper $request
     */
    public function renderPluginPage($request)
    {
        $config = simplexml_load_file(dirname(__FILE__) . '/config/config.xml');
        return (string)$config->description;
    }

    public static function onAfterGeneralInit($vars = array())
    {
        OTBase::import('system.packages.SearchNewOff.controllers.*');
    }

    public static function onBuildRules($baseRules)
    {
        $baseRules['search'] = 'search-old/search';
        $baseRules['vendor'] = 'search-old/vendor';
        $baseRules['brand'] = 'search-old/search';
        $baseRules['category'] = 'search-old/category';
        $baseRules['subcategory'] = 'search-old/subcategory';
        $baseRules['category/<alias>'] = 'search-old/category';
        $baseRules['subcategory/<alias>'] = 'search-old/subcategory';
        return $baseRules;
    }
}
