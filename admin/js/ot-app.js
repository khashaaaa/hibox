
// OT application code in here.
$(document).ready( function () {

    // popups for user info on discount page
    $('.info-discount-user').each(function(){
        var content = $(this).closest('tr').find('.user-discount-info').html();
        $(this).clickover({
            'content': content,
            'html': true,
            'trigger': 'click',
            'width': '300',
        });
    });

    // popups for item info on order page goods tab
    $('.ot_product_item_description_popup').each(function(){
        var content = $(this).closest('div').find('.ot_popup_product_item_description_info').html();
        $(this).clickover({
            'content': content,
            'html': true,
            'trigger': 'click',
            'width': '300'
        });
    });

    /*$('.icon-info-discount-user').click(function(){
     $(this).popover('show');
     });*/


    //modals
    $('.ot_show_modal_dialog').click(function(e){
        e.preventDefault();
        $('.ot_modal_dialog_window').modal('show');
    });

    $('.ot_show_delivery_tariff_modal').click(function(e){
        e.preventDefault();
        $('.ot_delivery_tariff_modal').modal('show');
    });

    $('.ot_product_img_carousel').carousel({
//      interval: 200
        interval: false
    });

    $('.ot_show_settle_descr_window').click(function(e){
        e.preventDefault();
        var target = e.target;
        var mediaBody = $(target).closest('.media-body');
        var content = $('.productDescription',mediaBody).html();
        $('.ot_settle_descr_window .modal-body	').html(content);
        $('.ot_settle_descr_window').modal('show');
    });

    $('.ot_show_settle_item_dicline_window').click(function(e){
        e.preventDefault();
        $('.ot_settle_item_dicline_window').modal('show');
    });

} );

function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}
