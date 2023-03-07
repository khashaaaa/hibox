var oData;
var FullItemInfo;
var confObject;
var confId;
var basketID;
var isChangingConfigProcess = false;
var xhrGetDeliveryCost;

function equals(x,y)
{
    if(x == undefined)
        return false;
    if(y == undefined)
        return false;
    var p;
    for(p in y) {
        if(typeof(x[p])=='undefined') {return false;}
    }

    for(p in y) {
        if (y[p]) {
            switch(typeof(y[p])) {
                case 'object':
                    if (!y[p].equals(x[p])) {return false;}break;
                case 'function':
                    if (typeof(x[p])=='undefined' ||
                        (p != 'equals' && y[p].toString() != x[p].toString()))
                        return false;
                    break;
                default:
                    if (y[p] != x[p]) {return false;}
            }
        } else {
            if (x[p])
                return false;
        }
    }

    for(p in x) {
        if(typeof(y[p])=='undefined') {return false;}
    }

    return true;
}

function getConfigurationObject(){
    confObject = {};
    for(var i in FullItemInfo.configurations){
        confObject[i] = $('#'+i).val();
    }
}

function afterRecountTotalPrice()
{
    resetOrderButtons();
}

var noErrorsChecking, // (bool) была ли ошибка
    noWarningChecking; // (bool) было ли предупреждение

function initCheckingVariable()
{
    noErrorsChecking = true; // в начале ошибок нет
    noWarningChecking = true; // в начале предупреждений нет
}

function resetOrderButtons() {
    $('#makeOrderBtn')
        .removeClass('btn-danger-important')
        .removeClass('btn-warning-important')
        .data('continue', 'false');

    $('.deliveryMakeOrderBtn')
        .removeClass('btn-danger-important')
        .removeClass('btn-warning-important')
        .data('continue', 'false');
}

function checkBasket(activityId, groupItems, finishCallback, stepCallback)
{
    $.ajax({
        url: '/?p=checkbasket',
        type: 'POST',
        data: {'activityId':activityId}
    })
        .success(function(data){
            if (data.ErrorCode === 'Ok') {

                if (typeof stepCallback === 'function') {
                    stepCallback(data);
                }

                if (data.IsFinished === 'true') {
                    if (typeof finishCallback === 'function') {
                        finishCallback(data);
                    }
                } else {
                    setTimeout(function () {
                        checkBasket(activityId, groupItems, finishCallback, stepCallback);
                    }, 1000);
                }
            }
        })
        .error(function(jqXHR, textStatus, errorThrown){
            hideOverlay();
            if (errorThrown=='NotAvailable') {
                error = 'NotAvailableError';
            } else {
                error = jqXHR.responseText;
            }
            $().toastmessage('showToast', {'text': error, 'stayTime': 6000, 'type': 'error'});
        });
}

