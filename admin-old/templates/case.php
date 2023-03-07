<?

include ("header.php");


$percent = isset($data->Settings->MarginPercentage) ? (string)$data->Settings->MarginPercentage : '';
$min_margin = isset($data->Settings->MinimumMargin) ? (string)$data->Settings->MinimumMargin : '';
$usediscount = ((string)$data->Settings->UseDiscount == 'true') ? ' checked' : '';
$usevipdiscount = ((string)$data->Settings->UseVipDiscount == 'true') ? ' checked' : '';

$current_delivery = (string)$data->Settings->DeliveryType;
$current_region_id = (int)$data->Settings->ExternalDeliveryRegionId;
$current_region_name = (string)$data->Settings->ExternalDeliveryRegionName;

$rub_use = $dol_use = $uan_use = '';

$rub_rate = $dol_rate = $uan_rate = '0';

$rub_use_bool = $dol_use_bool = $uan_use_bool = '0';

if (isset($data->Settings->Ruble)){
    if($data->Settings->Ruble->IsUse == 'true'){
        $rub_use = 'selected';
        $rub_use_bool = '1';
    } else {
        $rub_use = '';
        $rub_use_bool = '0';
    }
    
    $rub_rate = $data->Settings->Ruble->Rate;
}

if (isset($data->Settings->Dollar)){
    if($data->Settings->Dollar->IsUse == 'true'){
        $dol_use = 'selected';
        $dol_use_bool = '1';
    } else {
        $dol_use = '';
        $dol_use_bool = '0';
    }
    
    $dol_rate = $data->Settings->Dollar->Rate;
}

if (isset($data->Settings->Yuan)){ 
    if($data->Settings->Yuan->IsUse == 'true'){
        $uan_use = 'selected';
        $uan_use_bool = '1';
    } else {
        $uan_use = '';
        $uan_use_bool = '0';
    }
    
    $uan_rate = $data->Settings->Yuan->Rate;
}

$cb_checked = '';

if(isset($data->Settings->IsSinchroCB)){
    $cb_checked = ((string)$data->Settings->IsSinchroCB == 'true') ? 'checked' : '';
}

$IsAuctionTypeItemSellAllowed = (string)$data->Settings->IsAuctionTypeItemSellAllowed;
$IsNotDeliverableItemSellAllowed = (string)$data->Settings->IsNotDeliverableItemSellAllowed;
$IsSecondhandItemSellAllowed = (string)$data->Settings->IsSecondhandItemSellAllowed;
$IsFilteredItemsSellAllowed = (string)$data->Settings->IsFilteredItemsSellAllowed;

$MarginRate = isset($currency_settings['MarginRate']) && (string)$currency_settings['MarginRate'] ? ($currency_settings['MarginRate']-1)*100 : 0;
?>
	
<style>
#sortable1, #sortable2 { list-style-type: none; margin: 0; padding: 0 0 2.5em; float: left; margin-right: 10px; }
#sortable1 li, #sortable2 li { margin: 0 5px 5px 5px; padding: 5px; font-size: 1.2em; width: 120px; }
</style>
<script>
$(function() {
        $( "#sortable1, #sortable2" ).sortable({
                connectWith: ".connectedSortable"
        }).disableSelection();
        
        $('input[name=new_rate]').keypress(function(e) {
            if (!(e.which==8 || e.which==44 ||e.which==45 || 
                e.which==46 ||(e.which>47 && e.which<58))) return false;
        });
        
        $(window).keydown(function(e){
            if ($('#add-form:visible').length > 0) {
                if (e.which==8 || e.which==44 ||e.which==45 || 
                    e.which==46 ||(e.which>47 && e.which<58)) {
                    if (!$('input[name=new_rate]').is(":focus")) {
                        $('input[name=new_rate]').val('');
                        $('input[name=new_rate]').focus();
                    }
                }
            }
            
        });
});
</script>

