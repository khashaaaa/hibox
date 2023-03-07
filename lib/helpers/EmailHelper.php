<?php

OTBase::import('system.lib.HSTemplate');

class EmailHelper
{
    static $instance;

    private $templatePath = '/notify/';
    private $templater;
    private $templaterOptions;
    private $view;

    private function __construct()
    {
        $this->templaterOptions = array(
            'template_path' => defined('CFG_BASE_TPL_ROOT') ? CFG_BASE_TPL_ROOT : CFG_APP_ROOT . '/templates',
            'cache_path'    => CFG_APP_ROOT . '/' . (defined('CFG_CUSTOM_CACHE_DIR') ? CFG_CUSTOM_CACHE_DIR : 'cache'),
            'debug'         => false,
        );
        $this->templater = new HSTemplate($this->templaterOptions);
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            $class = __CLASS__;
            self::$instance = new $class();
        }
        return self::$instance;
    }

    public function generate()
    {
        return $this->view->fetch();
    }

    public function setData(array $data)
    {
        foreach ($data as $key => $value) {
            $this->view->assign($key, $value);
        }
    }

    public function setTemplate($templateName)
    {
        $fullTemplatePath = $this->templaterOptions['template_path'] . $this->templatePath;
        if (! file_exists($fullTemplatePath . $templateName . '.html')) {
            throw new Exception('Template "' . $templateName . '" not found in specified templates directories.');
        }
        $this->view = $this->templater->getDisplay($templateName, true);
        $this->view->addTemplate($templateName, $templateName . '.html', $fullTemplatePath);
    }
}
