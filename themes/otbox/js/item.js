if (!Array.prototype.filter)
{
    Array.prototype.filter = function(fun /*, thisp */)
    {
        "use strict";

        if (this === void 0 || this === null)
            throw new TypeError();

        var t = Object(this);
        var len = t.length >>> 0;
        if (typeof fun !== "function")
            throw new TypeError();

        var res = [];
        var thisp = arguments[1];
        for (var i = 0; i < len; i++)
        {
            if (i in t)
            {
                var val = t[i]; // in case fun mutates this
                if (fun.call(thisp, val, i, t))
                    res.push(val);
            }
        }

        return res;
    };
}

var Item = {};

Item.equals = function(x,y)
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

Item.resetForms = function(){
    /*
    $('#basket-action').find('i').removeClass('removeitem').addClass('additem');
    $('#basket-action').removeClass('remove-action').addClass('add-action');
    var i = $('#basket-action span i');
    $('#basket-action span').empty().text($('#add_to_cart').html()).prepend(i);
    $('#note-action').removeClass('btn-remove').addClass('btn-add');
    iteminfo.confid = 0;
    */
}

Item.clearPrices = function(){
    $('#c_promo_cost').html('');
    $('#c_cost, .c_cost').css('text-decoration', 'none');

    $('.c_cost, .c_promo, .br-promo').remove();
}

Item.getConfigurationText = function(){
    Item.ctext = '';
    Item.ctext_chy = '';
    for(var i in iteminfo.configurations){
        Item.ctext += configurations[i].cname+': '+configurations[i].val[$('#'+i).val()]+' <br>';
/* *** */
        var config_chy = '';
        try {
            config_chy = $('#val'+$('#'+i).val()).attr('config_chy');
        } catch(e) {
        }
        Item.ctext_chy += $('#cname'+i).attr('config_chy')+' '+ config_chy +' <br>';
/* *** */
    }
    $('#c_cur').html(Item.ctext);
}

Item.getConfigurationObject = function(){
    Item.confObject = {};
    for(var i in iteminfo.configurations){
        Item.confObject[i] = $("select[id='" + i + "']").val();
    }
}

Item.searchConfig = function(){
    Item.confId = 0;
    Item.found = true;
    $('.add_to_basket').show();
    
    $('.remove_from_basket').hide();
    $('.remove_from_note').hide();
    for(var i in iteminfo.item_with_config){
        Item.found = false;
        if(Item.equals(iteminfo.item_with_config[i].config, Item.confObject)){
            Item.confId = i;
            iteminfo.confid = i;
            Item.found = true;
            break;
        }
    }
}

Item.checkAddedByUserToCarts = function(){
    $('.add_to_basket').show();
    $('.remove_from_basket').hide();
    $('.add_to_note').show();
    $('.remove_from_note').hide();

    for(var i in iteminfo.addedToCart){
        if (iteminfo.confid==iteminfo.addedToCart[i].configurationid) {
            $('.add_to_basket').hide();
            $('.remove_from_basket').show();
            iteminfo.addedToCartId = iteminfo.addedToCart[i].id;
        }
    }

    for(var i in iteminfo.addedToNote){
        if (iteminfo.confid==iteminfo.addedToNote[i].configurationid) {
            $('.add_to_note').hide();
            $('.remove_from_note').show();
            iteminfo.addedToNoteId = iteminfo.addedToNote[i].id;
        }
    }
}

Item.deleteFormCart = function(conf_id){
    for(var i in iteminfo.addedToCart){
        if (conf_id==iteminfo.addedToCart[i].id) {
            delete iteminfo.addedToCart[i];
        }
    }
}

Item.deleteFormNote = function(conf_id){
    for(var i in iteminfo.addedToNote){
        if (conf_id==iteminfo.addedToNote[i].id) {
            delete iteminfo.addedToNote[i];
        }
    }
}