function runFullBasketChecking(groupItems, initButton, successUrl) {

    $('[id ^= status]')
        .text('')
        .removeClass('alert-warning')
        .removeClass('alert-danger');
    $('table.basket').find('tr').each(function () {
        $(this)
            .removeClass('shadow-warning')
            .removeClass('shadow-danger')
            .removeClass('shadow-success');
    });
    resetOrderButtons();
    showOverlay(true);

    RunBasketChecking(groupItems, function(result) {
        // finishCallback
        /* Делается scroll при необходимости */
        var firstError = $("[id ^= status].alert-danger").first(),
            firstWarning = $("[id ^= status].alert-warning").first();
        if(firstError.length !== 0) {
            $('body, html').animate({ scrollTop: firstError.offset().top - 70 }, 500);
        } else if (firstWarning.length !== 0) {
            $('body, html').animate({ scrollTop: firstWarning.offset().top - 70 }, 500);
        }

        if (firstError.length !== 0 || firstWarning.length !== 0) {
            showMessage(trans.attention_changes_in_the_basket, true);
        }

        if (!!noErrorsChecking === false) {
            /* Была ошибка */
            hideOverlay();
            initButton.addClass('btn-danger-important');
        } else if (!!noWarningChecking === false && initButton.data('continue') === 'false') {
            /* Было предупреждение без подтверждения пользователя */
            initButton
                .addClass('btn-warning-important')
                .data('continue', 'true'); // warning при следующей проверке не прерывает процедуру заказа

            hideOverlay();
            $().toastmessage('showToast', {'text': trans.order_has_comments, 'stayTime': 60000, 'type': 'warning'});
        } else {
            var form = $(
                '<form action="' + successUrl + '" method="post">' +
                '<input type="hidden" name="type" value="' + $('.selectedTab:visible').attr('alias') + '" />' +
                '<input type="hidden" name="items" value="' +  groupItems.join(',') + '" /></form>'
            );
            $('body').append(form);
            $(form).submit();
        }
    }, function (result) {
        // stepCallback
        $('#progressBar')
            .css('width', result.ProgressPercent + '%')
            .html(result.ProgressPercent + '%');

        for(var i in result.Messages) {
            var status = result.Messages[i].Status,
                statusClasses = 'alert ',
                listItemClass = '';

            if (status) {
                switch (status) {
                    case 'Ok' :
                        statusClasses += '';
                        listItemClass = 'shadow-success';
                        noErrorsChecking = noErrorsChecking * true;
                        noWarningChecking = noWarningChecking * true;
                        break;
                    case 'Warning' :
                        statusClasses += 'alert-warning';
                        listItemClass = 'shadow-warning';
                        noWarningChecking = noWarningChecking * false;
                        break;
                    case 'Error' :
                        statusClasses += 'alert-danger';
                        listItemClass = 'shadow-danger';
                        noErrorsChecking = noErrorsChecking * false;
                        break;
                    default :
                        noErrorsChecking = noErrorsChecking * true;
                        noWarningChecking = noWarningChecking * true;
                }
                $('#status_' + result.Messages[i].ElementId)
                    .addClass(statusClasses)
                    .text(result.Messages[i].Text);
                $('[data-rowid="' + result.Messages[i].ElementId + '"]')
                    .addClass(listItemClass);

                /* Растянем overlay если нужно */
                var $overlay = $('#overlay');
                $overlay.css({
                    height: $overlay.parent().height() + 'px'
                });
            } else {
                noErrorsChecking = noErrorsChecking * true;
                noWarningChecking = noWarningChecking * true;
            }
        }
    });
}

/**
 * Запустить проверку корзины для элементов groupItems
 *
 * @param groupItems - массив id элементов корзины
 * @param finishCallback - выполнится после завершения проверки
 * @param stepCallback - выполнится на каждой итерации проверки
 * @return {boolean}
 * @constructor
 */
function RunBasketChecking(groupItems, finishCallback, stepCallback)
{
    if (typeof finishCallback === 'undefined' && typeof stepCallback === 'undefined') {
        return false; // прервать проверку если нет ни одного обработчика
    }
    initCheckingVariable();

    $.ajax({
        url: '/?p=runbasketchecking',
        type: 'POST',
        data: {'items':groupItems}
    })
        .success(function(activity){
            // Проверить корзину
            checkBasket(activity.id, groupItems, finishCallback, stepCallback);
        })
        .error(function(jqXHR, textStatus, errorThrown){
            hideOverlay();
            if (errorThrown=='NotAvailable') {
                error = 'NotAvailableError';
            } else {
                error = jqXHR.responseText;
            }
            $().toastmessage('showToast', {'text': error, 'stayTime': 6000, 'type': 'error'});
        });
}

function makeOrder(initButton){
    show_save_dialog_result = false;
    $('.copy').click();

    //Ждем пока сохраняться комментарии
    var timerId = setInterval(function(){
        var to_step1 = true;
        $.each($('.copy'), function(index, elem){
            if ($(elem).attr('is-change-comment') == 'true') {
                to_step1 = false;
            }
        });
        if (to_step1) {
            if (timerId) {
                clearInterval(timerId);
            }
            provider = $('.tabs .active').attr('tab');
            var groupItems = [];
            $.each($('#'+ provider + ' .item-to-order input'), function(index, elem){
                if ($(elem).prop("checked")) {
                    groupItems.push($(elem).val());
                }
            });
            if (groupItems.length === 0) {
                $("#dialog-empty").dialog("open");
                return false;
            }


            var successUrl = $('#makeOrderBtn').attr('href');

            if (initButton.data('continue') === 'false') {
                runFullBasketChecking(groupItems, initButton, successUrl);
            } else {
                var form = $(
                    '<form action="' + successUrl + '" method="post">' +
                    '<input type="hidden" name="type" value="' + $('.selectedTab:visible').attr('alias') + '" />' +
                    '<input type="hidden" name="items" value="' +  groupItems.join(',') + '" /></form>'
                );
                $('body').append(form);
                $(form).submit();
            }
        }
    }, 500);    
}

function searchConfig(){
    confId = 0;
    for(var i in FullItemInfo.item_with_config){
        if(equals(FullItemInfo.item_with_config[i].config, confObject)){
            confId = i;
            break;
        }
    }
}

