<?

if( !function_exists('apache_request_headers') ) {
///
    function apache_request_headers() {
        $arh = array();
        $rx_http = '/\AHTTP_/';
        foreach($_SERVER as $key => $val) {
            if( preg_match($rx_http, $key) ) {
                $arh_key = preg_replace($rx_http, '', $key);
                $rx_matches = array();
                // do some nasty string manipulations to restore the original letter case
                // this should work in most cases
                $rx_matches = explode('_', $arh_key);
                if( count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
                    foreach($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
                    $arh_key = implode('-', $rx_matches);
                }
                $arh[$arh_key] = $val;
            }
        }
        return( $arh );
    }
///
}

$url = str_replace(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '', $_SERVER['REQUEST_URI']);

$sub = 's';
if (strpos($url, '/') !== false)
{
    $sub = substr($url, 0, strpos($url, '/'));
    $url = substr($url, strpos($url, '/'), 10000);
}
$url = 'http://'.$sub.'.taobao.com'.$url;

require_once('../lib/Curl.class.php');
$curl = new Curl($url, 10, true, 10, false, true, false);
$headers = apache_request_headers();
if (isset($_SERVER['HTTP_REFERER']))
	$curl->setReferer($_SERVER['HTTP_REFERER']);
$curl->setUserAgent($_SERVER['HTTP_USER_AGENT']);
$curl->setHeader($headers);
$cres = $curl->connect();
$str = $curl->__tostring();

$info = $curl->getInfo();
$header_size = $info['header_size'];

$headers = trim(substr($str, 0, $header_size));
$str = trim(substr($str, $header_size));

$headers = str_replace(chr(10), '', $headers);
$headers = explode(chr(13), $headers);


/*
Array
(
    [0] => 200 HTTP/1.1 200 OK
    [1] => Server: Tengine
    [2] => Date: Thu, 14 Mar 2013 13:28:57 GMT
    [3] => Content-Type: text/html; charset=GBK
    [4] => Transfer-Encoding: chunked
    [5] => Connection: close
    [6] => Vary: Accept-Encoding
)
*/
unset($headers[0]);
//print_r($headers);

foreach($headers as $header)
{
    //print $header;
    //if (substr($header, 0, 4) == 'Serv') continue;
    //if (substr($header, 0, 4) == 'Date') continue;
    //if (function_exists('ob_gzhandler') && ini_get('zlib.output_compression'))
        if (substr($header, 0, strlen('Content-Encoding')) == 'Content-Encoding') continue;
    if (substr($header, 0, strlen('Content-Length')) == 'Content-Length') continue;
    if (substr($header, 0, 4) == 'Tran') continue;
    if (substr($header, 0, 4) == 'Loca') continue;
    //if (substr($header, 0, 4) == 'Conn') continue;
    //if (substr($header, 0, 4) == 'Vary') continue;
    header($header);
}


print $str;
