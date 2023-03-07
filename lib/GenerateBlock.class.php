<?php
class GenerateBlock
{
    //в наследнике должны быть определены следующие свойства

    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = ''; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = ''; //- путь к шаблону
    protected $_hash = '';
    protected $defaultAction = null;

    /**
     * @var HSTemplateDisplay
     */
    protected $tpl;

    /**
     * @var OTAPIlib
     */
    protected $otapilib;

    /**
     * @var OrdersProxy
     */
    protected $ordersProxy;

    /**
     * @var CMS
     */
    protected $cms;

    /**
     * @var RequestWrapper
     */
    protected $request;

    /**
     * @var FileAndMysqlMemoryCache
     */
    protected $fileMysqlMemoryCache;

    private $action;

    /**
     * Конструктор класса.
     *
     */
    public function __construct()
    {
        $this->otapilib = new OTAPIlib();
        $this->cms = new CMS();
        $this->cms->Check();

        $this->ordersProxy = new OrdersProxy($this->otapilib);

        $this->request = new RequestWrapper();

        $this->fileMysqlMemoryCache = new FileAndMysqlMemoryCache($this->cms);

        $this->initTemplate();
        if ((isset($this->_cache)) && ($this->_cache == true))
        {
            $this->initCache();
        }
    }
    
    protected function getCacher()
    {
        return $this->fileMysqlMemoryCache;
    }

    public function setTemplate($templateName)
    {
        $this->_template = $templateName;
        return true;
    }

    protected function initCache()
    {
        if (! $this->otapilib->error_message) {
            $this->tpl->setCache($this->_template . $this->_hash, $this->_life_time);
        }
    }

    protected function initTemplate()
    {
        global $HSTemplate;
        $this->tpl = $HSTemplate->getDisplay($this->_template, true);
    }

    /**
     * Генерит блок для странички.
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

            $tpl = CFG_BASE_TPL_ROOT . $this->_template_path;

            if (General::getConfigValue('use_custom_view', 1)) {
                if (!General::getConfigValue('is_old_platform') && file_exists(General::getThemeDir() . '/templatescustom' . $this->_template_path . $this->_template . '.html')) {
                    $tpl = General::getThemeDir() . '/templatescustom' . $this->_template_path;
                } elseif (file_exists(CFG_TPL_ROOT . $this->_template_path . $this->_template . '.html')) {
                    $tpl = CFG_TPL_ROOT . $this->_template_path;
                }
            }
            $this->tpl->addTemplate($this->_template, $this->_template . '.html', $tpl);
        }
        if ((isset($this->_cache)) && ($this->_cache == false))
        {
            $this->tpl->unsetCache($this->_template . $this->_hash);
        }
        return trim($this->tpl->fetch());
    }

    protected function setVars()
    {
        $action = $this->getAction() . 'Action';
        if (method_exists($this, $action)) {
            try {
                if (! $this->_template) {
                    $this->_template = strtolower($this->getAction());
                }
                if (! $this->_template_path) {
                    $this->_template_path = '/' . strtolower(get_called_class()) . '/';
                }
                $this->$action($this->request);
            } catch (Exception $e) {
                throw new Exception($e->getMessage(), $e->getCode(), $e);
            }
        } else {
            throw new Exception('Unknown action "' . $action . '" requested in class "' . get_called_class() . '"');
        }
    }

    protected function getAction()
    {
        if (! $this->action) {
            $action = RequestWrapper::getUriPart(1);
            if (! $action) {
                $action = RequestWrapper::request('action', $this->defaultAction);
            }
            $this->action = $action;
        }
        return $this->action;
    }

    
    public function sendAjaxResponse(array $response = array(), $checkForOpera = false, $supressDebug = false)
    {
        if (OTBase::isTest()) {
            $response['debugLog'] = [
                'title' => '',
                'body' => Debugger::getRender(),
            ];
        }

        $errors = ErrorHandler::getErrors();
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        General::storeRequestGroup();
        if ($checkForOpera && (BrowserHelper::isOpera() && !BrowserHelper::isJsonAcceptable())) {
            header('Content-Type: text/plain; charset=utf-8');
        } else {
            header('Content-Type: application/json; charset=utf-8');
        }

        echo json_encode($response);
        die();
    }
    
    /**
     * @param ServiceException $e
     */
    public function throwAjaxError($e){
        if ($e instanceof ServiceException) {
            header('HTTP/1.1 500 ' . $e->getErrorCode());
            if ($e->getErrorCode() == 'NotAvailable') {
                $error = Lang::get('NotAvailable');
            } else {
                $error = $e->getErrorMessage();
            }
        } else {
            header('HTTP/1.1 500 Internal Site Error');
            $error = $e->getMessage();
        }
        die($error);
    }

    public function redirect($url)
    {
        header('Location: ' . $url);
        die();
    }
}
