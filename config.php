<?php

$server = str_replace('www.', '', isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '');

if (file_exists(dirname(__FILE__) . '/' . $server . '.php')) {
    if (! defined('CFG_SPLIT_CACHE_BY_DOMAIN')) {
        define('CFG_SPLIT_CACHE_BY_DOMAIN', true);
    }
    include(dirname(__FILE__) . '/' . $server . '.php');
} elseif (file_exists(dirname(__FILE__) . '/config/server/' . $server . '.php')) {
    if (! defined('CFG_SPLIT_CACHE_BY_DOMAIN')) {
        define('CFG_SPLIT_CACHE_BY_DOMAIN', true);
    }
    include(dirname(__FILE__) . '/config/server/' . $server . '.php');
} else {
    if (file_exists(dirname(__FILE__) . '/configcustom.php')) {
        include(dirname(__FILE__) . '/configcustom.php');
    } else {
        define('CFG_SERVICE_INSTANCEKEY', '');
    }
}