function setCosts(elemid, num){
    showOverlay();
    $.post(
        '/?p=editbasketitemquantity', {'id':elemid, 'num': num}, 
        function(data) {
            var sign;
            $(data.Basket.Elements).each(function(k, item){
                sign = item.CurrencySign;
                $('#total-price-'+item.Id)
                    .html(sdf_FTS(parseFloat(item.Cost), price_round_decimals, '&nbsp;') + '&nbsp;' + sign)
                    .attr('price', parseFloat(item.Cost));

                var newDeliveryPricePerItem = 0;
                if (item.GroupConvertedPriceList && item.GroupConvertedPriceList.Internal && item.GroupConvertedPriceList.Internal.Display) {
                    newDeliveryPricePerItem = item.GroupConvertedPriceList.Internal.Display;
                }

                $('[data-groupid='+item.GroupId+']')
                    .data('addprice', newDeliveryPricePerItem)
                    .find('span').eq(1).html(newDeliveryPricePerItem);

                $('#price-1-'+item.Id).html(item.DisplayPrice);
            });

            sign = $('span.total_price').attr('sign');
            var activeTabId = $('div.tab:visible').attr('id');
            
            cartRecountTotalData($('.tabs .active').attr('tab'));
            
            hideOverlay();
            if( data.error ){
                $('#message').html(data.error);
                $("#dialog-form").dialog("open");
            }
        }, 'json'
    );
}

function showOverlay(showProgress = false){
    var h = $('#overlay').parent().height();
    $('#overlay').css({
        height: h+'px',
        display: 'block'
    });
    if (showProgress) {
        $('#overlay-progressbar').css({
           display: 'block'
        });
    }
}

function hideOverlay(){
    var h = $('#overlay').parent().height();
    $('#overlay-progressbar').hide();
    $('#overlay').hide();
}

function check_weight_step1() {
    var result = true;
    $.each($('.change-weight'), function() {
        if (!($(this).attr('value')) || ($(this).attr('value')==0)) {
            $("#dialog").dialog("open");
            result = false;
            return;
        }
    });
    return result;
}


function saveNewConfig () {
    if (! isChangingConfigProcess) {
        $('.alert-change-form').html('');
        isChangingConfigProcess = true;        
        getConfigurationObject();
        searchConfig();
        $('#setconfig').val(basketID);
        $('#newconfig').val(confId);

        $('#pictureURL').val($('#'+basketID+'_pictureURL').val());
        for(var i in confObject) {
            for(var j in FullItemInfo.configurations[i].values){
                if (FullItemInfo.configurations[i].values[j].id == confObject[i]) {
                    if (FullItemInfo.configurations[i].values[j].imageurl != '') {
                        $('#pictureURL').val(FullItemInfo.configurations[i].values[j].imageurl);
                        console.log(FullItemInfo.configurations[i].values[j].imageurl);
                    }
                }
            }

        }

        ctext = '';
        ctext_chy = '';

        for(var i in FullItemInfo.configurations){
            ctext += FullItemInfo.configurations[i].name+' : '+$('#'+i+' option:selected').text()+';';
            ctext_chy += $('#'+i).attr('name_cny')+' : '+$('#'+i+' option:selected').attr('op_cny')+';';
        }

        EndedPrice = 0;
            //Если есть акции
        if (FullItemInfo.Promotions['Id']!=undefined) {
            for(var i in FullItemInfo.Promotions['ConfiguredItems']){
                if (FullItemInfo.Promotions['ConfiguredItems'][i].Id==confId) {
                    for(var j in FullItemInfo.Promotions['ConfiguredItems'][i].Price.ConvertedPriceList){
                        if (FullItemInfo.Promotions['ConfiguredItems'][i].Price.ConvertedPriceList[j].Sign==$('#'+basketID+'_currencyName').val()) {
                            EndedPrice = FullItemInfo.Promotions['ConfiguredItems'][i].Price.ConvertedPriceList[j].Val;
                        }
                    }
                }
            }
        } else {
            for(var i in FullItemInfo.item_with_config[confId].ConvertedPriceList){
                if (FullItemInfo.item_with_config[confId].ConvertedPriceList[i].Sign==$('#'+basketID+'_currencyName').val()) {
                    EndedPrice = FullItemInfo.item_with_config[confId].ConvertedPriceList[i].Val;
                }
            }
        }

        $('#price').val(EndedPrice);
        $('#itemConfiguration').val(ctext);
        $('#itemConfigurationChina').val(ctext_chy);
        $('#item_id').val($('#'+basketID+'_id').val());
        $('#quantity').val($('#count-'+basketID).val());
        $('#itemTitle').val($('#'+basketID+'_itemTitle').val());
        $('#promoId').val($('#'+basketID+'_promoId').val());
        $('#categoryId').val($('#'+basketID+'_categoryId').val());
        $('#categoryName').val($('#'+basketID+'_categoryName').val());
        $('#currencyName').val($('#'+basketID+'_currencyName').val());
        $('#externalURL').val($('#'+basketID+'_externalURL').val());
        $('#externalDeliveryId').val($('#'+basketID+'_externalDeliveryId').val());
        $('#currentProvider').val($('ul.tabs > li.active').attr('tab'));

        $('#vendorId').val($('#'+basketID+'_vendorId').val());
        $('#weight').val($('#'+basketID+'_weight').val());

        if (FullItemInfo.item_with_config[confId].quantity==0) {
            isChangingConfigProcess = false;
            $('.save-config').prop('disabled',false);
            $('.cancel-changes').prop('disabled', false);
            $('.alert-change-form').html(trans.get('Given_product_configuration_is_not_in_stock'));
        } else {
            $('#ch_con').prepend('<h1>'+trans.saving+'....</h1>');
            $('#changeform').submit();
        }
    }
}

