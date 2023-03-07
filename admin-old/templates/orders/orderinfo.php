<?
$status_desc = array(1 => LangAdmin::get('desc_not_paid'),
    2 => LangAdmin::get('desc_in_handling'),
    3 => LangAdmin::get('desc_price_conf'),
    4 => LangAdmin::get('desc_ordered'),
    5 => LangAdmin::get('desc_quality_control'),
    6 => LangAdmin::get('desc_received'),
    7 => LangAdmin::get('desc_packed'),
    8 => LangAdmin::get('desc_ready_to_ship'),
    9 => LangAdmin::get('desc_posted'),
    10 => LangAdmin::get('desc_completed'),
    12 => LangAdmin::get('desc_unable_deliver'),
    13 => LangAdmin::get('desc_cancelled'),);
?>
<script>
    $(function(){
        $('.cache').each(function(){
            var src = $(this).attr('path');
            var span = $(this);
            $.post('/admin-old/write_cache_image.php', {'src': src}, function(data){
            });
        })
        
        if (<?=@strlen($error)?>) {
            $("#dialog-error").dialog("open");
        }
    });
</script>

<? if(count($order_info['saleslineslist'])){?>
    <div class="grid_16">
    <div id="overlay"></div>
	<? if (defined('CFG_BUYINCHINA')) { ?>
		<form action="index.php" method="GET">
			<br/>
				<p>
					<?=LangAdmin::get('select_the_status_of')?>:&nbsp;
					<select name="filter[state]" class="combolist" style="width:auto;">
						<option value=""><?=LangAdmin::get('all')?></option>
						<? foreach($usedStatusList as $status) { ?>
						<option value="<?=$status?>" <? if(isset($_GET['filter']) && $_GET['filter']['state'] == addslashes($status)) print 'selected'; ?>><?=$status?></option>
						<? } ?>
					</select>
					<label>&nbsp;&nbsp;</label>
					<input type="submit" value="<?=LangAdmin::get('apply_filters')?>" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">
				</p>

			<input type="hidden" name="cmd" value="orders"/>
			<input type="hidden" name="do" value="orderinfo"/>
			<input type="hidden" name="id" value="<?=$_GET['id']?>"/>

			<!-- <?=LangAdmin::get('name_of_client')?>:<input type="text" name="filter[client]"/>
			<?=LangAdmin::get('name_of_operator')?>:<input type="text" name="filter[operator]"/> -->
		</form>
	<? }?>
    <!---   Для фотобудки-->
    <? if (in_array('PhotoReport', General::$enabledFeatures)) { ?>
        <link rel="stylesheet" type="text/css" href="js/ItemPhoto/css/styles.css" />
		<link rel="stylesheet" type="text/css" href="js/ItemPhoto/fancybox/jquery.fancybox-1.3.4.css" />

		
        <?=LangAdmin::loadJSTranslation(array('exe_error','load_more','loading'))?>
        <script src="js/ItemPhoto/fancybox/jquery.easing-1.3.pack.js"></script>
		<script src="js/ItemPhoto/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
		<script src="js/ItemPhoto/webcam/webcam.js"></script>
		<script src="js/ItemPhoto/js/script.js"></script>
        <div id="PhotosBlock">
        	<span id="HidePhotos"><?=LangAdmin::get('close')?></span>
            <div id="loading"> <img src="../../css/i/loading.gif" width="30" height="30" /></div>
        	<div id="photos"></div>
            
            
        </div>
        <div id="camera">
        	<div id="screen"></div>
        	<div id="buttons">
        		<div class="buttonPane">
        			<a id="shootButton" href="" class="blueButton"><?=LangAdmin::get('shoot')?></a>
        		</div>
        		<div class="buttonPane hidden">
        			<a id="cancelButton" href="" class="blueButton"><?=LangAdmin::get('cancel')?></a>
                    <a id="uploadButton" href="" class="greenButton"><?=LangAdmin::get('upload')?></a>
        		</div>
        	</div>
        	<span class="settings"></span>
            <span  id="HideCamera"><?=LangAdmin::get('close')?></span>
		</div>
    <? } ?>
    <!---   Для фотобудки-->
	<table>
        <thead>
            <tr>
                <th width="10%"><?=LangAdmin::get('photo')?></th>
                <th width="30%"><?=LangAdmin::get('product_information')?></th>
                <th width="20%"><?=LangAdmin::get('quantity')?>, <?=LangAdmin::get('amount')?></th>
                <th width="30%"><?=LangAdmin::get('status_of_goods')?></th>
                <th width="5%" colspan="2" style="text-align:right">
                    <input type="button" onclick="gettowork()" value="<?=LangAdmin::get('get_to_work')?>"> &nbsp;&nbsp;
                    <input type="checkbox" name="all_products" value="1" style="width:17px"/>
                </th>
            </tr>
        </thead>

        <tbody style="border: 1px dotted #D3D3D3;">
            <? $visible_items_count = 0; ?>
            <? foreach($order_info['saleslineslist'] as $item) { //var_dump($item); die;?>
            <? if (!Permission::show_order_field('r_status_id_'.(string)$item['statusid'])) continue; ?>
            <? $visible_items_count++; ?>
            <? $sign = ' '.LangAdmin::get('kg').'.'; ?>
            <? if (defined('CFG_GLOBAL_DELIVERY_PRICE')) { ?>
                <? $summa = round((float)$item['pricecust'] * (int)$item['qty'],2) + round((float)$item['weight'] * @CFG_GLOBAL_DELIVERY_PRICE * (int)$item['qty']); ?>
            <? } else { ?>
                <? $summa = round((float)$item['pricecust'] * (int)$item['qty'],2); ?>
            <? } ?>

            <? $configuration_orig = ''; ?>
            <? if (defined('CFG_BUYINCHINA')&&$_SESSION['active_lang_admin']==='en'
                    && isset($item['ConfigExternalTextOrig']) && !empty($item['ConfigExternalTextOrig'])) { ?>
                <? $configuration = explode(';', $item['ConfigExternalTextOrig']); ?>
                <? $configuration = implode('<br/>', $configuration); ?>
            <? } else { ?>
                <? $configuration = explode(';', $item['configtext']); ?>
                <? $configuration = implode('<br/>', $configuration); ?>
                <? if (isset($item['ConfigExternalTextOrig']) && !empty($item['ConfigExternalTextOrig'])) { ?>
                    <? $configuration_orig = explode(';', $item['ConfigExternalTextOrig']); ?>
                    <? $configuration_orig = implode('<br/>', $configuration_orig); ?>
                <? } ?>
            <? } ?>

            <? if($item['VendPurchId']){ ?>
            <tr>
                <td colspan="6">
                    <div style="background-color: #ccc; display: block; -moz-border-radius: 5px;border-radius: 4px; margin: 0 0 15px 0; font-size: 14px; padding: 5px">
                        <b>Данный товар забронирован на таобао. Его OutId: <?=$item['VendPurchId']?></b>
                    </div>
                </td>
            </tr>
            <? } ?>
            <tr id="item<?=$item['id']?>" style="border: 1px dotted #D3D3D3;">
                <td>
                    <b><nobr>№ <? $oid = str_replace('ORD-', '', $order_info['salesorderinfo']['id']); print (int)$oid; ?> - <?=@$item['linenum']?></nobr></b>
                    <br><br>
                    <img src="<?=$item['itemimageurl'];?>_70x70.jpg" class="big_image" alt="" width="70" height="70"/>
                    <br/><b><a target="_blank" href="../index.php?p=item&id=<?=$item['itemtaobaoid'];?>"><?=LangAdmin::get('link_to_site')?></a><br/></b>

                    <?
                    if(defined('CFG_CACHE_ADMIN_IMAGES')){
                        $cacheFilePath = explode('/',$item['itemimageurl']);
                        $cacheFileName = end($cacheFilePath);
                        $img_local = dirname(dirname(dirname(__FILE__))).'/cache/'.$cacheFileName;
                        if(file_exists($img_local) && @getimagesize($img_local)){
                            print 'Изображение закешировано';
                        }
                        else{
                            print '<span class="cache" path="'.$item['itemimageurl'].'"></span>';
                        }
                    }
                    ?>
                </td>
                <td>
                    <? if (defined('CFG_BUYINCHINA')&&$_SESSION['active_lang_admin']==='en'
						&& isset($item['NameOrig']) && !empty($item['NameOrig'])) { ?>
						<?=$item['NameOrig'];?><br/>
					<? } else { ?>
						<a href="http://<?=rtrim($_SERVER['HTTP_HOST'],'/').dirname(dirname($_SERVER['SCRIPT_NAME']))?>index.php?p=item&id=<?=$item['itemtaobaoid'];?>"><?=$item['briefdescrtrans'];?></a><br/>
					<? } ?>
                                        				<b><?=LangAdmin::get('original')?>:</b><a target="blank" href="<?=$item['itemexternalurl'];?>" id="taobaoid<?=$item['id']?>"><?=$item['itemtaobaoid'];?></a><br/>
                    <b><?=LangAdmin::get('seller')?>:</b><?=$item['vendnick'];?><br/>
                    <? if (isset($item['configtext'])) { ?>
                        <b><?=LangAdmin::get('configuration')?>:</b><br/><?=$configuration;?>
                    <? } ?>
                    <? if ($configuration_orig) { ?>
                        <?=$configuration_orig;?>
                    <? } ?>
                </td>
                <td>
                    <? if (defined('CFG_BUYINCHINA')) { ?>
                        <? if ((float)$item['NewPriceCust'] > 0.01) { ?>
                            <s><?=LangAdmin::get('price')?>:<span id="price<?=$item['id']?>"><?=round((float)$item['pricecust'],2);?></span><?=$sign_m;?></s><br/>
                            <strong><?=LangAdmin::get('new_price')?>:</strong> <span style="font-size:14px;color:Red"><b><?=number_format((float)$item['NewPriceCust'], 2, '.', ' ').' '.$sign_m?></b></span>
                        <? } else { ?>
                            <strong><?=LangAdmin::get('price')?>: </strong><span id="price<?=$item['id']?>"><?=round((float)$item['pricecust'],2);?></span><?=$sign_m;?><br/>
                        <? } ?>
                        <button class="change_price" value="<?=$item['id']?>" tag="<?=$price?>" title="<?=LangAdmin::get('change_price')?>"><?=LangAdmin::get('change_price')?></button><br/>
						<? if (isset($item['taobaoprice']) && $item['taobaoprice'] > 0) { ?>
							<strong><?=LangAdmin::get('taobaoprice')?>:</strong> <? echo $item['taobaoprice']?><?=isset($CNYsign)?$CNYsign:' 元';?><br/>
						<? }?>
						<? if (isset($item['taobaodelivery']) && $item['taobaodelivery'] > 0) { ?>
							<strong><?=LangAdmin::get('taobaodelivery')?>:</strong> <? echo $item['taobaodelivery']?><?=isset($CNYsign)?$CNYsign:' 元';?><br/>
						<? }?>
					<? } elseif(defined('CFG_SHOPTAO')) { ?>
                        <strong><?=LangAdmin::get('price')?>:</strong>
                        <? if (isset($item['taobaodelivery']) && $item['taobaodelivery'] > 0) { ?>
                        <span id="price<?=$item['id']?>">
                            <? echo round((float)$item['pricecust'],2)-$item['taobaodelivery'];?>
                        </span><?=$sign_m;?><br/>
                        <strong><?=LangAdmin::get('taobaodelivery')?>:</strong> <? echo $item['taobaodelivery'].' 元';?><br/>
                        <? } else { ?>
                        <span id="price<?=$item['id']?>">
                                <?=round((float)$item['pricecust'],2);?>
                        </span><?=$sign_m;?>
                        <? } ?>
                    <? } else { ?>
                        <strong><?=LangAdmin::get('weight')?>:</strong> <span id="weight<?=$item['id']?>"><?=(float)$item['weight']?></span> <?=$sign?><br/><br/>
                        <? if (defined('CFG_GLOBAL_DELIVERY_PRICE')) { ?>
                            <? $price = round((float)$item['pricecust']+round($item['weight'] * @CFG_GLOBAL_DELIVERY_PRICE * (int)$item['qty']),2);?>
                        <? } else { ?>
                            <? $price = round((float)$item['pricecust'], 2);?>
                        <? } ?>
                        <? if ((float)$item['NewPriceCust'] > 0.01) { ?>
                            <s><?=LangAdmin::get('price')?>: <span id="price<?=$item['id']?>"><?=$price?></span><?=$sign_m;?></s><br/>
                            <strong><?=LangAdmin::get('new_price')?>:</strong> <span style="font-size:14px;color:Red; white-space:nowrap"><b><?=number_format((float)$item['NewPriceCust'], 2, '.', ' ').' '.$sign_m?></b></span>
                        <? } else { ?>
                            <strong><?=LangAdmin::get('price')?>:</strong> <span id="price<?=$item['id']?>"><?=$price?></span><?=$sign_m;?><br/>
                        <? } ?>
                        <? if (!defined('NO_CHANGE_PRICE_ADMIN')) { ?>
                            <button class="change_price" value="<?=$item['id']?>" tag="<?=$price?>" title="<?=LangAdmin::get('change_price')?>"><?=LangAdmin::get('change_price')?></button><br/>
                        <? } ?>
                    <? } ?>
                    <br/>
                    <strong><?=LangAdmin::get('quantity')?>:</strong> <span id="qty<?=$item['id']?>"><?=$item['qty']?></span>
                    <br/><br/>
					<span style="display:inline-block; vertical-align: top;">
	                    <strong><?=LangAdmin::get('in_total')?>:</strong>
					</span>
					<span style="display:inline-block">
						<span id="total<?=$item['id']?>"><?=$summa?></span><? echo $sign_m; ?><br/>
						<? if (defined('CFG_BUYINCHINA')&&((isset($item['taobaodelivery']) && $item['taobaodelivery'] > 0) ||(isset($item['taobaoprice']) && $item['taobaoprice'] > 0))) { ?>
							<? echo ' ',$item['taobaoprice']+$item['taobaodelivery']?><?=isset($CNYsign)?$CNYsign:' 元';?><br/>
						<? } ?>
					</span>
				</td>
                <td>
                    <strong><?=LangAdmin::get('current_status')?>:</strong> <span id="curstatus<?=$item['id']?>"><?=$item['statusname'];?></span><br/><br/>
                    <span id="curstatusid<?=(int)$item['id']?>" style="display:none;"><?=$item['statusid'];?></span>
                    <strong><?=LangAdmin::get('change_status_to')?>:</strong><br/>

                    <? if(isset($status_list[$item['id']])) { ?>
                        <? $status_list[$item['id']] = Permission::filter_order_lines_status($status_list[$item['id']]);?>
                    <? } ?>

                    <? if(isset($status_list[$item['id']])) { ?>
                        <select class="__combolist" id="status<?=$item['id']?>" name="status<?=$item['id']?>" style="width:180px">
                            <option value=""><?=LangAdmin::get('select_the_status_of')?></option>
                            <? foreach($status_list[$item['id']] as $status) { ?>
                                <option value="<?=$status['id']?>"
                                        <? if ($status['id'] === $item['statusid']) echo 'selected="selected"'; ?>
                                        title="<?=@$status_desc[@$status['id']]?>">
                                    <?=$status['name']?>
                                </option>
                            <? } ?>
                        </select>
                        <br/>
                        <small id="desc_status_<?=$item['id']?>"></small>
                    <? //var_dump($status_list);?>
                    <? } else { ?>
                        <?=$item['statusname'];?>
                    <? } ?>
                </td>
                <td style="text-align: right;">
                    <? if (Permission::show_order_field('can_delete_good')) {  ?>
                        <? if($item['statusname'] != 10) { ?>
                        <button class="deleteitem" value="<?=$item['id']?>" title="<?=LangAdmin::get('delete_item')?>"><?=LangAdmin::get('delete_item')?></button>
                        <br/><br/>
                        <? } ?>
                    <? } ?>
                    <button class="savestatusitem" value="<?=$item['id']?>" title="<?=LangAdmin::get('desc_save_the_string')?>"><?=LangAdmin::get('save_the_string')?></button>
                    <? if($item['qty'] > 1) { ?>
                        <br/><br/>
                        <button class="show_part_dialog" value="<?=$item['id']?>" title="<?=LangAdmin::get('part_item')?>"><?=LangAdmin::get('part_item')?></button>
                    <? } ?>
                    <? if (in_array('PhotoReport', General::$enabledFeatures)) { ?>
                    	<br/><br/>
                    	<button class="ShowCamera" value="<?=$item['id']?>" class="ui-widget ui-state-default ui-corner-all ui-button-text-only"><?=LangAdmin::get('make_photo')?></button>
                    	<br/>
                        <button class="ShowPhotos" value="<?=$item['id']?>" class="ui-widget ui-state-default ui-corner-all ui-button-text-only"><?=LangAdmin::get('show_photo')?></button>
                    <? } ?>
                </td>
                <td style="text-align: right;">
                    <input type="checkbox" name="checked_item" value="<?=$item['id']?>" style="width:17px"/>
                </td>
              </tr>
              <tr id="itemcom<?=$item['id']?>" style="border: 1px dotted #D3D3D3;">
                  <td colspan="2">
                      <strong><?=LangAdmin::get('product_custom_description')?></strong><br/>
                      <textarea width="200px" rows="4" cols="30" name="desc_<?=$item['id']?>"  id="desc_<?=$item['id']?>"><?=$item['customdescription']?></textarea><br>

                  </td>
                  <td colspan="2">
                      <strong><?=LangAdmin::get('product_custom_description_english')?></strong><br/>
                      <textarea width="200px" rows="4" cols="30" name="desc_en_<?=$item['id']?>" id="desc_en_<?=$item['id']?>"><?=$item['customdescriptioninenglish']?></textarea><br>
                  </td>
                  <td colspan="2">
                      <button class="savedesc" id="savedesc<?=$item['id']?>" value="<?=$item['id']?>" title="<?=LangAdmin::get('save_this_description')?>"><?=LangAdmin::get('save_this_description')?></button>
                  </td>
              </tr>
              <tr id="itemcom<?=$item['id']?>" style="border: 1px dotted #D3D3D3;">
                  <td colspan="4"><strong><?=LangAdmin::get('comment_buyer_to_the_product')?>:<?=$item['custcomment']?></strong></td>
                  <td><strong><?=LangAdmin::get('commentary_to_the_operator_product')?>:</strong><br/>
                      <textarea width="200px" rows="4" cols="20" name="opcom<?=$item['id']?>" onfocus="$('#saveopcom<?=$item['id']?>').show()" id="opcom<?=$item['id']?>"><?=$item['operatorcomment']?></textarea><br>
                      <button class="saveopcom" id="saveopcom<?=$item['id']?>" style="display:none;" value="<?=$item['id']?>" title="<?=LangAdmin::get('save_this_comment')?>"><?=LangAdmin::get('save_this_comment')?></button>
                  </td>
                  <td style="text-align: right;">&nbsp;</td>
              </tr>
              <?  }  ?>
          </tbody>
    </table>

    <? if ($visible_items_count) { ?>
    <table>
        <tbody>
            <tr>
                <td></td>
                <td style="text-align: right;">
                    <button class="savestatusallitem" title="<?=LangAdmin::get('save')?>"><?=LangAdmin::get('save')?></button>
                </td>
            </tr>
        </tbody>
    </table>
    <? } ?>
    </div>
<? } ?>

