<?php

OTBase::import('system.lib.ThemeDesignPlugin');

class ThemeDesignElasticPlugin extends ThemeDesignPlugin
{
    public function __construct()
    {
        $this->designThemeName = 'elastic';
        $this->pluginFilesPath = dirname(__FILE__);
        $this->pluginZipPath = $this->pluginFilesPath . '/ThemeDesignElastic.zip';
        $this->designThemeFilesPath = CFG_APP_ROOT . '/themes/' . $this->designThemeName;
        $this->designThemeZipPath = $this->pluginFilesPath . '/ThemeDesignElasticViews.zip';
        $this->configFilePath = $this->pluginFilesPath . '/config/config.xml';
    }
}