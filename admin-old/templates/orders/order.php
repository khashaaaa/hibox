<?
include (TPL_ABSOLUTE_PATH . "header.php");

$sign_m = $order_info['salesorderinfo']['currencysign'];
?>

<div class="main">
    <div class="canvas clrfix">

        <div class="windialog" id="dialog-form" title="<?=LangAdmin::get('message')?>">
            <span id="info"></span>
        </div>

        <div class="windialog" id="dialog-form2" title="<?=LangAdmin::get('message')?>">
            <span id="info-message"><br/><?=LangAdmin::get('saved')?>!</span>
        </div>

        <div class="windialog" id="dialog-image" title="<?=LangAdmin::get('image')?>">
            <img id="big_image" src=""/>
        </div>

        <div class="windialog" id="dialog-merge" title="<?=LangAdmin::get('mergeorders')?>">
            <span><br/><?=LangAdmin::get('merge_text')?></span>
            <br><br>
            <input id="mergerec" value="">
            <span id="merge-error-message"></span>
        </div>

        <a href="index.php?cmd=orders&sid=<?=$GLOBALS['ssid']?>">
            &lt;&lt; <?=LangAdmin::get('back_to_the_list_of_orders')?></a>
        <br/><br/>

        <h2 style="float:left"><?=LangAdmin::get('order_number')?> <?=(defined('CFG_PREFIX_REPLACE_ORD')?str_replace('ORD', CFG_PREFIX_REPLACE_ORD, (string)$order_info['salesorderinfo']['id']):(string)$order_info['salesorderinfo']['id'])?></h2>
        <input type="hidden" name="order_id" id="order_id" value="<?=(string)$order_info['salesorderinfo']['id']?>" />
        <? if (Permission::show_order_field('order_status')) { ?><h2 style="float:right"><?=LangAdmin::get('order_status')?>: <?=(string)$order_info['salesorderinfo']['statusname']?></h2><? } ?>
        <br clear="all">
    <?
        $statuses = array();
        foreach($order_info['saleslineslist'] as $item)
        {
            //
            if (isset($statuses[$item['statusname']]))
            {
                $statuses[$item['statusname']] ++;
            } else {
                $statuses[$item['statusname']] = 1;
            }
        }
        //print_r($statuses);
    ?>
    <span style="float:right"><table><tr><th>Статус<th>Кол-во позиций</tr>
    <?
        foreach($statuses as $sname => $scount)
        {
            ?><tr><td><?=$sname?><td><?=$scount?></tr><?
        }
    ?>
    </table></span>
        <div id="overlay"></div>
        <div class="grid_16">
            <table>
                <thead>
                <tr>
                    <th><?=LangAdmin::get('order_number')?></th>
                    <th><?=LangAdmin::get('creation_time')?></th>
                    <th><?=LangAdmin::get('quantity_of_goods')?></th>
                    <th><?=LangAdmin::get('cost_of_goods')?></th>
                    <th><?=LangAdmin::get('cost_of_delivery')?></th>
                    <th><?=LangAdmin::get('total_value')?></th>
                    <th><?=LangAdmin::get('paid')?> / <?=LangAdmin::get('remain')?></th>
                    <th><?=LangAdmin::get('weight')?></th>
                </tr>
                </thead>

                <tbody>
                <tr>
                    <td>
                        <?=(defined('CFG_PREFIX_REPLACE_ORD')?str_replace('ORD', CFG_PREFIX_REPLACE_ORD, (string)$order_info['salesorderinfo']['id']):(string)$order_info['salesorderinfo']['id'])?>
                        <br>
                        <?=Plugins::invokeEvent('onAdminOrderListRenderCell', array($order_info['salesorderinfo']['id']));?>
                    </td>
                    <td><?=(string)$order_info['salesorderinfo']['createddatetime'] ?></td>
                    <td><?=(int)count($order_info['saleslineslist'])?></td>
                    <td><span
                        class="pr"><? echo round((float)$order_info['salesorderinfo']['goodsamount'], 2) . ' ' . $sign_m; ?> </span>
                        <?if (isset($order_info['salesorderinfo']['goodsamountCNY'])){?>
                            <br>
                            <span class="pr"><? echo round((float)$order_info['salesorderinfo']['goodsamountCNY'], 2) . ' ' . $order_info['salesorderinfo']['CNYsign']; ?> </span>
                        <?}?>
                    </td>
                    <td>
                        <span
                        class="pr"><? echo round((float)$order_info['salesorderinfo']['deliveryamount'], 2) . ' ' . $sign_m; ?> </span>
                        <?if (isset($order_info['salesorderinfo']['deliveryamountCNY'])){?>
                        <br>
                        <span class="pr"><? echo round((float)$order_info['salesorderinfo']['deliveryamountCNY'], 2) . ' ' . $order_info['salesorderinfo']['CNYsign']; ?> </span>
                        <?}?>
                        <br/><strong>(<? echo $order_info['salesorderinfo']['deliverymodename'];?>)</strong>
                    </td>
                    <td><span
                        class="pr"><? echo round((float)$order_info['salesorderinfo']['totalamount'], 2) . ' ' . $sign_m; ?> </span>
                        <?if (isset($order_info['salesorderinfo']['totalamountCNY'])){?>
                            <br>
                            <span class="pr"><? echo round((float)$order_info['salesorderinfo']['totalamountCNY'], 2) . ' ' . $order_info['salesorderinfo']['CNYsign']; ?> </span>
                        <?}?>
                    </td>
                    <td>
                        <span class="pr"><? echo round((float)$order_info['salesorderinfo']['totalamount'] - (float)$order_info['salesorderinfo']['remainamount'], 2) . ' ' . $sign_m; ?></span><br>
                        <span class="pr" style="color:red"><? if ((float)$order_info['salesorderinfo']['remainamount'] > 0) echo round((float)$order_info['salesorderinfo']['remainamount'], 2) . ' ' . $sign_m; ?></span>
                    </td>
                    <td><span
                        class="pr"><? echo $order_info['salesorderinfo']['weight']. ' ' . LangAdmin::get('kg'); ?></span>
                    </td>
                </tr>
                <tr>
                    <td colspan="8" id="order_action">
                        <b><?=LangAdmin::get('steps_to_order')?></b>
                        <br><br>

                        <? if ((int)$order_info['salesorderinfo']['cancancel']) { ?>
                        <button id="cancelorder"><?=LangAdmin::get('cancel')?></button>
                        <? } ?>

                        <button id="change_weight"><?=LangAdmin::get('change_weight')?></button>

                        <? if ((int)$order_info['salesorderinfo']['canclose']) { ?>
                        <button id="closeorder"><?=LangAdmin::get('close')?></button>
                        <? } ?>

                        <? if ((int)$order_info['salesorderinfo']['canclosecancel']) { ?>
                        <button id="closecancelorder"><?=LangAdmin::get('cancel')?> и закрыть</button>
                        <? } ?>

                        <? if ((float)$order_info['salesorderinfo']['remainamount'] > 0) { ?>
                        <button id="reservemoneyorder"><?=LangAdmin::get('reserve_money')?></button>
                        <? } ?>

                        <? if (defined('CFG_TRADEHUB_CELL')) {?>
                        <button id="sendToCalc">Перенести заказ в калькулятор</button>
                        <?}?>

                        <button id="mergeorders"><?=LangAdmin::get('mergeorders')?></button>

                        <button id="printdeclaration"><?=LangAdmin::get('opis')?></button>


                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <table>
            <? if (Permission::show_order_field('name_of_purchaser2')) {  ?>
                <tr>
                    <td width="50px"><strong><?=LangAdmin::get('name_of_purchaser')?>: </strong></td>
                    <td><?=$this->escape($order_info['salesorderinfo']['custname'])?>
                        <? if (Permission::show_order_field('link_to_user')) {  ?>
                        <button class="touser"
                                value="<?=$this->escape($order_info['salesorderinfo']['custid'])?>"><?=LangAdmin::get('go')?></button>
                        <br/>
                        <? } ?>
                    </td>
                </tr>
            <? } ?>
            <? if (Permission::show_order_field('account_balance')) {  ?>
            <? if (@$user_account) { ?>
            <tr>
                <td><strong>Количество тикетов:</strong></td>
                <td><?=$order_info['total_tickets']?> </td>
            </tr>
            <tr>
                <td><strong>Количество непрочитанных тикетов:</strong></td>
                <td><a href="index.php?cmd=support"><?=$order_info['unread_tickets']?></a></td>
            </tr>
            <? //if (defined('CFG_BUYINCHINA')) { ?>
                <tr>
                    <td width="50px"><strong><?=LangAdmin::get('account_balance')?>: </strong></td>
                    <td>
                        <?=(string)$user_account['availablecust'] . $user_account['currencysigncust']?>
                        <?if (isset($user_account['availablecustCNY'])){?>
                        <br />
                        <?=(string)number_format(round((float)$user_account['availablecustCNY'],2,2), 2, '.', ''). $user_account['availablecustCNYsign']?>
                        <?}?>
                    </td>
                </tr>
                <? } ?>
            <? } ?>
            <? if (Permission::show_order_field('comment_buyer')) {  ?>
                <tr>
                    <td><strong><?=LangAdmin::get('comment_buyer')?>:</strong></td>
                    <td><?=$this->escape($order_info['salesorderinfo']['custcomment'])?></td>
                </tr>
            <? } ?>
            <? if (Permission::show_order_field('name_of_operator')) {  ?>
                <tr>
                    <td><strong><?=LangAdmin::get('name_of_operator')?>:</strong></td>
                    <td><?=$order_info['salesorderinfo']['operatorname']?> </td>
                </tr>
            <? } ?>
            <? if (Permission::show_order_field('additional_info')) {  ?>
                <tr>
                    <td><strong><?=LangAdmin::get('additional_info')?>:</strong><br/>
                        <small><?=LangAdmin::get('additional_info_desc')?></small>
                    </td>
                    <td>
                        <textarea name="additionalinfo" id="addinfo" cols="45"><?=@$order_info['SalesOrderInfo']['additionalinfo']?></textarea><br/>
                        <button class="set_additional_info"><?=LangAdmin::get('save')?></button>
                    </td>
                </tr>
            <? } ?>

        </table>

        <div id="tabs">
            <ul>
                <? if (Permission::show_order_field('orderinfo_itab1')) {  ?>
                    <li id="itab1"><a href="#tabs-1"><?=LangAdmin::get('list_of_products')?></a></li>
                <? } ?>
                <? if (Permission::show_order_field('orderinfo_itab2')) {  ?>
                    <li id="itab2">
                        <a href="#tabs-2" onclick="update_purchase()"><?=LangAdmin::get('procurement_of_goods')?>
                        </a>
                    </li>
                <? } ?>
                <? if (Permission::show_order_field('orderinfo_itab3')) {  ?>
                    <li id="itab3"><a href="#tabs-3"><?=LangAdmin::get('parcels')?></a></li>
                <? } ?>
                <? if (Permission::show_order_field('orderinfo_itab4')) {  ?>
                    <li id="itab4">
                        <a href="#tabs-4" onclick="update_history()"><?=LangAdmin::get('history_of_changes')?>
                        </a>
                    </li>
                <? } ?>
                <? if (Permission::show_order_field('orderinfo_itab5')) {  ?>
                    <li id="itab5">
                        <a href="#tabs-5"><?=LangAdmin::get('dilaog_with_user')?>
                        </a>
                    </li>
                <? } ?>
                <? if (! empty($isLogist)) {  ?>
                    <li id="itab6">
                        <a href="#tabs-6" onclick="getCustomerInfo()"><?=LangAdmin::get('about_customer')?></a>
                    </li>
                <? } ?>
                <? if (! empty($isLogist)) {  ?>
                    <li id="itab6">
                        <a href="#tabs-7" onclick="getCustomerAccountInfo()"><?=LangAdmin::get('accountlog')?></a>
                    </li>
                <? } ?>
            </ul>

            <? if (Permission::show_order_field('orderinfo_itab1')) {  ?>
                <div id="tabs-1"><? include(TPL_ABSOLUTE_PATH . 'orders/orderinfo.php'); ?></div>
            <? } ?>
            <? if (Permission::show_order_field('orderinfo_itab2')) {  ?>
                <div id="tabs-2"></div>
            <? } ?>
            <? if (Permission::show_order_field('orderinfo_itab3')) {  ?>
                <div id="tabs-3"><? include (TPL_ABSOLUTE_PATH . 'orders/packages.php'); ?></div>
            <? } ?>
            <? if (Permission::show_order_field('orderinfo_itab4')) {  ?>
                <div id="tabs-4"></div>
            <? } ?>
            <? if (Permission::show_order_field('orderinfo_itab5')) {  ?>
                <div id="tabs-5"><? include(TPL_ABSOLUTE_PATH . 'orders/orderchat.php'); ?></div>
            <? } ?>
            <? if (! empty($isLogist)) {  ?>
                <div id="tabs-6"></div>
            <? } ?>
            <? if (! empty($isLogist)) {  ?>
                <div id="tabs-7"></div>
            <? } ?>
        </div>

        <? //var_dump($order_info);?>

        <br/>
    </div>
