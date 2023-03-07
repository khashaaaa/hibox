<style>

	table.printpackage{
		border: #D2D0D0 1px solid;
		border-collapse: collapse;
		font-size: 14px;
	}
	table.printpackage td{
		border: #D2D0D0 1px solid;
		font-size: 13px;
		height: 30px;
		padding:5px;
	}
	table.printpackage th{
		background: #F0F0F0;
		height: 30px;
		padding:5px;
		text-align: left;
	}

</style>
<table cellpadding="0"  cellspacing="0" class="printpackage"  width="900" >
<?
	$package = false;
	foreach ($package_list as $value)
		if ($value['id'] == $pid) {
			$package = $value;
			break;
		}
	if ($package){
?>
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
?>

	<tr>
		<th colspan="6"><b><?=LangAdmin::get('order_')?></b>: <?=$order_info['salesorderinfo']['id']?>&nbsp;&nbsp;&nbsp;<b><?=LangAdmin::get('package')?></b>: <?=$package['id']?></th>
	</tr>
	<tr>
		<td colspan="2"><b><?=LangAdmin::get('recipient_data')?></b></td>
		<td colspan="2"><b><?=LangAdmin::get('delivery_address')?></b></td>
		<td colspan="2"><b><?=LangAdmin::get('package')?></b></td>
	</tr>
	<tr>
		<td><b><?=LangAdmin::get('method_of_delivery')?></b></td>
		<td><?=$package['deliverymodename']?></td>
		<td><b><?=LangAdmin::get('street')?></b></td>
		<td><?=$user_info['address']?></td>
		<td><b><?=LangAdmin::get('count')?></b></td>
		<td><?=count($order_info['saleslineslist'])?> <?=LangAdmin::get('units')?></td>
	</tr>
	<tr>
		<td><b><?=LangAdmin::get('recipient')?></b></td>
		<td><? echo $user_info['recipientfirstname'].' '.$user_info['recipientmiddlename'].' '.$user_info['recipientlastname'] ?></td>
		<td><b><?=LangAdmin::get('city')?></b></td>
		<td><? echo $user_info['city'].', '.$user_info['region'].', '.$user_info['postalcode']?></td>
		<td><b><?=LangAdmin::get('weight')?></b></td>
		<td>                                    <? $weight = ((float)$package['weight'] != '') ? (float)$package['weight'] : 0; ?>
                                    <? echo $weight.' '.LangAdmin::get('kg'); ?></td>
	</tr>
	<tr>
		<td><b><?=LangAdmin::get('contact_information')?></b></td>
		<td><? echo $user_info['phone'].', '.$user_info['email'] ?></td>
		<td><b><?=LangAdmin::get('country')?></b></td>
		<td><?=$user_info['country']?></td>
		<td><b><?=LangAdmin::get('track')?></b></td>
		<td><?=$package['deliverytrackingnum']?></td>
	</tr>
<? } ?>
</table>

<br>
<br>
<button id="printpackage" onclick="javascript:window.print()" title="<?=LangAdmin::get('printpackage')?>"><?=LangAdmin::get('print')?></button>