<script type="text/javascript" src="js/jquery.combobox.js"></script>
<script type="text/javascript">

var OrederID='<?=$order_info['salesorderinfo']['id']?>';
var ItemID = 0;
var descs = Array();

/*
$(function(){
    $('.combolist').combobox();
    <? foreach ($status_desc as $key=>$status) { ?>
        descs['<?=$key?>'] = '<?=$status?>';
    <? }?>
});
*/

$('.combolist').change(function() {
    var id = $(this).attr('id').slice(6);
    $('#desc_status_' + id).html(descs[$(this).attr('value')]);
});

$('#cancelline')
    .button()
    .click(function(){
        window.location.href = 'index.php?cmd=orders&do=savenewpackage'+
            '&id=<?=$order_info['salesorderinfo']['id']?>'+
            '&user=<?=$order_info['salesorderinfo']['custid']?>';
});

$('.deleteitem')
    .button()
    .removeClass('ui-button')
    .click(function(){
        confirm_delete("<?=$order_info['salesorderinfo']['id']?>",$(this).val());

});

$('.ShowCamera').button().removeClass('ui-button');
$('.ShowPhotos').button().removeClass('ui-button');



$('.savestatusitem')
    .button()
    .removeClass('ui-button')
    .click(function(){
        cnange_status("<?=$order_info['salesorderinfo']['id']?>",$(this).val());

});

