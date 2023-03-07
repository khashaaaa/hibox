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

function setCosts(elemid, num){
    $('#add-to-basket-'+elemid+' input[name="quantity"]').val(num);
    showOverlay();
    $.get(
        '/?p=editnoteitemquantity&id=' + elemid + '&num=' + num,
        function(data) {
            var total = parseInt(0);
            var sign;
            $(data).each(function(k, item){
                sign = item.CurrencySign;
                $('#price-1-'+item.Id).text(sdf_FTS(parseFloat(item.Price), price_round_decimals , ' ') + " " +   item.CurrencySign);
                //$('#count-'+item.Id).val(item.Quantity);
                $('#total-price-'+item.Id).html(sdf_FTS(parseFloat(item.Cost), price_round_decimals , ' ') + " " +   item.CurrencySign);
                total+=parseFloat(item.Cost);
            });
            $('#price').html(sdf_FTS(Math.round(total*10)/10, price_round_decimals , ' ') + " " + sign);
            hideOverlay();
        }, 'json'
    );
}

$(function(){
    $('input[name=deleter]').change(function() {
        if($(this).is(':checked')) {
            $(this).parent().parent().parent().parent().parent().css('background-color', '#FFCC99');
        } else {
            $(this).parent().parent().parent().parent().parent().css('background-color', 'transparent');
        }
    });
    
    $('input[name=deleter_all]').click (function () {
        var thisCheck = $(this);
        if (thisCheck.is(':checked')) {
            $('input[name=deleter]').each(function(){
                $(this).prop("checked",true);
                $(this).parent().parent().parent().parent().parent().css('background-color', '#FFCC99');
            });
        } else {
            $('input[name=deleter]').each(function(){
                $(this).prop("checked", false);
                $(this).parent().parent().parent().parent().parent().css('background-color', 'transparent');
            });
        }

    });
    
    $('.item-quanity').change(function(){
        $(this).css('border-color', 'inherit');
        if ($(this).val() != 0 && $(this).val() != '') {
            setCosts($(this).attr('itemid'), $(this).val());
        } else {
            $(this).css('border-color', 'red');
        }
    });
    
    $('.plus').click(function(){
        var input_id = $(this).attr('rel');
        var quan = parseInt($('#'+input_id).val());
        if (isNaN(quan)) {
            quan = 1;
        } else {
            quan++;
        }
        $('#'+input_id).val(quan);
        $('#'+input_id).css('border-color', 'inherit');
        setCosts($(this).attr('itemid'), quan);
    });
    $('.minus').click(function(){
        var input_id = $(this).attr('rel');
        var quan = parseInt($('#'+input_id).val());
        if (isNaN(quan) || quan == 0) {
            quan = 1;
        } else {
            if(quan > 1)
                quan--;
        }
        $('#'+input_id).val(quan);
        $('#'+input_id).css('border-color', 'inherit');
        setCosts($(this).attr('itemid'), quan);
    });
    $('.copy').click(function(){
        var itemid = $(this).attr('itemid');
        var comment = $('textarea[itemid="'+itemid+'"]').val();

        $.post(
            '/?p=editnoteitemcomment', 
            {
                id: itemid,
                comment: comment
            },
            function(data) {
            }
        );
        $('#message').html($('#comment_saved').val()+'!');
        $("#dialog-form").dialog("open");
    });
    
    $('.add-to-basket').click(function(e){
        var itemid = $(this).attr('itemid');
        $('#add-to-basket-'+itemid).submit();
        return false;
    });
});


function add_group_to_basket () {
    var checkboxes = $('input[name=deleter]').filter(':checked');
    var count = checkboxes.filter(':checked').length;
    var perPage = $("select[name='per_page'] option:selected").val();
    var checks = "";
    if (count) {
        showOverlay();

        checkboxes.each(function() {
            var itemid = $(this).val();
            checks = checks + itemid + ",";
        });
        $.get(
            "//"+window.location.hostname,
            {
                p: 'MoveItemsFromNoteToBasket',
                items: checks
            },
            function() {
                document.location.href = "/?p=supportlist&per_page=" + perPage;
            }
        );

    } else {
        $("#dialog-empty").dialog("open");
    }
}
