<? include (TPL_DIR."header.php");

$title = empty($id) ? LangAdmin::get('bind_category') : LangAdmin::get('edit_category');
$item = isset($categories) ? $categories[0] : array();
?>

<br/>
<a href="index.php?sid=<?=$GLOBALS['ssid']?>&amp;cmd=my_categories"> << <?=LangAdmin::get('back')?></a><br/>

<h2><?=$title?></h2>
<form action="<?=BASE_DIR;?>index.php?cmd=my_categories&amp;do=save&amp;sid=<?=$GLOBALS['ssid'];?>" method="post">
    <table>
        <tr>
            <td width="30%"><strong><?=LangAdmin::get('categoty_name')?>:</strong></td>
            <td>
				<input type="text" style="width:70%" name="name" value="<?= (!empty($item)) ? $item['name'] : '';?>"/>
				<input type="hidden" name="id" value="<?= (!empty($item)) ? $item['id'] : '';?>"/>
				<input type="hidden" name="pid" value="<?= (isset($_GET['pid'])) ? (int)$_GET['pid'] : 0 ;?>"/>
			</td>
        </tr>
        <tr>
            <td><strong><?=LangAdmin::get('description')?>:</strong></td>
            <td><input type="text" style="width:70%" name="description" value="<?= (!empty($item)) ? $item['description'] : '';?>"/></td>
        </tr>
    </table>

    <div class="fbut clrfix">
        <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="<?=LangAdmin::get('save')?>"/>
    </div>
</form>

<? include (TPL_DIR."footer.php"); ?>