</div><!-- /.main -->

<form id="toCalc" action="http://calc.tradehubsystems.ru" method="post" target="_blank">
    <input type="hidden" name="order" value="<?php
    $data=json_encode($order_info);
    $encoded=htmlentities($data);
    echo $encoded;?>"
        />
</form>

<script>

var tab_number = <? echo (isset($tab_number)) ? $tab_number : 1; ?>;
$("#itab" + tab_number).addClass('ui-tabs-selected').addClass('ui-state-active');
$("#tab-" + tab_number).show();

var need_update_tab2 = 0;
var need_update_tab4 = 0;
var need_update_tab6 = 0;
var need_update_tab7 = 0;

var order_id = '<?=(string)$order_info['salesorderinfo']['id']?>';
var item_id = '';
var action = '';
var package_id = '';

function confirm_delete(id, itemid) {
    $("#dialog-form").dialog("open");
    $('#info').empty().html('<input type="hidden" name="line_id" value="' + itemid + '"/>'+
        '<?=LangAdmin::get('this_product_will_be_removed_from_the_order')?>. <?=LangAdmin::get('proceed')?>?');
    order_id = id;
    item_id = itemid;
    action = 0;
}

function confirm_cnange(id, itemid) {
    $("#dialog-form").dialog("open");
    $('#info').html('<?=LangAdmin::get('the_status_of_the_goods_will_be_changed')?>, <?=LangAdmin::get('proceed')?>?');

    order_id = id;
    item_id = itemid;
    action = 1;
}

