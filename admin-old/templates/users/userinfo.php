<? include (TPL_DIR."header.php"); ?>
<div id="pass_dialog-form" title="<?=LangAdmin::get('pass_recovery')?>">
	<span id="info"></span>
	<?if (!defined ('CFG_BUYINCHINA') || !CFG_BUYINCHINA){?>
		<p id="saveError" class="errorMessage"></p>
		<b><?=LangAdmin::get('enter_new_password')?>:</b><br/>
		<input id="newPassword" size="30" type="text" name="UserName" value="" /><br/>
		<button onclick="suggestPassword()"><?=LangAdmin::get('generatePass')?></button>
	<?}?>
</div>
<br/>
<a href="index.php?cmd=users&sid=<?=$GLOBALS['ssid']?>"> &lt; &lt; <?=LangAdmin::get('to_the_list_of_users')?></a><br/><br/>
<? if($user){?>
<? //var_dump($user); ?>
    <h2><?=LangAdmin::get('user_information')?></h2>
    
    <div align="right" class="flr">
    <?if(count($usedLanguages)>1){?>
                    
                    <form action="index.php" id="set-site-lang-form" method="post">
                        <label for="site-lang"><?=LangAdmin::get('choose_active_language')?>: </label>
                        <select name="site_lang" id="site-lang" onchange="$('#set-site-lang-form').submit();">
                            <?foreach($usedLanguages as $name=>$lang){?>
                            <option value="<?=$name?>" <?=@$_SESSION['active_lang']==$name?'selected':''?>>
                                <?=$lang?>
                            </option>
                            <?}?>
                            <input type="hidden" name="from" value="<?=$_SERVER['REQUEST_URI']?>" />
                        </select>
                    </form>
                    <?}?>
    </div><br/>
    
    <div id="overlay"></div>
    <div id="grid_16">
    <table>
        <thead>
            <tr>
                <th><?=LangAdmin::get('login')?></th>
                <th><?=LangAdmin::get('account_number')?></th>
                <!-- <td><?=LangAdmin::get('type_of_user')?></td> -->
                <th><?=LangAdmin::get('additional_information')?></th>
                <th colspan="2"><?=LangAdmin::get('possible_actions')?></th>
            </tr>
        </thead>
        <tbody>
            <tr id="user<?=$user['id']?>">
                <td><a href="index.php?sid=<?=$GLOBALS['ssid'];?>&cmd=users&do=userinfo&id=<?=$user['id']?>"><?=$this->escape($user['login'])?></a></td>
                <td><?=(string)$user_account['num']?></td> 
                <td>email: <?=$user['email']?></td>
                <td>
                    <a href="index.php?cmd=users&do=useredit&sid=<?=$GLOBALS['ssid']?>&id=<?=$user['id']?>" class="edit"><?=LangAdmin::get('edit')?></a>
                </td>
                <td>
                    <a href="#" value="<?=$this->escape($user['login'])?>" class="auth"><?=LangAdmin::get('auth_user')?></a>
                </td>
            </tr>
        </tbody>
    </table>
    </div>
    
    <div id="tabs">
        <ul>
            <li id="itab1"><a href="#tabs-1"><?=LangAdmin::get('general_information')?></a></li>
            <li id="itab5"><a href="#tabs-5"><?=LangAdmin::get('profiles')?></a></li>
            <li id="itab2"><a href="#tabs-2"><?=LangAdmin::get('account')?></a></li>
            <li id="itab4"><a href="#tabs-4"><?=LangAdmin::get('accountlog')?></a></li>
            <li id="itab3"><a href="#tabs-3"><?=LangAdmin::get('orders')?></a></li>
        </ul>
    
        <div id="tabs-1">
            <h2><?=LangAdmin::get('general_information')?></h2><br/>
            <? $recipient = $this->escape($user['recipientfirstname']).' '.$this->escape($user['recipientmiddlename']).
                ' '.$this->escape($user['recipientlastname']); ?>
            <? $address = array(); ?>
            <? if($recipient) $address[] = $recipient; ?>
            <? if($user['country']) $address[] = $this->escape($user['country']); ?>
            <? if($user['city']) $address[] = $this->escape($user['city']); ?>
            <? if($user['region']) $address[] = $this->escape($user['region']); ?>
            <? if((int)$user['postalcode']) $address[] = $this->escape($user['postalcode']); ?>
            <? if($user['address']) $address[] = $this->escape($user['address']); ?>
            <? if($user['phone']) $address[] = $this->escape($user['phone']); ?>
            <table> 
                <tr><td><strong><?=LangAdmin::get('login')?>:</strong></td><td><?=$this->escape($user['login'])?></td></tr>
                <tr><td><strong><?=LangAdmin::get('user_name')?>:</strong></td><td><? echo $this->escape($user['firstname']).' '.$this->escape($user['middlename']).' '.$this->escape($user['lastname']); ?></td></tr>
                <tr><td><strong><?=LangAdmin::get('name_and_address_of_the_recipient')?>:</strong></td><td><?=implode(', ', $address)?></td></tr>
                <tr><td><strong><?=LangAdmin::get('Skype')?>:</strong></td><td><?=$this->escape($user['skype'])?></td></tr>
                <tr><td><strong><?=LangAdmin::get('additional_information')?>:</strong></td><td></td></tr>
                <tr>
                    <td>
						<button class="linklocation" value="index.php?cmd=users&do=useredit&id=<?=$user['id']?>"><?=LangAdmin::get('change_data')?></button>
						<button id="recovery" class="linklocation" onclick="confirm_recover(<?=$user['id']?>)"><?=LangAdmin::get('pass_recovery')?></button>
					</td>
                </tr>
            </table>
            <br clear="all"/>
        </div>
    
        <div id="tabs-5">
            <h2><?=LangAdmin::get('profiles')?></h2>
            <div style="margin-left:30px;">
                <label for="Profile"><?=LangAdmin::get('profile')?></label>
                <? if (count($profiles)) { ?>
                    <select name="profiles_select" id="profiles_select">
                        <? $profiles_count = 0; ?>
                        <? foreach ($profiles as $profile) { ?>
                            <? $profiles_count++; ?>
                            <option value="<?=$profile['id']?>"><?=LangAdmin::get('profile') . ' '  . $profiles_count ?></option>
                        <? } ?>
                    </select><br/><br/>
                <? } ?>
            </div>
            <? $profiles_count = 0; ?>
            <table>
            <? foreach ($profiles as $profile) { ?>
            <? $profiles_count++; ?>
            <tbody class="profile-data" id="profile-<?=$profile['id']?>" <? if ($profiles_count > 1) { ?> style="display:none;" <? } ?> >
                <tr>
                    <td><strong><?=LangAdmin::get('name_of_recipient')?></strong></td>
                    <td><?=$this->escape($profile['firstname'])?></td>
                </tr>
                
                <tr>
                    <td><strong><?=LangAdmin::get('surname_of_recipient')?></strong></td>
                    <td><?=$this->escape($profile['lastname'])?></td>
                </tr>
                
                <tr>
                    <td><strong><?=LangAdmin::get('recipient_middle_name')?></strong></td>
                    <td><?=$this->escape($profile['middlename'])?></td>
                </tr>
                
                <tr>
                    <? $country = $profile['countrycode']; ?>
                    <? foreach($countries as $item) { ?>
                        <? if($profile['countrycode']==$item['id']) $country = $item['Name']; ?>
                    <? } ?>
                    <td><strong><?=LangAdmin::get('country')?></strong></td>
                    <td><?=$country?></td>
                </tr>
                
                <tr>
                    <td><strong><?=LangAdmin::get('city')?></strong></td>
                    <td><?=$this->escape($profile['city'])?></td>
                </tr>
                
                <tr>
                    <td><strong><?=LangAdmin::get('address')?></strong></td>
                    <td><?=$this->escape($profile['address'])?></td>
                </tr>
                
                <tr>
                    <td><strong><?=LangAdmin::get('zip_code')?></strong></td>
                    <td><?=$this->escape($profile['postalcode'])?></td>
                </tr>
                
                <tr>
                    <td><strong><?=LangAdmin::get('region')?></strong></td>
                    <td><?=$this->escape($profile['region'])?></td>
                </tr>
                
                <tr>
                    <td><strong><?=LangAdmin::get('phone')?></strong></td>
                    <td><?=$this->escape($profile['Phone'])?></td>
                </tr>
                <? if (in_array('PassportData', General::$enabledFeatures)) { ?>
                    <tr>
                        <td><strong><?=LangAdmin::get('passport')?></strong></td>
                        <td><?=$this->escape($profile['passportnumber'])?></td>
                    </tr>

                    <tr>
                        <td><strong><?=LangAdmin::get('registrationaddress')?></strong></td>
                        <td><?=$this->escape($profile['registrationaddress'])?></td>
                    </tr>
                <? } ?>
            </tbody>
            <? } ?>
            </table>
            <button class="linklocation" value="index.php?cmd=users&do=useredit&id=<?=$user['id']?>"><?=LangAdmin::get('change_data')?></button>
            <br clear="all"/>
        </div>
        
        <div id="tabs-2">
            <? $currency = $user_account['currencysigncust'];?>
            <h2><?=LangAdmin::get('account')?></h2><br/>
            <table>
                <tr>
                    <td><strong><?=LangAdmin::get('account_number')?>:</strong></td>
                    <td><?=(string)$user_account['num'])?></td>
                </tr>
                <!--tr>
                    <td><strong><?=LangAdmin::get('account_balance')?>:</strong></td>
                    <td><?=(string)$user_account['balancecust'].$currency?></td>
                </tr>
                <tr>
                    <td><strong><?=LangAdmin::get('amount')?>, <?=LangAdmin::get('reserved_under_the_orders')?>:</strong></td>
                    <td><?=(string)$user_account['reservedcust'].$currency?></td>
                </tr-->
                <tr>
                    <td><strong><?=LangAdmin::get('amount')?>, <?=LangAdmin::get('available_for_payment_of_orders')?>:</strong></td>
                    <td><?=(string)$user_account['availablecust'].$currency?></td>
                </tr>
                <tr>
                    <td><button class="linklocation" value="index.php?sid=<?=$GLOBALS['ssid']?>&cmd=users&do=accountinfo&id=<?=$user['id']?>"><?=LangAdmin::get('work_with_a_score_of')?></button>
                    </td>
                </tr>
            </table>
            <br clear="all"/>
        </div>
    
        <div id="tabs-4">
            <? $currency = $user_account['currencysigncust'];?>
            <h2><?=LangAdmin::get('accountlog')?></h2><br/>
            <? if($moneyhistory) {?>
            <table width="100%">
                <thead>
                    <tr>
                        <td><?=LangAdmin::get('date')?></td>
                        <td><?=LangAdmin::get('comment')?></td>
                        <td><?=LangAdmin::get('sum')?></td>
                    </tr>
                </thead>
                <tbody>
                    <? foreach($moneyhistory['translist'] as $element) {?>
                    <tr>
                        <td><?=$element['transdate']?></td>
                        <td><?=(defined('CFG_PREFIX_REPLACE_ORD')?str_replace('ORD', CFG_PREFIX_REPLACE_ORD, $this->escape((string)$element['comment'])):$this->escape((string)$element['comment']))?></td>
                        <td>
                            <? $style = (strpos((string)$element['amountinternal'], '-') !== false) ? 'style="color: Red"' : 'style="color: Green"'; ?> 
                            <span <?=$style;?> >
                                <? echo (string)$element['amountinternal'].' '.$currency; ?>
                            </span>
                        </td>
                    </tr>
                    <? } ?>
                </tbody>
            </table>
            <? }  else { ?>
                <h3 class="lgray tagc mt10"> <?=LangAdmin::get('empty_list')?> </h3>
            <? } ?>
            <br clear="all"/>
        </div>
    
        <div id="tabs-3">
            <h2><?=LangAdmin::get('orders')?></h2><br/>
            <div class="grid_16">
                <div class="grid_7">
                    <p>
                        <label><?=LangAdmin::get('status')?>:<br/> </label>
                        <? $status_list = Permission::filter_order_status($status_list); ?>
                        <table><tr><td>
                        <? $count = 0; ?>
                        <? $new_td = false; ?>
                        <? foreach ($status_list as $status) { ?>
                            <? $count++; ?>
                            <? if (($count-1) > count($status_list)/2 && !$new_td) { ?>
                                </td><td>
                                <? $new_td = true; ?>
                            <? } ?>
                            <? $checked = (isset($filters['status'][$status['Id']])) ? 'checked' : ''; ?>
                            <label><input style="width:16px" class="check-status" type="checkbox" name="filter[status][<?=$status['Id']?>]" value="<?=$status['Id']?>" <?=$checked?>/><?=$status['Name']?></label><br/>
                        <? } ?>
                        </td></tr></table>
                    </p>
                </div>
                <div class="grid_7">
                    <span style="float:right;">
                        <input id="apply_filters" type="submit" value="<?=LangAdmin::get('apply_filters')?>"
                                   class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">
                        <br/><br/>

                        <input id="reset_filters" type="submit" value="<?=LangAdmin::get('reset_filters')?>"
                                   class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">
                    </span>
                </div>
            <table>
                <thead>
                    <tr>
                        <th><?=LangAdmin::get('order_number')?></th>
                        <th><?=LangAdmin::get('creation_time')?></th>
                        <th><?=LangAdmin::get('amount')?></th>
                        <th><?=LangAdmin::get('paid')?></th>
                        <th><?=LangAdmin::get('name_of_operator')?></th>
                        <th><?=LangAdmin::get('order_status')?></th>
                    </tr>
                </thead>
                
                <tfoot>
                    <tr>
                        <td colspan="6" class="pagination">
                            <? $perpage = (isset($_GET['ps'])) ? $_GET['ps'] : '20'; ?>
                            <? $count = count($user_orders); ?>
                            <? $maxpage = ceil($count/$perpage); ?>
                            <? for($i=1; $i <= $maxpage; $i++){ ?>
                                <? if($maxpage == 1) break; ?>
                                <a class="curved <?= ($i==1)? ' active' : ''?>" href=""><?=$i?></a>
                            <? } ?>
                        </td>
                    </tr>
                </tfoot>

                <tbody>
                    <? $count_orders = 0; ?>
                    <? $page_count = 1; ?>
                    <? foreach ($user_orders as $order) { ?>
                        <? if($count_orders >= $perpage) { $page_count++; $count_orders = 0; }?>       
                        <? $cur = $order['currencysign']; ?>
                        <? $pay = round((float)$order['totalamount'] - (float)$order['remainamount'], 2); ?>
                        <tr class="page_<?=$page_count?>">
                            <td><a href="index.php?sid=<?=$GLOBALS['ssid'];?>&cmd=orders&do=orderinfo&id=<?=$order['id']?>"><?=(defined('CFG_PREFIX_REPLACE_ORD')?str_replace('ORD', CFG_PREFIX_REPLACE_ORD, (string)$order['id']):(string)$order['id'])?></a></td>
                            <td><?=$order['createddatetime']?></td>
                            <td><span class="pr"><nobr><?=round((float)$order['totalamount'],2).' '.$cur; ?></nobr></span></td> 
                            <td><span class="pr"><nobr><?=round((float)$order['totalamount'] - (float)$order['remainamount'],2).' '.$cur; ?></nobr></span></td>
                            <td><span class="pr"><?=@$order['operatorname']; ?></span></td>
                            <td class="status status-id-<?=$order['statusid']?>"><?=$order['statusname']?></td>
                        </tr>
                        <? $count_orders++; ?>
                    <?  }  ?>
                    
                </tbody>
            </table>
            </div>
            <br clear="all"/>
        </div>
        
    </div>

