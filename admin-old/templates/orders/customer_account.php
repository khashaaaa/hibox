<div class="grid_16">
<? if (! empty($user_account)) {?>
    <div id="general_information">
        <? $currency = $user_account['currencysigncust'];?>
        <h2><?=LangAdmin::get('account')?></h2><br/>
        <table>
            <tr>
                <td><strong><?=LangAdmin::get('account_number')?>:</strong></td>
                <td><?=(string)$user_account['num']?></td>
            </tr>
            <tr>
                <td><strong><?=LangAdmin::get('amount')?>, <?=LangAdmin::get('available_for_payment_of_orders')?>:</strong></td>
                <td><?=(string)$user_account['availablecust'].$currency?></td>
            </tr>
        </table>
        <br clear="all"/>
    </div>

    <div id="moneyhistory">
        <h2><?=LangAdmin::get('accountlog')?></h2><br/>
        <? if (! empty($moneyhistory)) {?>
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
        <? } else { ?>
            <h3 class="lgray tagc mt10"> <?=LangAdmin::get('empty_list')?> </h3>
        <? } ?>
        <br clear="all"/>
    </div>

<? } else { ?>
    <br/><strong><?=LangAdmin::get('User_not_found')?>!</strong><br/>
<? } ?>
</div>
 <br clear="all"/>