function confirm_delete_package(id) {
    $("#dialog-form").dialog("open");
    $('#info').html('<?=LangAdmin::get('shipping_will_be_removed')?>. <?=LangAdmin::get('proceed')?>?');

    package_id = id;
    action = 2;
}

$(function () {
    $('#tabs').tabs();
    $("#dialog-form:ui-dialog").dialog("destroy");
    $("#dialog-form2:ui-dialog").dialog("destroy");
    $("#dialog-image:ui-dialog").dialog("destroy");


    $("#dialog-form").dialog({
        autoOpen:false,
        modal:true,
        buttons:{
            "<?=LangAdmin::get('yes')?>":function () {
                if (action == 0) {
                    item_id = $('input[name=line_id]').val();
                    delete_item(order_id, item_id);
                }
                if (action == 1) cnange_status(order_id, item_id);
                if (action == 2) delete_package(package_id);
                if (action == 3) {
                    var id = $('input[name=line_id]').val();
                    var qty = $('#qty' + id).html();
                    var separate = parseInt($('input[name=separate]').val());

                    if ((separate >= 1) && (separate < qty)) {
                        part_item(id, separate);
                    } else {
                        var upper_limit = qty - 1;
                        var error = '<?=LangAdmin::get('part_item_require_qty')?>' + upper_limit;
                        show_error(error);
                    }
                } else if (action == 5) {
                    item_id = $('input[name=line_id]').val();
                    var new_price = $('input[name=new_price]').val();
                    change_price(item_id, new_price);
                } else {
                    $("#dialog-form").dialog("close");
                }
                if (action == 4) {
                    change_weight(order_id);
                }
            },
            "<?=LangAdmin::get('no')?>":function () {
                $("#dialog-form").dialog("close");
            }
        }
    });


    $("#dialog-form2").dialog({
        autoOpen:false,
        modal:true,
        buttons:{
            "<?=LangAdmin::get('ok')?>":function () {
                $("#dialog-form2").dialog("close");
            }
        }
    });

    $("#dialog-image").dialog({
        autoOpen:false,
        modal:false,
        height:550,
        width:730
    });

    $("#dialog-merge").dialog({
        autoOpen:false,
        modal:false,
        height:200,
        width:300,
        buttons:{
            "<?=LangAdmin::get('ok')?>":function () {
                if (!$('#mergerec').val()) return;
                $('#merge-error-message').html('<img src="css/loading.gif">');
                server_url = 'index.php?sid=&cmd=orders&do=mergeorders&id=<?=$order_info['salesorderinfo']['id']?>&id2='+$('#mergerec').val();
                $.ajax({
                    url:server_url,
                    success:function (data) {
                        if (data == 'RELOGIN') location.href = 'index.php?expired';
                        if (data == 'Ok') {
                            clear_error();
                            location.href = 'index.php?cmd=orders&sid=<?=$GLOBALS['ssid']?>&do=orderinfo&id=<?=$order_info['salesorderinfo']['id']?>';
                        } else {
                            show_error(data);
                        }
                        $('#merge-error-message').html('');
                    },
                    error:function () {
                        show_error('');
                        $('#merge-error-message').html('');
                    }
                });
            },
            "<?=LangAdmin::get('cancel')?>":function () {
                $("#dialog-merge").dialog("close");
            }
        }
    });

});