<div class="main"><div class="canvas clrfix">
    <div class="col240">
        <div class="opened">
            <?
            $GLOBALS['p2'] = 1;
            ?>
        </div>
    </div>

    <div class="col700">
        <div id="overlay"></div>
        <div class="tuning">
            
            <div class="windialog" id="confirm-form" title="<?=LangAdmin::get('confirmation')?>">
                <?=LangAdmin::get('the_record_will_be_removed')?>, <?=LangAdmin::get('proceed')?>?
                <div style="display:none;" class="spinner"></div>
            </div>
            
            <div class="windialog" id="add-form" title="<?=LangAdmin::get('add_currency')?>">
                <span id="info">
                    <?=LangAdmin::get('currency')?> 1:
                    <select name="new_first">
                    <? if($currency_list) foreach($currency_list as $currency) { ?>
                        <option value="<?=$currency['code']?>"><?=$currency['code']?></option>
                    <? } ?>
                    </select><br/>
                    <?=LangAdmin::get('currency')?> 2:
                    <select name="new_second">
                    <? if($currency_list) foreach($currency_list as $currency) { ?>
                        <option value="<?=$currency['code']?>"><?=$currency['code']?></option>
                    <? } ?>
                    </select><br/>
                    <? if ( (string)$currency_settings['syncmode'] != 'WithCB') { ?>
                        <?=LangAdmin::get('enter_the_proportion_of')?>:
                        <? $type = 'text'; ?>
                    <? } else { ?>
                        <? $type = 'hidden'; ?>
                    <? } ?>
                    <input type="<?=$type?>" value="1" name="new_rate"/>
                    
                    <div style="display:none;" class="spinner"></div>
                </span>
            </div>
            
            <div class="windialog" id="attention_added" title="<?=LangAdmin::get('attention')?>" align="center">
            	<span id="info">
                	
            		<? 	$showattention=false;						
				 			
					 if($currency_settings['currenciesdisplayingorder'])  {
					 	foreach ($currency_settings['currenciesdisplayingorder'] as $currency) {
								$find_inval=false;
					 			$find_cny=false; 
								foreach ($currency_settings['currencyratelist'] as $rate) {	
										if ($currency['code']=='CNY') $find_cny=true;
										if ($currency['code']==$currency_settings['internalcurrencycode']) $find_inval=true;
																											
                          				if  ((($rate['firstcode']==$currency['code']) and ($rate['secondcode']=='CNY')) or (($rate['firstcode']=='CNY') and ($rate['secondcode']==$currency['code']))) {											
						     					$find_cny=true;											
					      				}
										if ((($rate['firstcode']==$currency['code']) and ($rate['secondcode']==$currency_settings['internalcurrencycode'])) or (($rate['secondcode']==$currency['code']) and ($rate['firstcode']==$currency_settings['internalcurrencycode']))) {
											
						     					$find_inval=true;											
					      				}
										
										
									                    
               		    		}
								if (!$find_inval) { 
									echo  'Не задан курс '.$currency['code'].' к '.$currency_settings['internalcurrencycode'].'<br>'; 
									$showattention=true; 									
								}
								if (!$find_cny) { 
									echo  'Не задан курс '.$currency['code'].' к юаню<br>'; 
									$showattention=true; 									
								}	
														
															
						}
						
                    } 					
					?>					
					
					
                   <input name="button" id="close_attention" value="<?=LangAdmin::get('close')?>" />
                </span>
            </div>
            
            
            <div id="error" style="color:red;"></div>
            <form action="<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;do=savecurrency&amp;cmd=control" method="post">
                <h2><?=LangAdmin::get('customizing_currency')?></h2>
                <?=LangAdmin::get('currency_internal_calculations')?>: <strong><?= $currency_settings['internalcurrencycode'];?></strong>
                <br/><br/>


                <h2><?=LangAdmin::get('the_order_of_withdrawal_rates')?></h2> 
                <? $currency_settings_code = array(); ?>

                <small>
                    <?=LangAdmin::get('goods_on_display_will_be_converted_into_the_currency_selected_in_the_order_they_appear')?>.<br>
                    <span style="color:red"><?=LangAdmin::get('must_have_price_count')?></span>
                </small>
                
                <br/><br/>
                <div id="hiddenfields">
                    <? if($currency_settings['currenciesdisplayingorder']) foreach($currency_settings['currenciesdisplayingorder'] as $currency) { ?>
                        <input type="hidden" value="<?=$currency['code']?>" name="currency_order[<?=(string)$currency['order']?>]"/>
                    <? } ?>
                </div>

                <table style="display:block;te">
                    <tr><td width="100px"><?=LangAdmin::get('selected')?></td><td width="100px"><?=LangAdmin::get('available')?></td></tr>
                </table>
                        
                <ul id="sortable1" class="connectedSortable">
                    <? if($currency_settings['currenciesdisplayingorder']) foreach ($currency_settings['currenciesdisplayingorder'] as $currency) { ?>
                        <? $currency_settings_code[] = $currency['code']; ?>
                        <li class="ui-state-default" id="<?=$currency['code']?>"><?=$currency['code']?></li>
                    <? } ?>
                    <li></li>
                </ul>

                <ul id="sortable2" class="connectedSortable">
                    <? if($currency_list) foreach($currency_list as $currency) { ?>
                        <? if(in_array($currency['code'], $currency_settings_code)) continue; ?>
                        <li class="ui-state-highlight" id="<?=$currency['code']?>"><?=$currency['code']?></li>
                    <? } ?>
                    <li></li>
                </ul>    
                <br clear="all"/>
                <div class="fbut clrfix">	
                    <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="<?=LangAdmin::get('save_currencies_order')?>"/>
                </div>
                <br clear="all"/>
                <br clear="all"/>
                
                <h2><?=LangAdmin::get('the_synchronization_mode')?></h2>
                <select name="sync">
                    <? if($sync_model_list) foreach ($sync_model_list as $model) { ?>
                        <? $selected = ($model['name'] == (string)$currency_settings['syncmode']) ? 'selected' : '' ?>
                        <? if ($model['name'] == (string)$currency_settings['syncmode']) $sync_desc = (string)$model['description']; ?>
                        <option value="<?=$model['name']?>"  <?=$selected?>><?=$model['name']?></option>
                    <? } ?>
                </select> 
                <small id="sync_desc"><?=@$sync_desc?></small>
                
                <? if(CMS::IsFeatureEnabled('Converter')) { ?>
                <br/><br/>

                <small class="ihint"><?=LangAdmin::get('cb_markup')?></small><br/>
                <input type="text" name="MarginRate" value="<?=$MarginRate;?>"/>%
                <? } ?>

                <br/><br/>

                <h2><?=LangAdmin::get('exchange_rates')?></h2>
                <small><?=LangAdmin::get('these_exchange_rates_are_taken_into_account_when_translating_the_value_of_the_goods_from_the_shop_windows_in_other_currencies')?>.</small>
                <br/><br/>
                
                <input type="button" id="but_add" value="<?=LangAdmin::get('add')?>"/>

                <table border="1" width="300px">
                <? if($currency_settings['currencyratelist']) foreach ($currency_settings['currencyratelist'] as $rate) { ?>
                    <tr id="cur_<?=$rate['firstcode']?>_<?=$rate['secondcode']?>">
                        <td width="40px">
                            1 <?=$rate['firstcode']?>
                            <input type="hidden" value="<?=$rate['firstcode']?>" name="rate[firstcode][]">
                        </td>
                        <td width="10px">=</td>
                        <td width="80px"><input type="text" value="<?=(string)$rate['rate']?>" name="rate[value][]"/></td>
                        <td width="40px">
                            <?=$rate['secondcode']?>
                            <input type="hidden" value="<?=$rate['secondcode']?>" name="rate[secondcode][]">
                        </td>
                        <td width="20px">
                            <img id="del_<?=$rate['firstcode']?>_<?=$rate['secondcode']?>" width="12" height="12" class="del_cur" src="templates/i/del.png">
                        </td>
                    </tr>
                <? } ?>
                </table>

                <div class="fbut clrfix">	
                    <input id="savecurrency1" type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="<?=LangAdmin::get('save_currencies_rate')?>"/>
                </div>

            </form>

            <br/><br/>
            <? //print_r($round_settings); ?>
            <h2><?=LangAdmin::get('rounding_settings')?></h2>
            <div>
                <?=LangAdmin::get('rounding_settings_desc_1')?><br/><br/>
                <input type="text" name="round_settings" value="<?=@$round_settings['priceroundingfactor']?>"/>
                 <br/><br/>
                <small>
                    <?=LangAdmin::get('rounding_settings_desc_2')?><br/>
                    <?=LangAdmin::get('rounding_settings_desc_3')?><br/>
                    <?=LangAdmin::get('rounding_settings_desc_4')?><br/>
                </small>
            </div>
                        <div>
                        <br> <br>
                <?=LangAdmin::get('set_rounding_settings')?>               
                <input name="round_settings_cfg" type="checkbox" value="1" <? @$round_settings['RoundOriginalInternalDeliveryPrice']? 'checked="checked"' : ''?> /><br> <br>          
                
                
                <button class="round_settings"><?=LangAdmin::get('save')?></button>
                
            </div>
            
            <br/><br/>

            <form action="<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;do=savecase&amp;cmd=Control" method="post">

                
                <h2><?=LangAdmin::get('prohibition_of_sale')?></h2>
                
                <label for="IsAuctionTypeItemSellAllowed">
                    <input type="checkbox" name="IsAuctionTypeItemSellAllowed" value="true" <? if($IsAuctionTypeItemSellAllowed=='true'){?> checked <?} ?> />
                    <?=LangAdmin::get('IsAuctionTypeItemSellAllowed')?>
                </label>
                <br clear="all"/>
                <label for="IsNotDeliverableItemSellAllowed">
                    <input type="checkbox" name="IsNotDeliverableItemSellAllowed" value="true" <? if($IsNotDeliverableItemSellAllowed=='true'){?> checked <?} ?> />
                    <?=LangAdmin::get('IsNotDeliverableItemSellAllowed')?>
                </label>
                <br clear="all"/>
                <label for="IsSecondhandItemSellAllowed">
                    <input type="checkbox" name="IsSecondhandItemSellAllowed" value="true" <? if($IsSecondhandItemSellAllowed=='true'){?> checked <?} ?> />
                    <?=LangAdmin::get('IsSecondhandItemSellAllowed')?>
                </label>
                <br clear="all"/>
                <label for="IsFilteredItemsSellAllowed">
                    <input type="checkbox" name="IsFilteredItemsSellAllowed" value="true" <? if($IsFilteredItemsSellAllowed=='true'){?> checked <?} ?> />
                    <?=LangAdmin::get('IsFilteredItemsSellAllowed')?>
                </label>
                <br clear="all"/>
                <br clear="all"/>
                <div class="fbut clrfix">	
                    <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="<?=LangAdmin::get('save')?>"/>
                </div>
                
                <div style="display:none;">
                    <h2 class="mt30"><?=LangAdmin::get('available')?> валюты</h2>
                    <small class="ihint"><?=LangAdmin::get('select_those_currencies')?>, <?=LangAdmin::get('in_which_the_users_will_see_the_value_of_goods')?>.</small><br/>
                    <select name="currency">
                        <option value="dol" <?=$dol_use;?> ><?=LangAdmin::get('u.s._dollar')?></option>
                        <option value="rub" <?=$rub_use;?> ><?=LangAdmin::get('the_russian_ruble')?></option>
                        <option value="uan" <?=$uan_use;?> ><?=LangAdmin::get('yuan')?></option>
                    </select>
                    <br/><br/>


                    <h2 class="mt30"><?=LangAdmin::get('yuan')?></h2>
                    <small class="ihint"><?=LangAdmin::get('set_the_exchange_rate_in_relation_to_the')?> 1 <?=LangAdmin::get('u.s._dollar')?></small><br/>
                    <input type="text" name="dol_rate" value="<?=$dol_rate;?>"/><?=LangAdmin::get('yuan')?>

                    <br/><br/>
                    <small class="ihint"><?=LangAdmin::get('set_the_exchange_rate_in_relation_to_the')?> 1 <?=LangAdmin::get('russian_ruble')?></small><br/>
                    <input type="text" name="rub_rate" value="<?=$rub_rate;?>"/><?=LangAdmin::get('yuan')?>

                    <br/><br/>
                    <small class="ihint"><?=LangAdmin::get('tick')?>, <?=LangAdmin::get('if_you_want_to_automatically_synchronize_with_the_data_rate_of_the_central_bank')?></small>
                    <label><input type="checkbox" name="cbvalue" value="1" <?=$cb_checked;?>/> C<?=LangAdmin::get('inhronizatsiya_with_central_bank')?></label>
                </div>
                
                <br/><br/>
                <h2 class="mt30"><?=LangAdmin::get('the_percentage_mark-up')?></h2>
                <small class="ihint"><?=LangAdmin::get('automatically_added_when_displaying_prices_of_goods_on_the_site')?></small><br/>
                <input type="text" name="persent" value="<?=$percent;?>"/>%

                
                <br/><br/>
                <h2><?=LangAdmin::get('minimummargin')?></h2>
                <small><?=LangAdmin::get('minimummargin_desc')?></small><br/>
                <input name="minimummargin" value="<?=$min_margin?>"/> CNY
                
                <br/><br/>
                <h2><?=LangAdmin::get('usediscount')?></h2>
                <input type="checkbox" name="usediscount" value="true" <?=$usediscount;?>/><?=LangAdmin::get('usediscount')?><br/>
                <input type="checkbox" name="usevipdiscount" value="true" <?=$usevipdiscount;?>/><?=LangAdmin::get('usevipdiscount')?><br/>
                
                <br/><br/>
                <h2 class="mt30"><?=LangAdmin::get('delivery_to_china')?></h2>

                <ul class="deliveries-list">
                    <li class="stay-always"><?=LangAdmin::get('used_methods_of_delivery')?><br /><small><?=LangAdmin::get('in_order_of_priority')?></small></li>
                    <?
                    $enabledDels = array();
                    ?>
                    <? if(@$data->Settings->DeliveryTypes->DeliveryTypes) foreach($data->Settings->DeliveryTypes->DeliveryTypes as $del){ ?>
                        <?
                        $enabledDels[] = (string)$del;
                        ?>
                        <li delivery="<?=(string)$del?>"><?=(string)$del?></li>
                    <? } ?>
                    <li class="stay"><?=LangAdmin::get('unused_methods_of_delivery')?></li>
                    <? if(@$data->Settings->AvailableDeliveryTypes->NamedProperty) foreach($data->Settings->AvailableDeliveryTypes->NamedProperty as $prop){ ?>
                        <? if(!in_array((string)$prop->Name, $enabledDels)){ ?>
                            <li delivery="<?=(string)$prop->Name?>"><?=(string)$prop->Name?></li>
                        <? } ?>
                    <? } ?>
                </ul>

                <input type="hidden" name="DeliveryTypes" value='<?=json_encode($enabledDels)?>' />

                <br /><br />
                <a href="#" onclick="$('#dialog-form').dialog('open'); return false;"><?=LangAdmin::get('choice_of_delivery_region')?></a>
                <div id="delivery-choosed"><?=LangAdmin::get('selected_region')?>: <?=$current_region_id;?> <?=$current_region_name;?></div>

                <input type="hidden" name="ExternalDeliveryRegionId" value="<?=$current_region_id;?>" />
                <input type="hidden" name="ExternalDeliveryRegionName" value="<?=$current_region_name;?>" />

                <br/><br/>
                <div class="fbut clrfix">	
                    <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="<?=LangAdmin::get('save')?>"/>
                </div>
            </form>

            <div id="dialog-form" class="ui-dialog ui-widget ui-widget-content ui-corner-all ui-draggable ui-resizable"
                tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-dialog">
                <ul id="regions">
                </ul>
            </div> 

