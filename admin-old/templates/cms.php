<? include ("header.php"); ?>

<div class="main"><div class="canvas clrfix">

<?  /*
if ($status) { ?>
    <h1> <?=LangAdmin::get('page')?> </h1>
    <p>
    </p>
    <table>
        <tr>
            <th>ID</th>
            <th>A<?=LangAdmin::get('lias')?></th>
            <th><?=LangAdmin::get('title')?></th>
            <th><?=LangAdmin::get('language')?></th>
            <th><?=LangAdmin::get('actions')?></th>
        </tr>
        <? if (is_array($pages)) foreach ($pages as $page) { ?>
            <tr>
                <td><?=$page['id']?></td>
                <td><?=$page['alias']?></td>
                <td><?=$page['title']?></td>
                <td><? if (!@$page['lang_name']) {
                    print LangAdmin::get('not_selected');
                } else {
                    print $page['lang_name'];
                }?></td>
                <td>
                    <a href="../?p=<?=$page['alias']?>&pid=<?=$page['id']?>"
                       target="_blank"><?=LangAdmin::get('go')?></a>&nbsp;&nbsp;
                    <a href="../?p=<?=$page['alias']?>&edit&pid=<?=$page['id']?>"><?=LangAdmin::get('edit_the_contents')?></a>&nbsp;&nbsp;
                    <a href="?cmd=cmsadmin&do=edit&id=<?=$page['id']?>"><?=LangAdmin::get('change')?></a>&nbsp;&nbsp;
                    <a onclick="return confirm('<?=LangAdmin::get('remove')?>?');" href="?cmd=cmsadmin&do=del&id=<?=$page['id']?>"><?=LangAdmin::get('remove')?></a>&nbsp;&nbsp;
                    <a href="?cmd=cmsadmin&do=addSubPage&id_parent=<?=$page['id']?>">+&nbsp;подраздел</a>&nbsp;&nbsp;
                    &nbsp;</td>
            </tr>

            <? if (isset($page['children'][0])) foreach ($page['children'] as $p) { ?>
                <tr>
                    <td><?=$p['id']?></td>
                    <td><?=$p['alias']?></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$p['title']?></td>
                    <td><? if (!@$p['lang_name']) {
                        print LangAdmin::get('not_selected');
                    } else {
                        print $p['lang_name'];
                    }?></td>
                    <td>
                        <a href="../?p=<?=$p['alias']?>&pid=<?=$p['id']?>"
                           target="_blank"><?=LangAdmin::get('go')?></a>&nbsp;&nbsp;
                        <a href="../?p=<?=$p['alias']?>&edit&pid=<?=$p['id']?>"><?=LangAdmin::get('edit_the_contents')?></a>&nbsp;&nbsp;
                        <a href="?cmd=cmsadmin&do=edit&id=<?=$p['id']?>"><?=LangAdmin::get('change')?></a>&nbsp;&nbsp;
                        <a onclick="return confirm('<?=LangAdmin::get('remove')?>?');" href="?cmd=cmsadmin&do=del&id=<?=$p['id']?>"><?=LangAdmin::get('remove')?></a>&nbsp;&nbsp;
                        &nbsp;</td>
                </tr>
            <? } ?>
        <? } ?>
    </table>
    <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all"
        value="<?=LangAdmin::get('bookmark_this_page')?>" onclick="location.href='?cmd=cmsadmin&do=add'" />
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
  */
?>
</div></div>

<? include ("footer.php"); ?>