Item.setItemProperties = function (priceObj, resetMainImage) {
    resetMainImage = (typeof resetMainImage !== 'undefined' && resetMainImage === false) ? false : true;

    if (typeof priceObj === 'undefined') {
        //Нет конфига - нельзя купить!
        $('.add_to_basket').addClass('disabled');
        $('.add_to_note').addClass('disabled');
    } else {
        $('.add_to_basket').removeClass('disabled');
        $('.add_to_note').removeClass('disabled');

        // установить количество товара от конфигурации
        if (iteminfo.configurations && priceObj.quantity != undefined) {
            $('#c_count').html(priceObj.quantity + ' ' + langs.pcs);
            $('input#quantity').attr('max', priceObj.quantity);
            iteminfo.maxcount = priceObj.quantity;
        }

        // установить главную картинку от конфигурации
        for (var i in priceObj.config) {
            if ($('ul[id="list_' + i + '"]').length && resetMainImage) {
                $('ul[id="list_' + i + '"] li').removeClass('list_minipic_selected');
                $('ul[id="list_' + i + '"] li[config-value-id="' + priceObj.config[i] + '"]').addClass('list_minipic_selected');

                var sm_pic = $('ul[id="list_' + i + '"] .list_minipic_selected').find('a').attr('original-image');
                
                if (typeof sm_pic !== 'undefined' && sm_pic !== false) {
                    var $mainImageList = $('ul.lproduct.w310li'),
                        $imageTpl = $('#tpl-main-image'),
                        $currentItemLi = $mainImageList.find('.pic:not(.hidden)');
                    var $newMainImageLi = $imageTpl.clone(true),
                        $img = $newMainImageLi.find('img');

                    $img.attr('src', $img.attr('default'));
                    var newImage = new Image();
                    newImage.onload = function(){
                        $img.attr('src', sm_pic);
                        $img.closest('a').attr('href', sm_pic);
                    };
                    newImage.src = sm_pic;

                    $currentItemLi.remove();
                    $newMainImageLi.appendTo($mainImageList).removeClass('hidden');
                }
            }
        }
    }
};

var getPriceTemplate = function (priceObject, options, promoObject) {
    var prices = [];

    for (var i in priceObject) {
        var itemPrice = priceObject[i],
            $itemPriceEl = $('<span />'),
            $itemPromoEl = $('<span />');

        if (typeof promoObject !== 'undefined') {
            var itemPromo = promoObject[i];
        }

        $itemPriceEl
            .text(sdf_FTS(parseFloat(itemPrice.Val), iteminfo.CFG_ROUND_DECIMALS, ' ') + ' ' + itemPrice.Sign);

        if (typeof itemPromo !== 'undefined' && itemPrice.Val !== itemPromo.Val) {
            $itemPriceEl.css({'color': '#676767', 'text-decoration':'line-through', 'margin-right':'6px'});
            $itemPromoEl
                .text(sdf_FTS(parseFloat(itemPromo.Val), iteminfo.CFG_ROUND_DECIMALS, ' ') + ' ' + itemPromo.Sign);
        }

        var $itemWrapper = $('<div />')
            .html($itemPriceEl)
            .append($itemPromoEl)
            .attr('data-sign', itemPrice.Sign);

        if (typeof options.rowClass !== 'undefined') {
            $itemWrapper.addClass(options.rowClass);
        }
        if (typeof options.bold === 'undefined' || options.bold === true) {
            $itemWrapper.css('font-weight', 'bold');
        }

        var html = $('<div />').html($itemWrapper).html();
        prices.push(html);
    }

    return prices.join('');
};

var calculateOneItemPrice = function (priceObject, promoObject) {
    if (typeof priceObject === 'undefined') {
        priceObject = iteminfo.price.PriceWithoutDiscountAndDelivery;
    }
    if (typeof promoObject === 'undefined') {
        promoObject = iteminfo.promotions.Price.PriceWithoutDiscountAndDelivery;
    }

    var $priceEl = $('#c_cost_one_item'),
        options = {};

    var result = getPriceTemplate(priceObject, options, promoObject);

    $priceEl.html(result);
};

var calculatePrice = function (priceObject, promoObject) {
    if (typeof priceObject === 'undefined') {
        if (typeof iteminfo.promotions.Price.PriceWithoutDiscountAndDelivery !== 'undefined') {
            priceObject = iteminfo.promotions.Price.PriceWithoutDiscountAndDelivery;
        } else {
            priceObject = iteminfo.price.OneItemPriceWithoutDelivery;
        }
    }
    if (typeof promoObject === 'undefined') {
        promoObject = iteminfo.promotions.Price.PriceWithoutDelivery;
    }

    var $priceEl = $('#c_cost'),
        options = {};

    var result = getPriceTemplate(priceObject, options, promoObject);

    $priceEl.html(result);
};

