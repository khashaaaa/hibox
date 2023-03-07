$(function(){
    var orderId = $('#order_id').val();

    $('#order_action')
        .append(
            $('<button></button>')
                .text(transAdmin.export_items_list)
                .button()
                .off()
                .click(function(e){
                    e.preventDefault();

                    $.post(
                        '/lib/orders_excel_export_new/export_order.php',
                        {'id': orderId},
                        function (data) {
                            if (! data.error) {
                                window.location = data.url;
                            } else {
                                showError(data.message ? data.message : trans.get('Notify_error'));
                            }
                        }, 'json'
                    );

                    return false;
                })
        );
});

// Новая админка
$(function(){
    var goodsTab = $('#ot_order_goods_tab');
    goodsTab.find('.bulkExportOrderItems').show().find('a').removeClass('disabled').off().on('click', function(e){
        e.preventDefault();
        var button = $(e.target).parents('ul:first').prev();
        if (button.hasClass('disabled')) {
            return;
        }
        var selectedItems = [];
        goodsTab.find('.ot_order_product_item.selected_item').each(function(){
            selectedItems.push($(this).data('id'));
        });
        if (! selectedItems.length) {
            showError(trans.get('No_items_selected'));
            return;
        }
        button.parent().removeClass('open');
        button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
        var urlParams = {};
        urlParams['itemsIds'] = selectedItems.join(',');
        $.post(
            '/lib/orders_excel_export_new/export_order.php',
            {'id': Order.id,'params': urlParams},
            function (data) {
                button.removeClass('disabled').find('i').attr('class', 'icon-cog');
                if (! data.error) {
                    window.location = data.url;
                } else {
                    showError(data.message ? data.message : trans.get('Notify_error'));
                }
            }, 'json'
        );
        return false;
    });
    goodsTab.find('.exportOrderItem').show().find('a').removeClass('disabled').off().on('click', function(e){
        e.preventDefault();
        var button = $(e.target).parents('ul:first').prev();
        if (button.hasClass('disabled')) {
            return;
        }
        button.parent().removeClass('open');
        button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
        var itemRow = button.parents('.ot_order_product_item:first');
        var urlParams = {};
        urlParams['itemsIds'] = itemRow.data('id');
        $.post(
            '/lib/orders_excel_export_new/export_order.php',
            {'id': Order.id, 'params': urlParams},
            function (data) {
                button.removeClass('disabled').find('i').attr('class', 'icon-cog');
                if (! data.error) {
                    window.location = data.url;
                } else {
                    showError(data.message ? data.message : trans.get('Notify_error'));
                }
            }, 'json'
        );
        return false;
    });

    goodsTab.find('.exportOrderItem').show().find('a.cancelled').hide();
});
// Новая админка
