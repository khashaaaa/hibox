<?php
defined('CFG_APP_ROOT') or define('CFG_APP_ROOT', dirname(dirname(__FILE__)));
require_once(CFG_APP_ROOT . '/lib/Assets.class.php');
require_once(CFG_APP_ROOT . '/lib/CodeCompress.class.php');

$id       = (isset($_GET['id'])) ? $_GET['id'] : null;
$lang     = (isset($_GET['lang'])) ? $_GET['lang'] : null;
$ver      = (isset($_GET['ver'])) ? $_GET['ver'] : null;
$compress = (isset($_GET['compress'])) ? $_GET['compress'] : false;

$path = CFG_APP_ROOT . Assets::getCollectionPath($id);
if (! file_exists($path)) {
    $result = '';
} else {
    $sources = array();
    require_once $path;

    $result = CodeCompress::getJSCode($sources, $id, $ver, $compress);
}

header('Content-Type:text/javascript');
print $result;