var calculateTotalPrice = function (priceObject) {
    if (typeof priceObject === 'undefined') {
        priceObject = iteminfo.price.ConvertedPriceList;
    }
    // создать новый priceObject чтобы не изменять исхожный объект
    priceObject = $.extend(true, {}, priceObject);

    var $priceEl = $('#total'),
        options = {bold: false};

    var deliveryMode = $('#delivery-mode').length ? $('#delivery-mode option:selected').val() : false;
    var deliveryPrice = false;
    if (deliveryMode) {
        deliveryPrice = getDeliveryModePrice(deliveryMode);
    }

    for (var i in priceObject) {
        if(typeof window.onItemPricesReceived === 'function'){
            window.onItemPricesReceived(priceObject[i]);
        }
        if (deliveryPrice) {
            priceObject[i].Val = parseFloat(priceObject[i].Val) + parseFloat(deliveryPrice[i].Val) + '';
        }
    }

    var result = getPriceTemplate(priceObject, options);

    $priceEl.html(result);
};

var calculateDeliveryPrice = function (priceObject, promoObject) {
    if (typeof priceObject === 'undefined') {
        priceObject = iteminfo.price.DeliveryPrice;
    }
    if (typeof promoObject === 'undefined') {
        promoObject = iteminfo.promotions.Price.DeliveryPrice;
    }
    var $priceEl = $('#local_delivery_prices'),
        options = {bold: false};

    var result = getPriceTemplate(priceObject, options, promoObject);

    $priceEl.html(result);
};

var outputQuantityRanges = function (quantityRanges, quantityRangesPromo) {
    var $quantityRanges = $('.quantity-ranges');
    $quantityRanges.find('[data-range]').html('<img src="/i/ajax-loader-transparent.gif" />');

    for (var i in quantityRanges) {
        var range = quantityRanges[i],
            priceObject,
            promoObject;

        if (typeof iteminfo.price.PriceWithoutDiscountAndDelivery !== 'undefined') {
            priceObject = range.Price.PriceWithoutDiscountAndDelivery;
        } else {
            priceObject = range.Price.PriceWithoutDelivery;
        }
        if (typeof quantityRangesPromo !== 'undefined') {
            promoObject = quantityRangesPromo[i].Price.PriceWithoutDelivery;
        } else {
            promoObject = range.Price.PriceWithoutDelivery;
        }

        var itemPrice,
            itemPromo;
        for (var j in priceObject) {
            itemPrice = priceObject[j];

            if (itemPrice.Sign === defaultCurrencySign) {
                if (typeof promoObject !== 'undefined') {
                    itemPromo = promoObject[j];
                }
                break;
            }
        }

        var $itemPriceEl = $('<div />'),
            $itemPromoEl = $('<div />');

        $itemPriceEl.text(sdf_FTS(parseFloat(itemPrice.Val), iteminfo.CFG_ROUND_DECIMALS, ' ') + ' ' + itemPrice.Sign);

        if (typeof itemPromo !== 'undefined' && itemPrice.Val !== itemPromo.Val) {
            $itemPriceEl.css({'color': '#676767', 'text-decoration':'line-through'});
            $itemPromoEl
                .text(sdf_FTS(parseFloat(itemPromo.Val), iteminfo.CFG_ROUND_DECIMALS, ' ') + ' ' + itemPromo.Sign);
        }

        $quantityRanges.find('[data-range="' + range.MinQuantity + '"]')
            .html($itemPriceEl)
            .append($itemPromoEl);
    }
};

/**
 * Пересчитать цены в карточке
 *
 * @param prices - BatchGetItemTotalCost.TotalCost
 * @param promoPrices - iteminfo.promotions
 */