function delete_item(id, itemid) {
    var server_url = 'index.php?cmd=orders&sid=<?=$GLOBALS['ssid']?>&do=cancelitem&id=' + id + '&itemid=' + itemid;

    $.ajax({
        url:server_url,
        success:function (data) {
            if (data == 'RELOGIN') location.href = 'index.php?expired';

            if (data == 'Ok') {
                $('#item' + itemid).hide();
                $('#itemcom' + itemid).hide();

                need_update_tab2 = 1;
                need_update_tab4 = 1;

                clear_error();
            } else {
                show_error(data);
            }
        },
        error:function () {
            show_error('');
        }
    });
}

function part_item(itemid, separate) {
    var server_url = 'index.php?cmd=orders&sid=<?=$GLOBALS['ssid']?>&do=separate&itemid=' +
        itemid + '&separate=' + separate + '&id=<?=$order_info['salesorderinfo']['id']?>';

    $.ajax({
        url:server_url,
        success:function (data) {
            if (data == 'RELOGIN') location.href = 'index.php?expired';

            if (data == 'Ok') {
                location.href = 'index.php?cmd=orders&sid=<?=$GLOBALS['ssid']?>' +
                    '&do=orderinfo&id=<?=$order_info['salesorderinfo']['id']?>';
                clear_error();
            } else {
                $('#dialog-form').dialog("close");
                show_error(data);
            }
        },
        error:function () {
            show_error('');
        }
    });
}

