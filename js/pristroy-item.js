var I_count = 0;
var actionRecountTotalPriceRequest = false;
var cartid;
var suppid;
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
        Item.ctext_chy += $('#cname'+i).attr('config_chy')+' '+$('#val'+$('#'+i).val()).attr('config_chy')+' <br>';
/* *** */
    }
    $('#c_cur').html(Item.ctext);
}

Item.getConfigurationObject = function(){
    Item.confObject = {};
    for(var i in iteminfo.configurations){
        Item.confObject[i] = $('#'+i).val();
    }
}

Item.searchConfig = function(){
    Item.confId = 0;
    Item.found = true;

    $('.add_to_basket').hide();
    $('.add_to_note').hide();
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
            cartid = iteminfo.addedToCart[i].id;
            iteminfo.addedToCartId = iteminfo.addedToCart[i].id;

        }
    }

    for(var i in iteminfo.addedToNote){
        if (iteminfo.confid==iteminfo.addedToNote[i].configurationid) {
            $('.add_to_note').hide();
            $('.remove_from_note').show();
            suppid = iteminfo.addedToNote[i].id;
            iteminfo.addedToNoteId = iteminfo.addedToNote[i].id;

        }
    }
}

Item.setItemProperties = function(obj){
    if(obj != undefined)
        var conf = obj;
    else
        var conf = iteminfo.item_with_config[Item.confId];

    if(typeof conf === 'undefined'){
        return 0;
    }

    /*if(conf != undefined){
        if(iteminfo.configurations && conf != undefined){
            $('#c_count').html(conf.quantity+' '+langs.pcs);
        }
        if(conf.quantity != undefined)
            iteminfo.maxcount = conf.quantity;
    }*/

    delta = {};
    for(var i in conf.PriceWithoutDelivery){
        var cpl = conf.PriceWithoutDelivery[i];

        delta[cpl.Sign] = 0;

        if(i!=0)
            $('#c_cost').append('<br class="br-promo" />');
        $('#c_cost').append(
            $('<b></b>')
                .addClass('c_cost')
//                .text((parseFloat(cpl.Val)+parseFloat(delta[cpl.Sign])).toFixed(iteminfo.CFG_ROUND_DECIMALS)+cpl.Sign)
                .text(sdf_FTS(parseFloat(cpl.Val), iteminfo.CFG_ROUND_DECIMALS, ' ') + ' ' + cpl.Sign)
                .attr('sign', cpl.Sign)
        );
    }

    if(iteminfo.promo != null){
        //promo_non_config добавили чтобы избежать конфликта в iteminfo.promo.Id
    if (iteminfo.promo_non_config.Id==undefined) {
    //по конфигурациям
    //alert('Конфигурации есть');
        for(var j in iteminfo.promo){
            if(iteminfo.promo[j].Id != Item.confId)
                continue;

            if(typeof iteminfo.promo[j].Price.PriceWithoutDelivery === 'undefined'){
                continue;
            }
            var p = iteminfo.promo[j].Price.PriceWithoutDelivery.ConvertedPriceList;

            $('.c_cost').css({
                'text-decoration': 'line-through',
                'color': '#676767'
            });
            for(i in p){
                $(' <b class="c_promo"> '+sdf_FTS(parseFloat(p[i].Val), iteminfo.CFG_ROUND_DECIMALS, ' ') +' '+p[i].Sign+'</b>').insertAfter($('b[sign="'+p[i].Sign+'"]'));
            }
            break;
        }
    } else {
    //Без конфигурациям
        //alert('Конфигурации нет');
        var p = iteminfo.promo_non_config.Price.PriceWithoutDelivery;
        //alert(JSON.stringify(p));
        $('.c_cost').css({
                'text-decoration': 'line-through',
                'color': '#676767'
        });

        for(i in p){
            $(' <b class="c_promo"> '+sdf_FTS(parseFloat(p[i].Val), iteminfo.CFG_ROUND_DECIMALS, ' ') +' '+p[i].Sign+'</b>').insertAfter($('b[sign="'+p[i].Sign+'"]'));
        }

    }
    }
}

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