function initChangingConfigSelects() {
    $('.change-config-select').change(function(){
        $('.alert-change-form').html(trans.attention_price);
    });
}

function getActiveTab() {
    return $('.tabs li.active');
}

function cartRecountTotalData(provider) {
    var totalPrice = 0;
    var deliveryTotals = [];
    var deliveryCounts = [];
    var totalWeight = 0;
    if($('#'+ provider + ' .item-to-order input').length == 0) {
    	return;
    }
    if (typeof xhrGetDeliveryCost === 'object' && 'abort' in xhrGetDeliveryCost) {
        xhrGetDeliveryCost.abort();
    }

    $.each($('#'+ provider + ' .item-to-order input'), function(index, elem){
        if ($(elem).prop("checked")) {
            var itemId = $(elem).val();
            var weight = $('input#' + $(elem).val() + '_weight').val() * $('input#count-' + itemId).val();
            totalWeight += weight;

            var dId = $(elem).attr("data-deliveryid");
            var tmpPrice = parseFloat($("#total-price-" + $(elem).val()).attr('price'));
            if (isNaN(parseFloat(deliveryTotals[dId]))) {
                deliveryTotals[dId] = 0;
            }
            if (isNaN(parseFloat(deliveryCounts[dId]))) {
                deliveryCounts[dId] = 0;
            }
            deliveryCounts[dId]++;
            deliveryTotals[dId] = deliveryTotals[dId] + tmpPrice;
            totalPrice = totalPrice + tmpPrice;
        }
    });
    $('.bgr-panel #items_total_weight').html(sdf_FTS(totalWeight, 3));

    $('.delivery_cost').hide();
    $('input[name=deliveryMethod]').prop('checked', false);
    var checkedItems = $('#'+ provider + ' .item-to-order input:checked');
    var isCommonDelivery = false;
    var dId = '';
    if (checkedItems.length) {
        isCommonDelivery = true;
        $(checkedItems).each(function () {
            if (dId !== '' && dId !== $(this).attr("data-deliveryid")) {
                isCommonDelivery = false;
                return;
            }
            dId = $(this).attr("data-deliveryid");
        });
    }

    if (isCommonDelivery && dId && dId !== '0' && dId !== '') {
        var deliveryName = $('tr[data-externaldeliveryid=' + dId + '] .delivery-name').html();
        $('.delivery_method_from_group.delivery_method_span').html(deliveryName);
        $('.delivery_method_from_group.delivery_method_input').attr('data-deliveryid', dId).trigger('click');
        $('.delivery_method_from_group').show();
    } else {
        $('.delivery_method_from_group.delivery_method_input').attr('data-deliveryid', '');
        $('.delivery_method_from_group').hide();

        $('.delivery_method_from_order.delivery_method_input').trigger('click');

        if ($('a#makeOrderBtn').attr('href').indexOf('&deliveryId') !== -1) {
            var makeOrderBtnLink = $('a#makeOrderBtn').attr('href');
            var indexOfDeliveryId = makeOrderBtnLink.indexOf('&deliveryId');
            $('a#makeOrderBtn').attr('href', makeOrderBtnLink.substring(0, indexOfDeliveryId));
        }
    }

    var price = totalPrice;

    $('tr.groupid-tr').each(function(){
    	var addPrice = $(this).data('addprice');
    	var groupid = $(this).data('groupid');
    	if ($('#'+ provider + '').find(this).nextUntil('tr.groupid-tr').find('input#itemsToOrder:checked').length > 0 ) {
            var dId = $('#'+ provider + ' input#itemsToOrder[data-groupid="' + groupid + '"]').attr("data-externaldeliveryid");
            deliveryTotals[dId] = deliveryTotals[dId] + addPrice;
    		price += parseFloat(addPrice);
    	}
    });

    $('.bgr-panel #items_total_cost').attr('data-price', price);
    $('.bgr-panel #items_total_cost').html(sdf_FTS(price, price_round_decimals, '&nbsp;'));
    $(".bgr-panel #total-price-recount").html(sdf_FTS(price, price_round_decimals, '&nbsp;'));
    
    $.each($('#'+ provider + ' .item-to-order input'), function(index, elem){
        var dId = $(elem).attr("data-deliveryid");
        var makeOrderPartBlock = loggedIn ? ((deliveryTotals[dId] < min_order_cost && isReOrder) || (deliveryTotals[dId] >= min_order_cost)) : (simpleRegistration && ! userOrder && deliveryTotals[dId] >= min_order_cost);
        if (makeOrderPartBlock) {
            $(".deliveryCount-" + dId).parent().show();
        } else {
            $(".deliveryCount-" + dId).parent().hide();
        }
        if (typeof deliveryCounts[dId] === 'undefined') {
            $('.delivery-cost-block[deliveryId="' + dId + '"]').hide();
            $(".deliveryCount-" + dId).parent().attr('disabled', 'disabled');
            $(".deliveryCount-" + dId).html(' (0) ');
        } else {
            $('.delivery-cost-block[deliveryId="' + dId + '"]').show();
            $(".deliveryCount-" + dId).parent().removeAttr('disabled');
            $(".deliveryCount-" + dId).html(' (' + deliveryCounts[dId] + ') ');
        }
        
        $(".basketTotal-" + dId).html('');
        $(".basketTotal-" + dId).parent().hide();
        $('.delivery-cost[deliveryId="' + dId + '"]').html(precalculation_lang);
        $('.delivery-cost[deliveryId="' + dId + '"]').addClass('delivery-cost-link');
        if (isNaN(deliveryTotals[dId])) {
            $(".deliveryTotal-" + dId).html('0');
        } else {
            $(".deliveryTotal-" + dId).html(sdf_FTS(deliveryTotals[dId], price_round_decimals, '&nbsp;'));
        }
    });
    setTimeout(function() {
        $("#makeOrderBtn").find('span').html(' (' + $('input#itemsToOrder:checked', $('.selectedTab:visible')).length + ') ');
    }, 0);
    
    if (totalPrice < min_order_cost) {
        $('#minOrderCostError').show();
        try {
            showError($('#minOrderCostError').html());
        } catch(err) {
            $().toastmessage('showToast', {'text': $('#minOrderCostError').html(), 'stayTime': 60000, 'type': 'error'});
        }
        
        if (isReOrder) {
            $('#makeOrderBtn').show();
        } else {
            $('#makeOrderBtn').hide();
        }
    } else {
        $('#minOrderCostError').hide();
        $('#makeOrderBtn').show();
    }

    if ((loggedIn && ((totalPrice < min_order_cost && isReOrder) || (totalPrice >= min_order_cost))) || (simpleRegistration && ! userOrder && totalPrice >= min_order_cost)) {
        $('#makeOrderBtn').show();
        $('#orderLoginBtn').hide();
    } else if (!loggedIn) {
        $('#makeOrderBtn').hide();
        $('#orderLoginBtn').show();
    } else {
        $('#makeOrderBtn').hide();
        $('#orderLoginBtn').hide();
    }

    afterRecountTotalPrice();
}

