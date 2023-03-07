<?php

class ContentSection implements WidgetInterface
{
    const MODE_DISPLAY = 'display';
    const MODE_AJAX_LOAD = 'ajax';
    const MODE_LAZY_LOAD = 'lazy';

    /**
     * Проверит наличие кэша виджета и
     * в случае его отсутствие выполнит
     * ajax запрос на его получение
     *
     * @param array $options
     * @return bool|false|mixed|string
     * @throws DBException
     */
    public static function getWidget(array $options = [])
    {
        $defaultOptions = array(
            'route' => '', // маршрут по которому виджет получит данные
            'cache' => 0, // ttl кэша, если 0 - кэш не используется
            'id' => 'widget_content-section_' . rand(10000, 99999), // id html контейнера в случае ajax загрузки
            'mode' => self::MODE_DISPLAY, // режим отображения контента
        );

        // переопределить стандартные опции
        $options = array_merge($defaultOptions, $options);

        if (empty($options['route'])) {
            throw new Exception('Required parameter is not filled - route');
        }

        $fileAndMysqlMemoryCache = new FileAndMysqlMemoryCache(General::getCms());
        if (! empty($options['cacheKey'])) {
            $cacheKey = $options['cacheKey'];
        } else {
            $cacheKey = $options['route'] . '_' . Session::getActiveLang();
        }
        $cacheKey = 'ContentSection:' . md5($cacheKey);

        if ($options['cache'] > 0 && $fileAndMysqlMemoryCache->Exists($cacheKey)) {
            $result = $fileAndMysqlMemoryCache->GetCacheEl($cacheKey);
        } elseif ($options['mode'] == self::MODE_DISPLAY) {
            $request = new RequestWrapper();
            $response = self::run($request, $options);
            $result = $response['html'];
        } else {
            $requestedUrlParams = [
                'route' => $options['route'],
                'cache' => $options['cache'],
            ];
            if (! empty($options['cacheKey'])) {
                $requestedUrlParams['cacheKey'] = $options['cacheKey'];
            }

            $requestedUrl = UrlGenerator::toRoute('content-section/default', $requestedUrlParams);

            $result = General::viewFetch('widget/lazy-content', [
                'path' => CFG_VIEW_ROOT,
                'vars' => [
                    'id' => $options['id'],
                    'requestedUrl' => $requestedUrl,
                ]
            ]);
        }

        return $result;
    }

    /**
     * Выполнит действие контроллера из REQUEST_URI
     * заданного в $options['route']
     *
     * @param RequestWrapper $request
     * @param $options[]
     * @return array
     * @throws DBException
     */
    public static function run($request, $options)
    {
        $response = [];

        $fileAndMysqlMemoryCache = new FileAndMysqlMemoryCache(General::getCms());
        if (! empty($options['cacheKey'])) {
            $cacheKey = $options['cacheKey'];
        } else {
            $cacheKey = $options['route'] . '_' . Session::getActiveLang();
        }
        $cacheKey = 'ContentSection:' . md5($cacheKey);

        if ($options['cache'] > 0 && $fileAndMysqlMemoryCache->Exists($cacheKey)) {
            $result = $fileAndMysqlMemoryCache->GetCacheEl($cacheKey);
        } else {
            try {
                list($controller, $action, $parameters) = Router::getInstance()->resolve($request, $options['route']);
                $result = General::runController($controller, $action, $parameters);
                if ($options['cache'] > 0) {
                    $fileAndMysqlMemoryCache->AddCacheEl($cacheKey, $options['cache'], $result);
                }
            } catch (TemporarilyUnavailableException $e) {
                $response['timeout'] = 1;
                $result = '';
            } catch (Exception $e) {
                throw $e;
            }
        }

        $response['html'] = $result;

        return $response;
    }
}