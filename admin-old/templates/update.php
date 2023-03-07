<?
include ("header.php");

?>
<script type="text/javascript" src="js/update.js"></script>
<div class="main"><div class="canvas clrfix">

<div id="tabs">
        <ul>
            <li id="itab1"><a href="#tabs-1"><?=LangAdmin::get('update')?></a></li>
            <li id="itab2"><a href="#tabs-2"><?=LangAdmin::get('recover_site')?></a></li>            
        </ul>        
        <div id="tabs-1" style="padding:5px;"><? include('update/update.php'); ?></div>
        <div id="tabs-2" style="padding:5px;"><? include('update/recover.php'); ?></div>
        
    </div>
        
</div></div>


<script>

var tab_number = <? echo (isset($tab_number)) ? $tab_number : 1; ?>;
$("#itab" + tab_number).addClass('ui-tabs-selected').addClass('ui-state-active');
$("#tab-" + tab_number).show();

$(function() {
    $('#tabs').tabs();
    //$("#dialog-confirm:ui-dialog").dialog("destroy");
    //$("#dialog-add-category:ui-dialog").dialog("destroy");
	
});
    
</script>


<?
include ("footer.php");
?>