$('.show_part_dialog')
    .button()
    .removeClass('ui-button')
    .click(function() {
        var line_id = $(this).val();
        var qty = $('#qty' + line_id).val();

        var partitem = '<?=LangAdmin::get('part_item_desc1')?><br/>';
        partitem += '<input type="text" name="separate" value="1"/><br/>';
        partitem += '<input type="hidden" name="line_id" value="' + line_id + '"/>';
        partitem += '<br/><?=LangAdmin::get('part_item_desc2')?>';
        action = 3;

        $('#info').empty().html(partitem);
        $('#dialog-form').dialog("open");
});

$('.change_price')
    .button()
    .removeClass('ui-button')
    .click(function(){
        var line_id = $(this).val();
        var old_price = $(this).attr('tag');
        var qty = $('#qty' + line_id).val();

        var partitem = '<?=LangAdmin::get('change_price_desc1')?><br/>';
        partitem += '<input type="text" name="new_price" value="' + old_price + '"/><br/>';
        partitem += '<input type="hidden" name="line_id" value="' + line_id + '"/>';
        partitem += '<br/><?=LangAdmin::get('change_price_desc2')?>';
        action = 5;

        $('#info').empty().html(partitem);
        $('#dialog-form').dialog("open");
});

$('.savestatusallitem')
    .button()
    .click(function() {
        var items = $('input[name=checked_item]:checked');
        cnange_status_all(items, "<?=$order_info['salesorderinfo']['id']?>");
        $('input[name=checked_item]:checked').each(function(){
            //cnange_status_all("?=$order_info['salesorderinfo']['id']?",$(this).val(), true);
        });

});

$('.saveopcom')
    .button()
    .click(function(){
        cnange_status("<?=$order_info['salesorderinfo']['id']?>",$(this).val());

});

$('.savedesc')
    .button()
    .click(function(){
        save_description("<?=$order_info['salesorderinfo']['id']?>",$(this).val());

});

$('input[name=all_products]').click (function () {
    var thisCheck = $(this);
    if (thisCheck.is(':checked')) {
        $('input[name=checked_item]').each(function(){
            $(this).attr("checked","checked");
        });
    } else {
        $('input[name=checked_item]').each(function(){
            $(this).removeAttr("checked");
        });
    }

});

function gettowork()
{
    window.location.href = 'index.php?cmd=orders&do=gettowork&id=<?=$order_info['salesorderinfo']['id']?>';
}

</script>

<?=Plugins::invokeEvent('onRenderOrderInfo')?>

<br clear="all"/>