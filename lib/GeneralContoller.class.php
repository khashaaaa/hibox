<?php

OTBase::import('system.lib.Cache');
OTBase::import('system.lib.cache.Key');
OTBase::import('system.lib.cache.adapter.*');

class GeneralContoller
{
    protected $_cache = false; // кэшируем или нет.
    protected $_life_time = 3600; // время на которое будем кешировать

    protected $layout = 'oneColumn';

    /**
     * @var View
     */
    private $view;

    /**
     * @var CMS
     */
    protected $cms;

    /**
     * @var OTAPILib
     */
    protected $otapilib;

    /**
     * @var ErrorHandler
     */
    protected $errorHandler;

    /**
     * @var RequestWrapper
     */
    protected $request;

    /**
     * @var UrlWrapper
     */
    protected $baseUrl;

    /**
     * @var fileMysqlMemoryCache
     */
    protected $fileMysqlMemoryCache;

    public function __construct()
    {
        $this->view = new View();
        $this->initOTAPILib();
        $this->cms = General::getCms();
        $this->errorHandler = new ErrorHandler();
        $this->request = new RequestWrapper();

        $this->baseUrl = new UrlWrapper();
        $this->baseUrl->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");

        $this->fileMysqlMemoryCache = new FileAndMysqlMemoryCache($this->cms);
    }

    private function initOTAPILib()
    {
        global $otapilib;
        $this->otapilib = $otapilib;
        $this->otapilib->setErrorsAsExceptionsOn();
    }

    public function respondAjaxError($e)
    {
        $response = array(
            'error' => 1,
        );
        if (is_array($e)) {
            $response['errors'] = $e;
        } elseif ($e instanceof Exception) {
            $response['message'] = $e->getMessage();
        } else {
            $response['message'] = $e;
        }

        if (OTBase::isTest()) {
            $response['debugLog'] = [
                'title' => '',
                'body' => Debugger::getRender(),
            ];
        }

        header('Content-type: application/json');
        echo json_encode($response);
        die();
    }

    public function sendAjaxResponse(array $response = array(), $checkForOpera = false)
    {
        $errors = ErrorHandler::getErrors();
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        General::storeRequestGroup();

        if (OTBase::isTest()) {
            $response['debugLog'] = [
                'title' => '',
                'body' => Debugger::getRender(),
            ];
        }

        // При загрузке файла аяксом Опера не отправляет заголовок application/json
        if ($checkForOpera && (BrowserHelper::isOpera() && !BrowserHelper::isJsonAcceptable())) {
            header('Content-Type: text/plain; charset=utf-8');
        } else {
            header('Content-Type: application/json; charset=utf-8');
        }

        echo json_encode($response);
        die();
    }

    public function throwAjaxError($e, $code = 500)
    {
        $errorCode = $e instanceof ServiceException ? $e->getErrorCode() : ($e->getCode() ? $e->getCode() : 'Internal error');
        header('HTTP/1.1 '. $code .' ' . $errorCode);
        die($e->getMessage());
    }

    public function redirect($url)
    {
        if (RequestWrapper::isAjax()) {
            return $this->sendAjaxResponse(['redirect' => $url]);
        }
        header('Location: ' . $url);
        die();
    }