var calculatePrices = function (prices, promoPrices) {

    var configurationObject,    // цена от конфигурации
        quantityRanges,         // ценовые диапазоны
        quantityRangesPromo,    // скидка на ценовые диапазоны
        priceObject,            // основная цена
        promoObject;            // цена со скидкой

    if (typeof iteminfo.item_with_config[Item.confId] !== 'undefined') {
        configurationObject = iteminfo.item_with_config[Item.confId];
        quantityRanges = iteminfo.item_with_config[Item.confId].QuantityRanges;
    } else {
        configurationObject = iteminfo.price;
        quantityRanges = iteminfo.quantityranges;
    }

    // Ценовые диапазоны
    if ($('.quantity-ranges').length) {
        if (typeof promoPrices.ConfiguredItems !== 'undefined') {
            for (var i in promoPrices.ConfiguredItems) {
                var promoByConfig = promoPrices.ConfiguredItems[i];
                if (promoByConfig.Id === Item.confId) {
                    quantityRangesPromo = promoByConfig.QuantityRanges;
                    break;
                }
            }
        } else if (typeof promoPrices.QuantityRanges !== 'undefined'){
            quantityRangesPromo = promoPrices.QuantityRanges;
        }
        outputQuantityRanges(quantityRanges, quantityRangesPromo);
    }

    // Цена
    if (typeof iteminfo.price.PriceWithoutDiscountAndDelivery !== 'undefined') {
        // Цена за 1 шт.
        priceObject = configurationObject.PriceWithoutDiscountAndDelivery;
        promoObject = prices.OneItemPriceWithoutDelivery;
        calculateOneItemPrice(priceObject, promoObject);

        // Ваша цена В группе
        priceObject = prices.OneItemPriceWithoutDelivery;
        promoObject = {};
        calculatePrice(priceObject, promoObject);
    } else {
        // Цена за 1 шт.
        priceObject = configurationObject.PriceWithoutDelivery;
        promoObject = prices.OneItemPriceWithoutDelivery;
        calculatePrice(priceObject, promoObject);
    }

    // Цена местной доставки
    priceObject = prices.DeliveryPrice;
    promoObject = typeof promoPrices.DeliveryPrice !== 'undefined' ? promoPrices.DeliveryPrice : {};
    calculateDeliveryPrice(priceObject, promoObject);

    // Цена "Итого"
    priceObject = prices.ConvertedPriceList;
    calculateTotalPrice(priceObject);
};

Item.setDefaultConfig = function(configId){
    if(typeof configId == 'undefined' || !configId)
        return ;
    if(typeof iteminfo.item_with_config[configId] == 'undefined')
        return;
    if(typeof iteminfo.item_with_config[configId].config == 'undefined')
        return;
    for(var i in iteminfo.item_with_config[configId].config){
        if(typeof i == 'undefined')
            continue;
        $('#'+i).val(iteminfo.item_with_config[configId].config[i]);
    }
}

Item.changeConf = function(configId){
    if(iteminfo.configurations){
        Item.resetForms();
        Item.clearPrices();
        Item.getConfigurationText();
        Item.getConfigurationObject();
        Item.searchConfig();
        if(Item.found)
            Item.checkAddedByUserToCarts();

        var resetMainImage = (typeof configId !== 'undefined' && configId === '') ? false : true;
        Item.setItemProperties(iteminfo.item_with_config[Item.confId], resetMainImage);
    } else{
        Item.checkAddedByUserToCarts();
        Item.setItemProperties(iteminfo.price);
    }
    recountTotalPrice();
};

function changeconfig(confid){
    Item.setDefaultConfig(confid);
    Item.changeConf(confid);

    // если в Url страницы нет хеша с confid, добавить
    if((!confid || confid==undefined || confid == '') && iteminfo.confid){
        if(document.location.href.split('#').length>1){
            document.location.href=document.location.href.split('#')[0]+'#'+iteminfo.confid;
        } else{
            document.location.href=document.location.href+'#'+iteminfo.confid;
        }
    }
}

function showOverlay(){
    var h = $('#overlay').parent().height();
    $('#overlay').css({
        height: h+'px',
        display: 'block'
    });
}

function hideOverlay(){
    $('#overlay').hide();
}

var outputDeliveryModes = function (deliveryModes){
    var html,
        options;

    if (deliveryModes.length === 0) {
        defaultDeliveryMode = 0;
        options = '';
    } else {
        var existsDefaultDeliveryMode = false,
            lowCostDeliveryPrice = parseInt(deliveryModes[0].Price),
            lowCostDeliveryMode = deliveryModes[0].Id;

        for (var i in deliveryModes) {
            var deliveryMode = deliveryModes[i];

            if (parseInt(deliveryMode.Price) < lowCostDeliveryPrice) {
                lowCostDeliveryPrice = parseInt(deliveryMode.Price);
                lowCostDeliveryMode = deliveryMode.Id
            }
            if (deliveryMode.Id === defaultDeliveryMode) {
                existsDefaultDeliveryMode = true;
            }
        }
        // если доставка по умолчанию отсутствует в списке доступных, взять самую дешевую
        if (!existsDefaultDeliveryMode) {
            defaultDeliveryMode = lowCostDeliveryMode;
        }

        options = '';
        for (var i in deliveryModes) {
            var deliveryMode = deliveryModes[i];
            options += '<option id="' + deliveryMode.Id + '" value="' + deliveryMode.Id +
                '" data-price="' + deliveryMode.Price + '">' + deliveryMode.Name + '</option>';
        }
    }

    html = '<tr class="delivery-modes">' +
        '<td>' + langs.delivery + ':</td>' +
        '<td>' +
        '<select id="delivery-mode" class="w200">' +
        '<option value="0">' + langs.select_later + '</option>' +
        options;

    html += '</select>';
    html += '<div id="delivery-mode-description" style="padding-right: 10px;"></div>';
    html += '</td></tr>';

    var deliveryModesHtml = $('<div/>').html(html).html();
    $('tr.delivery-modes').remove();
    $('tr.total').before(deliveryModesHtml);
    $('#delivery-mode').val(defaultDeliveryMode).trigger('change');
};

