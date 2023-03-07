$(function(){
    $('#orders tr:gt(0)').each(function(){
        var orderId = $(this).attr('data-order-id');

        /*$(this)
            .find('td:last')
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
            );*/
    });
});

// Новая админка
function initOrdersItemsExportEvents () {
    var goodsTab = $('#ot_goods_filter_tab');
    goodsTab.find('.bulkExportOrderItems').show().find('a').removeClass('disabled').off().on('click', function(e){
        e.preventDefault();
        var button = $(e.target).parents('ul:first').prev();
        if (button.hasClass('disabled')) {
            return;
        }
        var selectedItems = {};
        var itemsStatuses = [];
        var itemsCount = 0;
        var ordersStatuses = Filter.orders_status;
        var ordersCreateTime = [];
        goodsTab.find('.itemRow.selected_item').each(function () {
            var item = OrdersItems.get($(this).attr('id'));
            itemsStatuses.push(item.get('statusid'));
            ordersCreateTime.push(item.get('ordereddatetime'));
            if ('undefined' === typeof selectedItems[$(this).data('order-id')]) {
                selectedItems[$(this).data('order-id')] = [];
            }
            selectedItems[$(this).data('order-id')].push(item.id);
            itemsCount++;
        });
        if (! _.size(selectedItems)) {
            showError(trans.get('No_items_selected'));
            return;
        }
        ordersCreateTime = _.uniq(ordersCreateTime).sort(function (a, b) {
            var re = /(\d{4})\-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})/;
            var aDateInfo = a.match(re);
            var bDateInfo = b.match(re);

            var dateA = new Date(aDateInfo[1], aDateInfo[2]-1, aDateInfo[3], aDateInfo[4], aDateInfo[5], aDateInfo[6]);
            var dateB = new Date(bDateInfo[1], bDateInfo[2]-1, bDateInfo[3], bDateInfo[4], bDateInfo[5], bDateInfo[6]);

            if (dateA > dateB) {
                return 1;
            } else {
                return -1;
            }
        });
        var urlParams = {};
        urlParams['fromdate'] = _.first(ordersCreateTime);
        urlParams['todate'] = _.last(ordersCreateTime);
        urlParams['orders_status'] = _.uniq(ordersStatuses);
        urlParams['items_status'] = _.uniq(itemsStatuses);
        urlParams['itemsIds'] = selectedItems;
        urlParams['itemsCount'] = itemsCount;
        urlParams['type'] = 'bulk';
        button.parent().removeClass('open');
        button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
        $.post(
            '/lib/orders_excel_export_new/export_order.php',
            {'params': urlParams},
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
        var itemRow = button.parents('.itemRow:first');
        var urlParams = {};
        urlParams['itemsIds'] = itemRow.attr('id');
        $.post(
            '/lib/orders_excel_export_new/export_order.php',
            {'id': itemRow.data('order-id'), 'params': urlParams},
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
}

$(function(){
    initOrdersItemsExportEvents();
});
// Новая админка


