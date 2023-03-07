<?
include ("header.php");

?>
<div class="main"><div class="canvas clrfix">

<? if ($status) { ?>
    <h1> <?=LangAdmin::get('news')?> </h1>
    <p>
    </p>
    <table>
        <tr>
            <th>ID</th>
            <th><?=LangAdmin::get('title')?></th>
            <th><?=LangAdmin::get('brief')?></th>
            <th><?=LangAdmin::get('image')?></th>
            <th><?=LangAdmin::get('language')?></th>
            <th><?=LangAdmin::get('actions')?></th>
        </tr>
        <? if(is_array($news)) foreach($news as $new) { ?>
            <tr>
            <td><?=$new['id']?></td>
            <td><?=$new['title']?></td>
            <td><?=$new['brief']?></td>
            <td><img src="<?=DigestRepository::getImage($new['image'], "thumb")?>"  width="100"/></td>
            <td><? if(!@$new['lang_name']) { print LangAdmin::get('not_selected'); } else { print $new['lang_name']; }?></td>
            <td>
            <a href="../?p=news&id=<?=$new['id']?>" target="_blank"><?=LangAdmin::get('go')?></a>&nbsp;&nbsp;&nbsp;
            <a href="../?p=news&edit&id=<?=$new['id']?>"><?=LangAdmin::get('edit_the_contents_of')?></a>&nbsp;&nbsp;&nbsp;
            <a href="?cmd=news&do=edit&id=<?=$new['id']?>"><?=LangAdmin::get('change')?></a>&nbsp;&nbsp;&nbsp;
            <a href="javascript:void(0)"  onclick="confirm_delete_new('<?=$new['id']?>');"><?=LangAdmin::get('remove')?></a>&nbsp;&nbsp;&nbsp;
            &nbsp;</td>
            </tr>
        <? } ?>
    </table>
    <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="<?=LangAdmin::get('add_news')?>" onclick="location.href='?cmd=news&do=add'"/>
<? } else { ?>
    <p><?=LangAdmin::get('error_connecting_to_database')?>.</p>
    <p><?=LangAdmin::get('check_configcustom_for_correct_db_accesses')?>.</p>
    <p><?=LangAdmin::get('example')?>:</p>
    <pre>
    define('DB_HOST', 'localhost');
    define('DB_USER', 'otbox');
    define('DB_PASS', '*******');
    define('DB_BASE', 'otbox');
    </pre>
<? } ?>
    
</div></div>
<script>
    function confirm_delete_new(id) {
        if (confirm("<?=LangAdmin::get('confirm_delete_new')?>" + ' (ID - ' + id + ' )')) {
            var url = '?cmd=news&do=del&id=' + id;
            window.location.href = url;
        } else {
            return false;
        }
    }
</script
<?
include ("footer.php");
?>