var outputAdditionalPrices = function (additionalPrices){
    var title,
        description;

    title = '<tr class="title-item-additionalPrices item-additionalPrices">' +
        '<th colspan="2">' +
            '<b>' + langs.additional_value + '</b>' +
            '<a href="javascript:void(0)" rel="tooltip" data-placement="right" title="" data-original-title="' + langs.supplement_for_heading + '">' +
                '<span class="glyphicon glyphicon-info-sign"></span>' +
            '</a>' +
        '</th>' +
    '</tr>';

    description = '';
    for (var i in additionalPrices) {
        var additionalPrice = additionalPrices[i];
        description += '<tr class="item-additionalPrices">' +
            '<td>' + additionalPrice.ShortDisplayName + '</td>' +
            '<td class="sign">' +
                '<b class="ltr-for-rtl">';
        for (var j in additionalPrice.ConvertedPriceList.DisplayedMoneys) {
            var money = additionalPrice.ConvertedPriceList.DisplayedMoneys[j].Money;
            description += '<b class="c_promo">' +
                            sdf_FTS(parseFloat(money[0]), iteminfo.CFG_ROUND_DECIMALS, ' ') + ' ' + money.Sign +
                        '</b><br class="br-promo">';
        }
        description += '</b>' +
            '</td>' +
        '</tr>';
    }

    var additionalPricesHtml = $('<div/>').html(title).append(description).html();
    $('tr.item-additionalPrices').remove();
    $('tr.total').after(additionalPricesHtml);
};

function recountTotalPrice() {
    iteminfopost = {
        id: iteminfo.id,
        promoid: iteminfo.promoid,
        confid: iteminfo.confid,
        count: iteminfo.count
    };

    var preloader = '<img src="/i/ajax-loader-transparent.gif" />';
    $('#c_cost').html(preloader);
    $('#c_cost_one_item').html(preloader);
    $('#local_delivery_prices').html(preloader);
    $('tr.item-additionalPrices').find('td.sign').html(preloader);
    $('#total').html(preloader);

    $.post('/?p=getprice', iteminfopost, function(data) {
        if (data && typeof data.DeliveryModes !== 'undefined') {
            deliveryModes = data.DeliveryModes;
            outputDeliveryModes(data.DeliveryModes);
        }

        if (data && data.AdditionalPrices && data.AdditionalPrices.length) {
            additionalPrices = data.AdditionalPrices;
            outputAdditionalPrices(additionalPrices);
        }

        totalCost = data.TotalCost;
        calculatePrices(totalCost, iteminfo.promotions);
    }, 'json');
}

function getDeliveryModePrice(deliveryMode) {
    if (deliveryMode) {
        for (var i in deliveryModes) {
            var mode = deliveryModes[i];
            if (mode.id == deliveryMode && mode.FullPrice && mode.FullPrice.ConvertedPriceList && mode.FullPrice.ConvertedPriceList.DisplayedMoneys) {
                return mode.FullPrice.ConvertedPriceList.DisplayedMoneys;
            }
        }
    }
    return false;
}

function tr_desc()
{
    $('#tr_but').hide();
    document.getElementById('photos-inner').innerHTML = $('#translation_loading').html()+'...';
    $.ajax({
        url: '/?p=itemdescription&itemid='+iteminfo.id,
        success: function(data) {
            $('#photos-inner').html(data);
        }
    });
}

