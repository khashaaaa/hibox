<? include (TPL_DIR . "header.php"); ?>

<div id="dialog-form" title="<?=LangAdmin::get('message')?>">
    <span id="info"></span>
</div>

<div id="overlay"></div>
<div>
    <br/>
    <a href="index.php?cmd=adminusers&sid=<?=$GLOBALS['ssid']?>"> <
        <<?=LangAdmin::get('to_the_list_of_users')?>
    </a><br/>
    <? if (count($user_info)) { ?>
        <h2><?=LangAdmin::get('user_information')?></h2>

        <? //var_dump($user);?>

        <form action="<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;do=saveuser&amp;cmd=adminusers" method="post">
            <input type="hidden" name="Login" value="<?=$user_info['login']?>"/>
            <table class="userinfo">
                <tr>
                    <td><strong><?=LangAdmin::get('name')?>:</strong></td>
                    <td><input type="text" name="Name" value="<?=$user_info['name']?>"/></td>
                </tr>
                <tr>
                    <td><strong><?=LangAdmin::get('password')?>:</strong></td>
                    <td><input type="text" name="Password" value="<?=$user_info['password']?>"/></td>
                </tr>
                <tr>
                    <td><strong>Email:</strong></td>
                    <td><input type="text" name="Email" value="<?=$user_info['email']?>"/></td>
                </tr>
            </table>

            <input type="hidden" name="Id" value="<?=(string)$user_info['id']?>"/>

            <div class="fbut clrfix">
                <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="<?=LangAdmin::get('save')?>"/>
            </div>
        </form>

    <? } ?>


    <? if (count($available_roles) || count($user_active_roles)) { ?>
    <br/>
    <h2><?=LangAdmin::get('admin_user_roles')?></h2>
    <? } ?>

    <? if (count($available_roles)) { ?>
        <b><?=LangAdmin::get('add_role')?></b>
        <select name="new_role">
            <? foreach ($available_roles as $role) { ?>
                <option value="<?=$role['Name']?>"><?=$role['Name']?></option>
            <? } ?>
        </select>
        <button class="add_role"><?=LangAdmin::get('add')?></button><br/><br/>
    <? } ?>


    <? if (count($user_active_roles)) { ?>
        <div class="grid_10">
            <table class="active_roles">
                <thead><tr>
                    <th><b><?=LangAdmin::get('role')?></b></th>
                    <th><b><?=LangAdmin::get('role_description')?></b></th>
                    <th><b><?=LangAdmin::get('action')?></b></th>
                </tr></thead>
                <tbody>
                <? foreach ($user_active_roles as $role) { ?>
                    <tr>
                        <td><?=$role['Name']?></td>
                        <td><?=$role['Description']?></td>
                        <td>
                            <button class="delete_role" value="<?=$role['name']?>"><?=LangAdmin::get('remove')?>
                            </button>
                        </td>
                    </tr>
                <? } ?>
                </tbody>
            </table>
        </div>
    <? } ?>
    
</div>

<script>
    var role = '';

    $(function () {
        $("#dialog-form:ui-dialog").dialog("destroy");

        $("#dialog-form").dialog({
            autoOpen:false,
            modal:true,
            buttons:{
                "<?=LangAdmin::get('yes')?>":function () {
                    delete_role(role);
                    $("#dialog-form").dialog("close");
                },
                "<?=LangAdmin::get('no')?>":function () {
                    $("#dialog-form").dialog("close");
                }
            }
        });

    });

    
    function delete_role(role) {
        var server_url = 'index.php?cmd=adminusers&sid=<?=$GLOBALS['ssid']?>' +
            '&do=deleterole&login=<?=(string)$user_info['login']?>&role=' + role;
        showOverlay();
        $.ajax({
            url:server_url,
            success:function (data) {
                if (data == 'RELOGIN') location.href = 'index.php?expired';
                if (data == 'Ok') {
                    location.href = 'index.php?cmd=adminusers&sid=<?=$GLOBALS['ssid']?>' + 
                            '&do=userinfo&id=<?=(string)$user_info['id']?>';
                } else {
                    $('#error').html('<?=LangAdmin::get('there_was_an_error')?> ' + data);
                }
            },
            error:function () {
                $('#error').html('<?=LangAdmin::get('there_was_an_error')?>');
            }
        });
    }

    function clear_error() {
        $('#error').html('');
    }
    
    $('.delete_role')
        .button()
        .click(function() {
            $('#dialog-form').dialog("open");
            $('#info').html('<?=LangAdmin::get('role_will_be_removed')?>. Продолжить?');
            role = $(this).val();

    });
    
    $('.add_role')
        .button()
        .click(function() {
            showOverlay();
            var new_role = $('select[name=new_role] option:selected').val();
            var server_url = 'index.php?cmd=adminusers&sid=<?=$GLOBALS['ssid']?>' + 
                '&do=addrole&new_role=' + new_role + '&login=<?=(string)$user_info['login']?>';
            
            $.ajax({
                url:server_url,
                success:function (data) {
                    if (data == 'RELOGIN') location.href = 'index.php?expired';

                    if (data == 'Ok') {
                        location.href = 'index.php?cmd=adminusers&sid=<?=$GLOBALS['ssid']?>' + 
                            '&do=userinfo&id=<?=(string)$user_info['id']?>';
                    } else {
                        hideOverlay();
                        $('#error').html('<?=LangAdmin::get('there_was_an_error')?> ' + data);
                    }
                },
                error:function () {
                    $('#error').html('<?=LangAdmin::get('there_was_an_error')?>');
                }
            });

    });

</script>

<? include (TPL_DIR . "footer.php"); ?>