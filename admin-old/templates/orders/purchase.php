<? $sign_m = $purchase_list['salesorderinfo']['currencysign']; ?>

<h3><?=LangAdmin::get('ready_to_purchase_goods')?></h3>
<? if(count($purchase_list['saleslineslist'])) { ?>
    <div class="grid_16">
        <table>
            <thead>
                <tr>
                    <th><?=LangAdmin::get('photo')?></th>
                    <th><?=LangAdmin::get('marking_the_store')?>, <?=LangAdmin::get('part_taobao')?>, <?=LangAdmin::get('seller_on_taobao')?>, <?=LangAdmin::get('product_name')?>, конфигурация</th>
                    <th><?=LangAdmin::get('standard_values_​​for')?></th>
                    <th><?=LangAdmin::get('status_of_goods')?></th>
                    <th><?=LangAdmin::get('actions')?></th>
                </tr>
            </thead>
            <tbody style="border: 1px dotted #D3D3D3;">
            <? foreach($purchase_list['saleslineslist'] as $item){ //var_dump($item);?>
            <? $sign = ' '.LangAdmin::get('kg').'.'; ?>
            <? $summa = round((float)$item['pricecust'] * (int)$item['qty'],2); ?>
            <? $configuration = explode(';', $item['configtext']); ?>
            <? $configuration = implode('; ', $configuration); ?>

            <tr style="border: 1px dotted #D3D3D3;">
                    <td >
                        <img src="<?=$item['itemimageurl'];?>_40x40.jpg" class="big_image ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" alt="" width="100%" height="100%"/>
                    </td>
                    <td>
                            <?=$item['briefdescrtrans'];?><br/>
                            <b><?=LangAdmin::get('original')?>:</b><a target="blank" href="<?=$item['itemexternalurl'];?>"><?=$item['itemtaobaoid'];?></a><br/>
                            <b><?=LangAdmin::get('seller')?>:</b><?=$item['vendnick'];?><br/>
                            <? if (isset($item['configtext'])) { ?>
                                <b><?=LangAdmin::get('configuration')?>:</b><?=$configuration;?>
                            <? } ?>
                    </td>
                    <td>
                        <strong><?=LangAdmin::get('price')?>:</strong><? echo round((float)$item['pricecust'],2).' '.$sign_m;?><br/>
                        <strong><?=LangAdmin::get('number_of')?>-<?=LangAdmin::get('in')?>:</strong><?=$item['qty']?><br/>
                        <strong><?=LangAdmin::get('in_total')?>:</strong><? echo $summa.' '.$sign_m; ?><br/>
                    </td> 
                    <td>
                        <strong><?=LangAdmin::get('current_status')?>:</strong><?=$item['statusname'];?><br/><br/>
                    </td>
                    <td style="text-align: right;">
                    </td>
              </tr>
              <tr style="border: 1px dotted #D3D3D3;">
                  <td colspan="5"><strong><?=LangAdmin::get('comment_buyer_to_the_product')?>:<?=$item['custcomment']?></strong></td>
              </tr>
              <!--tr style="border: 1px dotted #D3D3D3;">
                  <td colspan="5"><strong><?=LangAdmin::get('commentary_to_the_operator_product')?>:<?=$item['operatorcomment']?></strong></td>
              </tr-->
              <?  }  ?>
          </tbody>
        </table>
    </div>
    <button style="display:none" class="purchaseitems" value="<?=$item['id']?>"><?=LangAdmin::get('to_order_products_from_the_supplier')?></button>
<? }  else { ?>
    <small><?=LangAdmin::get('there_are_no_products_available_for_purchase')?>! <?=LangAdmin::get('products_fall_into_this_category_if')?>, <?=LangAdmin::get('if_their_status')?> "<?=LangAdmin::get('in_processing')?>"</small>
<? } ?>

<br/>
<h3><?=LangAdmin::get('purchased_goods')?></h3>
<? if(count($purchased_list['saleslineslist'])) { ?>
    <div class="grid_16">
        <table>
            <thead>
                <tr>
                    <th><?=LangAdmin::get('photo')?></th>
                    <th><?=LangAdmin::get('marking_the_store')?>, <?=LangAdmin::get('part_taobao')?>, <?=LangAdmin::get('seller_on_taobao')?>, <?=LangAdmin::get('product_name')?>, конфигурация</th>
                    <th><?=LangAdmin::get('standard_values_​​for')?></th>
                    <th><?=LangAdmin::get('status_of_goods')?></th>
                    <th><?=LangAdmin::get('actions')?></th>
                </tr>
            </thead>
            <tbody style="border: 1px dotted #D3D3D3;">
            <? foreach($purchased_list['saleslineslist'] as $item){ //var_dump($item);?>
            <? $sign = ' '.LangAdmin::get('kg').'.'; ?>
            <? $summa = round((float)$item['pricecust'] * (int)$item['qty'],2); ?>

            <tr style="border: 1px dotted #D3D3D3;">
                    <td >
                        <img src="<?=$item['itemimageurl'];?>_40x40.jpg" class="big_image ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" alt="" width="100%" height="100%"/>
                    </td>
                    <td>
                            <?=$item['briefdescrtrans'];?><br/>
                            <b><?=LangAdmin::get('original')?>:</b><a target="blank" href="<?=$item['itemexternalurl'];?>"><?=$item['itemtaobaoid'];?></a><br/>
                            <b><?=LangAdmin::get('seller')?>:</b><?=$item['vendnick'];?><br/>
                            <? if (isset($item['configtext'])) { ?>
                                <b><?=LangAdmin::get('configuration')?>:</b><?=$item['configtext'];?>
                            <? } ?>
                    </td>
                    <td>
                        <strong><?=LangAdmin::get('price')?>:</strong><? echo round((float)$item['pricecust'],2).' '.$sign_m;?><br/>
                        <strong><?=LangAdmin::get('number_of')?>-<?=LangAdmin::get('in')?>:</strong><?=$item['qty']?><br/>
                        <strong><?=LangAdmin::get('in_total')?>:</strong><? echo $summa.' '.$sign_m; ?><br/>
                    </td> 
                    <td>
                        <strong><?=LangAdmin::get('current_status')?>:</strong><?=$item['statusname'];?><br/><br/>
                    </td>
                    <td style="text-align: right;">
                    </td>
              </tr>
              <tr style="border: 1px dotted #D3D3D3;">
                  <td colspan="2"><strong><?=LangAdmin::get('comment_buyer_to_the_product')?>:</strong><?=$item['custcomment']?></td>
                  <td colspan="1"><?=LangAdmin::get('the_actual_data')?> </td>
                  <td colspan="2"><strong><?=LangAdmin::get('commentary_to_the_operator_product')?>:</strong><?=$item['operatorcomment']?></td>
              </tr>
              <?  }  ?>
          </tbody>
        </table>
    </div>
<? } else { ?>
    <small><?=LangAdmin::get('purchased_from_the_supplier_broths_to_this_order_is_not_found')?>!</small>
<? } ?>


<script>
    
$('.purchaseitems')
    .button()
    .click(function(){
        window.location.href = 'index.php?cmd=orders&do=purchaseitems&id=<?=$id;?>';     
});

$('.big_image')
    .button()
    .click(function () {
        img = $(this).attr('src');
        img = img.replace('_40x40.jpg', '');
        $('#big_image').attr('src', img);
        $("#dialog-image").dialog("open");
 });

</script>
    
<br clear="all"/>
<?=Plugins::invokeEvent('onAdminOrderPurchaseItemsRender', array('orderId' => RequestWrapper::get('id')))?>