function add_to_basket(){
    var delivery_mode = $('#delivery-mode option:selected').val();
    var conf_id = iteminfo.confid;
    var quantity = $('#quantity').val();

    if(isNaN(iteminfo.maxcount) || iteminfo.maxcount == 0){
        $().toastmessage('showToast', {'text': $('#item_not_exist').html(), 'stayTime': 6000, 'type': 'warning'});
        return false;
    }

    showOverlay();
    var params = $('#addToBasket').serializeArray();
    params[params.length] = {name: 'quantity', value: quantity};
    params[params.length] = {name: 'configurationId', value: conf_id};
    params[params.length] = {name: 'promoId', value: iteminfo.promoid};
    params[params.length] = {name: 'ItemURL', value: location.href};
    params[params.length] = {name: 'deliveryMode', value: delivery_mode};

    $.post('index_ajax.php?p=add_to_basket', params, function(data){
        var i = data.Count;
        $('.items_cart').text(i);
        if ($(".items_cart").length) {
            $('.items_cart').text(i);
        }
        
        iteminfo.addedToCart.push({configurationid:conf_id, id:data.itemId});

        $('.add_to_basket').hide();
        $('.remove_from_basket').show();
        hideOverlay();
        $().toastmessage('showToast', {'text': $('#good_added_to_cart').html(), 'stayTime': 6000, 'type': 'success'});

    }, 'json')
        .error(function(xhr, ajaxOptions, thrownError){
            hideOverlay();
            $().toastmessage('showToast', {'text': $('#good_not_added').html() + "<br /><b>" + xhr.responseText + "</b>", 'stayTime': 6000, 'type': 'error'});
        });

    return true;
}

function add_to_note(){
    showOverlay();

    var conf_id = iteminfo.confid;
    var conf_item = '';
    if(Item.ctext != undefined)
        conf_item = Item.ctext.split(' <br>').join(';').substr(0, Item.ctext.split(' <br>').join(';').length-1);
    var quantity = $('#quantity').val();

    var params = $('#addToFavourites').serializeArray();
    params[params.length] = {name: 'quantity', value: quantity};
    params[params.length] = {name: 'configurationId', value: conf_id};
    params[params.length] = {name: 'promoId', value: iteminfo.promoid};
    if ($('#delivery-mode').length) {
        params[params.length] = {name: 'externalDeliveryId', value: $('#delivery-mode').val()};
    }

    $.post('index_ajax.php?p=add_to_favourites', params, function(data){
        
        var i = data.Count;
        $('.mydata.favorites i').text(i);
        if ($("#note-items-count").length) {
            $('#note-items-count').text(i);
        }
                
        iteminfo.addedToNote.push({configurationid:conf_id, id:data.itemId});
        iteminfo.addedToNoteId = data.itemId;

        $('.add_to_note').hide();
        $('.remove_from_note').show();
        
        hideOverlay();
        $().toastmessage('showToast', {'text': langs.good_added_to_fav, 'stayTime': 6000, 'type': 'success'});
    }, 'json')
        .error(function(xhr, ajaxOptions, thrownError){
            hideOverlay();
            $().toastmessage('showToast', {'text': $('#good_not_added').html() + "<br /><b>" + xhr.responseText + "</b>", 'stayTime': 6000, 'type': 'error'});
        });
}

function remove_from_basket(){
    
    showOverlay();    
    Item.checkAddedByUserToCarts();
    var params = {
        addedToCartId: iteminfo.addedToCartId
    };
    
    $.post('/?p=basketremove', params, function(data){
        if(data.Success == 'Ok'){            
            var i = data.Count;
            $('.items_cart').text(i);
            if ($(".items_cart").length) {
                $('.items_cart').text(i);
            }
            
            $('.add_to_basket').show();
            $('.remove_from_basket').hide();    
            
            Item.deleteFormCart(iteminfo.addedToCartId);
            
            hideOverlay();
            $().toastmessage('showToast', {'text': $('#good_deleted_from_basket').html(), 'stayTime': 6000, 'type': 'success'});

        } else {
            hideOverlay();
            $().toastmessage('showToast', {'text': $('#product_not_removed').html(), 'stayTime': 6000, 'type': 'error'});
        }
    }, 'json');

    return true;
}

function remove_from_note(){
    showOverlay();
    $.get('/',
        {
            p: 'supportlistremove',
            id: iteminfo.addedToNoteId
        },
        function(data){
            if(data.Success == 'Ok'){               
                var i = data.Count;
                $('.mydata.favorites i').text(i);
                if ($("#note-items-count").length) {
                    $('#note-items-count').text(i);
                }
                
                $('.add_to_note').show();
                $('.remove_from_note').hide();                
                Item.deleteFormNote(iteminfo.addedToNoteId);
                hideOverlay();
                $().toastmessage('showToast', {'text': langs.good_removed_from_fav, 'stayTime': 6000, 'type': 'success'});
            } else {
                hideOverlay();
                $().toastmessage('showToast', {'text': langs.product_not_removed, 'stayTime': 6000, 'type': 'error'});
            }
        }, 'json');
}

