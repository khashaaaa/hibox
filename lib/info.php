<?php
require_once('../config.php');
require_once('../config/config.php');
OTBase::import('system.admin.lib.RightsManager');

if (is_null(Session::get('sid')) || !RightsManager::isSuperAdmin()) {
    header('HTTP/1.0 404 Not Found');
    die();
}

phpinfo();
