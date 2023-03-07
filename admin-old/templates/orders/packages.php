<br/>
<div>
<? //echo '$order_info = '; var_dump($order_info); ?>
<? if(isset($action)) { ?>
    <? if($action == 'createpackage') {?>
        <strong><?=LangAdmin::get('method_of_delivery')?>:</strong> 
            <? if(/*$delivery_models*/0) { ?>
                <select name="delivery" id="delivery">
                    <? foreach($delivery_models as $model) { ?>
                        <option value="<?=$model['id']?>" <? if($order_info['salesorderinfo']['deliverymodeid'] == $model['id']) print 'selected';?>><?=$model['name']?></option>
                    <? } ?>
                </select>
            <? } else { ?>
                <?=$order_info['salesorderinfo']['deliverymodeid']?> (<?=$order_info['salesorderinfo']['deliverymodename']?>)
            <? } ?>
        <br/><br/>
        <strong><?=LangAdmin::get('delivery_address')?>, С‚РµР»РµС„РѕРЅ, <?=LangAdmin::get('name_of_recipient')?>:</strong> <br/><br/>
        <strong><?=LangAdmin::get('weight_of_parcel')?>:</strong> <?=(float)$order_info['salesorderinfo']['packagesweight']?> <?=LangAdmin::get('kg')?>.<br/><br/> 

        <button id="save" title="<?=LangAdmin::get('save_the_package')?>"><?=LangAdmin::get('save_the_package')?></button>
        <button id="cancel" title="<?=LangAdmin::get('cancellation')?>"><?=LangAdmin::get('cancellation')?></button>
        
    <? } else if ($action == 'editpackage') {?>    
            <? include (TPL_ABSOLUTE_PATH.'orders/editpackage.php'); ?>
      <? } ?>
<? } else { ?>

    <div class="grid_3">
        <p>
            <button id="submit1" title="<?=LangAdmin::get('create_a_package')?>"><?=LangAdmin::get('create_a_package')?></button>
        </p>
    </div>
    <? if($package_list) { //var_dump($package_list);?>
            <div class="grid_16">
            <table>
                <thead>
                    <tr>
                        <th><?=LangAdmin::get('code_parcels')?></th>
                        <!--<th><?=LangAdmin::get('number_of')?>-<?=LangAdmin::get('items_on')?></th>-->
                        <th><?=LangAdmin::get('method_of_delivery')?></th>
                        <th><?=LangAdmin::get('delivery_address')?>, <?=LangAdmin::get('name_of_recipient')?></th>
                        <th><?=LangAdmin::get('weight_of_parcel')?></th>
                        <th><?=LangAdmin::get('tracking')?>-<?=LangAdmin::get('number')?></th>
                        <th><?=LangAdmin::get('creation_date')?> / <?=LangAdmin::get('shipment_date')?></th>
                        <th><?=LangAdmin::get('the_status_of_a_parcel')?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach($package_list as $package){ ?>
                    
                    <tr id="package<?=$package['id']?>">
                    <td><?=$package['id']?></td>
                    <td><?=$package['deliverymodename']?></td>
                    <td>
                        <? 
                        $user = array();
                        
                        $region = $package['deliveryregionname'];
                        if($region!='') $user[] = $region;
                        
                        $city = $package['deliverycity'];
                        if($city!='') $user[] = $city;
                        
                        $address = $package['deliveryaddress'];
                        if($address!='') $user[] = $address;
                        
                        $name = array($package['deliverycontactfirstname'],
                                        $package['deliverycontactmiddlename'],
                                        $package['deliverycontactlastname']);
                            
                        $user[] = implode(' ', $name);
                        
                        echo implode(', ', $user);
                        ?>
                        
                    </td>
                    <td>
                        <? $weight = ((float)$package['weight'] != '') ? (float)$package['weight'] : 0; ?>
                        <? echo $weight.' '.LangAdmin::get('kg').'.'; ?>
                    </td>
                    <td><?=$package['deliverytrackingnum']?></td>
                    <td>
                        <?
                            $date = explode('T', $package['creationdate']);
                            if (strpos($date[0], '0001') === false) {
                                echo $date[0] . ' / ';
                            } else {
                                echo '- / ';
                            }
                            
                            $date = explode('T', $package['shipmentdate']);
                            if (strpos($date[0], '0001') === false) {
                                echo $date[0];
                            } else {
                                echo '-';
                            } 
                        ?>
                    </td>
                    <td><?=$package['statusname']?></td>
                    <td width="20%" style="text-align: right;">
                        <? if((int)$package['candelete']) { ?> 
                            <button class="deletepackage" value="<?=$package['id']?>" title="<?=LangAdmin::get('remove')?>"><?=LangAdmin::get('remove')?></button><br/>
                        <? } ?>
                        <? if((int)$package['canupdate']) { ?> 
                            <button class="updatepackage" value="<?=$package['id']?>" title="<?=LangAdmin::get('edit')?>"><?=LangAdmin::get('edit')?></button>
                        <? } ?> 
                          <? if($package['CanPrintPackageReceipt']=='true') { ?>
						     <br/>                        
						     <button class="printpackage" value="<?=$package['id']?>" title="<?=LangAdmin::get('printpackagedata')?>"><?=LangAdmin::get('printpackagedata')?></button>
                          <? } ?>
                          <? if($package['CanExportToExternalDeliverySystem']=='true') { ?>
                        	<br/>
							<button class="exportpackage" value="<?=$package['id']?>" title="<?=LangAdmin::get('exportpackage')?>"><?=LangAdmin::get('exportpackage')?></button>
                          <? } ?>
                        <button class="printpackagemini" value="<?=$package['id']?>" title="<?=LangAdmin::get('printpackagedatamini')?>"><?=LangAdmin::get('printpackagedatamini')?></button>

                        <?=Plugins::invokeEvent('onPrintPackageStickerButton', array($order_info['salesorderinfo']['id'], $package['id'], array('old-admin' => true)))?>

					</td>
                    </tr>
                    <? } ?>
                <tbody>
            </table>
            </div>
    <? } else { ?>
        <div style="clear:both;"></div>
        <h3> <?=LangAdmin::get('parcel_not_found')?>! </h3>
    <? } ?>
<? } ?>
<div id="messagedialog" style="display: none"></div>
<script>