var checkQuantity = function(quantity) {
    var $quantityElement = $('#quantity'),
        maxQuantity = parseInt($quantityElement.attr('max'));

    if (isNaN(quantity)) {
        quantity = iteminfo.minCount;
    } else if (quantity > maxQuantity) {
        quantity = (maxQuantity > 0) ? maxQuantity : iteminfo.minCount;
        $().toastmessage('showToast', {'text': langs.choosen_max_item_count, 'stayTime': 6000, 'type': 'success'});
    } else if (quantity < 1) {
        quantity = iteminfo.minCount;
    }

    $quantityElement.val(quantity);
    iteminfo.count = quantity;

    return quantity;
};

var quickOrder = function(params) {
    showOverlay();

    $.get($('#quickOrder').data('action'), params,
        function(data) {
            if (data.error) {
                showError(data);
                hideOverlay();
                return false;
            }
            var layout = data.layout;

            modalDialog(langs.reg_order, layout,
                function (body) {
                    if (!validateOrder()) {
                        return false;
                    }
                    var form = body.find('form'),
                        formdata = form.serializeArray();

                    formdata[formdata.length] = {'name':'Item[id]', 'value':params.itemId};
                    formdata[formdata.length] = {'name':'Item[configurationId]', 'value':params.configurationId};
                    formdata[formdata.length] = {'name':'Item[quantity]', 'value':params.quantity};
                    formdata[formdata.length] = {'name':'Item[promotionId]', 'value':params.promotionId};
                    formdata[formdata.length] = {'name':'Item[weight]', 'value':params.itemWeight};

                    formdata[formdata.length] = {'name': 'items[0][id]', 'value': params.itemId};
                    formdata[formdata.length] = {'name': 'items[0][configurationId]', 'value': params.configurationId};
                    formdata[formdata.length] = {'name': 'items[0][quantity]', 'value': params.quantity};

                    $.post(form.attr('action'), formdata, function(data) {
                        if (data.error) {
                            showError(data);
                            return false;
                        }
                        location.href = "/?p=orderdetails&orderid=" + data.orderId;
                    }, 'json');
                    return false;
                },
                { confirm: langs.make_order, cancel: langs.continue_shopping },
                function (body) {
                    hideOverlay();
                }, undefined, 3
            );
        }, 'json'
    );
};

