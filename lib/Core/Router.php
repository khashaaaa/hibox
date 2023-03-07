<?php


class Router
{
    /**
     * Параметр с маршрутом при
     * выключенном ЧПУ
     */
    CONST ROUTE_PARAM = 'q';

    CONST DEFAULT_CONTROLLER = 'index';

    CONST DEFAULT_ACTION = 'default';

    CONST BUILD_RULES_EVENT = 'onBuildRules';

    private static $_instance = null;

    /**
     * Конфиг с алиасами контроллеров и параметрами в Url адресах
     * @var array|UrlRule[]
     */
    private $rules = [];

    private function __construct()
    {
        $ruleDeclarations = $this->getRulesDeclarations();
        $this->rules = $this->buildRules($ruleDeclarations);
    }

    private function __clone () {}
    private function __wakeup () {}

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function getRulesDeclarations()
    {
        $scriptNameConfigPath = CFG_APP_ROOT . '/config/routingRules.php';
        $ruleDeclarations = [];
        if (file_exists($scriptNameConfigPath)) {
            $ruleDeclarations = include $scriptNameConfigPath;
        }
        if (file_exists(General::getThemeDir() . '/config/routingRules.php')) {
            $themeRules = include General::getThemeDir() . '/config/routingRules.php';
            $ruleDeclarations = array_merge($ruleDeclarations, $themeRules);
        }

        return Plugins::runSerialEvent(self::BUILD_RULES_EVENT, $ruleDeclarations);
    }

    /**
     * @param RequestWrapper $request
     * @return array
     */
    public function resolve($request, $pathInfo = null)
    {
        if (is_null($pathInfo)) {
            if (General::IsFeatureEnabled('Seo2')) {
                $pathInfo = $request->path();
            }
            // Если выключен ЧПУ или если ЧПУ включен и $pathInfo указывает на главную
            // поискать параметры роутинга в self::ROUTE_PARAM
            if (!General::IsFeatureEnabled('Seo2') || isset($pathInfo) && $pathInfo === '/') {
                if (isset($_GET[self::ROUTE_PARAM])) {
                    $pathInfo = $_GET[self::ROUTE_PARAM];
                } else {
                    $pathInfo = '';
                }
            }
        }

        /* @var $rule UrlRule проверить правила разбора URL */
        foreach ($this->rules as $rule) {
            $result = $rule->parseRequest($request, $pathInfo);

            if ($result !== false) {
                return $this->prepareRouteData($result);
            }
        }

        $route = $this->getRouteByPath($pathInfo);

        return $this->prepareRouteData($route);
    }

    private function getRouteByPath($pathInfo)
    {
        $pathInfo = trim($pathInfo, '/');
        $params = [];

        if (!empty($pathInfo)) {
            $parts = explode('/', $pathInfo);
            $controller = $parts[0];
            $action = isset($parts[1]) ? $parts[1] : self::DEFAULT_ACTION;
        } else {
            $controller = self::DEFAULT_CONTROLLER;
            $action = self::DEFAULT_ACTION;
        }
        $route = $controller . '/' . $action;

        list($controller, $action) = $this->getControllerActionByRoute($route);
        $controllerName = $controller . 'Controller';
        $actionName = $action . 'Action';

        if (!class_exists($controllerName) || !method_exists($controllerName, $actionName)) {
            $contentRepository = new ContentRepository(General::getCms());
            if ($contentRepository->hasContentPage($pathInfo)) {
                $route = 'content/default';
                $params['alias'] = $pathInfo;
            }
        }

        return [$route, $params];
    }

    /**
     * Builds URL rule objects from the given rule declarations.
     *
     * @param array $ruleDeclarations the rule declarations. Each array element represents a single rule declaration.
     * Please refer to [[rules]] for the acceptable rule formats.
     * @return UrlRule[] the rule objects built from the given rule declarations
     */
    protected function buildRules($ruleDeclarations)
    {
        $cacher = new FileAndMysqlMemoryCache(new CMS());
        $builtRules = [];

        $cacheId = 'General:buildRules';
        if ($cacher->Exists($cacheId)) {
            $cacheRules = unserialize($cacher->GetCacheEl($cacheId));
            if ($cacheRules)
                $builtRules = $cacheRules;
        } else {
            $verbs = 'GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS';
            foreach ($ruleDeclarations as $key => $rule) {
                if (is_string($rule)) {
                    $rule = ['route' => $rule];
                    if (preg_match("/^((?:($verbs),)*($verbs))\\s+(.*)$/", $key, $matches)) {
                        $rule['verb'] = explode(',', $matches[1]);
                        // rules that are not applicable for GET requests should not be used to create URLs
                        if (!in_array('GET', $rule['verb'], true)) {
                            $rule['mode'] = UrlRule::PARSING_ONLY;
                        }
                        $key = $matches[4];
                    }
                    $rule['pattern'] = $key;
                }
                if (is_array($rule)) {
                    $rule = new UrlRule($rule);
                }
                $builtRules[] = $rule;
            }
            $cacher->AddCacheEl($cacheId, 86400, serialize($builtRules));
        }

        return $builtRules;
    }

    private function prepareRouteData($routeData)
    {
        list($route, $params) = $routeData;
        $_GET = $params + $_GET;

        list($controller, $action) = $this->getControllerActionByRoute($route);
        $parameters = array(
            'vars' => $params
        );

        return [$controller, $action, $parameters];
    }

    private function getControllerActionByRoute($route)
    {
        $parts = explode('/', $route);
        foreach ($parts as &$part) {
            $part = array_map(function ($p) {
                return ucfirst(strtolower($p));
            }, explode('-', $part));
        }

        $controller = implode('', $parts[0]);
        $action = implode('', $parts[1]);

        return [$controller, $action];
    }

    public function createUrl($route, array $params = [])
    {
        /* @var $rule UrlRule генерация по правила разбора URL */
        foreach ($this->rules as $rule) {
            $url = $rule->createUrl($route, $params);
            if ($url !== false) {
                return '/' . $url;
            }
        }

        /* обычная генерация URL */
        $url = '';
        if (General::IsFeatureEnabled('Seo2')) {
            $url .= '/' . $route;
        } else {
            $url .= '/?q=' . $route;
        }

        if (!empty($params)) {
            $url .= (General::IsFeatureEnabled('Seo2')) ? '?' : '&';
            $url .= http_build_query($params);
        }

        return $url;
    }
}