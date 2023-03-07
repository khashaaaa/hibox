<?php

// Запоминаем время начала генерации страницы
$GLOBALS['memory_usage'] = memory_get_usage();
$GLOBALS['script_start_time'] = microtime(true);
$GLOBALS['trace'] = array();

ini_set('memory_limit', '1024M');

// Задаем заголовки
header('Content-Type: text/html; charset=utf-8');
header("Cache-control: no-cache, must-revalidate, no-store");
header("Pragma: no-cache");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

date_default_timezone_set('Europe/Moscow');

session_cache_expire(60*24);
session_start();

// Включить вывод ошибок при наличии дебага
if (isset($_REQUEST['debug'])) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

// Задаем пути
define('CFG_APP_ROOT', str_replace(array('/config', '\config'), '', dirname(__FILE__)));
define('CFG_LIB_ROOT', CFG_APP_ROOT.'/lib');
define('CFG_TPL_ROOT', CFG_APP_ROOT.'/templatescustom');
define('CFG_BASE_TPL_ROOT', CFG_APP_ROOT.'/templates');
define('CFG_ARCA_ROOT', CFG_LIB_ROOT.'/arca');
define('CFG_BANNERS_SETTINGS', CFG_APP_ROOT.'/userdata/banner.txt');

define('CFG_VIEW_ROOT', CFG_APP_ROOT.'/views');

define('HOST_NAME', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
if (!defined('TS_HOST_NAME'))
    define('TS_HOST_NAME', preg_replace( '~:[0-9]+$~', '', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '' ));

// Подключаем функцию автоматической загрузки запрашиваемых классов
require_once(CFG_APP_ROOT.'/config/autoload.config.php');
// Подключаем почтовый скрипт
require_once(CFG_APP_ROOT . '/lib/SwiftMailer/swift_required.php');
// Вспомогательные функции
require_once(CFG_APP_ROOT . '/lib/functions.php');

// OTAPIlib
global $otapilib;
$otapilib = new OTAPIlib();
if (defined('CFG_SERVICE_URL')) $otapilib->_server = CFG_SERVICE_URL;

// OTAPILib2
// TODO: перенести класс otapilib2 в папку lib
OTBase::import('system.otapilib2.lib.*');
OTBase::import('system.otapilib2.types.*');
OTBase::import('system.otapilib2.UnboundedElementsIterator');
OTBase::import('system.otapilib2.OTAPILib2');
OTBase::import('system.otapilib2.AbstractOTAPILib2');
OTAPILib2::init();


if (preg_match('/admin/i', $_SERVER['SCRIPT_NAME'])) $_GET['p'] = 'admin';

if (!defined('CFG_SITE_VERSION')) define('CFG_SITE_VERSION', OTBase::getVersion());

if (! defined('CFG_CACHED')) define('CFG_CACHED', false);

define('CFG_TOOLS_URL', 'http://tools.otcommerce.com');
if (!defined('CFG_SUPPORT_URL'))
    define('CFG_SUPPORT_URL', 'https://support.otcommerce.com');
define('CFG_REVIEWS_LOG_ANALYZE_URL', CFG_SUPPORT_URL . '/log_analyzer/on_error/reviews');

// TODO: используется в старых фронт-контроллерах - убрать из глобальной области видимости
// HSTemplate initialization
$HSTemplate_options = array(
                'template_path' => CFG_TPL_ROOT,
                'cache_path'    => CFG_APP_ROOT . '/cache',
                'debug'         => false,
                );
if(defined('CFG_CUSTOM_CACHE_DIR')) $HSTemplate_options['cache_path'] = CFG_APP_ROOT . '/' . CFG_CUSTOM_CACHE_DIR;
$HSTemplate = new HSTemplate($HSTemplate_options);
