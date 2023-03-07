<?php

class GeneralUtil implements IAdminUtil{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = ''; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = ''; //- путь к шаблону
    protected $tpl;

    public $cms;
    public $cmsStatus;

    public function defaultAction(){
    }

    public function __construct(){
        $HSTemplate_options = array(
            'template_path' => TPL_DIR,
            'cache_path'    => TPL_DIR . 'cache',
            'debug'         => false,
        );
        $HSTemplate = new HSTemplate($HSTemplate_options);
        $this->tpl = $HSTemplate->getDisplay($this->_template, true);

        $this->cms = new CMS();
        $this->cmsStatus = $this->cms->Check();
    }

    public function checkAuth(){
        $login = Login::auth();
        if(!$login || Session::isSessionExpired()){
            Session::clearError();
            header('Location: index.php?cmd=login');
            return false;
        }
        return true;
    }
    
    public function linkCms(){
        $this->cms = new CMS();
        $status = $this->cms->Check();
        if (!$status){
            include(TPL_DIR . 'cms.php');
            die();
        }
    }

    public function setErrorAndRedirect($error, $redirectUrl){
        $_SESSION['error'] = $error;
        header('Location: '.$redirectUrl);
        return false;
    }

    public function getTemplateInfo(){
        $info = new stdClass();
        $info->template = $this->_template;
        $info->template_path = $this->_template_path;
        return $info;
    }

    public function fetchTemplate(){
        $tpl = TPL_DIR . $this->_template_path;
        $this->assign('header');
        $this->assign('footer');
        $this->tpl->addTemplate($this->_template, $this->_template.'.html', $tpl);
        return $this->tpl->fetch();
    }

    public function throwAjaxError($e){
        $errorCode = $e instanceof ServiceException ? $e->getErrorCode() : $e->getCode();
        header('HTTP/1.1 500 ' . $errorCode);
        die($e->getMessage());
    }

    private function assign($part){
        ob_start();
        require TPL_ABSOLUTE_PATH.$part.'.php';
        $header = ob_get_contents();
        $this->tpl->assign(ucfirst($part), $header);
        ob_end_clean();
    }
    public function getActiveLanguages()
    {
        global $otapilib;
        if (!$this->checkAuth()) return false;

        $sid = $_SESSION['sid'];
        $result = $otapilib->GetWebUISettings($sid);
        if ($result === false)
            return $this->setErrorAndRedirect($otapilib->error_message, 'index.php?sid=' . $GLOBALS['dssid'] . '&cmd=category');

        $allLanguages = array();
        foreach ($result->Settings->Languages->NamedProperty as $l)
            $allLanguages[(string)$l->Name] = (string)$l->Description;

        $languages = array();
        foreach ($result->Settings->UsedLanguages->string as $l)
            $languages[(string)$l] = $allLanguages[(string)$l];

        return $languages;
    }

    /**
     * Генерит блок.
     *
     * @param bool $args
     * @return string
     */
    public function Generate($args = false)
    {
        if (! $this->tpl->isCached())
        {
            if (method_exists($this, 'setVars'))
            {
                $this->setVars($args);
            }
            $tpl = CFG_APP_ROOT . '/templates' . $this->_template_path;
            $this->tpl->addTemplate($this->_template, $this->_template . '.html', $tpl);
        }
        return $this->tpl->fetch();
    }

    public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES);
    }
}

?>