    public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES);
    }

    /**
     * Возращает меню элементов из таблиц site_blocks и pages
     *
     * @param $type
     * @param $lang
     * @param array $params
     * @return array
     */
    public function getMenu($type, $lang, $params = [])
    {
        $activePageAlias = $this->request->getValue('p');
        $menu = array();

        // получаем страницы из настроек или берем значение по умолчанию
        $pageIds = $this->cms->getBlock($type . '_' . $lang);
        if ($pageIds) {
            $pageIds = json_decode($pageIds);
            $pageIds = CMS::removeNotAvailableMenuItems($pageIds);
        } else {
            $pageIds = isset($params['defaultItem']) ? $params['defaultItem'] : array();
        }

		$cRep = new ContentRepository($this->cms);
        foreach ($pageIds as $id) {
            $active = 0;
            $page = [];
            $page['id'] = $id;
            // если это страница контента
            if (is_numeric($id)) {
                $page = $cRep->GetPageByID($id);
                if (!$page) {
                    continue;
                }
            } else {
                $page['alias'] = $id;
                $page['title'] = Lang::get($id);
            }

            list($activeChild, $page['children']) = $this->getChildrenPages($page, $activePageAlias);

            $page['url'] = UrlGenerator::generateContentUrl($page['alias']);

            if ($page['alias'] == $activePageAlias) {
                $active = 1;
            }
            $page['active'] = $active;
            $page['hasActiveChildren'] = $activeChild;
            $menu[] = $page;
        }

        return $menu;
    }

    private function getChildrenPages($parentPage, $activePageAlias) {
        $cRep = new ContentRepository($this->cms);
        $active = 0;
        $childrenPages = [];

        if ($parentPage['id'] == 'digest') {
            $digestRepository = new DigestRepository($this->cms);
            $allCats = $digestRepository->GetAllDigestCategories(Session::get('active_lang'));
            foreach ($allCats as $cat) {
                $activeChild = 0;
                if ($cat['cid'] == $activePageAlias) {
                    $active = 1;
                    $activeChild = 1;
                }
                $child = [];
                $child['alias'] = $cat['cid'];
                $child['active'] = $activeChild;
                $child['title'] = $cat['title'] . ' ('. $cat['count'] .')';
                $child['url'] = UrlGenerator::generateDigestUrl('digest', $cat['cid']);
                $childrenPages[] = $child;
            }
        } else {
            $activePage = $cRep->GetPageByAlias($activePageAlias);
            $childrenTmp = $cRep->getChildrenPages($parentPage['id']);
            $childrenPages = [];
            foreach ($childrenTmp as $childTmp) {
                $activeChild = 0;
                if ($childTmp['alias'] == $activePageAlias) {
                    $active = 1;
                    $activeChild = 1;
                }
                $childrenPages[] = array_merge($childTmp, [
                    'active' => $activeChild,
                    'url' => UrlGenerator::generateContentUrl($childTmp['alias'])
                ]);
            }
        }
        return [$active, $childrenPages];
    }

    public function getUser()
    {
        return User::getObject();
    }

    public function httpAuthenticateBasic($login, $password)
    {
        $sapi_type = php_sapi_name();
        // если php сконфигурирован как модуль CGI
        if (substr($sapi_type, 0, 3) == 'cgi') {
            // для работы этого кода в файле .htacces должна быть запись:
            // RewriteCond %{HTTP:Authorization} ^Basic.*
            // RewriteRule (.*) index.php?authorization=%{HTTP:Authorization} [QSA,L]

            $authenticated = 0;

            if (isset($_GET['authorization'])) {
                if (preg_match('/^Basic\s+(.*)$/i', $_GET['authorization'], $user_pass)) {
                    list($user, $pass) = explode(':', base64_decode($user_pass[1]));
                    // Проверка корректности введенных реквизитов доступа
                    if ($user == $login && $pass == $password) {
                        $authenticated = 1;
                    }
                }
            }

            if (! $authenticated) {
                header('WWW-Authenticate: Basic realm="' . $_SERVER['HTTP_HOST'] . '"');
                header('HTTP/1.1 401 Unauthorized');
                echo 'Authentication canceled!';
                die();
            }
            // Авторизация успешно пройдена
        } else {
            if (
                !isset($_SERVER['PHP_AUTH_USER']) ||
                $_SERVER['PHP_AUTH_USER'] !== $login ||
                $_SERVER['PHP_AUTH_PW'] !== $password
            ) {
                header('WWW-Authenticate: Basic realm="' . $_SERVER['HTTP_HOST'] . '"');
                header('HTTP/1.0 401 Unauthorized');
                echo 'Authentication canceled!';
                die();
            }
            // Авторизация успешно пройдена
        }

        return true;
    }

    public function renderNotFoundPage()
    {
        $cRep = new ContentRepository($this->cms);
        $page = $cRep->GetPageByAlias('404');
        if (! $page) {
            return 'Page not found';
        }

        General::$_page['is_index'] = $page['is_index'];
        General::$_page['title'] = (!empty($page['pagetitle'])) ? $page['pagetitle'] : $page['title'];
        General::$_page['seo_keywords'] = (!empty($page['seo_keywords'])) ? $page['seo_keywords'] : '';
        General::$_page['seo_description'] = (!empty($page['seo_description'])) ? $page['seo_description'] : '';
        General::$_page['title_h1'] = (!empty($page['title_h1'])) ? $page['title_h1'] : General::$_page['title'];

        return $this->renderContent($page['text']);
    }

    /**
     * Binds the parameters to the action.
     * This method is invoked by [[\yii\base\Action]] when it begins to run with the given parameters.
     * This method will check the parameter names that the action requires and return
     * the provided parameters according to the requirement. If there is any missing parameter,
     * an exception will be thrown.
     * @param array $params the parameters to be bound to the action
     * @return array the valid parameters that the action can run with.
     */
    public function bindActionParams($action, $params)
    {
        $method = new ReflectionMethod($this, $action);

        $args = [];
        $missing = [];
        foreach ($method->getParameters() as $param) {
            $name = $param->getName();
            if (array_key_exists($name, $params)) {
                if ($param->isArray()) {
                    $args[] = (array) $params[$name];
                } elseif (!is_array($params[$name])) {
                    $args[] = $params[$name];
                } else {
                    throw new Exception(Lang::get('Invalid data received for parameter "{param}".', [
                        'param' => $name,
                    ]));
                }
                unset($params[$name]);
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } else {
                $missing[] = $name;
            }
        }

        if (!empty($missing)) {
            throw new Exception(Lang::get('Missing required parameters: {params}', [
                'params' => implode(', ', $missing),
            ]));
        }

        return $args;
    }

    protected function render($template, $vars = [], $parameters = [])
    {
        $parameters['vars'] = $vars;
        $content = $this->view->fetch($template, $parameters);

        return $this->renderContent($content);
    }

    public function renderContent($content)
    {
        return $this->view->fetch('layout/' . $this->layout, ['vars' => [
            'result' => $content
        ]]);
    }

    protected function renderPartial($template, $vars = [], $parameters = [])
    {
        $parameters['vars'] = $vars;
        return $this->view->fetch($template, $parameters);
    }
}
