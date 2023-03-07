<?
include (TPL_DIR . "header.php");

$sign_m = $order_info['salesorderinfo']['currencysign'];
?>

<div id="dialog-form" title="<?=LangAdmin::get('message')?>">
    <span id="info"></span>
</div>

<a href="index.php?cmd=orders&sid=<?=$GLOBALS['ssid']?>"> &lt; &lt; <?=LangAdmin::get('back_to_the_list_of_orders')?>
</a>
<br/><br/>

<h2><?=LangAdmin::get('order_number')?> <?=$order_info['salesorderinfo']['id']?></h2><br/>
<? //var_dump($order_info); ?>
<table class="notepad">
    <thead>
    <tr>
        <td><?=LangAdmin::get('order_number')?></td>
        <td><?=LangAdmin::get('creation_time')?></td>
        <td><?=LangAdmin::get('quantity_of_goods')?></td>
        <td><?=LangAdmin::get('cost_of_goods')?></td>
        <td><?=LangAdmin::get('cost_of_delivery')?></td>
        <td><?=LangAdmin::get('total_value')?></td>
        <td><?=LangAdmin::get('paid')?></td>
        <td><?=LangAdmin::get('steps_to_order')?></td>
    </tr>
    </thead>

    <tbody>
    <tr>
        <td><?=$order_info['salesorderinfo']['id']?></td>
        <td><? echo (string)$order_info['salesorderinfo']['createddatetime'] ?></td>
        <td><?=(int)$order_info['salesorderinfo']['itemscount']?></td>
        <td><span
            class="pr"><? echo round((float)$order_info['salesorderinfo']['goodsamount'], 2) . ' ' . $sign_m; ?> </span>
        </td>
        <td><span
            class="pr"><? echo round((float)$order_info['salesorderinfo']['deliveryamount'], 2) . ' ' . $sign_m; ?> </span>
        </td>
        <td><span
            class="pr"><? echo round((float)$order_info['salesorderinfo']['totalamount'], 2) . ' ' . $sign_m; ?> </span>
        </td>
        <td><span
            class="pr"><? echo round((float)$order_info['salesorderinfo']['totalamount'] - (float)$order_info['salesorderinfo']['remainamount'], 2) . ' ' . $sign_m; ?></span>
        </td>
        <td>

            <? if ((int)$order_info['salesorderinfo']['cancancel']) { ?>
            <a href="<?=$pageurl?>&do=cancel&id=<?=$order_info['salesorderinfo']['id']?>"><?=LangAdmin::get('cancel_the_order')?></a><br/>
            <? } ?>

            <? if ((int)$order_info['salesorderinfo']['canclose']) { ?>
            <a href="<?=$pageurl?>&do=close&id=<?=$order_info['salesorderinfo']['id']?>"><?=LangAdmin::get('close_order')?></a><br/>
            <? } ?>

            <? if ((int)$order_info['salesorderinfo']['canclosecancel']) { ?>
            <a href="<?=$pageurl?>&do=closecancel&id=<?=$order_info['salesorderinfo']['id']?>"><?=LangAdmin::get('cancel_and_close')?>
                <?=LangAdmin::get('order')?></a><br/>
            <? } ?>

            <a href="<?=$pageurl?>&do=paymentreserve&id=<?=$order_info['salesorderinfo']['id']?>"><?=LangAdmin::get('reserve_money')?>
                под <?=LangAdmin::get('order')?></a><br/>

        </td>
    </tr>
    </tbody>
</table>

<?=LangAdmin::get('name_of_purchaser')?>: <?=LangAdmin::get('ivanov_and')?>.<?=LangAdmin::get('and')?>.; <?=LangAdmin::get('comment_buyer')?>:
Ф<?=LangAdmin::get('and')?>О оператора: <?=LangAdmin::get('in_doe')?>.С.

<!-- tabs -->
<ul class="itabs mt30 flin clrfix">
    <li class="active" id="ttab1"><a href="#" onclick="showtab(1, this);return false"><?=LangAdmin::get('list_of_products')?></a></li>
    <li id="ttab2"><a href="#" onclick="showtab(2, this);return false"><?=LangAdmin::get('procurement_of_goods')?></a></li>
    <li id="ttab3"><a href="#" onclick="showtab(3, this);return false"><?=LangAdmin::get('parcels')?></a></li>
    <li id="ttab4"><a href="#" onclick="showtab(4, this);return false"><?=LangAdmin::get('and')?>стория изменений</a></li>