Item.changeConf = function(){
    iteminfo.isvalidpromotions = true;
    if(iteminfo.configurations){
        Item.resetForms();
        Item.clearPrices();
        //Item.getConfigurationText();
        //Item.getConfigurationObject();
        //Item.searchConfig();
        if(Item.found)
            Item.checkAddedByUserToCarts();
        Item.setItemProperties();
    }
    else{
        Item.checkAddedByUserToCarts();
        Item.setItemProperties(iteminfo.price);
    }

    $('#c_cur_row').show();
    recountTotalPrice();
}

function changeconfig(confid){
    Item.setDefaultConfig(confid);
    Item.changeConf();
    if((!confid || confid==undefined || confid == '') && iteminfo.confid){
        if(document.location.href.split('#').length>1){
            document.location.href=document.location.href.split('#')[0]+'#'+iteminfo.confid;
        }
        else{
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
function wrapperRecountTotalPrice(i_count_local)
{
    return;

    setTimeout(function() {
        if (i_count_local == I) {
            $('#total').html('<img src="i/ajax-loader-transparent.gif" />');
            recountTotalPrice();
        }
    }, 500);
}
function recountTotalPrice() {
    /*
    actionRecountTotalPriceRequest = true;
    iteminfopost = {
        id: iteminfo.id,
        promoid: iteminfo.promoid,
        confid: iteminfo.confid,
        count: iteminfo.count
    };
    $.post('/?p=getprice',iteminfopost,function(data){
        $('#total').html('');
        for(var i in data['prices']){
            $('#total').append(
                $('<div></div>')
                    .addClass('mb5')
//                    .html(data['prices'][i]+i)
                    .html(sdf_FTS(data['prices'][i], iteminfo.CFG_ROUND_DECIMALS, ' ') + ' '+i)
            );
        }
        I = 0;
        actionRecountTotalPriceRequest = false;
    }, 'json');
    */
}

function tr_desc()
{
    $('#tr_but').hide();
    document.getElementById('photos-inner').innerHTML = $('#translation_loading').html()+'...';
    $.ajax({
        url: '/?p=itemdescriptiontranslated&itemid='+iteminfo.id,
        success: function(data) {
            $('#photos-inner').html(data);
        }
    });
}
function itemtraderateinfo(){
    if ($('#TAOBAOcomments').html()) return;
    $('#TAOBAOcomments').html('<div class="spinner"></div>');
    $.ajax({
        url: '/?p=itemtraderateinfo&itemid='+iteminfo.id,
        success: function(data) {
            $('#TAOBAOcomments').html(data);
        }
    });
}

function add_to_basket(){

    var conf_id = iteminfo.confid;
    var conf_item = '';
    if(Item.ctext != undefined)
        conf_item = Item.ctext.split(' <br>').join(';').substr(0, Item.ctext.split(' <br>').join(';').length-1);
    else
        conf_item = '';

    var conf_item_china = '';
    if(Item.ctext_chy != undefined)
        conf_item_china = Item.ctext_chy.split(' <br>').join(';').substr(0, Item.ctext_chy.split(' <br>').join(';').length-1);
    else
        conf_item_china = '';

    var quantity = $('#quantity').val();

    if(isNaN(iteminfo.maxcount) || iteminfo.maxcount == 0){
        $("#basketinfo").html($('#item_not_exist').html());
        $("#dialog-form").dialog("open");
        return false;
    }

    showOverlay();
    var params = $('#addToBasket').serializeArray();
    params[params.length] = {name: 'quantity', value: quantity};
    params[params.length] = {name: 'configurationId', value: conf_id};
    params[params.length] = {
        name: 'itemConfiguration',
        value: conf_item
            .split(' ').filter(function(data){return data;}).join(' ')
            .split(' \n ').join('')
    };
    params[params.length] = {name: 'itemConfigurationChina', value: conf_item_china};
    params[params.length] = {name: 'promoId', value: iteminfo.promoid};
    params[params.length] = {name: 'ItemURL', value: location.href};



    $.get('index_ajax.php', params, function(data){

        $("#basketinfo").html($('#good_added_to_cart').html());
        iteminfo.addedToCartId = data.itemId;

        var i = data.Count;
        $('.mydata.basket i').text(i);
        if ($("#basket-items-count").length) {
            $('#basket-items-count').text(i);
        }

        iteminfo.addedToCart[iteminfo.addedToCart.length] = conf_id;
        $('.add_to_basket').hide();
        $('.remove_from_basket').show();

        $("#dialog-form").dialog("open");
        hideOverlay();
    }, 'json')
        .error(function(xhr, ajaxOptions, thrownError){
            hideOverlay();
            $("#basketinfo").html($('#good_not_added').html() + "<br /><b>" + xhr.responseText + "</b>");
            $("#dialog-form").dialog("open");
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
    params[params.length] = {name: 'itemConfiguration', value: conf_item};
    params[params.length] = {name: 'promoId', value: iteminfo.promoid};

    $.get('index_ajax.php', params, function(data){

        var i = data.Count;
        $('.mydata.favorites i').text(i);
        if ($("#note-items-count").length) {
            $('#note-items-count').text(i);
        }
        $("#basketinfo").html(langs.good_added_to_fav);
        iteminfo.addedToNoteId = data.itemId;


        $('.add_to_note').hide();
        $('.remove_from_note').show();
        //$('#note-action').removeClass('btn-add').addClass('btn-remove');
        //$('#note-action span').text(langs.delete_from_fav);


        hideOverlay();
        $("#dialog-form").dialog("open");
    }, 'json')
        .error(function(xhr, ajaxOptions, thrownError){
            hideOverlay();
            $("#basketinfo").html($('#good_not_added').html() + "<br /><b>" + xhr.responseText + "</b>");
            $("#dialog-form").dialog("open");
        });
}

function remove_from_basket(){


    showOverlay();


    var params = {
        p: 'basketremove',
        addedToCartId: iteminfo.addedToCartId
    };

    $.get("//"+window.location.hostname, params, function(data){
        if(data.Success == 'Ok'){
            $("#basketinfo").html($('#good_deleted_from_basket').html());
            var i = data.Count;
            $('.mydata.basket i').text(i);
            if ($("#basket-items-count").length) {
                $('#basket-items-count').text(i);
            }
            //$('#basket-action').find('i').removeClass('removeitem').addClass('additem');

            $('.add_to_basket').show();
            $('.remove_from_basket').hide();

            //$('#basket-action').removeClass('remove-action').addClass('add-action');
            //i = $('#basket-action span i');

            //$('#basket-action span').empty().text($('#add_to_cart').html()).prepend(i);
        } else {
            $("#basketinfo").html($('#product_not_removed').html());
        }
        hideOverlay();
        $("#dialog-form").dialog("open");
    }, 'json');

    return true;
}

function remove_from_note(){
    showOverlay();


    $.get('/?p=supportlistremove&',
        {
            p: 'supportlistremove',
            id: iteminfo.addedToNoteId
        },
        function(data){
            if(data.Success == 'Ok'){
                $("#basketinfo").html(langs.good_removed_from_fav);
                var i = data.Count;
                $('.mydata.favorites i').text(i);
                if ($("#note-items-count").length) {
                    $('#note-items-count').text(i);
                }

                $('.add_to_note').show();
                $('.remove_from_note').hide();

                //$('#note-action').removeClass('btn-remove').addClass('btn-add');
                //$('#note-action span').text(langs.add_to_fav);

            } else {
                $("#basketinfo").html(langs.product_not_removed);
            }
            hideOverlay();
            $("#dialog-form").dialog("open");

        }, 'json');
}

function init(iteminfoAjax, langsAjax){
    if(iteminfoAjax != undefined)
        iteminfo = iteminfoAjax;
    if(langsAjax != undefined)
        langs = langsAjax;

    changeconfig(window.location.hash.replace("#",""));

    $('#product-tabs li a').click(function(){
        $('#product-tabs li').removeClass('active');
        $(this).parent().addClass('active');
        $('.tabs-content div.tab').hide();
        $('div#'+$(this).parent().attr('tab')).show();

        return false;
    });
    $('#product-tabs li').click(function(){
        $('#product-tabs li').removeClass('active');
        $(this).addClass('active');
        $('.tabs-content div.tab').hide();
        $('div#'+$(this).attr('tab')).show();
    });

    $( "#dialog-form:ui-dialog" ).dialog( "destroy" );

    $("#dialog-form").dialog({
        autoOpen: false,
        modal: true,
        buttons : {
            "Ок" : function() {
                $("#dialog-form").dialog("close");
            }
        }
    });

    $.ajax({
        url: '/?p=itemdescription&itemid='+iteminfo.id,
        success: function(data) {
            $('#photos-inner').html(data);
        }
    });

    $('#basket-action.add-action').on('click', function(){
        add_to_basket();
        return false;
    });
    $('#basket-action.remove-action').on('click',function(){
        remove_from_basket();
        return false;
    });
    $('#note-action.btn-add').on('click', function(){
        add_to_note();
        return false;
    });
    $('#note-action.btn-remove').on('click', function(){
        remove_from_note();
        return false;
    });

    $('#main-image').closest('a').colorbox();

    $('.plus').click(function(){
        if (actionRecountTotalPriceRequest) return false;
        var q = $('#quantity').val();
        $('#quantity').val(parseInt(q)+1);
        iteminfo.count = parseInt(q)+1;

        I++;
        wrapperRecountTotalPrice(I);
    });

    $('.minus').click(function(){
        if (actionRecountTotalPriceRequest) return false;
        var q = $('#quantity').val();
        if(q>1){
            $('#quantity').val(parseInt(q)-1);
            iteminfo.count = parseInt(q)-1;
        }

        I++;
        wrapperRecountTotalPrice(I);
    });

    $('#quantity').keydown(function() {
        if (actionRecountTotalPriceRequest) return false;
    });

    $('#quantity').keyup(function() {
        if (actionRecountTotalPriceRequest) return false;
        var q = parseInt($('#quantity').val());
        if (q > 0) {
            iteminfo.count = q;
        }

        I++;
        wrapperRecountTotalPrice(I);
    });

    $('.switch').click(function(){

        $('#main-image').attr('src', $('#main-image').attr('default'));

        var img = new Image();
        var switchClass = $(this);
        img.onload = function(){
            $('#main-image').attr('src', switchClass.attr('href'));
            $('#main-image').closest('a').attr('href', switchClass.data('big-image'));
            $('#main-image').closest('a').colorbox();
        }
        img.src = $(this).attr('href');

        return false;
    });



    //$('#total').html('');

    startconf = false;
    //Выводим картинку если такова есть
    //Ищем конфигурацию
    for(var i in iteminfo.item_with_config){
        if (i==iteminfo.confid) {
            startconf = iteminfo.item_with_config[i].config;
        }
    //  alert(JSON.stringify(iteminfo.item_with_config[i]));

    }

    //выводим ее параметры в зависисмоти от конфига
    if(startconf)
    for(var i in startconf){
        for(var j in iteminfo.configurations){
            if (i==j) {
                for(var z in iteminfo.configurations[j].values){
                    if (startconf[i]==iteminfo.configurations[j].values[z].id) {
                        if ((iteminfo.configurations[j].values[z].imageurl!='') && (iteminfo.configurations[j].values[z].imageurl!=undefined)) {
                            //Наконец выставляем каринку
                            //alert(iteminfo.configurations[j].values[z].imageurl);
                            $('#main-image').attr('src', iteminfo.configurations[j].values[z].imageurl+'_310x310.jpg');
                        }
                    }
                }
            }
        }
    }




}

function item_debug(message){
    if(iteminfo.debug){
        $('#item-info').append($('<div></div>').text(message));
    }
}

init();
$(function(){
    $('body').append($('<a></a>').attr({'id': 'changeconfig'}));
    $('#changeconfig').click(function(){
        changeconfig();
        return false;
    });


});