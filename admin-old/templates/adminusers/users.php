<? include (TPL_DIR . "header.php"); ?>

<?
$perpage = (isset($_GET['ps'])) ? $_GET['ps'] : '10';
$page = (isset($_SESSION['admin_user_page'])) ? $_SESSION['admin_user_page'] : 1;
$page = (isset($_GET['p'])) ? $_GET['p'] : $page;
if ((isset($_GET['p']))) $_SESSION['admin_user_page'] = $_GET['p'];
$count = count($users);

$pages = array(10, 20, 50, 100);
?>

<div id="dialog-form" title="<?=LangAdmin::get('message')?>">
    <span id="info"></span>
</div>

<div style="clear:both;">

    <a href="index.php?cmd=adminusers&do=usercreate&sid=<?=$GLOBALS['ssid']?>"><?=LangAdmin::get('create_a_user')?></a>

    <? $from = ($page - 1) * $perpage + 1; ?>
    <? $end = min($from + $perpage - 1, $from + count($users) - 1); ?>

    <strong><?=LangAdmin::get('found')?> <?=$count?> <?=LangAdmin::get('users')?>
        <? if ($count) { ?>
            ; <?=LangAdmin::get('showing')?>: <?=LangAdmin::get('with')?> <?= $from ?> <?=LangAdmin::get('on')?> <?= $end ?>
            <? } ?>
    </strong><br/><br/>
    <? $error = (isset($error)) ? $error . @$_GET['error'] : @$_GET['error']; ?>
    <span id="error" style="color:red;font-weight: bold;">
        <? if (isset($error)) {
        print $error;
    } ?>
    </span>
</div>

<? if (isset($success)): ?>
    <strong style="color:green;"><?=$success;?></strong>
<? endif; ?>

<? if ($count): ?>
    <h2><?=LangAdmin::get('members')?></h2>

    <div style="float:right;">
        <select name="perpage">
            <? foreach ($pages as $perpagecount) { ?>
            <? $selected = ($perpagecount==$perpage) ? ' selected' : ''; ?>
            <option value="<?=$perpagecount?>" <?=$selected?>><?=$perpagecount?></option>
            <? } ?>
        </select> <br/>
    </div>

    <div id="overlay"></div>
    <div class="grid_16">
        <table>
            <thead>
            <tr>
                <th><?=LangAdmin::get('id')?></th>
                <th><?=LangAdmin::get('name')?></th>
                <th><?=LangAdmin::get('login')?></th>
                <th><?=LangAdmin::get('password')?></th>
                <th><?=LangAdmin::get('email')?></th>
                <th><?=LangAdmin::get('role')?></th>
                <th></th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td colspan="6" class="pagination">
                    <? $curpage = $page; ?>
                    <? $maxpage = ceil($count / $perpage); ?>

                    <? for ($i = 1; $i <= $maxpage; $i++) { ?>
                    <? if ($curpage == $i) { ?>
                        <span class="active curved"><?=$i?></span>
                        <? } else { ?>
                        <a class="curved" href="<?=$pageurl?>&p=<?=$i?>"><?=$i?></a>
                        <? } ?>
                    <? } ?>
                </td>
            </tr>
            </tfoot>
            <tbody>
            <? $count_users = 1; ?>
            <? $counter = 0; ?>
            <? for ($i = $from; $i <= $from + $perpage; $i++): ?>
                <? $counter++; ?>
                <? if ($count_users > $perpage) break; ?>
                <? if (!isset($users[$counter - 1])) continue; ?>
                <? $user = $users[$counter - 1]; ?>
                <? $user_id = (string)$user['id']; ?>
                <? $roles_list = array(); ?>
                <? foreach ($user['Roles'] as $r) { ?>
                    <? $roles_list[] = $r['Name']; ?>
                <? } ?>
                <tr id="user<?=$user['login']?>">
                    <td><?=$user_id?></td>
                    <td><a
                        href="index.php?sid=<?=$GLOBALS['ssid'];?>&cmd=adminusers&do=userinfo&id=<?=(string)$user['id']?>"><?=$user['name']?></a></td>
                    <td><?=$this->escape($user['login'])?></td>
                    <td><?=$user['password']?></td>
                    <td><?=$user['email']?></td>
                    <td><?=  implode(', ', $roles_list);?></td>
                    <td><a href="#" class="delete" onClick="confirm_delete_user('<?=$this->escape($user['login'])?>')"><?=LangAdmin::get('remove')?></a></td>
                </tr>
            <? endfor; ?>
            </tbody>
        </table>
    </div>
<? endif; ?>

<script>
    var user_id = '';
    
    $('select[name=perpage]').change(function() {
        var count = $('select[name=perpage] option:selected').val();
        location.href = 'index.php?cmd=adminusers&ps=' + count + '&sid=';
    });

    $(function () {
        $("#dialog-form:ui-dialog").dialog("destroy");

        $("#dialog-form").dialog({
            autoOpen:false,
            modal:true,
            buttons:{
                "<?=LangAdmin::get('yes')?>":function () {
                    delete_user(user_id);
                    $("#dialog-form").dialog("close");
                },
                "<?=LangAdmin::get('no')?>":function () {
                    $("#dialog-form").dialog("close");
                }
            }
        });

    });

    function confirm_delete_user(id) {
        $("#dialog-form").dialog("open");
        $('#info').html('<?=LangAdmin::get('users_will_be_removed')?>. Продолжить?');
        user_id = id;
    }
    
    function delete_user(id) {
        var server_url = 'index.php?cmd=adminusers&sid=<?=$GLOBALS['ssid']?>&do=delete&login=' + id;

        $.ajax({
            url:server_url,
            success:function (data) {
                if (data == 'RELOGIN') location.href = 'index.php?expired';

                if (data == 'Ok') {
                    $('#user' + id).hide();
                    clear_error();
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

</script>

<? include (TPL_DIR . "footer.php"); ?>	