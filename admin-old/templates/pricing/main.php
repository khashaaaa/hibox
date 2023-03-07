<? include (TPL_DIR."header.php"); ?>

<div class="main"><div class="canvas clrfix">
    <div class="windialog" id="dialog-add-category" title="<?=LangAdmin::get('message')?>">
        <?=LangAdmin::get('add_category_to_group')?><br/>
        <? include(TPL_DIR.'pricing/categories.php'); ?>
    </div>
        
    <div class="windialog" id="dialog-confirm-delete-cat" title="<?=LangAdmin::get('message')?>">
        <?=LangAdmin::get('qw')?>
    </div>
        
    <div id="tabs">
        <ul>
            <li id="itab1"><a href="#tabs-1"><?=LangAdmin::get('settings_of_prices')?></a></li>
            <li id="itab2"><a href="#tabs-2"><?=LangAdmin::get('list_price_groups')?></a></li>
            <li id="itab3"><a href="#tabs-3"><?=LangAdmin::get('add_price_group')?></a></li>
        </ul>
        <span id="error" style="color:red;font-weight: bold;">
            <? if(isset($error)) { print $error; } ?>
        </span>
        <div id="tabs-1"><? include(TPL_DIR.'pricing/prices.php'); ?></div>
        <div id="tabs-2"><? include(TPL_DIR.'pricing/groups.php'); ?></div>
        <div id="tabs-3"><? include(TPL_DIR.'pricing/add_group.php'); ?></div>
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

<? include (TPL_DIR."footer.php"); ?>