</ul>
<!-- /tabs -->

<? //var_dump($order_info);?>

<br/>
<span id="error" style="color:red;font-weight: bold;">
        <? if (isset($error)) {
    print $error;
} ?>
    </span>

<div class="itab" id="tab1">
    <? include(TPL_DIR . 'orders/orderinfo.php'); ?>
</div>

<div class="itab" id="tab2">

</div>

<div class="itab" id="tab3">
    <? include (TPL_DIR . 'orders/packages.php'); ?>
</div>

<div class="itab" id="tab4">
    <? include (TPL_DIR . "orders/processlog.php"); ?>
</div>

<script>
    function showtab(num, elm) {
        for (i = 1; i <= 4; i++) {
            document.getElementById('tab' + i).style.display = 'none';
            document.getElementById('ttab' + i).className = '';
        }
        document.getElementById('tab' + num).style.display = 'block';
        document.getElementById('ttab' + num).className = 'active';
    }
    var tab_number = <? echo (isset($tab_number)) ? $tab_number : 1; ?>;
    showtab(tab_number, false);

    var order_id = '';
    var item_id = '';
    var action = '';
    var package_id = '';

    function confirm_delete(id, itemid) {
        $("#dialog-form").dialog("open");
        $('#info').html('Товар будет удален из <?=LangAdmin::get('order')?>а. Продолжить?');

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
        $('#info').html('<?=LangAdmin::get('shipping_will_be_removed')?>. Продолжить?');

        package_id = id;
        action = 2;
    }

    $(function () {
        $("#dialog-form:ui-dialog").dialog("destroy");

        $("#dialog-form").dialog({
            autoOpen:false,
            modal:true,
            buttons:{
                "<?=LangAdmin::get('yes')?>":function () {
                    if (action == 0) delete_item(order_id, item_id);
                    if (action == 1) cnange_status(order_id, item_id);
                    if (action == 2) delete_package(package_id);

                    $("#dialog-form").dialog("close");
                },
                "<?=LangAdmin::get('no')?>":function () {
                    $("#dialog-form").dialog("close");
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
                    clear_error();
                } else {
                    $('#error').html('<?=LangAdmin::get('there_was_an_error')?> ' + data);
                }
            },
            error:function () {
                $('#error').html('<?=LangAdmin::get('there_was_an_error')?> ');
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
                    $('#error').html('<?=LangAdmin::get('there_was_an_error')?> ' + data);
                }
            },
            error:function () {
                $('#error').html('<?=LangAdmin::get('there_was_an_error')?>!');
            }
        });
    }

    function cnange_status(id, itemid) {
        //alert($("#opcom" + itemid).val());
        //alert($("#qty" + itemid).html());
        //alert($("#status" + itemid +" option:selected").text());
        var qty = $("#qty" + itemid).html();
        var comment = $("#opcom" + itemid).val();
        var status = $("#status" + itemid + " option:selected").val();
        if (status == '') status = $("#curstatusid" + itemid).html();
        var server_url = 'index.php?cmd=orders&sid=<?=$GLOBALS['ssid']?>&do=changestatus&id=' + id +
            '&itemid=' + itemid + '&status=' + status + '&comment=' + comment + '&qty=' + qty;

        $.ajax({
            url:server_url,
            success:function (data) {
                if (data == 'RELOGIN') location.href = 'index.php?expired';
                if (data == 'Ok') {
                    var new_status = $("#status" + itemid + " option:selected").text();
                    $('#curstatus' + itemid).html(new_status);
                    clear_error();
                } else {
                    $('#error').html('<?=LangAdmin::get('there_was_an_error')?> ' + data);
                }
            },
            error:function () {
                $('#error').html('<?=LangAdmin::get('there_was_an_error')?> ');
            }
        });
    }

    function clear_error() {
        $('#error').html('');
    }

</script>


<?
include (TPL_DIR . "footer.php");
?>	