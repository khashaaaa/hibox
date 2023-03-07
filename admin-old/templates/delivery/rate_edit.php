<? include (TPL_DIR."header.php");

$title = empty($id) ? LangAdmin::get('new_delivery_rate') : LangAdmin::get('edit_delivery_rate');
$item = isset($rate) ? $rate : array();
?>

<br/>
<a href="index.php?sid=<?=$GLOBALS['ssid']?>&amp;cmd=delivery#tabs-1"> << <?=LangAdmin::get('back')?></a>
<br/><br/>

<h2><?=$title?></h2>
<form action="<?=BASE_DIR;?>index.php?cmd=delivery&amp;do=rate_save&amp;sid=<?=$GLOBALS['ssid'];?>" method="post">
    <? if (isset($_GET['delivery_id'])) { ?>
        <? $delivery_id = $_GET['delivery_id']; ?>
    <? } else { ?>
        <? $delivery_id = (!empty($item)) ? $item['externaldeliverytypeid'] : ''; ?>
    <? } ?>
    <input type="hidden" name="id" value="<?= (!empty($item)) ? $item['id'] : '';?>"/>
    <input type="hidden" name="externaldeliverytypeid" value="<?=$delivery['id'];?>"/>
    
    <table>
        <tr>
            <td><strong><?=LangAdmin::get('delivery')?>:</strong></td>
            <td><?= $delivery['name']; ?></td>
  
        </tr>
        <tr>
            <td><strong><?=LangAdmin::get('country')?>:</strong></td>
            <td>
                <select name="countrycode">
                    <? foreach ($all_countries as $id=>$country) { ?>
                        <? $selected = ($id==@$item['countrycode']) ? 'selected' : ''; ?>
                        <option value="<?=$id?>" <?=$selected?>> <?=$country?> (<?=$id?>)</option>
                    <? } ?>
                </select>
            </td>
  
        </tr>
        <tr>
            <td width="200"><strong><?=LangAdmin::get('start')?>:</strong></td>
            <td><input type="text" name="start" value="<?= (!empty($item)) ? $item['start'] : '';?>"/></td>
        </tr>
        <tr>
            <td><strong><?=LangAdmin::get('step')?>:</strong></td>
            <td><input type="text" name="step" value="<?= (!empty($item)) ? $item['step'] : '';?>"/></td>
        </tr>
        <tr>
            <td><strong><?=LangAdmin::get('isenabled')?>:</strong></td>
            <td>
                <? $checked = (!empty($item) && $item['isenabled']) ? 'checked' : ''; ?>
                <input type="checkbox" name="isenabled" value="1" <?=$checked?>/>
            </td>
        </tr>
    </table>

    <div class="fbut clrfix">
        <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="<?=LangAdmin::get('save')?>"/>
    </div>
</form>

<? include (TPL_DIR."footer.php"); ?>