function OrderQueue(){
    this.queue = 0;
    this.orders = [];
    this.pop = function(callback){
        this.queue--;
        if(!this.queue){
            var that = this;
            $('.batch-export-single-order.success-export').each(function(){
                that.orders.push($(this).attr('data-order-id'));
            });
            $('.batch-export-single-order').removeClass('batch-export-single-order');
            this.exportOrders(callback);
        }
    }
    this.push = function(){
        this.queue++;
    }
    this.exportOrders = function(callback){
    	var self = this;
        $.post('/lib/orders_excel_export/batch_export.php', {'orders': this.orders})
            .success(function(fileUrl){
                if ('function' === typeof callback) {
                    callback(fileUrl);
                }
                self.orders = [];
                window.location = fileUrl;
            })
            .error(function(xhr, ajaxOptions, thrownError){
                handleAjaxError(xhr, ajaxOptions, thrownError);
                self.orders = [];
            });
    }
}
var orderQueue = new OrderQueue();

(function( $ ) {
    $.fn.exportOrder = function(silent, callback) {
        var that = this;
        silent = !!silent;
        if (! silent) {
            $(this).append(
                $('<img />')
                    .attr({
                        'src': "css/i/ajax-loader.gif"
                    })
                    .addClass('batch-export-single-order')
            );
        }

        $.get('/lib/orders_excel_export/save_order_info.php', {'orderId': $(that).attr('data-order-id')})
            .success(function(){
                $(that).addClass('success-export');
                if (! silent) {
                    $(that).html($('<strong></strong>')
                        .css('color', 'green')
                        .text(trans.get('order_ready_for_export')));
                }
                orderQueue.pop(callback);
            })
            .error(function(xhr, ajaxOptions, thrownError){
                orderQueue.pop(callback);
                if(thrownError == 'SessionExpired'){
                    window.location = '/?cmd=login';
                }
                else{
                    if ('function' === typeof showError) {
                        showError(thrownError + '<br />' + xhr.responseText);
                    } else {
                        $(that).html($('<strong></strong>')
                            .css('color', 'red')
                            .html(thrownError + '<br />' + xhr.responseText));
                    }
                }
            });
    };
})(jQuery);

// Новая админка
function initOrdersExportEvents () {
    $('.orders-wrapper .exportOrderBlock').show().find('a').on('click', function(e){
        e.preventDefault();
        $(this).closest('div.btn-group').removeClass('open');
        window.location.replace('/lib/orders_excel_export/export_order.php?id=' + $(this).closest('tr').attr('id'));
        return false;
    });
    $('.order-view-wrapper .exportOrder').show().find('a').on('click', function(e){
        e.preventDefault();
        $(this).closest('div.btn-group').removeClass('open');
        window.location.replace('/lib/orders_excel_export/export_order.php?id=' + $(this).data('order-id'));
        return false;
    });
    $('.orders-wrapper #bulkExportOrders').show().removeClass('disabled').off().on('click', function(e){
        e.preventDefault();
        var bulkExportOrdersBtn = $(this);
        if (bulkExportOrdersBtn.hasClass('disabled')) {
            return;
        }
        var selectedOrders = $('.orders-wrapper .orderRow.selected_item');
        if (! selectedOrders.length) {
            showError(trans.get('No_orders_selected'));
            return;
        }
        bulkExportOrdersBtn.addClass('disabled');
        $('.orders-wrapper #bulkActionsLoader').show();
        $.each(selectedOrders, function(){
            var orderRow = $(this);
            orderRow.addClass('batch-export-single-order').attr('data-order-id', orderRow.attr('id'));
            orderQueue.push();
            orderRow.exportOrder(true, function(){
                bulkExportOrdersBtn.removeClass('disabled');
                $('.orders-wrapper #bulkActionsLoader').hide();
            });
        });
        return false;
    });
    $('.orders-wrapper #bulkActionsLoader').hide();
}
$(function(){
    initOrdersExportEvents();
});
// Новая админка

$(function(){
    $('#orders tr:gt(0)').each(function(){
        var orderId = $(this).find('td:first').find('a').text();

        $(this)
            .find('td:last')
            .append(
                $('<button></button>')
                    .text(trans.export)
                    .button()
                    .off()
                    .click(function(e){
                        e.preventDefault();
                        window.location = '/lib/orders_excel_export/export_order.php?id='+orderId;
                        return false;
                    })
            );
    });
    $('#filter-buttons')
        .append('<br />')
        .append('<br />')
        .append(
        $('<button></button>')
            .attr('class', 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only')
            .text(trans.get('orders_export'))
            .click(function(){
                $('#orders tr:gt(0)').each(function(){
                    $(this)
                        .find('td:last')
                        .append('<br /><br />')
                        .append($('<div></div>')
                            .attr({
                                'data-order-id': $(this).attr('data-order-id')
                            })
                            .addClass('batch-export-single-order'));
                });
                $('.batch-export-single-order').each(function(){
                    orderQueue.push();
                    $(this).exportOrder();
                });

                return false;
            })
    );
});

