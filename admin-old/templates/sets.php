<?
include ("header.php");
$cid = @$_GET['cid'];
?>



<div class="main"><div class="canvas clrfix">
    <h1><?=LangAdmin::get('collections')?></h1>
    <span style="color:red; font-size:16px"><?=LangAdmin::get('collections_go_to_new_admin_description')?></span><br>
    <a href="/admin/?cmd=sets&do=default" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-hover" role="button" aria-disabled="false"><span class="ui-button-text"><?=LangAdmin::get('go_to_new_admin')?></span></a>

</div></div><!-- /.main -->


<?
include ("footer.php");
?>