function delete_package(id) {
    var server_url = 'index.php?cmd=orders&sid=<?=$GLOBALS['ssid']?>&do=deletepackage&id=' + id;

    $.ajax({
        url:server_url,
        success:function (data) {
            if (data == 'RELOGIN') location.href = 'index.php?expired';

            if (data == 'Ok') {
                $('#package' + id).hide();
                clear_error();
            } else {
                show_error(data);
            }
        },
        error:function () {
            show_error('');
        }
    });
}

// *** <?=LangAdmin::get('update_procurement')?> ***

function update_purchase() {

    if ($('#tabs-2').html() && !need_update_tab2) return;

    $('#tabs-2').html('<div class="spinner"></div>');

    $.ajax({
        url:'index.php?cmd=orders&do=updatepurchase&id=<?=$order_info['salesorderinfo']['id']?>',
        success:function (data) {
            if (data == 'RELOGIN') location.href = 'index.php?expired';
            $('#tabs-2').html(data);

            need_update_tab2 = 0;
        },
        error:function () {
            show_error('');
        }
    });
}

// *** <?=LangAdmin::get('update_history_of_change_orders')?> ***

function update_history() {

    if ($('#tabs-4').html() && !need_update_tab4) return;

    $('#tabs-4').html('<div class="spinner"></div>');
    $.ajax({
        url:'index.php?cmd=orders&do=updatehistory&id=<?=$order_info['salesorderinfo']['id']?>',
        success:function (data) {
            if (data == 'RELOGIN') location.href = 'index.php?expired';
            $('#tabs-4').html(data);

            need_update_tab4 = 0;
        },
        error:function () {
            show_error('');
        }
    });
}