$(function() {
    var show_save_dialog_result = true;
    var ActiveTabElement = getActiveTab();
    
    $('.delivery-cost').click(function() {
        var self = this;
        var dId = $(self).attr("deliveryId");
        if ($(".basketTotal-" + dId).parent().is(':visible')) {
            return;
        }
        $(self).html('<img src="css/i/loading.gif">');
        var provider = $('.selectedTab:visible').attr('alias');
        var items = [];
        var fullCost = 0;
        $.each($('#itemsToOrder[data-deliveryid="' + dId + '"]'), function(){
            if ($(this).prop("checked")) {
                items.push($(this).val());
                fullCost = fullCost + parseFloat($('#total-price-' + $(this).val()).attr("price"));
            }
        });
        $.get(
            '/?p=getDeliveryCost',
            {
                deliveryId: dId,
                type: provider,
                items: items.join(',')
            },
            function(data) {
                if( data.error ) {
                    $(self).html(precalculation_lang);
                    try {
                        showError(data.error);
                    } catch(err) {
                        $().toastmessage('showToast', {'text': data.error, 'stayTime': 60000, 'type': 'error'});
                    }
                    
                } else {
                    $(self).html(sdf_FTS(data.Price, price_round_decimals, '&nbsp;') + ' ' + data.CurrencySign);
                    $(self).removeClass('delivery-cost-link');
                    fullCost = fullCost + parseFloat(data.Price);                    
                    $(".basketTotal-" + dId).html(sdf_FTS(fullCost, price_round_decimals, '&nbsp;') + ' ' + data.CurrencySign);
                    $(".basketTotal-" + dId).parent().show();
                }
            }
        )
        .fail(function() {
            $(self).html(precalculation_lang);
        });
    });
    
    $('.js-checkbox-all, .js-checkbox-delivery').on('click', function(e) {
        var $checkbox = $(e.currentTarget),
            checkedState = $checkbox.prop('checked'),
            $selectedTab = $checkbox.closest('.js-selected-tab'),
            $checkboxes = null;

        if ($checkbox.hasClass('js-checkbox-delivery')) {
            // если чекбокс доставки, то выбираем только товары с этой доставкой
            var deliveryId = $checkbox.attr('deliveryid');
            $checkboxes = $selectedTab.find('.js-item-checkbox[data-deliveryid=' + deliveryId + ']');
        } else {
            // иначе, выбираем все чекбоксы
            $checkboxes = $selectedTab.find('.js-checkbox-delivery, .js-item-checkbox');
        }

        $checkboxes.prop('checked', checkedState);

        var $items = $checkboxes.closest('.js-item-line-wrapper');
        if (checkedState) {
            $items.removeClass('item-no-order');
        } else {
            $items.addClass('item-no-order');
        }

        cartRecountTotalData($selectedTab.attr('id'));
    });

    $('.js-item-checkbox').on('click', function(e) {
        var $checkbox = $(e.currentTarget),
            $itemLine = $checkbox.closest('.js-item-line-wrapper'),
            checkedState = $checkbox.prop('checked');

        if (checkedState) {
            $itemLine.removeClass('item-no-order');
        } else {
            $itemLine.addClass('item-no-order');
        }

        cartRecountTotalData($checkbox.closest('.js-selected-tab').attr('id'));
    });
    
    $('.btn-delete-group').click(function() {
        provider = $('.tabs .active').attr('tab');
        var groupItems = [];
        $.each($('#'+ provider + ' .item-to-order input'), function(index, elem){
            if ($(elem).prop("checked")) {
                groupItems.push($(elem).val());
            }
        });
        confirm(groupItems);
        return false;
    });
    
    $('.btn-move-group').click(function() {
        provider = $('.tabs .active').attr('tab');
        var groupItems = [];
        $.each($('#'+ provider + ' .item-to-order input'), function(index, elem){
            if ($(elem).prop("checked")) {
                groupItems.push($(elem).val());
            }
        });
        if (groupItems.length === 0) {
            $("#dialog-empty").dialog("open");
            return false;
        }
        showOverlay();
        count = groupItems.length;
        $.each(groupItems, function( index, itemId ) {
            $.get(
                "//"+window.location.hostname,
            {
                p: 'move_to_favourites',
                id: itemId,
                isAjax: true
            },
            function(data) {
                count--;
                if (count == 0) {
                    document.location.href = "/?p=basket";
                }
            });
        });
        return false;
    });
    

    $('.tabs li a').on('click', function() {
        resetOrderButtons();
        ActiveTabElement = $(this).parent();

        switch ($(this).parent().attr('tab')) {
            case 'whItems':
                $('.whStats').show();
                $('.taoStats').hide();
            break;

            case 'taobaoItems':
            default:
                $('.whStats').hide();
                $('.taoStats').show();
            break;
        }
        cartRecountTotalData($(this).parent().attr('tab'));
    });

    $('.quantity').keydown(function(e){
        if(e.keyCode === 13) {
            var input_id = event.target.id;

            var quan = parseInt($('#'+input_id).val());

            if (isNaN(quan)) {
                $('#'+input_id).val(iteminfo.minCount);
                return false;
            }

            $('#'+input_id).val(quan);

            setCosts($(this).attr('itemid'), quan);
        }
    });

    $('.plus').click(function(){
        var input_id = $(this).attr('rel');
        
        var quan = parseInt($('#'+input_id).val());
        quan = quan + 1;
        $('#'+input_id).val(quan);

        setCosts($(this).attr('itemid'), quan);
    });    
    $('.minus').click(function(){
        var input_id = $(this).attr('rel');
        var quan = parseInt($('#'+input_id).val());

        if(quan > 1) {
            quan = quan - 1;
            $('#' + input_id).val(quan);

            setCosts($(this).attr('itemid'), quan);
        }
    });
    $('.quantity').change(function(){
        var n = $(this).val();

        if(n < 1) {
            n = 1;
            $(this).val(n);
        }

        setCosts($(this).attr('itemid'), n);
    });

    
    $('.comment-area').bind('paste click', function(ev){
        var itemid = $(this).attr('itemid');
        $('#copy_' + itemid).attr('is-change-comment', 'true');
    });

    $('.copy').click(function(){
        var is_change = $(this).attr("is-change-comment");
        if (is_change == 'false') return;

        showOverlay();

        var itemid = $(this).attr('itemid');
        var comment = $('textarea[itemid="'+itemid+'"]').val();

        $.post(
            '/?p=editbasketitemcomment',
            {
                id: itemid,
                comment: comment
            },
            function(data) {
                $('.comment[itemid="'+itemid+'"]').val(comment);
                hideOverlay();

                if( data.error ){
                    show_save_dialog_result = true;
                    $('#message').html(data.error);
                } else{
                    $('#copy_' + itemid).attr('is-change-comment', 'false');
                    $('#message').html($('#comment_saved').val()+'!');
                }
                if (show_save_dialog_result) {
                    $("#dialog-form").dialog("open");
                }
            }
        );
    });

    $('#makeOrderBtn').on('click', function(ev){
        ev.preventDefault();
        makeOrder($(this));
        return false;
    });
    
    $('.deliveryMakeOrderBtn').on('click', function(){
        var initButton = $(this),
            provider = $('.tabs .active').attr('tab'),
            groupItems = [],
            deliveryId = $(this).data('deliveryid');

        $.each($('#'+ provider + ' .item-to-order input[data-deliveryid=' + deliveryId + ']'), function(index, elem){
            if ($(elem).prop("checked")) {
                groupItems.push($(elem).val());
            }
        });
        if (groupItems.length === 0) {
            $("#dialog-empty").dialog("open");
            return false;
        }

        var successUrl = $('#makeOrderBtn').attr('href');
        if (deliveryId != 0 ) {
            successUrl = successUrl + '&deliveryId=' + deliveryId;
        }

        if (initButton.data('continue') === 'false') {
            runFullBasketChecking(groupItems, initButton, successUrl);
        } else {
            var form = $(
                '<form action="' + successUrl + '" method="post">' +
                '<input type="hidden" name="type" value="' + $('.selectedTab:visible').attr('alias') + '" />' +
                '<input type="hidden" name="items" value="' +  groupItems.join(',') + '" /></form>'
            );
            $('body').append(form);
            $(form).submit();
        }

        return false;
    });

    /**
     * Step 1
     */
    var active_row = 0;
    $('.change-weight').click(function(){
        active_row = $(this);
    });
    $('.change-weight').change(function() {
        var elemid = $(this).attr('itemid');
        var weight = $(this).val();
        weight = weight.replace(/\,/, '.');
        if (isNaN(weight)) weight = 0;
        if (weight < 0) weight = 0;
        showOverlay();
        $.get("//"+window.location.hostname,{
            p: 'editorderweight',
            id: elemid,
            w: weight
        }, function(data){
            var total = parseInt(0);
            var sign = ' '+$('#sign').html();
            $(data.Elements).each(function(k, item){
            	if (undefined == item.Weight) {
            		item.Weight = 0;
            	}
                var w = parseFloat(item.Quantity.replace(/\,/, '.') * item.Weight);
                $('.row-weight[itemid="'+item.Id+'"]').html(w.toFixed(2) + sign);
                total+=parseFloat(item.Quantity.replace(/\,/, '.')*item.Weight);
                total = Math.round(total * 100) / 100;
            });
            total = Math.round(total * 100) / 100;
            $('#total_w').html(total+sign);
            hideOverlay();
        }, 'json');
    });
    /*$('#next').click(function(){
        if(active_row){
            $.ajaxSetup({
                async: false
            });
            active_row.change();
        }
        return true;
    });*/
    $('.change-config').click(function() {
        $("#change-window").dialog("open");
        isChangingConfigProcess = true;
        $('#change-window-content span.alert-change-form').show();
        $('#ch_con').html('<h1>'+trans.loading+'....</h1>');
        basketID = $(this).attr('basketid');
        var conf_names = '';
        var basketId = $(this).attr('basketid');
        $.ajax({
            url: '/?p=get_item_config&itemid=' + $(this).attr('itemid'),
            success: function(data) {
                $('#ch_con').html('');
                oData = JSON.parse(data);
                FullItemInfo = oData.Item;
                var currentConfigId = $('#' + basketId + '_ConfigurationId').val();
                if (oData.Item.item_with_config[currentConfigId] != undefined) {
                    var currentConfig = oData.Item.item_with_config[currentConfigId].config;
                } else {
                    var currentConfig = false
                }
                var hasConfig = false;

                $.each(oData.Item.configurations, function(i, config) {
                    hasConfig = true;
                    $('#ch_con').append(config.name+': ');
                    if (config.name_cny==undefined) {
                        name_cny_selecter = '';
                    } else {
                        name_cny_selecter = config.name_cny;
                    }
                    var s = $("<select id='"+i+"' name='"+i+"' class='change-config-select' name_cny='"+ name_cny_selecter +"'/> ");
                    $.each(config.values, function(j, confs){
                        if (confs.alias!='') {conf_names = confs.alias;} else {conf_names = confs.name;}
                        if ((currentConfig != false) && (currentConfig[i] == confs.id)) {
                            $("<option />", {value: confs.id, text: conf_names, op_cny:confs.name_cny, selected:'selected'}).appendTo(s);
                         } else {
                            $("<option />", {value: confs.id, text: conf_names, op_cny:confs.name_cny}).appendTo(s);
                         }
                    });
                    s.appendTo("#ch_con");
                    $('#ch_con').append('<br>');
                });
                if (! hasConfig) {
                    $('#change-window-content span.alert-change-form').hide();
                    $("#change-window").dialog("close");
                    
                    $("#dialog-form").dialog("open");
                    $("#dialog-form #message").html(trans.not_found_configuration);
                }
                isChangingConfigProcess = false;
                initChangingConfigSelects();
            }

        });
    });

    $('input[name=deliveryMethod]').click(function() {
        var makeOrderBtnLink = $('a#makeOrderBtn').attr('href');
        var indexOfDeliveryId = makeOrderBtnLink.indexOf('&deliveryId');

        if ($(this).val() === 'methodFromGroup') {
            $('#items_total_delivery_cost').html('<img src="css/i/loading.gif">');

            var deliveryId = $('.delivery_method_from_group.delivery_method_input').attr('data-deliveryid');
            var type = $('.selectedTab:visible').attr('alias');

            if (indexOfDeliveryId !== -1) {
                $('a#makeOrderBtn').attr('href', makeOrderBtnLink.substring(0, indexOfDeliveryId));
            }
            $('a#makeOrderBtn').attr('href', $('a#makeOrderBtn').attr('href') + '&deliveryId=' + deliveryId);

            var items = [];
            $.each($('#'+ $('.tabs .active').attr('tab') + ' .item-to-order input:checked'), function(index, elem){
                items.push($(elem).val());
            });

            xhrGetDeliveryCost = $.get(
                '/?p=getDeliveryCost',
                {
                    deliveryId: deliveryId,
                    type: type,
                    items: items.join(',')
                },
                function(data) {
                    if( data.error ) {
                        $(self).html(precalculation_lang);
                        try {
                            showError(data.error);
                        } catch(err) {
                            $().toastmessage('showToast', {'text': data.error, 'stayTime': 60000, 'type': 'error'});
                        }
                        $('.delivery_method_from_order.delivery_method_input').trigger('click');
                    } else {
                        $('#items_total_delivery_cost').html(sdf_FTS(data.Price, price_round_decimals, '&nbsp;') + ' ' + data.CurrencySign);

                        var totalPrice = Number(data.Price) + Number($('#items_total_cost').attr('data-price'));
                        $('#total-price-recount').html(sdf_FTS(totalPrice, price_round_decimals, '&nbsp;'));
                    }
                }
            )
            $('.bgr-panel div.delivery_cost').show();
        } else {
            if (typeof xhrGetDeliveryCost === 'object' && 'abort' in xhrGetDeliveryCost) {
                xhrGetDeliveryCost.abort();
            }

            if (indexOfDeliveryId !== -1) {
                $('a#makeOrderBtn').attr('href', makeOrderBtnLink.substring(0, indexOfDeliveryId));
            }

            $('#total-price-recount').html(sdf_FTS($('#items_total_cost').attr('data-price'), price_round_decimals, '&nbsp;'));
            $('.bgr-panel div.delivery_cost').hide();
        }
    });

    cartRecountTotalData($('.tabs .active').attr('tab'));
});