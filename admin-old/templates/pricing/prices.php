<div id="overlay"></div>
<div>
<? if (isset($action) && $action=='groupcategory') { ?>
   <br/><h2><?=LangAdmin::get('groups_settings')?></h2>
    <div class="grid_16">
        <? if (count($group_categories)) { ?>
        <table>
            <thead>
            <tr>
                <th><?=LangAdmin::get('External Id')?></th>
                <th><?=LangAdmin::get('Id')?></th>
                <th><?=LangAdmin::get('categoty_name')?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
                <? foreach ($group_categories as $category) { ?>
                <tr id="cat<?=$category['id']?>" style="border: 1px dotted #D3D3D3;">
                    <td><?=$category['externalid']?></td>
                    <td><?=$category['id']?></td>
                    <td><?=$category['name']?></td>
                    <td>
                        <button value="<?=$category['id']?>" class='delete_category'>
                            <?=LangAdmin::get('remove')?>
                        </button>
                    </td>
                </tr>
                <? } ?>
            </tbody>
        </table>
        <? } else { ?>
            <h3><?=LangAdmin::get('not_found_categories')?></h3>
        <? } ?>
        <button class="back_to_pricing"><?=LangAdmin::get('back')?></button>
    </div>
<? } else { ?>
    <br/><h2><?=LangAdmin::get('groups_settings')?></h2>
    <div class="grid_16">
        <table>
            <thead>
            <tr>
                <th><?=LangAdmin::get('name_of_price_group')?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
                <? foreach ($price_groups as $group) { ?>
                <tr id="group<?=$group['id']?>" style="border: 1px dotted #D3D3D3;">
                    <td><a href="index.php?sid=&do=pricing&groupid=<?=$group['id']?>"><?=$group['Name']?></a></td>
                    <td>
                        <button class="edit_group_categories" value="<?=$group['id']?>"><?=LangAdmin::get('edit_group_categories')?></button>
                        <button class="bind_category" value="<?=$group['id']?>"><?=LangAdmin::get('bind_category')?></button>
                    </td>
                </tr>
                <? } ?>
            </tbody>
        </table>
    </div>
<? } ?>

</div>

<script>
    
var group_id = 0;
var categoty_id = 0;


$('.round_settings')
    .button()
    .click(function () {
        showOverlay();
        var round = $('input[name=round_settings]').val();
        var server_url = 'index.php?cmd=control&sid=<?=$_SESSION['sid']?>' +
            '&do=setroundsettings&round=' + round;
        $.ajax({
            url: server_url,
            success: function(data) {
                hideOverlay();
                if (data == 'RELOGIN') location.href='index.php?expired';
                if (data == 'Ok') {
                    hideOverlay();
                }
            },
            error: function() {
                
            }
        });
        
});


$('.bind_category')
    .button()
    .click(function () {
        group_id = $(this).val();
        $("#dialog-add-category").dialog("open");
});


$('.delete_category')
    .button()
    .click(function () {
        categoty_id = $(this).val();
        $("#dialog-confirm-delete-cat").html("<?=LangAdmin::get('confirm_delete_categoty')?>");
        $("#dialog-confirm-delete-cat").dialog("open");
});


$('.back_to_pricing')
    .button()
    .click(function () {
        categoty_id = $(this).val();
        window.location.href = 'index.php?sid=<?=$_SESSION['sid'];?>&do=pricing';
});


$('.edit_group_categories')
    .button()
    .click(function () {
        group_id = $(this).val();
        window.location.href = 'index.php?sid=<?=$_SESSION['sid'];?>&do=pricing&groupcategory&gid=' + group_id;
});


$("#dialog-confirm-delete-cat").dialog({
    autoOpen: false,
    modal: true,
    buttons : {
        "<?=LangAdmin::get('yes')?>" : function() {
            delete_category_from_group(categoty_id, '<?=$group_id?>');
            
        },
        "<?=LangAdmin::get('cancellation')?>" : function() {
            $(this).dialog("close");
        }
    }
});


$("#dialog-add-category").dialog({
    autoOpen: false,
    width:550,
    modal: true,
    resizable: false,
    height:500,
    buttons : {
        "<?=LangAdmin::get('bind_category')?>" : function() {
            add_category_to_price_group(group_id);
            
        },
        "<?=LangAdmin::get('cancellation')?>" : function() {
            $(this).dialog("close");
        }
    }
});

function add_category_to_price_group(group_id) {
    var cid = $('input[name=bind_cat]:checked').val();
    var server_url = 'index.php?cmd=control&sid=<?=$_SESSION['sid']?>&do=addcategory&gid=' + group_id + '&cid=' + cid;
    $.ajax({
        url: server_url,
        success: function(data) {
            if (data == 'RELOGIN') location.href='index.php?expired';
            if (data == 'Ok') {
                $("#dialog-add-category").dialog("close");
            }
            $("#info").html(data);
        },
        error: function() {

        }
    });
}

function delete_category_from_group(categoty_id, group_id) {
    var server_url = 'index.php?cmd=control&sid=<?=$_SESSION['sid']?>&do=deletecategory&gid=' + group_id + '&cid=' + categoty_id;
    $.ajax({
        url: server_url,
        success: function(data) {
            if (data == 'RELOGIN') location.href='index.php?expired';
            if (data == 'Ok') {
                $('tr#cat' + categoty_id).hide();
                $("#dialog-confirm-delete-cat").dialog("close");
            }
            $("#info1").html(data);
        },
        error: function() {

        }
    });
}
</script>

<br clear="all"/>