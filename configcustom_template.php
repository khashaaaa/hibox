<?

define('CFG_SERVICE_INSTANCEKEY', '');
define('CFG_CACHED', false);
define('CFG_MULTI_CURL', true);

if(!isset($_GET['debug']))
    define('NO_DEBUG', true);

define('DB_HOST', '');
define('DB_USER', '');
define('DB_PASS', '');
define('DB_BASE', '');

if(!defined('TS_HOST_NAME'))
    define('TS_HOST_NAME', preg_replace( '~:[0-9]+$~', '', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '' ));

if(!defined('CFG_BASE_HREF'))
    define('CFG_BASE_HREF', 'http://'.TS_HOST_NAME);