function getCustomerInfo() {

    if ($('#tabs-6').html() && !need_update_tab6) return;

    $('#tabs-6').html('<div class="spinner"></div>');
    $.ajax({
        url:'index.php?cmd=orders&do=getCustomerInfo&id=<?=$order_info['salesorderinfo']['id']?>',
        success:function (data) {
            if (data == 'RELOGIN') location.href = 'index.php?expired';
            $('#tabs-6').html(data);

            need_update_tab6 = 0;
        },
        error:function () {
            show_error('');
        }
    });
}

function getCustomerAccountInfo() {

    if ($('#tabs-7').html() && !need_update_tab7) return;

    $('#tabs-7').html('<div class="spinner"></div>');
    $.ajax({
        url:'index.php?cmd=orders&do=getCustomerAccountInfo&id=<?=$order_info['salesorderinfo']['id']?>',
        success:function (data) {
            if (data == 'RELOGIN') location.href = 'index.php?expired';
            $('#tabs-7').html(data);

            need_update_tab7 = 0;
        },
        error:function () {
            show_error('');
        }
    });
}

function cnange_status_all (items, id) {
    if (items.length) {
        showOverlay();
    } else {
        $("#dialog-form2").dialog("open");
        $('#info-message').html('<?=LangAdmin::get('need_check_lines')?>');
    }
    var count_items = 0;
    items.each(function() {
        var itemid = $(this).val();
        var qty = $("#qty" + itemid).html();
        var comment = $("#opcom" + itemid).val().replace("\n", "    ", "g");
        var status = $("#status" + itemid + " option:selected").val();

        if (status == '')  status = $("#curstatusid" + itemid).html();
        if (status == '0') status = $("#curstatusid" + itemid).html();
        var server_url = 'index.php?cmd=orders&sid=<?=$GLOBALS['ssid']?>&do=changestatus&id=' + id +
            '&itemid=' + itemid + '&status=' + status + '&comment=' + comment + '&qty=' + qty;

        $.ajax({
            url:server_url,
            success:function (data) {
                if (data == 'RELOGIN') location.href = 'index.php?expired';
                if (data == 'Ok') {
                    count_items +=1;
                    var new_status = $("#status" + itemid + " option:selected").text();
                    $('#curstatus' + itemid).html(new_status);
                    if (count_items == items.length) {
                        hideOverlay();
                        $("#dialog-form2").dialog("open");
                        $("#info-message").html('<?=LangAdmin::get('saved')?>!');
                    }
                    need_update_tab2 = 1;
                    need_update_tab4 = 1;
                    clear_error();
                } else {
                    show_error(data);
                }
            },
            error:function () {
                show_error('');
            }
        });
    });

}

