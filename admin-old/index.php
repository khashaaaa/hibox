<?php
header('Content-Type: text/html; charset=utf-8');
// Запоминаем время начала генерации страницы
$GLOBALS['script_start_time'] = microtime(true);
$GLOBALS['trace'] = array();

chdir('../');
include('config.php');
chdir('admin-old/');
include('cfg/main.cfg.php');
include('cfg/error.cfg.php');

@define('CFG_SITE_NAME', General::getConfigValue('site_name'));

require_once CFG_APP_ROOT . '/logs/Log.class.php';
require_once CFG_APP_ROOT . '/logs/AdminLog.php';

$request = new RequestWrapper();
$isAjax = $request->env('HTTP_X_REQUESTED_WITH') && strtolower($request->env('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest';
if(!$isAjax && $request->getValue('do') != 'getTranslations'){
    $L = new AdminLog();
    $L->Start();
}

$_SESSION['active_lang_admin'] = @$_SESSION['active_lang_admin'] ? $_SESSION['active_lang_admin'] : 'ru';

if(@$_POST['site_lang']){
    $_SESSION['active_lang'] = $_POST['site_lang'];
    header('Location: '.$_POST['from']);
    die();
}
if(@$_POST['lang'] && !isset($_POST['cms'])){
    $_SESSION['active_lang_admin'] = $_POST['lang'];

    if(@$_SESSION['sid'])
        $otapilib->UpdateInstanceOptions($_SESSION['sid'],
            '<InstanceOptionsData><AdminPanelLanguage>'.$_POST['lang'].'</AdminPanelLanguage></InstanceOptionsData>');
    header('Location: '.$_POST['from']);
    die();
}

unset($_SESSION['fatal_error']);

ob_start();
if(!file_exists(dirname(__FILE__).'/utils/Lang.class.php'))
    General::init();
ob_end_clean();

if(file_exists(dirname(__FILE__).'/utils/Lang.class.php'))
    $_SESSION['fatal_error'] = 'Удалите файл admin-old/utils/Lang.class.php';

require BASE_PATH.'lib/LangAdmin.class.php';
LangAdmin::getTranslations(BASE_ADMIN_PATH.'langs/', $_SESSION['active_lang_admin']);
Lang::getTranslations(BASE_PATH.'langs/', $_SESSION['active_lang_admin']);

//include('../index.php');

$GLOBALS['ssid'] = isset($_GET['sid']) ? $_GET['sid'] : '';

/*
 * @todo delete $GLOBALS['log']
 */
$GLOBALS['log'] = isset($_GET['log']) ? $_GET['log'] : (isset($_POST['login']) ? $_POST['login'] : '');

try {
    Request::handle();
} catch (Exception  $e) {

    $message = $e->getMessage();
    //var_dump($message);
    switch($e->getCode()){
        case 1:
            include(TPL_DIR.'/login.php');
            break;
        default:
            include(TPL_DIR.'/error.php');
    }
}

if(!$isAjax && $request->getValue('do') != 'getTranslations' && !$request->getValue('login')){
    $L->CompleteClose();
}

if (!defined('NO_DEBUG'))
{
    // Выводим отладочную информацию
    $runtime = round(microtime(true) - $GLOBALS['script_start_time'], 5);
    print '<!-- {{ отладочная информация --><hr>';
    print "<a style='cursor:pointer' onclick=\"$('.debug_info').toggle()\">Время основной сборки страницы: $runtime сек</a><br>";
    print '<div align="left" style="display:none" class="debug_info"><pre>';
    $other = $runtime;
    if (isset($GLOBALS['trace']))
    {
        krsort($GLOBALS['trace']);
        foreach($GLOBALS['trace'] as $time=>$line)
        {
            print '    '.str_replace("\n",'<br>',$line).'<br>';
            $other -= $time;
        }
    }
    $other = round($other, 5);
    print "    прочее — $other сек.<br>";

    if (isset($GLOBALS['trace']))  $logs = General::GetArrayLogs($GLOBALS['trace']);
    $toecho='';
    //$toecho+="<br><br><br>--------------------------Проверка массива---------------------------------<br>";
    for ($i = 1; $i <= count($logs); $i++) {
        $toecho.="<br>".$i.". - {$logs[$i]['method']} - Время - {$logs[$i]['time']}<br>";

        for ($j = 1; $j < count($logs[$i])-1; $j++) {
            $toecho.="  -->>   {$logs[$i][$j]['method']} - Время : {$logs[$i][$j]['time']} - Время(overhead) : {$logs[$i][$j]['overtime']}<br>";
        }

    }
    print $toecho;

    print "</pre>";
    print "</div><!-- отладочная информация }} -->";

}


function show_error($msg='', $file='')
{
    global $otapilib;
    $msg = $msg ? $msg : $otapilib->error_message;
    @$GLOBALS['error_text'] .= $msg;
    @$GLOBALS['error_text_hidden'] .= $otapilib->error_message;
    $GLOBALS['file_with_error'] = $_SERVER["HTTP_HOST"].$_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING'];
    $GLOBALS['show_error'] = true;
    $GLOBALS['error_code'] = (string)$otapilib->error_code;
    $GLOBALS['error_method'] = $otapilib->call_method;
}

if (@$GLOBALS['show_error'])
{
    // Выводим ошибку
    ?>
<div id="error-dialog" style="display: none"></div>
<script type="text/javascript">
    $(function(){
        $('#error-dialog').dialog({
            title: '<?=Lang::get('otapi_request_error')?>',
            buttons:{
                "<?=Lang::get('report_to_email')?>": function(){
                    var e_message = get_error_message();
                    var e_file = get_file_with_error();
                    var e_otapi = get_otapi_url();
                    var e_code = get_error_code();
                    $.post(
                            'index.php?p=sendemail',
                            {m: e_message, f: e_file, o: e_otapi, e: e_code},
                            function(data) {
                                $('#error-dialog').dialog('close');
                            }
                    );
                }
            }
        });
        $('#error-dialog').html('<?=htmlspecialchars(mysql_real_escape_string($GLOBALS['error_text']))?>').dialog('open');
    });
</script>
<?
    print '
    <script>
    function get_error_message(){
       return "'.substr(htmlspecialchars(mysql_real_escape_string($GLOBALS['error_text_hidden'])),0,500).'";
    }

    function get_error_code(){
        return "'.$GLOBALS['error_code'].'";
    }

    function get_otapi_url(){
       return "'.rtrim(CFG_SERVICE_URL,'/').'?op='.$GLOBALS['error_method'].'";
    }

    function get_file_with_error(){
       return "'.mysql_real_escape_string($GLOBALS['file_with_error']).'";
    }
    </script>';
}
