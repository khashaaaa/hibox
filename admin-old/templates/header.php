<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <title>OT Commerce Admin</title>
    <link rel="stylesheet" href="css/960.css" type="text/css" media="screen" charset="utf-8">
    <!--<link rel="stylesheet" href="css/fluid.css" type="text/css" media="screen" charset="utf-8" />-->
    <link rel="stylesheet" href="css/template.css" type="text/css" media="screen" charset="utf-8">
    <link rel="stylesheet" href="css/colour.css" type="text/css" media="screen" charset="utf-8">

    <?if(RequestWrapper::get('cmd') == 'Set2' || RequestWrapper::get('cmd') == 'set2'){?>
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />

    <script src="http://code.jquery.com/jquery-1.8.3.js"></script>
    <script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
    <?}else{?>
    <link rel="stylesheet" href="css/jquery-ui.css" type="text/css" media="screen" charset="utf-8">

    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.8.18.custom.min.js"></script>
    <?}?>

    <script type="text/javascript" src="js/fileuploader.js"></script>
    <script type="text/javascript" src="js/all.js"></script>
    <link rel="stylesheet" href="css/jquery.loadmask.css" type="text/css" />
    <script type='text/javascript' src='js/jquery.loadmask.js'></script>

    <?if(@$_GET['do'] == 'case'){?>
    <link rel="stylesheet" href="css/jquery.treeview.css" />
    <script src="js/jquery.cookie.js" type="text/javascript"></script>
    <script src="js/jquery.treeview.js" type="text/javascript"></script>
    <script src="js/jquery.treeview.edit.js" type="text/javascript"></script>
    <script src="js/jquery.treeview.async.js" type="text/javascript"></script>

    <script src="js/validate/jquery.validate.pack.js" type="text/javascript"></script>
    <script src="js/validate/lc/messages_ru.js" type="text/javascript"></script>

    <script src="js/ui/jquery.ui.core.js"></script>
    <script src="js/ui/jquery.ui.widget.js"></script>
    <script src="js/ui/jquery.ui.mouse.js"></script>
    <script src="js/ui/jquery.ui.sortable.js"></script>
    <?}?>

</head>
<body>
<h1 id="head">
    <span style="float: right; margin-left: 15px;"><a href="http://<?=$_SERVER['SERVER_NAME']?>" style="color: white;" target="_blank"><?=@LangAdmin::get('open_site')?></a></span>
    <span style="float: right; margin-left: 15px;"><a href="http://<?=$_SERVER['SERVER_NAME']?>/admin/" style="color: white;" target="_blank"><?=@LangAdmin::get('admin_new')?></a></span>
    OT Commerce Admin &nbsp;&nbsp;&nbsp;&nbsp;
<?
    if(CFG_SITE_NAME == 'Opentao dev' || isset($_GET['show_session'])){
        @print $_SESSION['sid'];
    }
?>
    <? if(CFG_SERVICE_INSTANCEKEY == 'opendemo'){ ?>
    <br />
        <span style="font-size: 15px; color: #f88"><?=@LangAdmin::get('opendemo_text')?></span>
    <? } ?>

    <? if (Login::auth()) { ?>
        <?=Plugins::invokeEvent('onRenderAdminHead')?>
    <? } ?>
</h1>

<? include('right_menu.php'); ?>

<div class="windialog" id="dialog-error" title="<?=LangAdmin::get('message')?>">
    <span id="error" style="color:red;font-weight: bold;">
        <? if (isset($error)) {
        print $error;
    } ?>
    </span>
</div>

<script>
    $("#dialog-error").dialog({
        autoOpen:false,
        modal:true,
        buttons:{
            "<?=LangAdmin::get('ok')?>":function () {
                $("#dialog-error").dialog("close");
            }
        }
    });

    var error_messages = {
        'RKdevException' : 'Ошибка в бизнес логике',
    };
</script>

<? Session::checkAdminErrors(); ?>

<div id="content" class="container_16 clearfix">