$('#save')
    .button()
    .click(function(){
        window.location.href = 'index.php?cmd=orders&do=savenewpackage' +
            '&id=<?=$order_info['salesorderinfo']['id']?>' +
            '&user=<?=$order_info['salesorderinfo']['custid']?>';
});
$('#cancel')
    .button()
    .click(function(){
        window.location.href = 'index.php?cmd=orders&do=orderinfo&id=<?=$order_info['salesorderinfo']['id']?>&tab=3';
});
$("#submit1")
    .button()
    .click(function () {
        window.location.href = 'index.php?cmd=orders&do=savenewpackage' +
            '&id=<?=$order_info['salesorderinfo']['id']?>&tab=3' + 
            '&user=<?=$order_info['salesorderinfo']['custid']?>';
}); 

$("#submit2")
    .button()
    .click(function () {
        window.location.href = 'index.php?cmd=orders&do=orderinfo&id=<?=$order_info['salesorderinfo']['id']?>';
}); 

$(".deletepackage")
    .button()
    .click(function () {
        var pid = $(this).val();
        confirm_delete_package(pid);
});

$(".deletepackage")
    .button()
    .click(function () {
        var pid = $(this).val();
        confirm_delete_package(pid);
});

$(".updatepackage")
    .button()
    .click(function () {
        var pid = $(this).val();
        window.location.href = 'index.php?cmd=orders&do=packageinfo&id=<?=$order_info['salesorderinfo']['id']?>&pid=' + pid;
});
$(".printpackage")
	.button()
	.click(function () {
		var pid = $(this).val();
		var pid = $(this).val();
		$('#messagedialog').dialog({
            title: 'Р�РЅС„РѕСЂРјР°С†РёСЏ'            
        });
		$('#messagedialog').html('<div align="center"><img src="/css/i/loading.gif" width="30" height="30"/></div>').dialog('open');
		$.ajax({
       		url: 'index.php?cmd=orders&do=printpackagev2&id=<?=$order_info['salesorderinfo']['id']?>&pid='+pid,
        	success: function(data) {				
				$('#messagedialog').html(data).dialog('open');	   				
        	}
			
    	});
		//window.open('http://<?=$_SERVER['SERVER_NAME']?>/admin-old/index.php?cmd=orders&do=printpackage&id=<?=$order_info['salesorderinfo']['id']?>&pid='+pid,'_blank');
		
		//window.open('index.php?cmd=orders&do=printpackagev2&id=<?=$order_info['salesorderinfo']['id']?>&pid='+pid,'_blank');
	});
	
$(".exportpackage")
    .button()
    .click(function () {
        var pid = $(this).val();
		$('#messagedialog').dialog({
            title: 'Р�РЅС„РѕСЂРјР°С†РёСЏ'            
        });
		$('#messagedialog').html('<div align="center"><img src="/css/i/loading.gif" width="30" height="30"/></div>').dialog('open');
		$.ajax({
       		url: 'index.php?cmd=orders&do=exportpackage&pid=' + pid,
        	success: function(data) {				
				$('#messagedialog').html(data).dialog('open');	   				
        	}
			
    	});
});

$(".printpackagemini")
.button()
.click(function () {
	var pid = $(this).val();
	window.open('http://<?=$_SERVER['SERVER_NAME']?>/admin-old/index.php?cmd=orders&do=printpackagemini&id=<?=$order_info['salesorderinfo']['id']?>&pid=' + pid,'_blank');
});

</script>

</div>
<br clear="all"/>