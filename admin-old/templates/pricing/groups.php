
<div class="windialog" id="dialog-confirm" title="<?=LangAdmin::get('message')?>">
    <span id="info">
        <?=LangAdmin::get('confirm_delete_group')?>
    </span>
</div>

<? if (isset($action) && $action=='edit') { ?>
    <? include(TPL_DIR.'pricing/edit_group.php'); ?>
<? } else { ?> 
    <div class="grid_16">
        <table>
            <thead>
            <tr>
                <th></th>
                <th><?=LangAdmin::get('name')?></th>
                <th><?=LangAdmin::get('description')?></th>
                <th><?=LangAdmin::get('pricing_strategy')?></th>
                <th><?=LangAdmin::get('intervals')?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
                <? foreach ($price_groups as $group) { ?>
                <tr id="group<?=$group['id']?>" style="border: 1px dotted #D3D3D3;">
                    <td>
                        <? if ($group['isdefault'] == 'true') { ?>
                            <img src='templates/i/star.gif' width='20' height='20' align='middle' style='vertical-align:middle'/>
                        <? } ?>
                    </td>
                    <td><a href="index.php?sid=&do=pricing&group&gid=<?=$group['id']?>"><?=$group['Name']?></a></td>
                    <td><?=$group['description']?></td>
                    <td><?=$group['strategytype']?></td>
                    <td>
                        <? foreach ($group['settings']['priceformationintervals'] as $interval) { ?>
                            <!--strong>Id:</strong><?=$interval['id']?><br/-->
                            <strong><?=LangAdmin::get('limit')?>:</strong><?=$interval['minimumlimit']?><br/>
                            <?if((float)$interval['marginpercent']){ ?>
                            <strong><?=LangAdmin::get('margin_product')?>:</strong><?= ((float)$interval['marginpercent']-1)*100;?>%<br/><br/>
                            <? } ?>
                            <?if($interval['marginfixed']){ ?>
                            <strong><?=LangAdmin::get('margin_fixed')?>:</strong><?=$interval['marginfixed']?><br/><br/>
                            <? } ?>
                        <? } ?>
                    </td>
                    <td>
                        <button id="edit_group" class="" value="<?=$group['id']?>" onclick="EditGroup(<?=$group['id']?>);"><span class="ui-button-text"><?=LangAdmin::get('edit')?></span></button>
                        <button id="delete_group" class="" value="<?=$group['id']?>" onclick="DelGroup(<?=$group['id']?>);"><span class="ui-button-text"><?=LangAdmin::get('remove')?></span></button>
                    </td>
                </tr>
                <? } ?>
            </tbody>
        </table>
    </div>
<? } ?>
<style>
#delete_group:hover,#edit_group:hover {
	border: 1px solid #999999;	
}

</style>


<script>
var group_id = 0;
/*
$('#delete_group')
    .button()
    .click(function () {
        group_id = $(this).val();
        console.log(group_id);
        $("#info").html("<?=LangAdmin::get('confirm_delete_group')?>");
        $("#dialog-confirm").dialog("open");
});

$('#edit_group')
    .button()
    .click(function () {
        location.href = 'index.php?sid=&do=pricing&group&gid=' + $(this).val();
});
*/
$('.cancel_edit')
    .button()
    .click(function () {
        location.href = 'index.php?sid=&tab=2&do=pricing';
});

$('.save_edit')
    .button()
    .click(function () {
        $('#edit-form').submit();
});

$("#dialog-confirm").dialog({
    autoOpen: false,
    modal: true,
    buttons : {
        "<?=LangAdmin::get('yes')?>" : function() {
            delete_price_group(group_id_full);
        },
        "<?=LangAdmin::get('cancellation')?>" : function() {
            $(this).dialog("close");
        }
    }
});

function EditGroup(group_id) {
	
    location.href = 'index.php?sid=&do=pricing&group&gid=' + group_id;
}
function DelGroup(group_id) {
	group_id_full = group_id;
	console.log(group_id_full);
    $("#info").html("<?=LangAdmin::get('confirm_delete_group')?>");
    $("#dialog-confirm").dialog("open");
}


function delete_price_group(group_id) {
    var server_url = 'index.php?cmd=control&sid=<?=$GLOBALS['ssid']?>&do=deletegroup&id=' + group_id;
    $.ajax({
        url: server_url,
        success: function(data) {
            if (data == 'RELOGIN') location.href='index.php?expired';
            if (data == 'Ok') {
                $('tr#group' + group_id).hide();
                $("#dialog-confirm").dialog("close");
            }
            $("#info").html(data);
        },
        error: function() {

        }
    });
}
</script>

<br clear="all"/>