function change_price(itemid, new_price)
{
    // Изменение цены
    var server_url = 'index.php?cmd=orders&sid=<?=$GLOBALS['ssid']?>&do=changeprice&id=' + order_id +
        '&itemid=' + itemid + '&new_price=' + new_price;
    $.ajax({
        url:server_url,
        success:function (data) {
            if (data == 'RELOGIN') {
                location.href = 'index.php?expired';
            } else {
                if (data == 'Ok') {
                    window.location.href = 'index.php?cmd=orders&do=orderinfo&id=<?=$order_info['salesorderinfo']['id']?>';
                } else {
                    show_error(data);
                }
            }
        },
        error:function () {
            show_error('');
        }
    });
}

function save_description(order, itemid)
{
    showOverlay();
    var params = {
        cmd : 'orders',
        do : 'saveDescription',
        item_id : itemid,
        order_id : order,
        desc : $('#desc_' + itemid).val(),
        desc_en : $('#desc_en_' + itemid).val()
    };

    $.post("index.php", params)
        .success(function(data) {
            hideOverlay();
            data = JSON.parse(data);
            if (!data.success) {
                if (data.message) {
                    show_error(data.message);
                } else {
                    show_error(data);
                }
            }
        })
        .error(function(xhr, ajaxOptions, thrownError){
            if(thrownError == 'SessionExpired'){
                window.location = 'index.php?cmd=login';
            }
            else{
                show_error(thrownError + '<br />' + xhr.responseText);
            }
        });
}

function lines(n, source) {

    var a = source,
        b = a.split('\n');

    return b[n - 1];

}

function cnange_status(id, itemid) {
    showOverlay();
    var qty = $("#qty" + itemid).html();
    var comment = $("#opcom" + itemid).val().replace("\n", "    ", "g");
    var status = $("#status" + itemid + " option:selected").val();
    var taobaoid = $("#taobaoid" + itemid).html();

    var status_cur = $("#curstatus" + itemid).html();
    var status_new = $("#status" + itemid + " option:selected").html();

    if (status == '' || status == '0')  {
        status = $("#curstatusid" + itemid).html();
        status_new = status_cur;
    }

    var server_url = 'index.php?cmd=orders&sid=<?=$GLOBALS['ssid']?>&do=changestatus&id=' + id +
        '&itemid=' + itemid + '&status=' + status + '&qty=' + qty +
        '&taobaoid=' + taobaoid + '&status_cur=' + status_cur + '&status_new=' + status_new;

    $.ajax({
        url:server_url,
        type:'POST',
        data: {comment: comment},
        success:function (data) {
            if (data == 'RELOGIN') {
                location.href = 'index.php?expired';
            } else {
                if (data == 'Ok') {
                    hideOverlay();
                    if (status_cur != status_new) {
                        var new_status = $("#status" + itemid + " option:selected").text();
                        $('#curstatus' + itemid).html(new_status);
                    }
                    need_update_tab2 = 1;
                    need_update_tab4 = 1;

                    clear_error();
                } else {
                    show_error(data);
                }
            }
        },
        error:function () {
            show_error('');
        }
    });
}


