<? include (TPL_DIR."header.php"); ?>
<? $type = 'Recommend'; ?>

<script language="javascript" type="text/javascript" src="../js/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="js/edit_descr.js"></script>

<div class="windialog" id="dialog-form" title="<?=LangAdmin::get('editing_the_name_of_the_goods')?>">
    <p class="validateTips"></p>
    <b><?=LangAdmin::get('product_name')?>:</b><br/>
    <textarea class="text ui-widget-content ui-corner-all" id="Title" name="Title" style="height: 360px; width: 510px"></textarea>
</div>    
<div class="windialog" id="dialog-form-descr" title="<?=LangAdmin::get('edit_the_description_of_the_goods')?>">
    <p class="validateTips"></p>
    <b><?=LangAdmin::get('description_of_goods')?>:</b><br/>
    <input type="hidden" name="ItemId" value="" />
    <textarea class="text ui-widget-content ui-corner-all" id="Description" name="Description" style="height: 360px; width: 510px"></textarea>
</div>    

<div class="main"><div class="canvas clrfix">
        <div class="col700">
            <div class="tuning">
                
                <div id="tabs">
                    <ul>
                        <li id="itab1"><a href="#tabs-1"><?=LangAdmin::get('set_1')?></a></li>
                        <li id="itab2"><a href="#tabs-2"><?=LangAdmin::get('set_2')?></a></li>
                        <li id="itab3"><a href="#tabs-3"><?=LangAdmin::get('set_3')?></a></li>
                        <li id="itab4"><a href="#tabs-4"><?=LangAdmin::get('set_4')?></a></li>
                    </ul>
                    
                    <span id="error" style="color:red;font-weight: bold;">
                        <? if(isset($error)) { print $error; } ?>
                    </span>
                    
                    <div id="tabs-1">
                        <? $catid = 'set1'; ?>
                        <? include(TPL_DIR.'randomset/set1.php'); ?>
                    </div>
                    
                    <div id="tabs-2">
                        <? $catid = 'set2'; ?>
                        <? include(TPL_DIR.'randomset/set2.php'); ?>
                    </div>
                    
                    <div id="tabs-3">
                        <? $catid = 'set3'; ?>
                        <? include(TPL_DIR.'randomset/set3.php'); ?>
                    </div>

                    <div id="tabs-4">
                        <? $catid = 'set4'; ?>
                        <? include(TPL_DIR.'randomset/set4.php'); ?>
                    </div>
                </div>
            </div>
        </div>

</div></div><!-- /.main -->

<script type="text/javascript" src="js/jquery.combobox.js"></script>
<script type="text/javascript">
    
var shstat = new Array(<?=$hidden_s1?>,<?=$hidden_s2?>,<?=$hidden_s3?>,<?=$hidden_s4?>);

$(function(){
    $('#tabs').tabs();
    
    $('li[id^=itab]').each(function() {
        $(this).removeClass('ui-tabs-selected').removeClass('ui-state-active');
    });
    $('div[id^=tabs-]').each(function() {
        $(this).addClass('ui-tabs-hide');
    });
    
    var tab_number = <? echo (isset($_GET['active_tab'])) ? $_GET['active_tab'] : 1; ?>;
    $("#itab" + tab_number).addClass('ui-tabs-selected').addClass('ui-state-active');
    $("#tabs-" + tab_number).removeClass('ui-tabs-hide');
    
    $( ".sortable" ).sortable({
        change: function(event, ui) { 
            $(event.target).next().next().find('[value="save"]').closest('div').show();
        }
    });
    
    $('button[type="submit"]').button();
    $('button[type="submit"][value="save"]').click(function(){
        var sortable = $(this).closest('div.block-container').find('.sortable');
        
        var result = $(sortable).sortable('toArray');
        var str = '';
        $.each( result, function(i, value){
            str += value.substr(3) + ';';
        });
        $(this).closest('form').find('[name="ids"]').val(str);
        
        $(this).closest('form').submit();
    });
});

function save_state(block) {
    if(block == 'pop') {
        if (shstat[0] == 1) {
            $('#popular').hide();
            shstat[0] = 0;
        } else {
            $('#popular').show();
            shstat[0] = 1;
        }
    }
    
    if(block == 'rec') {
        if (shstat[1] == 1) {
            $('#recommend').hide();
            shstat[1] = 0;
        } else {
            $('#recommend').show();
            shstat[1] = 1;
        }
    }

    $.get('index.php',{
        cmd: 'set',
        'do': 'savestat',
        stats1: shstat[0],
        stats2: shstat[1],
        stats3: shstat[2],
        stats4: shstat[3],
        sid: '<?=$GLOBALS['ssid']?>'
    });
}

</script>


<? include (TPL_DIR."footer.php"); ?>