<? } ?>
<script>
$('a.auth').live('click', function (ev) {
    ev.preventDefault();
    showOverlay();

    var login = $(this).attr('value');
    var server_url = 'index.php?cmd=users&sid=<?=$GLOBALS['ssid']?>&do=auth&login=' + login;

    $.get(server_url, function (data) {
            if (data == 'RELOGIN') location.href = 'index.php?expired';
            if (data == 'Ok') {
                window.open('http://<?=$_SERVER['SERVER_NAME']?>','_blank');
            } else {
                $('#error').html('<?=LangAdmin::get('there_was_an_error')?> ' + data);
            }
            hideOverlay();
            
    }).error(function (jqXHR, textStatus, errorThrown) {
        $('#error').html('Error');
        hideOverlay();
    });
});


$('#apply_filters').live('click', function (ev) {
    ev.preventDefault();
    $('.status').parent().hide();
    $('.check-status').each(function(){
        if ($(this).is(':checked')) {
            var status_id = $(this).val();
            $('.status-id-' + status_id).parent().show();
        }
    });
});


$('#reset_filters').live('click', function (ev) {
    ev.preventDefault();
    $('.check-status').removeAttr('checked');
    $('.status').parent().show();
});

    
    
    
	function confirm_recover(id) {
		$("#pass_dialog-form").dialog("open");
		user_id = id;
		$('#info').html('<?=defined('CFG_BUYINCHINA')&&@CFG_BUYINCHINA?LangAdmin::get('confirm_pass_new'):LangAdmin::get('confirm_pass_recovery')?>');
		return false;
	}