<script type="text/javascript" src="js/jquery.combobox.js"></script>
<script type="text/javascript" src="js/json.js"></script>

<script type="text/javascript">

$('.round_settings')
    .button()
    .click(function () {
        showOverlay();
        var round = $('input[name=round_settings]').val();
		var round_cfg = $('input[name=round_settings_cfg]').val();
        var server_url = 'index.php?cmd=control&sid=<?=$GLOBALS['ssid']?>' +
            '&do=setroundsettings&round=' + round;
        if ($('input[name=round_settings_cfg]').attr('checked')) { 
            var round_cfg = $('input[name=round_settings_cfg]').val();
            server_url += '&round_cfg=' + round_cfg;
        }
			
			
        $.ajax({
            url: server_url,
            success: function(data) {
                hideOverlay();
                if (data == 'RELOGIN') location.href='index.php?expired';
                if (data == 'Ok') {
                    hideOverlay();
                } else {
                    alert(data);
                }
            },
            error: function() {
                alert('Error!');
            }
        });
        
});


$(function(){
    $("#add-form:ui-dialog").dialog("destroy");
    $("confirm-form:ui-dialog").dialog("destroy");
    
    var first_currency = '';
    var second_currency = '';
    
    var descs = Array();
    <? foreach ($sync_model_list as $model) { ?>
        descs['<?=$model['name']?>'] = '<?=$model['description']?>';
    <? }?>
     
	 
	
	
	
	$("#attention_added").dialog({
        autoOpen: false,
        modal: true
    });	 
	<? if ($showattention) {
		echo '$("#attention_added").dialog("open");';							
	   }  	
	?>
	$('#close_attention')
        .button()
        .click(function(){
			$("#attention_added").dialog("close");            
    });  
	  
	   
    $('#but_add')
        .button()
        .click(function(){
            $("#add-form").dialog("open");
    });
        
    $('.del_cur')
        .button()
        .click(function(){
            $('#confirm-form').dialog('open');
            var params_str = $(this).attr('id');
            var params_array = params_str.split("_"); 
            first_currency = params_array[1];
            second_currency = params_array[2]; 
    });
    
    $('#sortable1').sortable({
        update: function(event, ui){
           var items = $('#sortable1').sortable('toArray');
           $("#hiddenfields").empty();
           for(var i=0; i<items.length; i++) {
               $('#hiddenfields').append($('<input></input>')
                    .attr({ type : 'hidden', 
                            name : 'currency_order['+i+']', 
                            value : items[i]})
                )
           }
        }
    });
    
    $('[name=sync]').change(function() {
        $('#sync_desc').html(descs[$(this).attr('value')]);
    });
    
    $(".deliveries-list").sortable({
        items: 'li:not(.stay-always)',
        cancel: ".stay",
        stop:function (event, ui) {
            var dels = [];
            $('.deliveries-list li:gt(0)').each(function(){
                if( !$(this).hasClass('stay') ){
                    dels[dels.length] = $(this).attr('delivery');
                }
                else{
                    return false;
                }
            });
            $('[name="DeliveryTypes"]').val(JSON.stringify(dels));
            console.log(JSON.stringify(dels));
        }
    });
    $(".deliveries-list li").disableSelection();

    $('.combolist').combobox();
    $('.region-select').live('click', function(){
        $('[name="ExternalDeliveryRegionId"]').val($(this).attr('regid'));
        $('[name="ExternalDeliveryRegionName"]').val($(this).attr('regname'));
        $('#delivery-choosed').html('<?=LangAdmin::get('selected_region')?>: '+$(this).attr('regid')+' '+$(this).attr('regname'));
        $( "#dialog-form" ).dialog( "close" );
        return false;
    });

    $("#regions").treeview({
            url: "index.php?do=regions"
    })

    $( "#dialog-form" ).dialog({
            autoOpen: false,
            height: 315,
            width: 350,
            modal: true,
            buttons: {
                "<?=LangAdmin::get('cancellation')?>": function() {
                        $( this ).dialog( "close" );
                }
            },
            close: function() {
            }
    }); 
    
    $('#confirm-form').dialog({
       autoOpen:false,
       modal:true,
       buttons : {
           "<?=LangAdmin::get('yes')?>" : function() {
               $('.spinner').show();
               $.ajax({
                    url:'index.php?do=deleterate&first='+first_currency+'&second='+second_currency,
                    success:function (data) {
                        if (data == 'RELOGIN') location.href = 'index.php?expired';
                        if (data == 'Ok') {
                            $('#confirm-form').dialog('close');
                            $('.spinner').hide();
                            $('#cur_'+first_currency+'_'+second_currency).hide();
                        } else {
                            $('#error').html('<?=LangAdmin::get('there_was_an_error')?> ' + data);
                        }
                    },
                    error:function () {
                        $('#error').html('<?=LangAdmin::get('there_was_an_error')?> ');
                    }
                });
           },
           "<?=LangAdmin::get('no')?>" : function() {
                $("#confirm-form").dialog("close");
           }
       }
    });
    
    $("#add-form").dialog({
        autoOpen: false,
        modal: true,
        buttons : {
            "<?=LangAdmin::get('yes')?>" : function() {
                var first  = $('[name=new_first]  option:selected').val();
                var second = $('[name=new_second] option:selected').val();
                var rate = $('[name=new_rate]').val();
                $('.spinner').show();
                $.ajax({
                    url:'index.php?do=createrate&first='+first+'&second='+second+'&rate='+rate,
                    success:function (data) {
                        if (data == 'RELOGIN') location.href = 'index.php?expired';
                        if (data == 'Ok') {
                            window.location.href = 'index.php?sid=&do=case';
                        } else {
                            $('.spinner').hide();
                            $('#error').html('<?=LangAdmin::get('there_was_an_error')?> ' + data);
                        }
                    },
                    error:function () {
                        $('#error').html('<?=LangAdmin::get('there_was_an_error')?> ');
                    }
                });
            },
            "<?=LangAdmin::get('no')?>" : function() {
                $("#add-form").dialog("close");
            }
        }
    });	
});
</script>
        </div>
    </div>

</div></div><!-- /.main -->
	
<?
include ("footer.php");
?>