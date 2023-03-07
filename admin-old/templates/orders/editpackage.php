<? if($package_info){?>
<h2><?=LangAdmin::get('editing_a_parcel')?> <?=$package_info['id']?></h2>

<? if(/*@$new_package*/0) { ?>
    <small style="color:green; font-weight: bold;"><?=LangAdmin::get('make_a_well_established')?>!</small>
<? } ?>

<? if (isset($error)) { ?>
    <font color="red"><?=$error?></font>
<? } ?>
<form action="index.php?cmd=orders&do=updatepackage&pid=<?=$package_info['id']?>&id=<?=$order_info['salesorderinfo']['id']?>" method="POST">
 
<table>
    <tr><td colspan="2"><h3><?=LangAdmin::get('package_info_common')?></h3></td></tr>
    <tr>
        <td><?=LangAdmin::get('zip_code_trekking')?></td>
        <td><input name="DeliveryTrackingNum" value="<?=$package_info['deliverytrackingnum']?>"/></td></tr>
    <tr>
        <td><?=LangAdmin::get('weight')?></td>
        <td><input name="Weight" value="<?=(float)$package_info['weight']?>"/></td></tr>
<?/*
    <tr>
        <td><?=LangAdmin::get('manually_change_in_the_price')?></td>
        <td>
            <select name="ManualPrice">
                <option value="1" <? if((int)$package_info['manualprice'] == 1) print 'selected';?>>да</option>
                <option value="0" <? if((int)$package_info['manualprice'] == 0) print 'selected';?>>нет</option>
            </select>
        </td></tr>
*/?>
    <tr>
        <td><?=LangAdmin::get('status')?></td>
        <td>
            <select name="status" <?=$package_info['CanChangeStatus']=='true' ? '' : 'disabled="disabled"' ?> >
            <? if ($package_info['CanChangeStatus']=='true') { ?>
                <? foreach ($p_statuses as $status) { ?>
                    <option name="status" value="<?=$status['id']?>" <? if((int)$package_info['statuscode'] == $status['id']) print 'selected';?>><?=$status['name']?></option>
                <? } ?>
            <? } else { ?>
            	   <option name="status" value="<?=$package_info['StatusCode']?>" selected="selected"><?=$package_info['StatusName']?></option>
			<? } ?>
            </select>
            <input type="hidden" name="old_status" value="<?=$package_info['CanChangeStatus']=='true' ? $package_info['statuscode'] : $package_info['StatusCode'] ?>">
        </td></tr>
    <tr>
        <td>
            <?=LangAdmin::get('shipping')?><br/> 
            <small><?=LangAdmin::get('used_to_manually_change_the_price')?></small>
        </td>
        <td>
            <label><input type="checkbox" name="ManualPrice" id="ManualPrice"<? if ($package_info['ManualPrice']){?> checked="checked"<?}?>> <?=LangAdmin::get('manually_change_in_the_price')?></label>
            <br>
            <input name="PriceInternal" <? if (!$package_info['ManualPrice']){?> disabled="disabled"<?}?> value="<?=(float)$package_info['priceinternal']?>" id="PriceInternal"/>
            <input name="CurrentPriceInternal" value="<?=(float)$package_info['priceinternal']?>" type='hidden'/>
            <?=$package_info['currencysigncust']?>
        </td></tr>
    <? if (defined('CFG_BUYINCHINA')) { ?>
        <tr>
            <td>
                <?=LangAdmin::get('additional_shipping')?>
            </td>
            <? $disabled = ((float)$package_info['priceinternal'] > 0) ? '' : 'disabled'; ?>
            <td>
                <input type="hidden" name="PriceCurrencyCode" value="CNY" <?=$disabled?>/>
                <input type="text" name="AdditionalPrice" value="<?=round((float)$package_info['additionalprice'])?>" <?=$disabled?>/> 
                CNY
            </td>
        </tr>
    <? } ?>
    <tr>
        <td><?=LangAdmin::get('id_of_the_mode_of_delivery')?></td>
        <td>
            <? if($delivery_models) { ?>
                <select name="DeliveryModeId">
                    <? foreach($delivery_models as $model) { ?>
                        <option value="<?=$model['id']?>" <? if((string)$package_info['deliverymodeid'] == $model['id']) print 'selected';?>><?=$model['name']?></option>
                    <? } ?>
                </select>
            <? } else { ?> 
                    <input disabled="disabled" name="DeliveryModeId" value="<?=(string)$package_info['deliverymodeid']?>"/>
            <? } ?>
            
        </td></tr>  <!--deliverymodename -->
    <tr>
        <td><?=LangAdmin::get('package_size')?></td>
        <td>
            <table>
                <tr>
                    <td width="100px">
                        <?=LangAdmin::get('length')?>(<?=LangAdmin::get('cm')?>)<br/>
                        <input name="Size[Length]" value="<?=(float)$package_info['size']['length']?>"/>
                    </td>
                    <td>
                        <?=LangAdmin::get('height')?>( <?=LangAdmin::get('cm')?>)<br/>
                        <input name="Size[Height]" value="<?=(float)$package_info['size']['height']?>"/>
                    </td>
                    <td>
                        <?=LangAdmin::get('width')?>( <?=LangAdmin::get('cm')?>)<br/>
                        <input name="Size[Width]" value="<?=(float)$package_info['size']['width']?>"/>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td><?=LangAdmin::get('package_items')?></td>
        <td>
            <? if (count($package_items)) { ?>
                <table>
                    <thead>
                        <th></th>
                        <th><?=LangAdmin::get('photo')?></th>
                        <th><?=LangAdmin::get('qty')?></th>
                        <th><?=LangAdmin::get('price')?></th>
                        <th><?=LangAdmin::get('status')?></th>
                    </thead>
                    <? foreach ($package_items as $item) { ?>
                        <tr>
                            <td>
                                <? $disabled = ($item['canmovetopackage'] == 'true') ? '' : 'disabled'; ?>
                                <? $checked = isset($item['in_package']) ? 'checked="checked"' : ''; ?>
                                <input name="Items[<?=$item['id']?>]" type="checkbox" <?=$checked?> <?=$disabled ?>/>
                            </td>
                            <td>
                                Id:<a href="<?=$item['itemexternalurl']?>" target="_blank"><?=$item['itemtaobaoid']?></a><br/>
                                <img src="<?=$item['itemimageurl'];?>_70x70.jpg" class="big_image" alt="" width="70" height="70"/>
                            </td>
                            <td>
                                <?=$item['qty']?>
                            </td>
                            <td>
                                <?=$item['pricecust']?><?=$item['currencycust']?>
                            </td>
                            <td>
                                <?=$item['statusname']?>
                            </td>
                        </tr>
                    <? } ?>
                </table>
            <? } ?>
        </td>
    </tr>
    <tr><td colspan="2"><h3><?=LangAdmin::get('package_info_about_delivery')?></h3></td></tr>
    <tr>
    <tr>
        <td><?=LangAdmin::get('country')?></td>
        <td>
            <input type="hidden" id="DeliveryCountry" name="DeliveryCountry" value="<?=$package_info['deliverycountry']?>"/>
            <select name="DeliveryCountryCode" id="DeliveryCountryCode">
            <?php foreach ($countries as $country) { ?>
                <option value="<?=$country['id']?>" <?=($country['id'] == $package_info['deliverycountrycode'] ? 'selected' : '');?>><?=$country['name'];?></option>
            <?php } ?>
            </select>
        </td></tr>
    <tr>
        <td><?=LangAdmin::get('last_name')?></td>
        <td><input name="DeliveryContactLastname" id="DeliveryContactLastname" value="<?=$package_info['deliverycontactlastname']?>"/></td></tr>
    <tr>
        <td><?=LangAdmin::get('name_short')?></td>
        <td><input name="DeliveryContactFirstname" id="DeliveryContactFirstname" value="<?=$package_info['deliverycontactfirstname']?>"/></td></tr>
    <tr>
        <td><?=LangAdmin::get('middle_name')?></td>
        <td><input name="DeliveryContactMiddlename" id="DeliveryContactMiddlename" value="<?=$package_info['deliverycontactmiddlename']?>"/></td></tr>
    <tr>
        <td><?=LangAdmin::get('phone')?></td>
        <td><input name="DeliveryContactPhone" id="DeliveryContactPhone" value="<?=$package_info['deliverycontactphone']?>"/></td></tr>
    <tr>
        <td><?=LangAdmin::get('zip_code')?></td>
        <td><input name="DeliveryPostalCode" id="DeliveryPostalCode" value="<?=(int)$package_info['deliverypostalcode']?>"/></td></tr>
    <tr>
        <td><?=LangAdmin::get('region')?></td>
        <td><input name="DeliveryRegionName" id="DeliveryRegionName" value="<?=$package_info['deliveryregionname']?>"/></td></tr>
    <tr>
        <td><?=LangAdmin::get('city')?></td>
        <td><input name="DeliveryCity" id="DeliveryCity" value="<?=$package_info['deliverycity']?>"/></td></tr>
    <tr>
        <td><?=LangAdmin::get('address')?></td>
        <td><input name="DeliveryAddress" id="DeliveryAddress" value="<?=$package_info['deliveryaddress']?>"/></td></tr>
    
    <? if (in_array('PassportData', General::$enabledFeatures)) { ?>
        <tr>
            <td><?=LangAdmin::get('passport')?></td>
            <td><input name="DeliveryContactPassportNumber" id="DeliveryContactPassportNumber" value="<?=$package_info['deliverycontactpassportnumber']?>"/></td></tr>
        <tr>
            <td><?=LangAdmin::get('registrationaddress')?></td>
            <td><input name="DeliveryContactRegistrationAddress" id="DeliveryContactRegistrationAddress" value="<?=$package_info['deliverycontactregistrationaddress']?>"/></td></tr>
    <? } ?>

    <tr>
        <td><?=LangAdmin::get('additional_information')?></td>
        <td>
            <textarea name="AdditionalInfo" rows="5" cols="80"/><?=$package_info['additionalinfo']?></textarea>
        </td></tr>
</table>
<button id="saveupdatedpackage" title="<?=LangAdmin::get('save')?>"><?=LangAdmin::get('save')?></button>
<button id="canceleditpackage" title="<?=LangAdmin::get('cancellation')?>"><?=LangAdmin::get('cancellation')?></button>
<? if($package_info['CanPrintPackageReceipt']=='true') { ?>
<button id="printpackage" value="<?=$package_info['id']?>" title="<?=LangAdmin::get('printpackagedata')?>"><?=LangAdmin::get('printpackagedata')?></button>
<? } ?>
</form>

    <? //var_dump($order_info); ?>

<script type="text/javascript">
$('#ManualPrice').change(function () {
    if ($('#ManualPrice').attr('checked') == 'checked')
    {
        $('#PriceInternal').removeAttr('disabled');
    } else {
        $('#PriceInternal').attr('disabled', 'disabled');
    }
});

$('#DeliveryCountryCode').change(function () {
   $('#DeliveryCountry').val($(this).find('option:selected').text());
});

$("input[name=PriceInternal]").keyup(function () {
    if ($(this).val() > 0) {
        $("input[name=AdditionalPrice]").removeAttr('disabled');
    } else {
        $("input[name=AdditionalPrice]").attr('disabled','disabled')
    }
    //$("div").text(str);
})
$('#saveupdatedpackage')
    .button()
    .click(function () {
        $(this).submit();
});

$('#canceleditpackage')
    .button()
    .click(function (ev) {
        ev.preventDefault();
        location.href = '<?=$pageurl?>&do=orderinfo&id=<?=$order_info['salesorderinfo']['id']?>&tab=3';
});

$("#printpackage")
	.button()
	.click(function () {
		var pid = $(this).val();
		//window.open('http://<?=$_SERVER['SERVER_NAME']?>/admin-old/index.php?cmd=orders&do=printpackage&id=<?=$order_info['salesorderinfo']['id']?>&pid='+pid,'_blank');
		window.open('index.php?cmd=orders&do=printpackagev2&id=<?=$order_info['salesorderinfo']['id']?>&pid='+pid,'_blank');
		return false;
	});
	

</script>
<? }  else { ?>
    <h3><?=LangAdmin::get('no_data_received_on_the_premise')?>!</h3>
<? } ?>
<?=Plugins::invokeEvent('onEditPackage')?>
<?=Plugins::invokeEvent('onEditPackage')?>
<?=Plugins::invokeEvent('onEditPackage')?>
<?=Plugins::invokeEvent('onEditPackage')?>