$(function () {
    $('#tabs').tabs(); 
    
    $('tr[class^=page]').hide();
    $('.page_1').show();
    
    $('select#profiles_select').change(function() {
        $('.profile-data').hide();
        $('#profile-' + $(this).val()).show();
    });
    
    $('.curved').click(function () {
        $('tr[class^=page]').hide();
        $('.page_' + $(this).html()).show();
        
        $('tr a').removeClass('active');
        $(this).addClass('active');
        return false;
    });
	$("#dialog-form:ui-dialog").dialog("destroy");

	$("#pass_dialog-form").dialog({
		autoOpen:false,
		modal:true,
		buttons:{
		<?if (!defined ('CFG_BUYINCHINA') || !CFG_BUYINCHINA){?>
			"<?=LangAdmin::get('save')?>":function () {
				savePassword(user_id);
			},
			"<?=LangAdmin::get('cancellation')?>":function () {
				$("#pass_dialog-form").dialog("close");
				var a = document.getElementById('newPassword');
				a.value='';
			}
		<?} else {?>
			"<?=LangAdmin::get('yes')?>":function () {
				window.location.href = "index.php?cmd=users&do=recoverpassword&id="+user_id;
			},
			"<?=LangAdmin::get('no')?>":function () {
				$("#pass_dialog-form").dialog("close");
			}
			<?}?>
		}
	});
});

$('.linklocation')
    .button()
    .click(function () {
		if (this.id!='recovery')
        window.location.href = $(this).val();
});
function suggestPassword(){
	var a = document.getElementById('newPassword');
	a.value='';
	for(i=0;i<16;i++)
		a.value+="abcdefhjmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWYXZ".charAt(Math.floor(Math.random()*53));
	return true;
}
function savePassword(id) {
	var server_url = 'index.php?cmd=users&do=savePassword&id=' + id;
	var a = document.getElementById('newPassword');

	$.ajax({
		url:server_url,
		type:"POST",
		data:{"Password":a.value,"Id":id},
		success:function (data) {
			if (data == 'RELOGIN') location.href = 'index.php?expired';
			if (data == 'Ok') {
				$("#pass_dialog-form").dialog("close");
				var a = document.getElementById('newPassword');
				a.value='';
			} else {
					$("#saveError").html(data);

			}
		},
		error:function () {
			$("#saveError").html(data);
		}
	});
}

</script>
<? include (TPL_DIR."footer.php"); ?>