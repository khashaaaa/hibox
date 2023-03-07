var OrdersPage = Backbone.View.extend({
    "el": ".orders-common-wrapper",
    "events": {
        "click .checkAll"       : "toggleCheckAll",
        "click #restoreOrders"  : "bulkRestoreOrdersAction",
        "click li.bulkCancelOrders a"   : "bulkCancelOrdersAction",

        "click #showAllFilters" : "showAllFilters",

        "click .exportOrder"    : "exportOrderAction",
        "click .restoreOrder"   : "restoreOrderAction",
        "click .cancelOrder"    : "cancelOrderAction",

        "click .deleteItemFromOrder"    : "deleteItemFromOrderAction",
        "click .splitItemQuantity"      : "splitItemQuantityAction",
        "click .changeItemStatus A"     : "changeItemStatusAction",

        "click .itemRow span[data-target*=more-info]" : "toggleItemInfo",

        "click .orderRow .ot_show_goods_list" : "showOrderItemsList",
        "click a[href=#ot_goods_filter_tab]" : "showOrdersItemsTab",
        "click a[href=#ot_orders_filter_tab]" : "showOrdersListTab",

        "click a.showCustomerInfo" : "showCustomerInfoAction",

        "change input[type=checkbox]" : "updateSelectedRows",
        "click a.mergeOrders": "mergeOrders",
        
        "click .bulkChangeItemStatus A" : "bulkChangeItemStatusAction",
        "click #select-all-statuses" : "selectAllOrderStatuses",
        "click #deselect-all-statuses" : "deselectAllOrderStatuses"
    },
    getAllOrderStatuses: function() 
    {
        return $('.orders-filter input[type="checkbox"]');
    },
    getAllOrderSelectedStatuses: function() 
    {
        return $('.orders-filter input[type="checkbox"]:checked');
    }, 
    isSetedOrderStatus: function(statusCode) 
    {
        var statuses = this.getAllOrderSelectedStatuses();
        var result = false;
        statuses.each(function() {
            if (this.value == statusCode) {
                result = true;
                return;
            }
        });
        return result;
    },
    selectAllOrderStatuses: function() 
    {
    	$('.orders-filter input[type="checkbox"]').each(function(){this.checked = true;});
    },
    deselectAllOrderStatuses: function() 
    {
    	$('.orders-filter input[type="checkbox"]').each(function(){this.checked = false;});
    },
    "count": 0,
    "success": 0,
    "error": 0,
    "done": 0,
    "isWork": false,
    goodsFilterActivate: function(){
    	$('.goods-filter input').removeAttr('disabled');
    	$('.goods-filter label').removeClass('disabled');
    },
    ordersFilterDeactivate: function(){
    	$('.goods-filter input').attr('disabled','disabled');
    	$('.goods-filter label').addClass('disabled');
    },
    bulkChangeItemStatusAction: function(ev){
        ev.preventDefault();
        var target = this.$(ev.target);
        var button = target.parents('.btn-group:first').find('button');
        var items = {};
        var count = 0;
        
        $.each($('.goods-wrapper table tr.itemRow.selected_item:visible'), function(){
        	if (undefined == items[$(this).data('order-id')]) {
        		items[$(this).data('order-id')] = [];        		
        	}
        	items[$(this).data('order-id')].push($(this).attr('id'));
        	count++;
        });
        if (! count) {
            showError(trans.get('No_items_selected'));
            return;
        }
        button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
        button.parent().removeClass('open');
        $.post(
            '?cmd=orders&do=changeItemsStatus',
            {
                'orders'     : items,
                'status'    : target.data('status')
            },
            function (data) {
                button.removeClass('disabled').find('i').attr('class', 'icon-star-empty');
                if (! data.error) {
                    showMessage(data.message ? data.message : trans.get('Notify_success'));
                    window.location.reload();
                } else {
                    showError(data);
                }
            }, 'json'
        );
        return false;
    	
    },
    render: function()
    {
        return this;
    },
    renderOrder: function(order){
        var orderTr = $('#ot_orders_filter_tab tr#' + order.id);
        orderTr.find('td.order_status').html(order.statusname);
        var lineStatuses = [];

        $.each(order.items, function(){
            var item = $(this);
            lineStatuses.push(this.statusname + ': ' + this.qty);
        });
        orderTr.find('td span.ot_show_goods_list').html(lineStatuses.join(', '));

        var price = parseFloat(order.totalamount) + order.currencysign + '<br>' + 
                    '(' + parseFloat(order.totalamount - order.remainamount) + order.currencysign + ')';

        orderTr.find('td.order_price').html(price);
    },
    initialize: function(){
        this.render();
        if ($('#ot_orders_filter_tab.active').length > 0 ) {
			this.ordersFilterDeactivate();
		} else if ($('#ot_goods_filter_tab.active').length > 0 ) {
			this.goodsFilterActivate();
		}
    },
    showCustomerInfoAction: function(ev){
        ev.preventDefault();
        var itemRow = $(ev.target).closest('tr');
        $(ev.target).attr('class', 'ot-preloader-micro').empty();
        var orderId = itemRow.data('order-id');
        var order = Orders.get(orderId);
        var fillData = function (order) {
            var customerInfoHtml = '<a target="_blank" href="index.php?cmd=users&do=profile&id=' +
                order.get('custid') + '" title="'+trans.get('User_profile')+'">' + escapeData(order.get('custname')) +
                '</a><br/>' + order.get('useraccountavailableamount') + '&nbsp;' + order.get('currencysign');
            $('.itemRow[data-order-id="' + order.id + '"]').each(function (i, row) {
                $(row).find('.ot_operator_name').html(order.get('operatorname'));
                $(row).find('.ot_customer_info').html(customerInfoHtml);
            });
        }
        if (order && order.id === orderId) {
            fillData(order);
        } else {
            $.post('?cmd=orders&do=getOrderInfo', {'orderId': orderId}, function (data) {
                if (data.error) {
                    showError(data);
                } else if (data.order) {
                    Orders.add(data.order);
                    fillData(Orders.get(orderId));
                }
            }, 'json');
        }
        return false;
    },
    showOrdersListTab: function(ev){
    	this.ordersFilterDeactivate();

    	var targetDiv = $($(ev.currentTarget).attr('href'));
        if (targetDiv.hasClass('orders-list-loaded')) {
            return;
        }
        
        var params = $.deparam.querystring();
        params['do'] = 'searchOrders';
        params['cmd'] = 'orders';
        $.post('?', params, function (data) {
            if (data.error) {
                showError(data);
            } else {
                if (data.html) {
                    targetDiv.find('tbody').empty().html(data.html);
                    targetDiv.addClass('orders-list-loaded');
                    targetDiv.find('.pagination-orders').html(data.pagination);
                    _.each(data.orders, function (order) {
                        Orders.add(order);
                    });
                    if ('function' === typeof window['initOrdersExportEvents']) {
                        initOrdersExportEvents();
                    }
                    if ('function' === typeof window['initOrdersItemsExportEvents']) {
                        initOrdersItemsExportEvents();
                    }
                }
            }
        }, 'json');
    },
    showOrdersItemsTab: function(ev){
    	this.goodsFilterActivate();
    	
        var targetDiv = $($(ev.currentTarget).attr('href'));
        if (targetDiv.hasClass('items-list-loaded')) {
            return;
        }
        
        var params = $.deparam.querystring();
        params['do'] = 'searchOrdersLines';
        params['cmd'] = 'orders';
        $.post('?', params, function (data) {
            if (data.error) {
                showError(data);
            } else {
                if (data.html) {
                    targetDiv.find('tbody').empty().html(data.html);
                    targetDiv.addClass('items-list-loaded');
                    targetDiv.find('.pagination-items').html(data.pagination);
                    _.each(data.items, function (item) {
                        OrdersItems.add(item);
                    });
                    if ('function' === typeof window['initOrdersExportEvents']) {
                        initOrdersExportEvents();
                    }
                    if ('function' === typeof window['initOrdersItemsExportEvents']) {
                        initOrdersItemsExportEvents();
                    }
                }
            }
        }, 'json');
    },
    showOrderItemsList: function(ev){
        var targetDiv = $($(ev.currentTarget).data('target'));
        if (targetDiv.find('> div.well').hasClass('items-list-loaded')) {
            return;
        }
        
        var orderRow = $(ev.target).closest('tr');
        var orderId = orderRow.attr('id');
        $.post('?cmd=orders&do=getOrderItems', {id: orderId}, function (data) {
            if (data.error) {
                showError(data);
            } else {
                _.each(data.items, function (item) {
                    OrdersItems.add(item);
                });
                var order = Orders.get(orderId);
                order.set('numericId', orderRow.data('numeric-id'));
                var itemsListHtml = renderTemplate('orders/item/list', {
                    'items': data.items,
                    'order': order
                });
                targetDiv.find('.ot_items_list_content').replaceWith(itemsListHtml);
                targetDiv.find('> div.well').removeClass('well-transp').addClass('items-list-loaded');
            }
        }, 'json');
    },
    toggleItemInfo: function(ev){
        var targetDiv = $($(ev.currentTarget).data('target'));
        if (targetDiv.find('> div.well').hasClass('item-details-loaded')) {
            return;
        }
        var itemRow = $(ev.target).closest('tr');
        var item = OrdersItems.get(itemRow.attr('id'));
        item.set('imagePreview', itemRow.data('image-preview'));
        var itemPrice = parseFloat(item.get('newpricecust')) ? item.get('newpricecust') : item.get('pricecust');
        item.set('NewAmountCust', parseFloat(itemPrice * item.get('qty')));
        var config = item.get('configtext').split(';');
        var originalConfig = item.get('configexternaltextorig').split(';');
        item.set('config', _.filter(config.concat(originalConfig), function (configItem) {
            return configItem.length;
        }));

        $.get('templates/orders/item.html?' + Math.random(), function (tpl) {
            var itemDetailsHtml = _.template(tpl, {'item': item});
            targetDiv.html(itemDetailsHtml);

            // Вывести цену товара
            $.get('templates/inline_elements/text.html?' + Math.random(), function (tpl) {
                var itemPriceHtml = _.template(tpl, {
                    'name': 'newPrice',
                    'value': itemPrice,
                    'saveUrl': '?cmd=orders&do=changeItemPrice',
                    'parameters': {
                        'useLabel': false,
                        'useWrapper': false,
                        'pk': item.get('id') + '_' + item.get('orderid'),
                    }
                });
                targetDiv.find('.editablePrice').html(itemPriceHtml).find('a').editable({
                    inputclass: 'input-custom-mini',
                    clear: false,
                    success: function(data){
                        if (data && 'undefined' !== typeof data.amountcust) {
                            targetDiv.find('.amountcust span').html(
                                parseFloat(data.amountcust) + '&nbsp;' + item.get('internalpricecurrencycode')
                            );
                            var newStatusCode = 3; // Подтверждение цены
                            var newStatusName = itemRow.find('.changeItemStatus a[data-status=' + newStatusCode + ']').text();
                            itemRow.find('.statusName').text($.trim(newStatusName));
                            item.set('statusname', $.trim(newStatusName));
                            item.set('statusid', newStatusCode);
                            item.set('statuscode', newStatusCode);
                        }
                    }
                });
            });
        });
    },
    bulkRestoreOrdersAction: function(ev){
        ev.preventDefault();
        showError('bulkRestoreOrdersAction: Not implemented yet');
        return false;
    },
    generateProgressBar: function()
    {
    	var self = this;
    	var active = (self.done == self.count) ? '' : 'active' ;
		var success = self.success / self.count * 100;
		var error = self.error / self.count * 100;
		var progressBar = '<div class="progress progress-striped pull-left' + active + '" style="width: 100%;">';
		if (self.success > 0 ) {
			progressBar += '<div class="bar bar-success" style="width: ' + success + '%;">' + self.success + '</div>';
		} else {
			progressBar += '<div class="bar bar-success" style="width: ' + success + '%;"></div>';
		}
		if (self.error > 0 ) {
			progressBar += '<div class="bar bar-danger" style="width: ' + error + '%;">' + self.error + '</div>';
		} else {
			progressBar += '<div class="bar bar-danger" style="width: ' + error + '%;"></div>';
		}
		progressBar += '</div>';
		return progressBar;
    },
    updateBulkOperationStatus: function() 
    {
    	var self = this;
    	var statusContainer = $('.orders-wrapper .row-fluid #bulk-operation-status-container');
    	if (statusContainer.length == 0 ) {
    		var html = '<div id="bulk-operation-status-container" class="span5"></div>';
    		$('.orders-wrapper .row-fluid').append(html);
    		statusContainer = $('.orders-wrapper .row-fluid #bulk-operation-status-container');
    		$('.orders-wrapper .row-fluid .btn-group button').addClass('disabled');
    	}
    	$(statusContainer).show();
    	$(statusContainer).css('opacity','1');

    	var progressBar = self.generateProgressBar();
    	$(statusContainer).html(progressBar);
    	if (self.done == self.count) {
    		$(statusContainer).fadeOut(10000);
    		$('.orders-wrapper .row-fluid .btn-group button').removeClass('disabled');
    		//showMessage(trans.get('Notify_success'));
    		self.isWork = false;
    	} 
    },
    bulkCancelOrdersAction: function(ev){
    	var self = this;
        ev.preventDefault();
        var orders = [];
        
        if (self.isWork) {
        	return false;
        }
        
        self.isWork = true;
        
        $(ev.target).closest('.btn-group').removeClass('open');

        var selectedOrders = $('.orders-wrapper .orderRow.selected_item');
        if (! selectedOrders.length) {
            showError(trans.get('No_orders_selected'));
            return;
        }

        var count = 0;
        $.each(selectedOrders, function(){
            var orderRow = $(this);
            orders.push(orderRow.attr('id'));
            count++;
        });
        
        
        orders = orders.reverse();
    	self.done = 0;
    	self.count = count;
    	self.error = 0;
    	self.success = 0;
    	self.updateBulkOperationStatus();
    	self.cancelOrder(orders);
        
        return false;
    },
    cancelOrder: function(orders) 
    {
    	var self = this;
    	var orderId = orders.pop();
    	
    	if (undefined != orderId) {
    		var canCancel = $('tr[id="' + orderId +'"]').attr('cancancel');
    		if (canCancel == 1) {
    			$.post('?cmd=orders&do=cancelOrder', {'id': orderId},
    					function (data) {
    				if (!data.error) {
    					var tr =  $('tr[id="' + orderId +'"]');
    					$('input[type="checkbox"]', tr).removeAttr('checked');
    					$(tr).removeClass('selected_item').next('tr').removeClass('selected_item');
    					$(tr).addClass('success').next('tr').addClass('success');
    					$('td:eq(3)', tr).html(trans.get('Order_canceled'));
    					self.success++;
    					self.done++;
    				} else {
    					showError(data);
    					var tr =  $('tr[id="' + orderId +'"]');
    					$(tr).addClass('error').next('tr').addClass('error');
    					self.error++;
    					self.done++;
    				}
    				self.updateBulkOperationStatus();
    				self.cancelOrder(orders);
    			}, 'json'
    			);
    		} else {  
    			var tr =  $('tr[id="' + orderId +'"]');
    			$(tr).addClass('error').next('tr').addClass('error');
    			showError(trans.get('Order_cannot_be_canceled', {orderId: orderId}));
    			self.error++;
    			self.done++;
				self.updateBulkOperationStatus();
				self.cancelOrder(orders);
    		}
    	}
    },
    restoreOrderAction: function(ev){
        ev.preventDefault();
        var self = this;
        modalDialog(trans.get('Confirm_needed'), trans.get('Really_restore_this_order'), function(dialog){
            var target = this.$(ev.target);
            var button = target.parents('ul:first').prev();
            button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
            button.parent().removeClass('open');
            $.post(
                target.data('action'),
                {'id': target.parents('tr').attr('id')},
                function (data) {
                    button.removeClass('disabled').find('i').attr('class', 'icon-cog');
                    if (! data.error) {
                        showMessage(data.message ? data.message : trans.get('Notify_success'));
                        if (! self.isSetedOrderStatus(data.order.statuscode)) {
                            target.parents('tr').next().andSelf().remove();
                        }
                        $('li#restoreOrderMenuItem', target.parents('tr')).hide();
                        $('li#exportOrderMenuItem', target.parents('tr')).show();
                        $('li#mergeOrdersMenuItem', target.parents('tr')).show();
            			$('li#cancelOrderMenuItem', target.parents('tr')).show();
                        
                        self.renderOrder(data.order);
                    } else {
                        showError(data);
                    }
                }, 'json'
            );
        });
        return false;
    },
    cancelOrderAction: function(ev){
        ev.preventDefault();
        var self = this;
        modalDialog(trans.get('Confirm_needed'), trans.get('Really_cancel_this_order'), function(dialog){
            var target = this.$(ev.target);
            var button = target.parents('ul:first').prev();
            button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
            button.parent().removeClass('open');
            $.post(
                target.data('action'),
                {'id': target.parents('tr').attr('id')},
                function (data) {
                    button.removeClass('disabled').find('i').attr('class', 'icon-cog');
                    if (! data.error) {
                        showMessage(data.message ? data.message : trans.get('Notify_success'));
                        if (! self.isSetedOrderStatus(data.order.statuscode)) {
                            target.parents('tr').next().andSelf().remove();
                        }
                        $('li#restoreOrderMenuItem', target.parents('tr')).show();
                        $('li#exportOrderMenuItem', target.parents('tr')).hide();
                        $('li#mergeOrdersMenuItem', target.parents('tr')).hide();
            			$('li#cancelOrderMenuItem', target.parents('tr')).hide();
                        
                        self.renderOrder(data.order);
                    } else {
                        showError(data);
                    }
                }, 'json'
            );
        });
        return false;
    },
    deleteItemFromOrderAction: function(ev){
        ev.preventDefault();
        modalDialog(trans.get('Confirm_needed'), trans.get('Really_delete_this_item_from_order'), function(dialog){
            var target = this.$(ev.target);
            var button = target.parents('ul:first').prev();
            button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
            button.parent().removeClass('open');
            $.post(
                target.data('action'),
                {'itemid': target.parents('tr').attr('id'), 'orderid': target.parents('tr').data('order-id')},
                function (data) {
                    button.removeClass('disabled').find('i').attr('class', 'icon-cog');
                    if (! data.error) {
                        showMessage(data.message ? data.message : trans.get('Notify_success'));
                        target.parents('tr').next().andSelf().remove();
                    } else {
                        showError(data);
                    }
                }, 'json'
            );
        }, {
           'confirm' : trans.get('Delete'),
        });
        return false;
    },
    changeItemStatusAction: function(ev){
        ev.preventDefault();
        var target = this.$(ev.target);
        var button = target.parents('.btn-group:first').find('button');
        button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
        button.parent().removeClass('open');
        var itemRow = $(ev.target).closest('tr');
        var item = OrdersItems.get(itemRow.attr('id'));
        $.post(
            '?cmd=orders&do=changeItemStatus',
            {
                'itemId'    : item.get('id'),
                'orderId'   : item.get('orderid'),
                'status'    : target.data('status'),
                'comment'   : item.get('operatorcomment'),
                'quantity'  : item.get('qty')
            },
            function (data) {
                button.removeClass('disabled').find('i').attr('class', 'icon-star-empty');
                if (! data.error) {
                    showMessage(data.message ? data.message : trans.get('Notify_success'));
                    // 13 - cancelled
                    if (target.data('status') == 13) {
                        itemRow.find('.exportOrderItem a').hide();
                        itemRow.find('input.for_group_action').attr('disabled', 'disabled');
                    } else {
                        itemRow.find('.exportOrderItem a').show();
                        itemRow.find('input.for_group_action').removeAttr('disabled');
                    }
                    itemRow.find('.statusName').text($.trim(target.text()));
                    item.set('statusname', $.trim(target.text()));
                    item.set('statusid', target.data('status'));
                    item.set('statuscode', target.data('status'));
                } else {
                    showError(data);
                }
            }, 'json'
        );
        return false;
    },
    splitItemQuantityAction: function(ev){
        ev.preventDefault();
        var itemRow = $(ev.target).closest('tr');
        var item = OrdersItems.get(itemRow.attr('id'));
        if (item.get('qty') <= 1) {
            return false;
        }

        var content = '<input type="text" name="splitQuantity" value="1" />';
        content += '<script>$("input[name=splitQuantity]").numeric();</script>';
        
        modalDialog(
            trans.get('Split_item_suggestion'),
            content,
            function (dialog) {
                var splitQuantity = parseInt(dialog.find('input[name=splitQuantity]').val());
                if (splitQuantity < 1 || splitQuantity >= item.get('qty')) {
                    showError(trans.get('Incorrect_quantity'));
                    return false;
                }
                var target = this.$(ev.target);
                var button = target.parents('ul:first').prev();
                button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
                button.parent().removeClass('open');
                $.post(
                    target.data('action'),
                    {
                        'itemId': item.get('id'),
                        'orderId': item.get('orderid'),
                        'splitQuantity': splitQuantity
                    },
                    function (data) {
                        button.removeClass('disabled').find('i').attr('class', 'icon-cog');
                        if (! data.error) {
                            showMessage(data.message ? data.message : trans.get('Notify_success'));
                            window.location.reload();
                        } else {
                            showError(data);
                        }
                    }, 'json'
                );
            }, {
               'confirm' : trans.get('Split'),
            }
        );
        return false;
    },
    showAllFilters: function(ev){
        ev.preventDefault();
        this.$('#filtersShort').fadeOut(100);
        this.$('#filtersAll').fadeIn(100);
        return false;
    },
    toggleCheckAll: function(ev){
        var self = this.$(ev.target);
        self.parents('thead').next().find('input[type=checkbox]:not(:disabled)')
            .prop('checked', self.is(':checked'))
            .trigger('change');
    },
    updateSelectedRows: function(ev){
        var checkbox = $(ev.target);
        if (checkbox.hasClass('checkAll')) {
            return;
        }
        if (checkbox.is(':checked')) {
            checkbox.closest('tr').addClass('selected_item').next('tr').addClass('selected_item');
        } else {
            checkbox.closest('tr').removeClass('selected_item').next('tr').removeClass('selected_item');
        }
    },
    mergeOrders: function(e)
    {
        var tr = $(e.currentTarget).closest('tr');
        var orderId = $(tr).attr('id');
        var customerId = $(tr).attr('customerId');

        var dialog;
        var content = '<div class="editableform-loading"></div>';
        var onConfirmCallback = function (body) {
            var selected = $("input[type='radio']:checked", body);
            if (selected.length > 0) {
                var order2Id = $(selected).attr('id');
                //merge orders
                $.post('?cmd=orders&do=mergeOrders',
                    {
                        'orderId': orderId,
                        'order2Id': order2Id
                    },
                    function (data) {
                        if (! data.error) {
                            if (data.result === 'ok') {
                                //redirect
                                window.location.href = 'index.php?cmd=orders&do=list';
                            } else {
                                showError(data.result);
                            }
                        } else {
                            showError(data.message);
                            dialog.find('.close').trigger('click');
                        }
                    },
                    'json'
                );
                return true;
            } else {
                showError(trans.get('Select_order_to_merge_please_or_click_Cancel'));
                return false;
            }
        };

        var onShowCallback = function (body) {
            $.post(
                '?cmd=orders&do=getOrdersListForMerge',
                {
                    'orderId': orderId,
                    'customerId': customerId
                },
                function (data) {
                    if (! data.error) {
                        contents = data.orders;
                        body.html(contents);
                    } else {
                        showError(data);
                        dialog.find('.close').trigger('click');
                    }
                },
                'json'
            );
        };

        dialog = modalDialog(
            trans.get('Select_order_for_merge'),
            content,
            onConfirmCallback,
            {'confirm': trans.get('Merge'), 'cancel': trans.get('Cancel')},
            onShowCallback
        );
    }

});

$(function(){
    var O = new OrdersPage();
});
