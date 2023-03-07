<? include (TPL_DIR."header.php");

$title = empty($id) ? LangAdmin::get('new_delivery') : LangAdmin::get('edit_delivery');
$item = isset($delivery) ? $delivery : array();
?>
<script type="text/javascript" src="js/delivery.js"></script>
<style>
.error{
  color: red;
}
#build_help_full {
	position:absolute; display:none; width:700px; border:#000 2px solid; background:#CCC; height:500px; top:50%; margin-top:-350px; left:50%; margin-left:-350px; padding:10px;
}
</style>

<br/>
<a href="index.php?sid=<?=$GLOBALS['ssid']?>&amp;cmd=delivery#tabs-2"> << <?=LangAdmin::get('back')?></a>
<br/><br/>
<? if (isset($_GET['construcor'])) {  
	$posturl = '&construcor=1';?>	
	<a href="index.php?cmd=delivery&do=<?=$_GET['do']?>&id=<?=@$item['id']?>"> <?=LangAdmin::get('build_old_view')?></a>
<? } else { 	 
	$posturl = '';?>
	<a href="index.php?cmd=delivery&do=<?=$_GET['do']?>&id=<?=@$item['id']?>&construcor=1"> <?=LangAdmin::get('build_new_view')?></a>
<? } ?>  
<? if (isset($_GET['construcor'])) { ?>	
	<a href="#" onclick="document.getElementById('build_help_full').style.display = 'block'">Примеры использрвания конструктора.</a>
    <div id="build_help_full">Здесь представлено несколько примеров использования конструктора.
    	<br><br>
    	1. У службы доставки единые тарифы. К прмиру они доставляют посылки весом до 5 кг за 20 USD(незасимо хоть будет 1 кг, хоть 4,8 кг).<br>
        В таком случае заполняется поле "Максимальный вес" и ставится галочка только на "Учитывать в формуле минимальную стоимость доставки".<br>
        Итоговая формула будет иметь вид : ($weight > 5) && ($weight <= 0) ? 0 : $start <br><br>
        2. У службы доставки стоимость зависит только от веса. К прмиеру они берут по 5 USD за кг. <br>
        В таком случае ставится галочка только на "Учитывать в формуле стоимость шага по весу" и "Округлять вес до большего целого" все остальное остается пустым.<br>
        Если к примеру у службы доставки цена зависит даже от граммов - "Округлять вес до большего целого" не включаем<br>
        Итоговая формула будет иметь вид : $weight * $step <br><br>
        3. Служба доставки доставляет только крупные грузы от 10 кг и чем тяжелее груз тем дороже за каждые 2 кг. <br>
        В таком случае заполняется поле "Минимальный вес" и "Шаг по весу" и ставится галочка только на "Учитывать в формуле стоимость шага по весу" и "Округлять вес до большего целого".<br>
        Итоговая формула будет иметь вид : ($weight > 999) && ($weight <= 10) ? 0 : (  ceil ( $weight / 2 ) - 1 ) * $step <br><br>
        <a href="#" onclick="document.getElementById('build_help_full').style.display = 'none'" style="font-size:16px;">Закрыть.</a>
    </div>
<? } ?>
<h2><?=$title?></h2>
<? if (isset($parsed_formula['errorparse'])) { ?> 
<div style="width:95%; border:#F00 2px solid; padding:10px; color:#F00"> <?=LangAdmin::get('build_error')?></div>
<? } ?>
<form action="<?=BASE_DIR;?>index.php?cmd=delivery&amp;do=delivery_save&amp;sid=<?=$GLOBALS['ssid'];?><?=$posturl;?>" method="post" name="formul_gen" <? if (isset($_GET['construcor'])) { ?> onsubmit="return ValidateForm(); "<? } ?>>
    
    <input type="hidden" name="id" value="<?= (!empty($item)) ? $item['id'] : '';?>"/>
    
    <table>
        <tr>
            <td><strong><?=LangAdmin::get('name')?>:</strong></td>
            <td><input type="text" style="width:80%" name="name" value="<?= (!empty($item)) ? $item['name'] : '';?>"/></td>
  
        </tr>        
        <? if (isset($_GET['construcor'])) { ?>
        <tr>
            <td colspan="2"><strong><?=LangAdmin::get('build_go_on')?>:</strong></td>            
        </tr>  
        <tr>
            <td width="200"><?=LangAdmin::get('build_min_weight')?>:</strong></td>
            <td><input style="width:80%" type="text" name="min_weight"  id="min_weight" value="<?= (!empty($parsed_formula['min_weight'])) ? $parsed_formula['min_weight'] : '';?>"/></td>            
        </tr> 
        <tr>            
            <td colspan="2"><small><?=LangAdmin::get('build_help1')?></small></td>
        </tr>
        <tr>
            <td width="200"><?=LangAdmin::get('build_max_weight')?>:</strong></td>
            <td><input style="width:80%" type="text" name="max_weight" id="max_weight" value="<?= (!empty($parsed_formula['max_weight'])) ? $parsed_formula['max_weight'] : '';?>"/></td>            
        </tr>
        <tr>            
            <td colspan="2"><small><?=LangAdmin::get('build_help2')?></small></td>
        </tr>
        <tr>
            <td width="200"><?=LangAdmin::get('build_step_weight')?>:</strong></td>
            <td><input style="width:80%" type="text" name="step_weight" id="step_weight" value="<?= (!empty($parsed_formula['step_weight'])) ? $parsed_formula['step_weight'] : '';?>"/></td>
        </tr>
        <tr>            
            <td colspan="2"><small><?=LangAdmin::get('build_help3')?></small></td>
        </tr>
        <tr>
            <td width="200" id="min_price_delivery_txt"><?=LangAdmin::get('build_min_delivery')?> :</strong></td>
            <td><input type="checkbox" name="min_price_delivery" id="min_price_delivery" value="1"  <?= (!empty($parsed_formula['min_price_delivery'])) ? 'checked' : '';?>/></td>
        </tr>
        <tr>            
            <td colspan="2"><small><?=LangAdmin::get('build_help4')?></small></td>
        </tr>
        <tr>
            <td width="200" id="step_price_txt"><?=LangAdmin::get('build_price_delivery_step')?> :</strong></td>
            <td><input type="checkbox" name="step_price" id="step_price" value="1" <?= (!empty($parsed_formula['step_price'])) ? 'checked' : '';?>/></td>
        </tr>
        <tr>            
            <td colspan="2"><small><?=LangAdmin::get('build_help5')?></small></td>
        </tr>
        <tr>
        	<td width="200"><?=LangAdmin::get('build_round')?>:</strong></td>
            <td><input type="checkbox" name="rounding" id="checkbox" value="1" <?= (!empty($parsed_formula['rounding'])) ? 'checked' : '';?>/></td>            
        </tr> 
        <tr>            
            <td colspan="2"><small><?=LangAdmin::get('build_help6')?></small></td>
        </tr>
        <? } else { ?>
        <tr>
            <td width="200"><strong><?=LangAdmin::get('formula')?>:</strong></td>
            <td><input style="width:80%" type="text" name="formula" value="<?= (!empty($item)) ? $item['formula'] : '';?>"/></td>
        </tr>
        <tr>
            <td colspan="2">
                <small>
                    <?=LangAdmin::get('delivery_formula_desc1')?><br/>
                    <?=LangAdmin::get('delivery_formula_desc2')?><br/>
                    <?=LangAdmin::get('delivery_formula_desc3')?><br/>
                    <?=LangAdmin::get('delivery_formula_desc4')?><br/>
                    <?=LangAdmin::get('delivery_formula_desc5')?><br/>
                    <?=LangAdmin::get('delivery_formula_desc6')?><br/>
                </small>
            </td>
        </tr>
        <? } ?>
        
        <tr>
            <td><strong><?=LangAdmin::get('description')?>:</strong></td>
            <td><input style="width:80%" type="text" name="description" value="<?= (!empty($item)) ? $item['description'] : '';?>"/></td>
        </tr>
        <tr>
            <td><strong><?=LangAdmin::get('currency2')?>:</strong></td>
            <td>
                <select style="width:150px;" id="currency" name="currencycode">
                    <? $hasBeenSelected = false; ?>
                    <? foreach ($currency_list as $currency) { ?>
                        <? $selected = (!empty($item) && $item['currencycode']==$currency['code']) ? ' selected' : ''; ?>
                        <? $hasBeenSelected = $hasBeenSelected || $selected; ?>
                        <option <?=$selected?> value="<?=$currency['code']?>"> <?=$currency['code']?> (<?=$currency['sign']?>)</option>
                    <? } ?>
                    <? if(!$hasBeenSelected && isset($item['currencycode'])){ ?>
                        <option selected value="<?=$item['currencycode']?>"> <?=$item['currencycode']?></option>
                    <? } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><strong><?=LangAdmin::get('delivery_order')?>:</strong></td>
            <td><input type="text" name="order" value="<?= (!empty($item)) ? $item['order'] : '';?>"/></td>
        </tr>
        <tr>
            <td><strong><?=LangAdmin::get('delivery_integration_type')?>:</strong></td>
            <td>
                <select style="width:250px;" id="integration_type" name="integration_type">
                    <? foreach ($integrationTypes as $type) { ?>
                        <? $selected = ($item['IntegrationType'] == $type['IntegrationType']) ? 'selected' : ''; ?>
                        <? if (($type['IsAvailable'] == 'true') || ($selected != '')) { ?>
                            <? $disabled = ($type['IsAvailable'] != 'true') ? 'style="color: grey;"' : ''; ?>
                            <option <?=$selected?> <?=$disabled?> value="<?=$type['IntegrationType']?>"><?=$type['Name']?></option>
                        <? } ?>
                    <? } ?>
                </select>
                <input type="hidden" name="current_integration_type" value="<?=$item['IntegrationType']?>" />
            </td>
        </tr>
    </table>

    <div class="fbut clrfix">
        <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="<?=LangAdmin::get('save')?>"/>
    </div>
</form>

<? include (TPL_DIR."footer.php"); ?>