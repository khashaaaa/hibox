<? include (TPL_DIR."header.php"); ?>
<div id="dialog-form" title="<?=LangAdmin::get('message')?>">
	<span id="info"></span>
</div>
<br/>
<a href="index.php?cmd=adminusers&sid=<?=$GLOBALS['ssid']?>"> <<<?=LangAdmin::get('to_the_list_of_users')?></a><br/><br/>
<? if($user){?>
<? //var_dump($user); ?>
    <h2><?=LangAdmin::get('user_information')?></h2><br/>
    <div id="overlay"></div>
    <div id="grid_16">
    <table>
        <thead>
            <tr>
                <th><?=LangAdmin::get('user_name')?></th>
                <th><?=LangAdmin::get('account_number')?></th>
                <!-- <td><?=LangAdmin::get('type_of_user')?></td> -->
                <th><?=LangAdmin::get('additional_information')?></th>
                <th colspan="2"><?=LangAdmin::get('possible_actions')?></th>
            </tr>
        </thead>
        <tbody>
            <tr id="user<?=$user['id']?>">
                <td><a href="index.php?sid=<?=$GLOBALS['ssid'];?>&cmd=users&do=userinfo&id=<?=$user['id']?>"><?=$this->escape($user['login'])?></a></td>
                <td><?=$user_account['num']?></td> 
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
            <li id="itab2"><a href="#tabs-2"><?=LangAdmin::get('account')?></a></li>
            <li id="itab3"><a href="#tabs-3"><?=LangAdmin::get('orders')?></a></li>
        </ul>
    
        <div id="tabs-1">
            <h2><?=LangAdmin::get('general_information')?></h2><br/>
            <? $recipient = $user['recipientfirstname'].' '.$user['recipientmiddlename'].' '.$user['recipientlastname']; ?>
            <? $address = array(); ?>
            <? if($recipient) $address[] = $recipient; ?>
            <? if($user['country']) $address[] = $user['country']; ?>
            <? if($user['city']) $address[] = $user['city']; ?>
            <? if($user['region']) $address[] = $user['region']; ?>
            <? if((int)$user['postalcode']) $address[] = (int)$user['postalcode']; ?>
            <? if($user['address']) $address[] = $user['address']; ?>
            <? if($user['phone']) $address[] = $user['phone']; ?>
            <table> 
                <tr><td><strong><?=LangAdmin::get('login')?>:</strong></td><td><?=$this->escape($user['login'])?></td></tr>
                <tr><td><strong><?=LangAdmin::get('user_name')?>:</strong></td><td><? echo $user['firstname'].' '.$user['middlename'].' '.$user['lastname']; ?></td></tr>
                <tr><td><strong><?=LangAdmin::get('name_and_address_of_the_recipient')?>:</strong></td><td><?=implode(', ', $address)?></td></tr>
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
    
        <div id="tabs-2">
            <? $currency = $user_account['currencysigncust'];?>
            <h2><?=LangAdmin::get('account')?></h2><br/>
            <table>
                <tr>
                    <td><strong><?=LangAdmin::get('account_number')?>:</strong></td>
                    <td><?=$user_account['num']?></td>
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
    
        <div id="tabs-3">
            <h2><?=LangAdmin::get('orders')?></h2><br/>
            <div class="grid_16">
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
                            <td><a href="index.php?sid=<?=$GLOBALS['ssid'];?>&cmd=orders&do=orderinfo&id=<?=$order['id']?>"><?=$order['id']?></a></td>
                            <td><?=$order['createddatetime']?></td>
                            <td><span class="pr"><nobr><?=round((float)$order['totalamount'],2).' '.$cur; ?></nobr></span></td> 
                            <td><span class="pr"><nobr><?=round((float)$order['totalamount'] - (float)$order['remainamount'],2).' '.$cur; ?></nobr></span></td>
                            <td><span class="pr"><?=@$order['operatorname']; ?></span></td>
                            <td><?=$order['statusname']?></td>
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
    
    
    
	function confirm_recover(id) {
		$("#dialog-form").dialog("open");
		user_id = id;
		$('#info').html('<?=CFG_BUYINCHINA?LangAdmin::get('confirm_pass_new'):LangAdmin::get('confirm_pass_recovery')?>');
		return false;
	}

$(function () {
    $('#tabs').tabs(); 
    
    $('tr[class^=page]').hide();
    $('.page_1').show();
    
    $('.curved').click(function () {
        $('tr[class^=page]').hide();
        $('.page_' + $(this).html()).show();
        
        $('tr a').removeClass('active');
        $(this).addClass('active');
        return false;
    });
	$("#dialog-form:ui-dialog").dialog("destroy");

	$("#dialog-form").dialog({
		autoOpen:false,
		modal:true,
		buttons:{
			"<?=LangAdmin::get('yes')?>":function () {
				window.location.href = "index.php?cmd=users&do=recoverpassword&id="+user_id;
			},
			"<?=LangAdmin::get('no')?>":function () {
				$("#dialog-form").dialog("close");
			}
		}
	});
});

$('.linklocation')
    .button()
    .click(function () {
		if (this.id!='recovery')
        window.location.href = $(this).val();
});
</script>
<? include (TPL_DIR."footer.php"); ?>