function change_weight(order_id) {
    var new_weight = $("input[name=changed_weight]").val();
    var server_url = 'index.php?cmd=orders&sid=<?=$GLOBALS['ssid']?>' +
        '&do=changeweight&id=<?=$order_info['salesorderinfo']['id']?>&new_weight=' + new_weight;

    showOverlay();

    $.ajax({
        url:server_url,
        success:function (data) {
            if (data == 'RELOGIN') {
                location.href = 'index.php?expired';
            } else {
                if (data == 'Ok') {
                    window.location.href = 'index.php?cmd=orders&do=orderinfo&id=<?=$order_info['salesorderinfo']['id']?>';
                } else {
                    hideOverlay();
                    show_error(data);
                }
            }
        },
        error:function () {
            hideOverlay();
            show_error('');
        }
    });
}


function clear_error() {
    $('#error').html('');
}

$('#change_weight')
    .button()
    .click(function(){
        var info = '<?=LangAdmin::get('change_weight_desc1')?><br/>';
        info += '<input type="text" name="changed_weight" value="<?=$order_info['salesorderinfo']['weight']?>"/><br/>';
        info += '<br/><?=LangAdmin::get('change_weight_desc2')?>';
        action = 4;
        $('#info').empty().html(info);
        $('#dialog-form').dialog("open");
});

$("#cancelorder")
    .button()
    .click(function () {
        window.location.href = '<?=$pageurl?>&do=cancel&id=<?=$order_info['salesorderinfo']['id']?>';
    });

$("#closeorder")
    .button()
    .click(function () {
        window.location.href = '<?=$pageurl?>&do=close&id=<?=$order_info['salesorderinfo']['id']?>';
    });

$("#closecancelorder")
    .button()
    .click(function () {
        window.location.href = '<?=$pageurl?>&do=closecancel&id=<?=$order_info['salesorderinfo']['id']?>';
    });

$('#reservemoneyorder')
    .button()
    .click(function () {
        window.location.href = '<?=$pageurl?>&do=paymentreserve&id=<?=$order_info['salesorderinfo']['id']?>';
    });

$('#sendToCalc')
    .button()
    .click(function () {
        $('#toCalc').submit();
    });

$('.touser')
    .button()
    .click(function () {
        var id = $(this).val();
        window.location.href = 'index.php?sid=<?=$GLOBALS['ssid'];?>&cmd=users&do=userinfo&id=' + id;
    });

$('.set_additional_info')
    .button()
    .click(function (ev) {
        ev.preventDefault();
        showOverlay();
        var additional = $("textarea#addinfo").val();
        var url = 'index.php?sid=<?=$GLOBALS['ssid'];?>&cmd=orders' +
            '&do=setadditional&id=<?=$order_info['salesorderinfo']['id']?>' +
            '&additional=' + additional;
        $.get(url, function (data) {
            if (data == 'RELOGIN') {
                location.href = 'index.php?expired';
            } else {
                hideOverlay();
                if (data != 'Ok') {
                    show_error(data);
                }
            }

        }).error(function () {
            hideOverlay();
            show_error('ERROR');
        });
    });

$('.big_image')
    .button()
    .click(function () {
        img = $(this).attr('src');
        img = img.replace('_40x40.jpg', '');
        img = img.replace('_70x70.jpg', '');
        $('#dialog-image').empty();
        $('#dialog-image').append($('<img></img>')
            .attr({ src:img })
        )
        $("#dialog-image").dialog("open");
    });

$('#mergeorders')
    .button()
    .click(function () {
        $("#dialog-merge").dialog("open");
    });

$("#printdeclaration")
.button()
.click(function () {
    window.open('http://<?=$_SERVER['SERVER_NAME']?>/admin-old/index.php?cmd=orders&do=printdeclaration&id=<?=$order_info['salesorderinfo']['id']?>', '_blank');
});

</script>

<?=Plugins::invokeEvent('onRenderOrder')?>
<?=Plugins::invokeEvent('onRenderButtons')?>
<?
include (TPL_ABSOLUTE_PATH . "footer.php");
?>