function init(iteminfoAjax, langsAjax){
    if(iteminfoAjax != undefined)
        iteminfo = iteminfoAjax;
    if(langsAjax != undefined)
        langs = langsAjax;

    $('#product-tabs li a').click(function(){
        $('#product-tabs li').removeClass('active');
        $(this).parent().addClass('active');
        $('.tabs-content div.tab').hide();
        $('div#'+$(this).parent().attr('tab')).show();

        return false;
    });
    
    $('[attr="tab2"]').click(function(){
        if (preLoadDescription == false) {
            $('#photos-inner').html('<div class="spinner"></div>');
            $.ajax({
                url: '/?p=itemdescriptiontranslated&itemid='+iteminfo.id,
                success: function(data) {
                    $('#photos-inner').html(data);
                    preLoadDescription = true;
                }
            });
        }
        return false;
    });
    
    $('#basket-action.add-action').on('click', function(){
        if ($('.add_to_basket').hasClass('disabled')) {
            $().toastmessage('showToast', {'text': langs.sell_not_allowed_without_config, 'stayTime': 6000, 'type': 'warning'});
        } else {
            add_to_basket();
        }
        return false;
    });

    $('#quickOrder').on('click', function(event){
        if ($('.add_to_basket').hasClass('disabled')) {
            $().toastmessage('showToast', {'text': langs.sell_not_allowed_without_config, 'stayTime': 6000, 'type': 'warning'});
        } else {
            var redirectUrl = $(this).data('redirect-url');
            if (typeof redirectUrl !== 'undefined') {
                // редирект для не авторизованного пользователя
                window.location.href = redirectUrl;
                return false;
            }

            var totalPrice;
            $.each(totalCost.ConvertedPriceList, function (i, price) {
                if (price.Sign === defaultCurrencySign) {
                    totalPrice = price.Val;
                    return false;
                }
            });

            if (typeof additionalPrices !== 'undefined') {
                var additionalPrice;
                $.each(additionalPrices[0].ConvertedPriceList.DisplayedMoneys, function (i, addPrice) {
                    if (addPrice.Money.Sign === defaultCurrencySign) {
                        additionalPrice = addPrice.Money[0];
                        return false;
                    }
                });

                totalPrice = parseFloat(totalPrice ) + parseFloat(additionalPrice);
            }

            var params = {
                'itemId':iteminfo.id,
                'totalPrice':totalPrice,
                'configurationId':Item.confId,
                'quantity':$('#quantity').val(),
                'itemWeight':iteminfo.weight,
                'promotionId':iteminfo.promoid,
                'type':iteminfo.providerType,
                'deliveryId':$('#delivery-mode option:selected').val()
            };
            quickOrder(params);
        }
    });

    $('#basket-action.remove-action').on('click',function(){
        remove_from_basket();
        return false;
    });
    $('#note-action.btn-add').on('click', function(){
        if ($('.add_to_note').hasClass('disabled')) {
            $().toastmessage('showToast', {'text': langs.sell_not_allowed_without_config, 'stayTime': 6000, 'type': 'warning'});
        } else {
            add_to_note();
        }
        return false;
    });
    $('#note-action.btn-remove').on('click', function(){
        remove_from_note();
        return false;
    });

    $('.main-image').closest('a').colorbox();

    $('#quantity')
        .keydown(function(e){
            if(e.keyCode === 13) {
                var $quantity = $('#quantity'),
                    q = parseInt($quantity.val());

                checkQuantity(q);
                recountTotalPrice();
            }
        })
        .focusout(function() {
            var $quantity = $('#quantity'),
                q = parseInt($quantity.val());

            checkQuantity(q);
            recountTotalPrice();
        });

    $('.plus').click(function(){
        var $quantity = $('#quantity'),
            q = parseInt($quantity.val());

        q++;

        checkQuantity(q);
        recountTotalPrice();
    });
    $('.minus').click(function(){
        var $quantity = $('#quantity'),
            q = parseInt($quantity.val());

        q--;

        checkQuantity(q);
        recountTotalPrice();
    });

    $('.iteminfo .col340')
        .on('change', '#delivery-mode', function () {
            var $deliveryModeDescription = $('#delivery-mode-description'),
                currentMode = $(this).val(),
                rowsDescription = [];

            defaultDeliveryMode = currentMode;

            for(i in deliveryModes) {
                var mode = deliveryModes[i];
                if (mode.id === currentMode ) {
                    if (mode.description) {
                        rowsDescription.push('<br/>' + mode.description + '<br/><br/>');
                    } else {
                        rowsDescription.push('<br/>');
                    }

                    if (mode.FullPrice && mode.FullPrice.ConvertedPriceList && mode.FullPrice.ConvertedPriceList.DisplayedMoneys) {
                        for(var i in mode.FullPrice.ConvertedPriceList.DisplayedMoneys) {
                            var money = mode.FullPrice.ConvertedPriceList.DisplayedMoneys[i];
                            rowsDescription.push('<div>' + sdf_FTS(parseFloat(money.Val), iteminfo.CFG_ROUND_DECIMALS, ' ') + ' ' + money.Sign + '</div>');
                        }
                    }
                }
            }
            $deliveryModeDescription.html(rowsDescription.join(''));

            if (typeof totalCost !== 'undefined') {
                // Пересчитать цену "Итого"
                calculateTotalPrice(totalCost.ConvertedPriceList);
            }
        });

    $('.switch').click(function() {
        var switchButton = $(this),
            $mainImageList = $('ul.lproduct.w310li'),
            $currentItemLi = $mainImageList.find('.pic:not(.hidden)'),
            $selectedItemLi = switchButton.parent('li'),
            $imageTpl = $('#tpl-main-image'),
            $videoTpl = $('#tpl-main-video');

        var isImage = $selectedItemLi.hasClass('image-item'),
            isVideo = $selectedItemLi.hasClass('video-item');

        if (isImage) {
            var $newMainImageLi = $imageTpl.clone(true),
                $img = $newMainImageLi.find('img');

            $img.attr('src', $img.attr('default'));
            var newImage = new Image();
            newImage.onload = function(){
                $img.attr('src', switchButton.attr('mini-pic'));
                $img.closest('a').attr('href', switchButton.attr('href'));
                $img.closest('a').colorbox();
            };
            newImage.src = switchButton.attr('href');
        } else if (isVideo) {
            var $newMainImageLi = $videoTpl.clone(true),
                $video = $newMainImageLi.find('video'),
                $selectedVideo = switchButton.find('video');

            $video.attr({'poster':$selectedVideo.attr('poster'), 'src':$selectedVideo.attr('src')});
        }
        $currentItemLi.remove();
        $newMainImageLi.appendTo($mainImageList).removeClass('hidden');

        return false;
    });

    changeconfig(window.location.hash.replace("#",""));
}

$(function(){
    init();
});