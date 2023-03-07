 <? if(count($order_info['saleslineslist'])){?>
    <table class="notepad">
        <thead>
            <tr>
                <td><?=LangAdmin::get('photo')?></td>
                <td><?=LangAdmin::get('product_information')?></td>
                <td><?=LangAdmin::get('price')?>*кол-<?=LangAdmin::get('in')?> = <?=LangAdmin::get('amount')?></td>
                <td><?=LangAdmin::get('status_of_goods')?></td>
                <td><?=LangAdmin::get('actions_with_the_goods')?></td>
            </tr>
        </thead>
        <tbody>
            <? //$total_w = 0; ?>
            <? foreach($order_info['saleslineslist'] as $item){ ?>
            <? //$weight = isset($item['weight']) ? (string)$item['weight']: 0; ?>
            <? //$total_w += (int)$item['qty']*$weight;?>
            <? //$total_m += $item['qty']*$item['pricecust'];?>
            <? $sign = ' '.LangAdmin::get('kg').'.'; ?>
            <? $summa = round((float)$item['pricecust'] * (int)$item['qty'],2); ?>

            <tr id="item<?=$item['id']?>">
                    <td class="td1">
                            <ul class="i40 flin"><li><a href="index.php?p=item&id=<?=$item['id'];?>"><img src="<?=$item['itemimageurl'];?>" alt="" width="100%" height="100%"/></a></li></ul>
                    </td>
                    <td width="250px">
                            <?=$item['briefdescrtrans'];?><br/>
                            <b><?=LangAdmin::get('original')?>:</b><a target="blank" href="<?=$item['itemexternalurl'];?>"><?=$item['itemtaobaoid'];?></a><br/>
                            <b><?=LangAdmin::get('seller')?>:</b><a href="index.php?p=vendor&id=<?=$item['vendid'];?>"><?=$item['vendnick'];?></a><br/>
                            <? if (isset($item['configtext'])) { ?>
                                <b><?=LangAdmin::get('configuration')?>:</b><?=$item['configtext'];?>
                            <? } ?>
                    </td>
                    <td>
                        <span class="pr"><? echo round((float)$item['pricecust'],2).'  '.$sign_m;?></span>
                        <? echo ' * '.$item['qty'].' = <span class="pr">'.$summa.'  '.$sign_m. '</span>'; ?>
                        <div id="qty<?=$item['id']?>" style="display:none;"><?=$item['qty']?></div>
                    </td> 
                    <td>
                        <strong><?=LangAdmin::get('current_status')?>:</strong> <span id="curstatus<?=$item['id']?>"><?=$item['statusname'];?></span><br/><br/>
                        <span id="curstatusid<?=(int)$item['id']?>" style="display:none;"><?=$item['statuscode'];?></span>
                        <strong><?=LangAdmin::get('change_status_to')?>:</strong><br/>
                        <? if(isset($status_list[$item['id']])) { ?>
                            <select id="status<?=$item['id']?>" name="status<?=$item['id']?>">
                                <option value=""></option>
                                <? foreach($status_list[$item['id']] as $status) { ?>
                                    <option value="<?=$status['id']?>" ><?=$status['name']?></option>
                                <? } ?>
                            </select> <span onClick='confirm_cnange("<?=$order_info['salesorderinfo']['id']?>","<?=$item['id']?>")' style="cursor:pointer;"><?=LangAdmin::get('save')?></span>
                        <? } else { ?>
                            <?=$item['statusname'];?>
                        <? } ?> 
                    </td>
                    <td>
                        <? if($item['statusname'] != 10) { ?>
                        <span id="delete<?=$item['id']?>" onClick='confirm_delete("<?=$order_info['salesorderinfo']['id']?>","<?=$item['id']?>")' style="cursor:pointer;">
                            <!-- <img src="<i/del.png" width="12" height="12" title="<?=LangAdmin::get('remove_item')?>"/> -->
                            <?=LangAdmin::get('cancel_item')?>
                        </span>
                        <? } ?>
                    </td>
              </tr>
              <tr id="itemcom<?=$item['id']?>">
                  <td colspan="3"><strong><?=LangAdmin::get('comment_buyer_to_the_product')?>:<?=$item['custcomment']?></strong></td>
                  <td colspan="2"><strong><?=LangAdmin::get('commentary_to_the_operator_product')?>:</strong><br/>
                      <textarea rows="4" cols="20" name="opcom<?=$item['id']?>" id="opcom<?=$item['id']?>"><?=$item['operatorcomment']?></textarea>
                      <span onClick='cnange_status("<?=$order_info['salesorderinfo']['id']?>","<?=$item['id']?>")' style="cursor:pointer;"><?=LangAdmin::get('save')?></span>
                  <br/>
                  </td>
              </tr>
              <?  }  ?>
              <tr height="80px">
                  <td colspan="3"></td>
                  <td colspan="2">
                      <strong><?=LangAdmin::get('total_cost_of')?>:</strong><span class="pr" id="total_w"><? echo ' '.round((float)$order_info['salesorderinfo']['goodsamount'],2).' '.$sign_m;?></span><br/>
                      <strong><?=LangAdmin::get('total_weight_of')?>:</strong><span id="total_m"><? echo ' '.(string)$order_info['saleslineslist']['packagesweight'].' '.$sign;?></span>
                  </td>
              </tr>
          </tbody>
    </table>
<? } ?>