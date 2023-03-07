<? include (TPL_DIR."header.php");

$title = empty($id) ? LangAdmin::get('new_country') : LangAdmin::get('edit_country');
$item = isset($countries) ? $countries[0] : array();
?>

<br/>
<a href="index.php?sid=<?=$GLOBALS['ssid']?>&amp;cmd=calculator#tabs-3"> << <?=LangAdmin::get('back')?></a><br/>

<h2><?=$title?></h2>
<form action="<?=BASE_DIR;?>index.php?cmd=calculator&amp;do=country_save&amp;sid=<?=$GLOBALS['ssid'];?>" method="post">
    <table>
        <tr>
            <td><strong><?=LangAdmin::get('name')?>:</strong></td>
            <td><input type="text" name="name" value="<?= (!empty($item)) ? $item['name'] : '';?>"/></td>
            <td><input type="hidden" name="id" value="<?= (!empty($item)) ? $item['id'] : '';?>"/></td>
        </tr>
    </table>

    <div class="fbut clrfix">
        <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="<?=LangAdmin::get('save')?>"/>
    </div>
</form>

<? include (TPL_DIR."footer.php"); ?>
