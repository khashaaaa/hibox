<? include (TPL_DIR."header.php");

$title = empty($id) ? LangAdmin::get('new_delivery') : LangAdmin::get('edit_delivery');
$item = isset($delivery) ? $delivery[0] : array();
?>

<br/>
<a href="index.php?sid=<?=$GLOBALS['ssid']?>&amp;cmd=calculator#tabs-2"> << <?=LangAdmin::get('back')?></a><br/>

<h2><?=$title?></h2>
<form action="<?=BASE_DIR;?>index.php?cmd=calculator&amp;do=delivery_save&amp;sid=<?=$GLOBALS['ssid'];?>" method="post">
    <table>
        <tr>
            <td><strong><?=LangAdmin::get('kind_of_delivery')?>:</strong></td>
            <td><input type="text" name="kind_of_delivery" value="<?= (!empty($item)) ? $item['name'] : '';?>"/></td>
            <td><input type="hidden" name="id" value="<?= (!empty($item)) ? $item['id'] : '';?>"/></td>
        </tr>
        <tr>
            <td width="200"><strong><?=LangAdmin::get('formula')?>:</strong></td>
            <td><input style="width:80%" type="text" name="formula" value="<?= (!empty($item)) ? $item['formula'] : '';?>"/></td>
        </tr>
    </table>

    <div class="fbut clrfix">
        <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="<?=LangAdmin::get('save')?>"/>
    </div>
</form>

<? include (TPL_DIR."footer.php"); ?>