<? include (TPL_DIR."header.php");

$title = LangAdmin::get('edit_params_of_formula');
$item = isset($data) ? $data : array();
?>

<br/>
<a href="index.php?cmd=calculator"> << <?=LangAdmin::get('back')?></a><br/>

<h2><?=$title?></h2>
<form action="<?=BASE_DIR;?>index.php?cmd=calculator&amp;do=delivery_for_country_save&amp;sid=<?=$GLOBALS['ssid'];?>" method="post">
    <table>
        <tr>
            <td><strong><?=LangAdmin::get('kind_of_delivery')?>:</strong></td>
            <td><label><?= (!empty($item)) ? $item['delivery_name'] : '';?></label>
                <input type="hidden" name="id" value="<?= (!empty($item)) ? $item['id'] : '';?>"/>
            </td>
        </tr>
        <tr>
            <td><strong><?=LangAdmin::get('country')?>:</strong></td>
            <td><label><?= (!empty($item)) ? $item['country_name'] : '';?></label></td>
        </tr>
        <tr>
            <td><strong><?=LangAdmin::get('formula')?>:</strong></td>
            <td><label><?= (!empty($item)) ? $item['formula'] : '';?></label></td>
        </tr>
        <tr>
            <td><strong><?=LangAdmin::get('step_of_weight')?> ($step):</strong></td>
            <td><input type="text" id="param_step" name="param_step" value="<?= (!empty($item)) ? $item['param_step'] : '';?>"/></td>
        </tr>
        <tr>
            <td width="200"><strong><?=LangAdmin::get('start_price')?> ($start):</strong></td>
            <td><input type="text" id="param_start" name="param_start" value="<?= (!empty($item)) ? $item['param_start'] : '';?>"/></td>
        </tr>
    </table>

    <div class="fbut clrfix">
        <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="<?=LangAdmin::get('save')?>"/>
    </div>
</form>

<? include (TPL_DIR."footer.php"); ?>
