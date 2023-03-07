<? include (TPL_DIR."header.php"); ?>
<? //var_dump($user); ?>
<br/>
<a href="index.php?cmd=users&sid=<?=$GLOBALS['ssid']?>"> &lt; &lt;  <?=LangAdmin::get('to_the_list_of_users')?></a><br/><br/>
<? if($user){?>
<? //var_dump($user); ?>
    <h2><?=LangAdmin::get('user_information')?></h2><br/>
    <div id="grid_16">
    <table>
        <thead>
            <tr>
                <th><?=LangAdmin::get('user_name')?></th>
                <th><?=LangAdmin::get('account_number')?></th>
                <!-- <td><?=LangAdmin::get('type_of_user')?></td> -->
                <th><?=LangAdmin::get('additional_information')?></th>
                <th><?=LangAdmin::get('possible_actions')?></th>
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
            </tr>
        </tbody>
    </table>
    </div>
    
    <? $currency = $user_account['currencysigncust'];?>
    <h2><?=LangAdmin::get('working_with_the_customer_account')?></h2>
    <? if(isset($error) && $error!='') {?>
        <font color="red"><?=LangAdmin::get('there_was_an_error')?>: <?=$error;?></font>
    <? } ?> <br/><br/>
    <form action="index.php?cmd=users&do=accountaction&sid=<?=$GLOBALS['ssid']?>&id=<?=$user['id']?>&login=<?=$this->escape($user['login'])?>" method="POST">
    <table>
        <tr>
            <td><strong><?=LangAdmin::get('account_number')?>:</strong></td>
            <td><?=(string)$user_account['num']?></td>
        </tr>
        <!--tr>
            <td><strong><?=LangAdmin::get('account_balance')?> (<?=LangAdmin::get('the_customers_balance')?>):</strong></td>
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
            <td><strong><?=LangAdmin::get('amount')?> для действия:</strong><br/>
            <input type="text" name="summa" value="0"/>
            </td>
        </tr>
        <tr>
            <td><strong><?=LangAdmin::get('actions')?>:</strong><br/>
                <label><input type="radio" name="isdebit" value="false" checked="checked"><?=LangAdmin::get('withdraw_funds')?>(<?=LangAdmin::get('withdraw_from_the_buyer')?>)</label><br/>
                <label><input type="radio" name="isdebit" value="true"><?=LangAdmin::get('enroll')?> (<?=LangAdmin::get('recharge')?>)</label><br/>
            </td>
        </tr>
        <tr>
            <td><strong><?=LangAdmin::get('comment')?>:</strong><br/>
                <textarea rows="4" cols="20" name="comment"></textarea>
            </td>
        </tr>
        <tr>
            <td>
                <button id="submit"><?=LangAdmin::get('perform_the_action')?></button>
                <button class="linklocation" type="button" value="index.php?cmd=users&do=userinfo&sid=<?=$GLOBALS['ssid']?>&id=<?=$user['id']?>"><?=LangAdmin::get('cancellation')?></button>
            </td>
        </tr>
    </table>
    </form>
    
<? } ?>
<script>
$('.linklocation')
    .button()
    .click(function (){
        window.location.href = $(this).val();
});

$('#submit')
    .button()
    .click(function () {
        $('#submit').submit();
});
</script>
<? include (TPL_DIR."footer.php"); ?>