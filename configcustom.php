<?

define('CFG_SERVICE_INSTANCEKEY', '93681402-f43b-4ce0-9424-8cc113ae9c5b');
define('CFG_CACHED', false);
define('CFG_MULTI_CURL', true);

if(!isset($_GET['debug']))
    define('NO_DEBUG', true);

define('DB_HOST', 'localdb');
define('DB_USER', 'gateway_mn');
define('DB_PASS', 'g8AM3KcnXQaD');
define('DB_BASE', 'gateway_mn');

if(!defined('TS_HOST_NAME'))
    define('TS_HOST_NAME', preg_replace( '~:[0-9]+$~', '', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '' ));

if(!defined('CFG_BASE_HREF'))
    define('CFG_BASE_HREF', 'http://'.TS_HOST_NAME);
