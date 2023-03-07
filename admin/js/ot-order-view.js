var OrdersPage = Backbone.View.extend({
    "el": ".order-view-wrapper",
    "events": {
        "click .restoreOrder": "restoreOrderAction",
        "click .cancelOrder": "cancelOrderAction",
        "click .closeOrder": "closeOrderAction",
        "click #addWeight": "addWeightAction",

        "click .deleteItemFromOrder": "deleteItemFromOrderAction",
        "click .splitItemQuantity": "splitItemQuantityAction",
        "click .changeItemStatus A": "changeItemStatusAction",
        "click .bulkChangeItemStatus A": "bulkChangeItemStatusAction",
        "click #purchaseOrderItemsBtn": "purchaseOrderItemsAction",

        "click .deletePackage": "deletePackageAction",
        "click .editInternalDelivery": "editInternalDelivery",
        "click .recalculationItemPrice": "recalculationItemPrice",
        "click .editOriginalPrice": "editOriginalPrice",
        "click .createPackageForItems": "createPackageForItemsAction",
        "click .addItemsToExistingPackage": "addItemsToExistingPackageAction",
        "click .deleteItemsFromPackageBtn": "deleteItemsFromPackageAction",
        "click .moveItemsToNewPackageBtn": "moveItemsToNewPackageAction",
        "click .packagesElectionBtn": "packagesElectionAction",
        "click .printPackageInvoiceBtn": "printPackageInvoiceAction",
        "click .printPackageReceipt": "printPackageReceiptAction",
        "click .exportPackage": "exportPackageAction",
        "click .packagesConfirmingAction": "packagesConfirmingAction",
        "click .js-package-tracking": 'getPackageTracking',

        "click #enrollMoneyBtn, #withdrawMoneyBtn": "updateAccountAction",
        "mouseup #toggleEnrollForm": "toggleEnrollForm",
        "mouseup #toggleWithdrawForm": "toggleWithdrawForm",
        "mouseup #paymentReserveBtn": "paymentReserveAction",
        "click .goods-status-filter button": "filterGoodsByStatusAction",
        "click #showAllFilters": "showFullFilterGoodsByStatusAction",

        "click #ot_order_support_panel form button:first": "addTicketMessageAction",
        "click a[href='#ot_order_support_panel']": "markTicketMessagesAsReadAction",
        "click .order-additional-info-form button:first": "changeOrderAdditionalInfoAction",
        "click .item-comments-block .changeOperatorCommentBtn": "changeOperatorCommentAction",
        "change .ot_order_product_item input[type=checkbox]": "updateSelectedRows",
        "change .tab-container .checkAll": "toggleCheckAll",
        "change .tab-container .checkAllGrouped": "toggleCheckAllGrouped",
        "click .ot_group_items": "groupItems",
        "click a.mergeOrders": "mergeOrders",
        "click #showOrderLogBtn": "showOrderLog",
        "click #hideOrderLogBtn": "hideOrderLog",
        "keyup .onEnterAddWeight" : "onEnterAddWeight"
    },
    'itemConfigs': {},
    render: function () {
        this.setItemConfigs();
        this.checkAvailableMoney();
        $('#showAllFilters').hide();
        return this;
    },
    initialize: function () {
        this.render();
        $(document).ajaxComplete(function (event, xhr, settings) {
            try {
                var deliveryAccess = $('#deliveryAccess').val()
                var response = $.parseJSON(xhr.responseText);

                        if ('object' === typeof response) {
                            if (response.hasOwnProperty("deliveryAccess")) {
                                if (Boolean(response.deliveryAccess) !== Boolean(deliveryAccess)) {
                                    location.reload();
                                }
                            } else {
                                if ('undefined' !== typeof response.orderDeliveryPrice) {
                                    $('#order-delivery-price').html(response.orderDeliveryPrice);
                                }
                            }
                        }
            } catch (error) {
            }
        });

    },

    changeItemPriceCallback: function (response, value, object) {
        var oldPriceContainer = $(object.$element).parent().prev('span.old_price');
        var oldPrice = $(oldPriceContainer).html().trim() !== '' ? Number($(oldPriceContainer).html()) : Number(object.value);
        var newPrice = Number(value);

        if (!response.error) {
            if (oldPrice > newPrice) {
                location.reload();
            } else {
                /* Обновление статуса */
                var itemId = response['itemid'].split('_')[0];
                $('div.ot_order_product_item[data-id=' + itemId + ']').find('span.itemStatus').html(response.status);

                /* Обновление общей информации по статусам товаров */
                $.get('templates/orders/view/itemsSummary.html?' + Math.random(), function (tpl) {
                    var itemsByStatusHtml = _.template(tpl, {'itemsByStatus': response.itemsByStatus});
                    $('div.orderItemsSummary dl').html(itemsByStatusHtml);
                });

                var item = OrderItems.get(itemId);
                item.set('statuscode', response.statuscode);
                item.set('statusid', response.statusid);
                item.set('statusname', response.status);

                if ($(oldPriceContainer).html().trim() === '') {
                    $(oldPriceContainer).html(oldPrice);
                } else if (oldPrice === newPrice) {
                    $(oldPriceContainer).empty();
                }
            }
            return false;
        } else {
            showError(response);
        }
    },
    changeItemWeightCallback: function (response, value, object) {
        if (!response.error) {
            var oldOrderStatus = Order.get('statusid'),
                oldOrderWeight = Order.get('weight'),
                newOrderStatus = response.order.statusid,
                deliveryAccess = $('#deliveryAccess').val(),
                newOrderWeight = response.order.weight;

            if (response.hasOwnProperty("deliveryAccess")) {
                if (Boolean(response.deliveryAccess) !== Boolean(deliveryAccess)) {
                    location.reload();
                }
            }
            if (oldOrderWeight !== newOrderWeight) {
                if (oldOrderStatus !== newOrderStatus) {
                    /* Перезагрузить страницу */
                    location.reload();
                } else {
                    /* меняем общий вес заказа без перезагрузки страницы */
                    var elOrderWeight = $('dd.orderWeight')
                        .children("div:first")
                        .children("a:first");

                    elOrderWeight.text(newOrderWeight);
                    InlineFields.each(function(item){
                        if (item.get('name') === 'newWeight') {
                            item.set('value', newOrderWeight);
                        }
                    });
                }
            }
        } else {
            showError(response);
        }
    },
    deleteItemsFromPackageAction: function (ev) {
        var target = $(ev.target);
        var button = target.parents('ul:first').prev();
        var packageBlock = target.parents('.packageItemBlock:first');
        var itemsIds = [];
        packageBlock.find('.ot_parsel_goods_list li').each(function () {
            if ($(this).find('input[type=checkbox]').is(':checked')) {
                itemsIds.push($(this).data('item-id'));
            }
        });
        if (!itemsIds.length) {
            showError(trans.get('No_items_selected'));
            return;
        }
        button.parent().removeClass('open');
        modalDialog(
            trans.get('Removing_items_from_package'),
            trans.get('Removing_items_from_package_confirmation'),
            function (dialog) {
                button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
                $.post(
                    '?cmd=orders&do=moveItemsToPackage',
                    {
                        'itemsIds': itemsIds,
                        'toPackageId': packageBlock.data('id'),
                        'doDelete': 1
                    },
                    function (data) {
                        button.removeClass('disabled').find('i').attr('class', 'icon-cog');
                        if (!data.error) {
                            showMessage(data.message ? data.message : trans.get('Notify_success'));
                            window.location.reload();
                        } else {
                            showError(data);
                        }
                    },
                    'json'
                );
            },
            {confirm: trans.get('Remove_items_from_package')}
        );
    },
    moveItemsToNewPackageAction: function (ev) {
        var target = $(ev.target);
        var button = target.parents('ul:first').prev();
        var packageBlock = target.parents('.packageItemBlock:first');
        var itemsIds = [];
        packageBlock.find('.ot_parsel_goods_list li').each(function () {
            if ($(this).find('input[type=checkbox]').is(':checked')) {
                itemsIds.push($(this).data('item-id'));
            }
        });
        if (!itemsIds.length) {
            showError(trans.get('No_items_selected'));
            return;
        }
        button.parent().removeClass('open');
        modalDialog(
            trans.get('Moving_items_to_new_package'),
            trans.get('Moving_items_to_new_package_confirmation'),
            function (dialog) {
                button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
                $.post(
                    '?cmd=orders&do=moveItemsToPackage',
                    {
                        'itemsIds': itemsIds,
                        'fromPackageId': packageBlock.data('id'),
                        'orderId': Order.id
                    },
                    function (data) {
                        button.removeClass('disabled').find('i').attr('class', 'icon-cog');
                        if (!data.error) {
                            showMessage(data.message ? data.message : trans.get('Notify_success'));
                            window.location.reload();
                        } else {
                            showError(data);
                        }
                    },
                    'json'
                );
            },
            {confirm: trans.get('Create_new_package_and_move_selected_items')}
        );
    },
    packagesElectionAction: function (ev) {
        var target = $(ev.target);
        var packageBlock = target.parents('.packageItemBlock:first');
        var itemsIds = [];
        packageBlock.find('.ot_parsel_goods_list li').each(function () {
            if ($(this).find('input[type=checkbox]').is(':checked')) {
                itemsIds.push($(this).data('item-id'));
            }
        });
        if (!itemsIds.length) {
            showError(trans.get('No_items_selected'));
            return;
        }
        var modalContent = this.$('.packagesElectionWindow .modal-body').html();
        var modalEventsAttached = false;
        var button = target.parents('ul:first').prev();
        button.parent().removeClass('open');
        var modal = modalDialog(trans.get('Package_election'), modalContent, function (dialog) {
            var selectedPackage = $(dialog).find('label input:checked').val();
            var currentPackage = packageBlock.data('id');
            if (selectedPackage == currentPackage) {
                showError(trans.get('Choose_another_package_for_items'));
                return false;
            }
            button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
            $.post(
                '?cmd=orders&do=moveItemsToPackage',
                {
                    'itemsIds': itemsIds,
                    'toPackageId': selectedPackage,
                    'fromPackageId': currentPackage
                },
                function (data) {
                    button.removeClass('disabled').find('i').attr('class', 'icon-cog');
                    if (!data.error) {
                        showMessage(data.message ? data.message : trans.get('Notify_success'));
                        window.location.reload();
                    } else {
                        showError(data);
                    }
                },
                'json'
            );
        }, {
            confirm: trans.get('Move')
        });
        $(modal).find('label input').on('change', function (ev) {
            if (!modalEventsAttached) {
                modalEventsAttached = true;
                var label = $(this).parent();
                if ($(this).is(':checked')) {
                    $(this).parents('label:first').addClass('selected_item')
                        .siblings('label').removeClass('selected_item');
                }
            }
        });
    },
    printPackageReceiptAction: function (ev) {
        ev.preventDefault();
        var target = this.$(ev.target);
        var packageRow = target.parents('.packageItemBlock:first');
        var packageId = packageRow.data('id');
        $('i', target).removeClass('icon-print');
        $('i', target).addClass('ot-preloader-micro');
        $.post(target.data('action'), {'packageId': packageId}, function (data) {
            $('i', target).removeClass('ot-preloader-micro');
            $('i', target).addClass('icon-print');
            if (!data.error) {
                modalDialog('', data.message, false, {'confirm': false, 'cancel': trans.get('Close')});
            } else {
                showError(data);
            }
        }, 'json');

        return false;
    },
    exportPackageAction: function (ev) {
        ev.preventDefault();
        var target = this.$(ev.target);
        var packageRow = target.parents('.packageItemBlock:first');
        var packageId = packageRow.data('id');
        $.post(target.data('action'), {'packageId': packageId}, function (data) {
            if (!data.error) {
                showMessage(trans.get('Package_exported_successfuly'));
                window.location.reload();
            } else {
                showError(data);
            }
        }, 'json');

        return false;
    },
    printPackageInvoiceAction: function (ev) {
        ev.preventDefault();
        var packageBlock = $(ev.target).parents('.packageItemBlock:first');
        var packageModel = OrderPackages.get(packageBlock.data('id'));
        $.get('templates/orders/package/invoice.html?' + Math.random(), function (tpl) {
            var params = [
                'height=' + screen.height,
                'width=' + screen.width,
                'fullscreen=yes'
            ].join(',');
            var invoiceWindow = window.open('', trans.get('Package_invoice'), params);
            if (invoiceWindow) {
                invoiceWindow.moveTo(0, 0);
                invoiceWindow.document.body.innerHTML = '';
                invoiceWindow.document.write(_.template(tpl, {'package': packageModel}));
                invoiceWindow.print();
            } else {
                showError(trans.get('Allow_popup_windows_in_browser'), 1);
            }
        });
        return false;
    },
    packagesConfirmingAction: function (ev) {
        ev.preventDefault();
        var button = this.$(ev.target);
        button.button('loading');
        $.post(button.data('action'), {}, function (data) {
            if (!data.error) {
                showMessage(data.message ? data.message : trans.get('Notify_success'));
                var activityId = data.activityId;
                var activityType = data.activityType;
                openActivity(activityId, activityType, self.refreshOrders);
            } else {
                showError(data);
            }
            button.button('reset');
        }, 'json');

        return false;
    },
    getPackageTracking: function (e) {
        e.preventDefault();

        let self = this,
            $btn = this.$(e.currentTarget),
            packageId = $btn.data('packageId'),
            action = $btn.data('action');

        $.post(action, {'packageId': packageId}, function (data) {
            if (data.error) {
                showError(data.message);
            } else {
                if (data.externalTrackingUrl) {
                    $btn.removeClass('js-package-tracking')
                        .attr('href', data.externalTrackingUrl)
                        .attr('target', '_blank');
                    window.open(data.externalTrackingUrl, "_blank");
                } else {
                    let tracking = trans.get('No_tracking');

                    if (data.packageTrackingCheckpoints) {
                        let $table = self.$('.js-package-tracking-table').clone().show(),
                            $lines = $table.find('.js-package-tracking-table-lines'),
                            $lineSkeleton = $lines.find('.js-package-tracking-table-line');

                        $.each(data.packageTrackingCheckpoints, function (i, checkpoint) {
                            let $line = $lineSkeleton.clone().show();
                            $line.find('.js-package-tracking-table-line-time').html(checkpoint.time);
                            $line.find('.js-package-tracking-table-line-status').html(checkpoint.status);
                            $line.find('.js-package-tracking-table-line-location').html(checkpoint.location);
                            $lines.append($line);
                        });

                        tracking = $table;
                    }

                    modalDialog(trans.get('Tracking'), tracking, null, {'cancel': trans.get('Close'), 'confirm': false});
                }
            }
        });
    },
    setItemConfigs: function () {
        var self = this;
        var getItemConfig = function (itemTaoId, configName, configId, configuratorId) {
            if ('undefined' === typeof self.itemConfigs[itemTaoId]) {
                $.ajax({
                    url: '?cmd=orders&do=getItemConfig',
                    data: {'itemId': itemTaoId},
                    type: 'POST',
                    async: false,
                    success: function (data) {
                        if (data && data.item_with_config) {
                            data.currentConfig = data.item_with_config[configId];
                            self.itemConfigs[itemTaoId] = data;
                        } else if (data.error) {
                            showError(data);
                        }
                    }
                });
            }
            var valuesList = [];
            if ('undefined' !== typeof self.itemConfigs[itemTaoId]) {
                if (self.itemConfigs[itemTaoId].HasHierarchicalConfigurators == 'true') {
                    $.each(self.itemConfigs[itemTaoId].configurations[1].values, function (i, config) {
                        valuesList.push({
                            'value': config.id,
                            'text': (config.alias !== '') ? config.alias : config.name
                        });
                    });
                } else {
                    $.each(self.itemConfigs[itemTaoId].configurations, function (i, config) {
                        if (config.id == configuratorId) {
                            $.each(config.values, function (j, value) {
                                valuesList.push({
                                    'value': value.id,
                                    'text': (value.alias !== '') ? value.alias : value.name
                                });
                            });
                            return;
                        }
                    });
                }
            }
            return valuesList;
        };
        var getNewConfig = function (itemTaoId, configName, newValue) {
            var newConfig = {};
            if ('undefined' !== typeof self.itemConfigs[itemTaoId]) {
                var current = self.itemConfigs[itemTaoId].currentConfig;
                var configuration = clone(current.config);
                $.each(self.itemConfigs[itemTaoId].configurations, function (i, config) {
                    if (config.name == configName) {
                        configuration[config.id] = newValue;
                        return;
                    }
                });
                $.each(self.itemConfigs[itemTaoId].item_with_config, function (i, variant) {
                    if (_.isEqual(configuration, variant.config)) {
                        newConfig = variant;
                        newConfig.id = i;
                        return;
                    }
                });
            }
            return newConfig;
        };
        $.get('templates/orders/item/config.html?' + Math.random(), function(configTpl){

            $('#ot_order_goods_tab .ot_order_product_item').each(function () {
            var itemBlock = $(this);
            var item = OrderItems.get(itemBlock.data('id'));
            var localConfig = item.get('configtext').split(';');
            var originalConfig = item.get('configexternaltextorig').split(';');
            var configMerged = _.filter(localConfig.concat(originalConfig), function (configItem) {
                return configItem.length;
            });

            var config = [];
            $.each(configMerged, function (i, configItem) {
                config.push(configItem.split(':'));
            });
            item.set('config', config);

            var originalConfigCode = item.get('configexternalcode').split(';');
            var configCode = [];
            $.each(originalConfigCode, function(i, configItem){
                configCode.push(configItem.split(':'));
            });
            item.set('configCode', configCode);

            // Вывести конфиг товара
                var itemConfigHtml = _.template(configTpl, {'item': item});
                itemBlock.find('.itemEditableConfig').html(itemConfigHtml).find('a').editable({
                    type: 'select',
                    pk: item.id,
                    url: '?cmd=orders&do=setItemConfig',
                    params: function () {
                        return {
                            'itemId': item.id,
                            'orderId': Order.id,
                            'configId': $(this).data('config-id')
                        };
                    },
                    inputclass: 'input-small',
                    clear: false,
                    source: function () {
                        return getItemConfig(item.get('itemid'), $(this).data('name'), item.get('configid'), $(this).data('code'));
                    },
                    success: function (data) {
                        location.reload();
                        console.log(data);
                    }
                }).on('shown', function () {
                    var link = $(this);
                    var editable = link.data('editable');

                    var itemTaoId = item.get('itemid');
                    var configId = '';
                    $.each(self.itemConfigs[itemTaoId].configurations, function(i, config){
                        if (config.name == link.data('name')) {
                            configId = config.id;
                            return;
                        }
                    });

                    var cfgId = self.itemConfigs[itemTaoId].currentConfig.config[configId];
                    $('option[value="' + cfgId + '"]', editable.input.$input).attr('selected', 'selected');

                    editable.input.$input.on('change', function () {
                        if ('undefined' !== typeof self.itemConfigs[itemTaoId]) {
                            if (self.itemConfigs[itemTaoId].HasHierarchicalConfigurators == 'true') {
                                var newConfig = self.itemConfigs[itemTaoId].item_with_config[$(this).val()];
                            } else {
                                var newConfig = getNewConfig(itemTaoId, link.data('name'), $(this).val());
                            }
                            if (newConfig.quantity == 0) {
                                showError('' + trans.get('Given_product_configuration_is_not_in_stock') + '.');
                                return;
                            }
                            if (self.itemConfigs[itemTaoId].HasHierarchicalConfigurators == 'true') {
                                link.data('config-id', $(this).val());
                            } else {
                                link.data('config-id', newConfig.id);
                            }
                            // TODO: пересчитать цену новой конфигурации
                            $.each(self.itemConfigs[itemTaoId]['item_with_config'], function () {
                                // TODO: Ограничить возможные варианты конфигураций,
                                // основываясь на конкретном выбранном варианте $(this).val()
                            });
                        }
                    });
                });
            });
        });
    },
    restoreOrderAction: function (ev) {
        ev.preventDefault();
        modalDialog(trans.get('Order_restore'), trans.get('Really_restore_this_order'), function () {
            var target = this.$(ev.target);
            var button = target.parents('ul:first').prev();
            button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
            button.parent().removeClass('open');
            $.post(
                target.data('action'),
                {'id': Order.id},
                function (data) {
                    button.removeClass('disabled').find('i').attr('class', 'icon-cog');
                    if (!data.error) {
                        showMessage(data.message ? data.message : trans.get('Notify_success'));
                        window.location.reload();
                    } else {
                        showError(data);
                    }
                }, 'json'
            );
        });
        return false;
    },
    cancelOrderAction: function (ev) {
        ev.preventDefault();
        modalDialog(trans.get('Order_cancel'), trans.get('Really_cancel_this_order'), function () {
            var target = this.$(ev.target);
            var button = target.parents('ul:first').prev();
            button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
            button.parent().removeClass('open');
            $.post(
                target.data('action'),
                {'id': Order.id},
                function (data) {
                    button.removeClass('disabled').find('i').attr('class', 'icon-cog');
                    if (!data.error) {
                        showMessage(data.message ? data.message : trans.get('Notify_success'));
                        window.location.reload();
                    } else {
                        showError(data);
                    }
                }, 'json'
            );
        });
        return false;
    },
    closeOrderAction: function (ev) {
        ev.preventDefault();
        modalDialog(trans.get('Order_close'), trans.get('Really_close_this_order'), function () {
            var target = this.$(ev.target);
            var button = target.parents('ul:first').prev();
            button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
            button.parent().removeClass('open');
            $.post(
                target.data('action'),
                {'id': Order.id},
                function (data) {
                    button.removeClass('disabled').find('i').attr('class', 'icon-cog');
                    if (!data.error) {
                        showMessage(data.message ? data.message : trans.get('Notify_success'));
                        window.location.reload();
                    } else {
                        showError(data);
                    }
                }, 'json'
            );
        });
        return false;
    },

    /*Функция для добавления нового значения к весу посылки */
    addWeightAction: function () {
        //текущий вес
        var oldWeight = parseFloat($('.ot_inline_editable[data-name="newWeight"]').editable('getValue').newWeight);
        //вес который нужно прибавить
        $('#addWeightArea').val($('#addWeightArea').val().replace(',', '.'));
        var nowWight = (parseFloat($('#addWeightArea').val())) ? parseFloat($('#addWeightArea').val()) : 0;
        //получившийся вес
        var sumWeight = (nowWight + oldWeight).toFixed(2);
        console.log(oldWeight, nowWight);
        //выводим новый вес в окне пользователю
        $('.ot_inline_editable[data-name="newWeight"]').editable('setValue', sumWeight);
        var data = $('.ot_inline_editable[data-name="newWeight"]').data();
        data.value = $('.ot_inline_editable[data-name="newWeight"]').editable('getValue').newWeight;
        // TODO: передача данных должна осуществляться с помощью встроенных методов плагина x-editable
        //передаем новые данные на сервер
        $('.ot_inline_editable[data-name="newWeight"]').editable('submit',{
            url: data.url,
            data: {
                name: data.name,
                value: data.value,
                pk: data.pk
            }
        });
        $('#addWeightArea').val(0);
        var elOrderWeight = $('dd.orderWeight')
            .children("div:first")
            .children("a:first");

        elOrderWeight.text(sumWeight);
        InlineFields.each(function(item){
            if (item.get('name') === 'newWeight') {
                item.set('value', sumWeight);
            }
        });
    },
    deleteItemFromOrderAction: function (ev) {
        ev.preventDefault();
        modalDialog(trans.get('Item_removal'), trans.get('Really_delete_this_item_from_order'), function () {
            var target = this.$(ev.target);
            var button = target.parents('ul:first').prev();
            button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
            button.parent().removeClass('open');
            $.post(
                target.data('action'),
                {
                    'itemid': target.parents('.ot_order_product_item:first').data('id'),
                    'orderid': Order.id
                },
                function (data) {
                    button.removeClass('disabled').find('i').attr('class', 'icon-cog');
                    if (!data.error) {
                        showMessage(data.message ? data.message : trans.get('Notify_success'));
                        window.location.reload();
                    } else {
                        showError(data);
                    }
                }, 'json'
            );
        });
        return false;
    },
    purchaseOrderItemsAction: function (ev) {
        ev.preventDefault();
        var target = this.$(ev.target);
        var button = target.parents('.btn-group:first').find('button');
        var itemsIds = [];
        var container = button.parents('.tab-container:first');
        $.each(container.find('.ot_order_product_item.selected_item:visible'), function () {
            itemsIds.push($(this).data('id'));
        });
        if (!itemsIds.length) {
            showError(trans.get('No_items_selected'));
            return;
        }
        button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
        button.parent().removeClass('open');
        $.post(
            '?cmd=orders&do=purchaseItems',
            {
                'itemsIds': itemsIds,
                'orderId': Order.id
            },
            function (data) {
                button.removeClass('disabled').find('i').attr('class', 'icon-cog');
                if (!data.error) {
                    showMessage(data.message ? data.message : trans.get('Notify_success'));
                    window.location.reload();
                } else {
                    showError(data);
                }
            }, 'json'
        );
        return false;
    },
    bulkChangeItemStatusAction: function (ev) {
        ev.preventDefault();
        var target = this.$(ev.target);
        var button = target.parents('.btn-group:first').find('button');
        var itemsIds = [];
        var container = button.parents('.tab-container:first');
        $.each(container.find('.ot_order_product_item.selected_item:visible'), function () {
            itemsIds.push($(this).data('id'));
        });
        if (!itemsIds.length) {
            showError(trans.get('No_items_selected'));
            return;
        }
        button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
        button.parent().removeClass('open');
        $.post(
            '?cmd=orders&do=changeItemStatus',
            {
                'itemId': itemsIds,
                'orderId': Order.id,
                'status': target.data('status')
            },
            function (data) {
                button.removeClass('disabled').find('i').attr('class', 'icon-star-empty');
                if (!data.error) {
                    showMessage(data.message ? data.message : trans.get('Notify_success'));
                    window.location.reload();
                } else {
                    showError(data);
                }
            }, 'json'
        );
        return false;
    },
    addItemsToExistingPackageAction: function (ev) {
        ev.preventDefault();
        var target = this.$(ev.target);
        var button = target.parents('.btn-group:first').find('button');
        var itemsIds = [];
        var container = button.parents('.tab-container:first');
        var onlyNewItemsSelected = true;
        var errors = false;
        $.each(container.find('.ot_order_product_item.selected_item:visible'), function () {
            if ($(this).find('.itemInPackage').is('span')) {
                onlyNewItemsSelected = false;
                return;
            }
            var item = OrderItems.get($(this).data('id'));
            // TODO: это костыль. Все статусы должны быть доступны в одном месте.
            // Коды возможных статусов
            // 6 - Получен на склад
            // 7 - Упаковка
            // 8 - Готов к отправке
            if ($.inArray(parseInt(item.get('statusid')), [6, 7, 8]) == -1) {
                showError(trans.get('Some_goods_have_incorrect_status_to_package', {status: item.get('statusname')}));
                errors = true;
                return;
            }
            itemsIds.push(item.id);
        });
        if (!onlyNewItemsSelected) {
            showError(trans.get('Adding_to_package_is_for_items_without_packages'));
            return;
        }
        if (errors) {
            return;
        }
        if (!itemsIds.length) {
            showError(trans.get('No_items_selected'));
            return;
        }
        button.parent().removeClass('open');
        var modalContent = this.$('.packagesElectionWindow .modal-body').html();
        var modalEventsAttached = false;
        var modal = modalDialog(trans.get('Package_election'), modalContent, function (dialog) {
            var selectedPackage = $(dialog).find('label input:checked').val();
            $.post(
                '?cmd=orders&do=moveItemsToPackage',
                {
                    'itemsIds': itemsIds,
                    'toPackageId': selectedPackage
                },
                function (data) {
                    if (!data.error) {
                        showMessage(data.message ? data.message : trans.get('Notify_success'));
                        window.location.reload();
                    } else {
                        showError(data);
                    }
                },
                'json'
            );
        }, {
            confirm: trans.get('Add_to_package')
        });
        $(modal).find('label input').on('change', function (ev) {
            if (!modalEventsAttached) {
                modalEventsAttached = true;
                var label = $(this).parent();
                if ($(this).is(':checked')) {
                    $(this).parents('label:first').addClass('selected_item')
                        .siblings('label').removeClass('selected_item');
                }
            }
        });

        return false;
    },
    createPackageForItemsAction: function (ev) {
        ev.preventDefault();
        var target = this.$(ev.target);
        var button = target.parents('.btn-group:first').find('button');
        button.parent().removeClass('open');
        var itemsIdsStr = '';
        var container = button.parents('.tab-container:first');
        var onlyNewItemsSelected = true;
        var errors = false;
        $.each(container.find('.ot_order_product_item.selected_item:visible'), function () {
            if ($(this).find('.itemInPackage').is('span')) {
                onlyNewItemsSelected = false;
                return;
            }
            var item = OrderItems.get($(this).data('id'));
            // TODO: это костыль. Все статусы должны быть доступны в одном месте.
            // Коды возможных статусов
            // 6 - Получен на склад
            // 7 - Упаковка
            // 8 - Готов к отправке
            if ($.inArray(parseInt(item.get('statusid')), [6, 7, 8]) == -1) {
                showError(trans.get('Some_goods_have_incorrect_status_to_package', {status: item.get('statusname')}));
                errors = true;
                return;
            }
            itemsIdsStr += 'itemsIds[]=' + item.id + '&';
        });
        if (!onlyNewItemsSelected) {
            showError(trans.get('Adding_to_package_is_for_items_without_packages'));
            return;
        }
        if (errors) {
            return;
        }
        if (!itemsIdsStr.length) {
            showError(trans.get('No_items_selected'));
            return;
        }
        button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
        window.location.replace($.param.querystring(target.attr('href'), itemsIdsStr));
        return false;
    },
    deletePackageAction: function (ev) {
        ev.preventDefault();
        var target = this.$(ev.target);
        if (target.hasClass('disabled')) {
            return;
        }
        modalDialog(trans.get('Package_removal'), trans.get('Really_delete_this_package'), function () {
            target.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
            var packageRow = target.parents('.packageItemBlock:first');
            var packageId = packageRow.data('id');
            $.post(target.data('action'), {'packageId': packageId}, function (data) {
                target.removeClass('disabled').find('i').attr('class', 'icon-remove font-14');
                if (!data.error) {
                    packageRow.remove();
                    $('#ot_order_goods_tab').find('.itemInPackage:contains("' + packageId + '")').remove();
                    OrderPackages = new Backbone.Collection(OrderPackages.filter(function (orderPackage) {
                        return orderPackage.id != packageId;
                    }));
                    if (!OrderPackages.length) {
                        $('.addItemsToExistingPackage').remove();
                        $('.createNewPackageBlock').show();
                    }
                    showMessage(data.message ? data.message : trans.get('Notify_success'));
                } else {
                    showError(data);
                }
            }, 'json');
        });
        return false;
    },
    editInternalDelivery: function (ev) {
        ev.preventDefault();
        var target = this.$(ev.target);
        var button = target.parents('.btn-group:first').find('button');
        var editInternalDeliveryForm = $('#editInternalDeliveryForm').clone(true);

        var container = button.parents('.tab-container:first');
        if (container.find('.ot_order_product_item.selected_item:visible').length < 1) {
            showError(trans.get('No_items_selected'));
            return false;
        }
        var ids = [];
        $.each(container.find('.ot_order_product_item.selected_item:visible'), function () {
            var item = OrderItems.get($(this).data('id'));
            ids.push(item.get('id'));
        });
        editInternalDeliveryForm.find('input[name="lines"]').val(ids.join(';'));
        editInternalDeliveryForm.find('input[name="order"]').val(Order.get('id'));

        modalDialog(trans.get('Editing_internal_delivery'), editInternalDeliveryForm.show(), function () {
            var newPrice = editInternalDeliveryForm.find('input[name="internalDeliveryPrice"]').val();

            if (newPrice < 0) {
                showError(trans.get('Price_must_be_positive'));
                return false;
            } else {
                $.post(
                    editInternalDeliveryForm.attr('action'),
                    editInternalDeliveryForm.serializeArray(),
                    function (data) {
                        if (!data.error) {
                            showMessage(trans.get('Notify_success'));
                            window.location.reload();
                        } else {
                            showError(data);
                        }
                    }, 'json');
            }
        },{confirm: trans.get('Save'), cancel: trans.get('Cancel') });
    },
    recalculationItemPrice: function(ev) {
        ev.preventDefault();
        var target = this.$(ev.target);
        var button = target.parents('.btn-group:first').find('button');
        var recalculationItemPriceForm = $('#recalculationItemPriceForm').clone(true);

        var container = button.parents('.tab-container:first');
        if (container.find('.ot_order_product_item.selected_item:visible').length < 1) {
            showError(trans.get('No_items_selected'));
            return false;
        }
        var ids = [];
        $.each(container.find('.ot_order_product_item.selected_item:visible'), function () {
            var item = OrderItems.get($(this).data('id'));
            ids.push(item.get('id'));
        });
        recalculationItemPriceForm.find('input[name="lines"]').val(ids.join(';'));
        recalculationItemPriceForm.find('input[name="order"]').val(Order.get('id'));

        modalDialog(trans.get('Recalculation_item_price'), recalculationItemPriceForm.show(), function () {

                $.post(
                    recalculationItemPriceForm.attr('action'),
                    recalculationItemPriceForm.serializeArray(),
                    function (data) {
                        if (!data.error) {
                            showMessage(trans.get('Notify_success'));
                            window.location.reload();
                        } else {
                            showError(data);
                        }
                    }, 'json');
        },{confirm: trans.get('Save'), cancel: trans.get('Cancel') });

    },
    editOriginalPrice: function (ev) {
        ev.preventDefault();
        var target = this.$(ev.target);
        var button = target.parents('.btn-group:first').find('button');
        var editOriginalPriceForm = $('#editOriginalPriceForm').clone(true);

        var container = button.parents('.tab-container:first');
        if (container.find('.ot_order_product_item.selected_item:visible').length < 1) {
            showError(trans.get('No_items_selected'));
            return false;
        }
        var ids = [];
        $.each(container.find('.ot_order_product_item.selected_item:visible'), function () {
            var item = OrderItems.get($(this).data('id'));
            ids.push(item.get('id'));
        });
        editOriginalPriceForm.find('input[name="lines"]').val(ids.join(';'));
        editOriginalPriceForm.find('input[name="order"]').val(Order.get('id'));

        modalDialog(trans.get('Editing_original_price'), editOriginalPriceForm.show(), function () {
            var newPrice = editOriginalPriceForm.find('input[name="originalItemPrice"]').val();

            if (newPrice <= 0) {
                showError(trans.get('Price_must_be_positive'));
                return false;
            } else {
                $.post(
                    editOriginalPriceForm.attr('action'),
                    editOriginalPriceForm.serializeArray(),
                    function (data) {
                        if (!data.error) {
                            showMessage(trans.get('Notify_success'));
                            window.location.reload();
                        } else {
                            showError(data);
                        }
                    }, 'json');
            }
        },{confirm: trans.get('Save'), cancel: trans.get('Cancel') });
    },
    showFullFilterGoodsByStatusAction: function (ev) {
        ev.preventDefault();
        $.each(this.$('.goods-status-filter input'), function () {
            $(this).parent().parent().show();
        });
        $('#showAllFilters').hide();

        return false;
    },
    filterGoodsByStatusAction: function (ev) {
        ev.preventDefault();
        var statusIds = [];

        $.each(this.$('.goods-status-filter input:checked'), function () {
            statusIds.push(parseInt($(this).val()));
        });
        var container = $(ev.target).parents('.tab-container:first');
        if (!statusIds.length) {
            // Сброс фильтра
            container.find('.ot_order_product_item').show();
            return;
        }
        OrderItems.each(function (item) {
            var itemBlock = container.find('.ot_order_product_item[data-id=' + item.id + ']');
            if (_.contains(statusIds, parseInt(item.get('statusid')))) {
                itemBlock.show();
            } else {
                itemBlock.removeClass('selected_item').find('input[type=checkbox]').prop('checked', false);
                itemBlock.hide();
            }
        });
        $.each(this.$('.goods-status-filter input'), function () {
            if (!$(this).prop('checked')) {
                $(this).parent().parent().hide();
            }
        });
        $('#showAllFilters').show();

        return false;
    },
    changeItemStatusAction: function (ev) {
        ev.preventDefault();
        var target = this.$(ev.target);
        var button = target.parents('.btn-group:first').find('button');
        button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
        button.parent().removeClass('open');
        var itemRow = $(ev.target).parents('.ot_order_product_item:first');
        var item = OrderItems.get(itemRow.data('id'));
        var orderInfo = $('#orderInfo');
        var orderUserPanel = $('#ot_order_user_panel');
        var newStatusId = target.data('status');
        var self = this;
        $.post(
            '?cmd=orders&do=changeItemStatus',
            {
                'itemId': item.get('id'),
                'orderId': Order.id,
                'status': newStatusId,
                'comment': item.get('operatorcomment'),
                'quantity': item.get('qty')
            },
            function (data) {
                button.removeClass('disabled').find('i').attr('class', 'icon-star-empty');
                if (!data.error) {
                    // 11 - Returned to supplier, 12 - Unable to deliver, 13 - cancelled
                    if (target.data('status') == 11 || target.data('status') == 12 || target.data('status') == 13) {
                        itemRow.find('.exportOrderItem a').hide();
                        itemRow.find('input.for_group_action').attr('disabled', 'disabled');
                    } else {
                        itemRow.find('.exportOrderItem a').show();
                        itemRow.find('input.for_group_action').removeAttr('disabled');
                    }
                    var status = OrderItemsStatusList.get(newStatusId);
                    item.set('statuscode', newStatusId);
                    item.set('statusid', newStatusId);
                    item.set('statusname', status.get('name'));
                    itemRow.find('.itemStatus').text(status.get('name'));
                    var container = $('.ot_order_summary');
                    container.find('.orderStatus').text(data.orderStatusName);
                    container.find('.orderPaidRemainAmount').html(data.orderPaid + ' / ' + data.orderRemain);
                    $.get('templates/orders/view/itemsSummary.html?' + Math.random(), function (tpl) {
                        var itemsByStatusHtml = _.template(tpl, {'itemsByStatus': data.itemsByStatus});
                        container.find('.orderItemsSummary dl').html(itemsByStatusHtml);
                    });
                    orderInfo.find('.goodsPrice').text(decodeData(data.orderGoodsAmount));
                    orderInfo.find('.orderPaidRemainAmount').text(decodeData(data.orderPaid + ' / ' + data.orderRemain));
                    Order.set('remainamount', data.orderRemainWithoutSign);
                    if (data.userBalance !== null) {
                        orderUserPanel.find('span#accountAvailableAmount').text(decodeData(data.userBalance));
                    }
                    self.checkAvailableMoney();
                    showMessage(data.message ? data.message : trans.get('Notify_success'));
                } else {
                    showError(data);
                }
            }, 'json'
        );
        return false;
    },
    changeOperatorCommentAction: function (ev) {
        ev.preventDefault();
        var target = this.$(ev.target);
        target.button('loading');
        var itemRow = target.parents('.ot_order_product_item:first');
        var item = OrderItems.get(itemRow.data('id'));
        var form = target.closest('form');
        var comment = $.trim(form.find('textarea').val());
        $.post(
            form.attr('action'),
            {
                'orderId': Order.id,
                'itemId': item.id,
                'comment': comment,
                'quantity': item.get('qty'),
                'status': item.get('statusid')
            },
            function (data) {
                target.button('reset');
                target.text(trans.get('Save'));
                if (data.comment) {
                    item.set('operatorcomment', data.comment.text);
                    target.next().trigger('click');
                    $.get('templates/orders/item/comments.html?' + Math.random(), function (tpl) {
                        var commentHtml = _.template(tpl, {
                            'comments': [data.comment], 'useWrapper': false, 'operatorComment': true, 'itemId': item.id
                        });
                        var commentsBlock = target.parents('.item-comments-block:first');
                        var commentsList = commentsBlock.find('.comments-list');
                        commentsList.find('blockquote:not([class=custcomment]):last').remove();
                        commentsList.append(commentHtml);
                        commentsBlock.find('.addOperatorCommentBtn').remove();

                        var productItem = target.parents('.ot_order_product_item');
                        var productitemId = $(productItem).data('id');
                        var comments = $('.item-comments-block', productItem).html();
                        if ($('#ot_order_purchase_tab .ot_order_product_item[data-id="' + productitemId + '"]').length > 0) {
                            var productItem2 = $('#ot_order_purchase_tab .ot_order_product_item[data-id="' + productitemId + '"]');
                            $('.item-comments-block', productItem2).html(comments);
                            $('.item-comments-block button', productItem2).remove();
                        }
                    });
                } else {
                    showError(data);
                }
            }, 'json'
        );
        return false;
    },
    addTicketMessageAction: function (ev) {
        ev.preventDefault();
        var target = this.$(ev.target);
        var form = target.closest('form');
        var comment = $.trim(form.find('textarea').val());
        if (!comment.length) {
            showError(trans.get('Value_must_not_be_empty'));
            return;
        }
        var container = $('#ot_order_support_panel .chat-messages');
        target.button('loading');
        $.post(
            form.attr('action'),
            {
                'orderId': Order.id,
                'customerId': Customer.get('id'),
                'customerLogin': Customer.get('login'),
                'ticketId': Order.get('ticketid'),
                'comment': comment,
                'isNewTicket': !container.children().length
            },
            function (data) {
                target.button('reset');
                if (data.newTicketId) {
                    Order.set('ticketid', parseInt(data.newTicketId));
                }
                if (data.comment) {
                    target.next().trigger('click');
                    form.find('textarea').val('');
                    Order.set('ticketmessages', Order.get('ticketmessages').concat(data.comment));
                    $.get('templates/orders/view/support/message.html?' + Math.random(), function (tpl) {
                        var commentHtml = _.template(tpl, {'messages': [data.comment]});
                        container.prepend(commentHtml);
                        $('a[href=#ot_order_support_panel] .count-all').text(Order.get('ticketmessages').length);
                    });
                } else {
                    showError(data);
                }
            }, 'json'
        );
        return false;
    },
    markTicketMessagesAsReadAction: function (ev) {
        var unreadMessages = _.filter(Order.get('ticketmessages'), function (message) {
            return message.read == 0;
        });
        if (unreadMessages.length) {
            $.post(
                '?cmd=support&do=markTicketMessagesRead',
                {'ticketId': Order.get('ticketid')},
                function (data) {
                    if (!data.error) {
                        $.each(Order.get('ticketmessages'), function (i, message) {
                            message.read = 1;
                        });
                        setTimeout(function () {
                            var container = $('#ot_order_support_panel .chat-messages');
                            container.find('.message-box.new').removeClass('new');
                            $('a[href=#ot_order_support_panel] .badge-success').text('0');
                        }, 30000);
                    } else {
                        showError(data);
                    }
                }, 'json'
            );
        }
    },
    changeOrderAdditionalInfoAction: function (ev) {
        ev.preventDefault();
        var target = this.$(ev.target);
        var form = target.closest('form');
        var info = $.trim(form.find('textarea').val());
        target.button('loading');
        $.post(
            form.attr('action'),
            {'orderId': Order.id, 'info': info},
            function (data) {
                target.button('reset');
                if (!data.error) {
                    showMessage(data.message ? data.message : trans.get('Notify_success'));
                    target.next().trigger('click');
                    target.parents('dd:first').find('span').html(escapeData(info));
                    var actionBtn = target.parents('dd:first').find('button:first');
                    if (actionBtn.find('i').hasClass('icon-plus')) {
                        actionBtn.find('i').attr('class', 'icon-pencil')
                        actionBtn.attr('title', trans.get('Edit'));
                        target.text(trans.get('Save'));
                    }
                } else {
                    showError(data);
                }
            }, 'json'
        );
        return false;
    },
    splitItemQuantityAction: function (ev) {
        ev.preventDefault();
        var itemRow = $(ev.target).parents('.ot_order_product_item:first');
        var item = OrderItems.get(itemRow.data('id'));
        if (item.get('qty') <= 1) {
            return false;
        }

        var content = '<input type="text" class="numeric" name="splitQuantity" value="1" />';
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
                        'orderId': Order.id,
                        'splitQuantity': splitQuantity
                    },
                    function (data) {
                        button.removeClass('disabled').find('i').attr('class', 'icon-cog');
                        if (!data.error) {
                            showMessage(data.message ? data.message : trans.get('Notify_success'));
                            window.location.reload();
                        } else {
                            showError(data);
                        }
                    }, 'json'
                );
            }, {
                'confirm': trans.get('Split'),
            }
        );
        return false;
    },
    toggleCheckAll: function (ev) {
        var self = this.$(ev.target);
        self.parents('.tab-container:first').find('.ot_order_product_item input[type=checkbox]:visible:not(:disabled)')
            .prop('checked', self.is(':checked'))
            .trigger('change');
    },
    toggleCheckAllGrouped: function (ev) {
        var self = this.$(ev.target);
        self.parents('.group-container:first').find('.ot_items_grouped .ot_order_product_item input[type=checkbox]:visible:not(:disabled)')
            .prop('checked', self.is(':checked'))
            .trigger('change');
    },
    groupItems: function (ev) {
        ev.preventDefault();
        var target = this.$(ev.target);
        $.post('?cmd=orders&do=view',
            {'itemsGroupBy': target.data('groupItems')},
            function (data) {
                if (!data.error) {
                    window.location.reload();
                } else {
                    showError(data.message);
                }
            }
        );
    },
    updateSelectedRows: function (ev) {
        var checkbox = $(ev.target);
        if (checkbox.hasClass('checkAll')) {
            return;
        }
        if (checkbox.is(':checked')) {
            checkbox.parents('.ot_order_product_item:first').addClass('selected_item');
        } else {
            checkbox.parents('.ot_order_product_item:first').removeClass('selected_item');
        }
    },

    /* Действия со счетом пользователя */
    paymentReserveAction: function (ev) {
        ev.preventDefault();
        //if (parseFloat(CustomerAccount.get('availablecust')) < parseFloat(Order.get('remainamount'))) {
        //    showError(trans.get('Not_enough_money_on_user_account'));
        //    return;
        //}
        modalDialog(trans.get('Confirm_needed'), trans.get('Really_reserve_money_from_user_account'), function () {
            var target = this.$(ev.target).is('a') ? this.$(ev.target) : this.$(ev.target).parents('a:first');
            var button = target.parents('ul:first').prev();
            button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
            button.parent().removeClass('open');
            $.post(target.data('action'), {'orderId': Order.id}, function (data) {
                button.removeClass('disabled').find('i').attr('class', 'icon-cog');
                if (!data.error) {
                    showMessage(data.message ? data.message : trans.get('Notify_success'));
                    window.location.reload();
                } else {
                    showError(data);
                }
            }, 'json');
        });
        return false;
    },
    updateAccountAction: function (ev) {
        ev.preventDefault();
        var self = this;
        var form = self.$(ev.target).parents('form:first');
        if (!form.find('input[name=amount]').val().length) {
            showError(trans.get('Value_must_not_be_empty'));
            return;
        }
        form.find('button').attr('disabled', 'disabled');
        $.post('?cmd=users&do=updateAccount&id=' + Order.get('custid') + '&login=' + Customer.get('login'),
            form.serialize(),
            function (data) {
                form.find('button').removeAttr('disabled');
                if (!data.error && data.userAccount) {
                    self.$('#accountAvailableAmount').text(data.userAccount.AvailableCust + ' ' + data.userAccount.CurrencySignCust);
                    CustomerAccount.set('availablecust', data.userAccount.AvailableCust);
                    form.find(':input').not(':button, :submit, :reset, :hidden').val('');
                    form.find('button:last').trigger('click');
                    self.checkAvailableMoney();
                    showMessage(data.message ? data.message : trans.get('Notify_success'));
                } else {
                    showError(data);
                }
            }, 'json'
        );
        return false;
    },
    toggleEnrollForm: function (ev) {
        ev.preventDefault();
        this.$(this.$('#toggleWithdrawForm').data('target')).css('height', '0px').removeClass('in');
        this.$('#toggleWithdrawForm').removeClass('active');
        return false;
    },
    toggleWithdrawForm: function (ev) {
        ev.preventDefault();
        this.$(this.$('#toggleEnrollForm').data('target')).css('height', '0px').removeClass('in');
        this.$('#toggleEnrollForm').removeClass('active');
        return false;
    },
    onEnrollFormHidden: function (ev) {
        ev.preventDefault();
        $('#toggleEnrollForm').removeClass('active');
        return false;
    },
    onWithdrawFormHidden: function (ev) {
        ev.preventDefault();
        $('#toggleWithdrawForm').removeClass('active');
        return false;
    },
    checkAvailableMoney: function () {
        if (parseFloat(Order.get('remainamount')) > 0) {
            $('.paymentReserve').show();
        } else {
            $('.paymentReserve').hide();
        }
        if (parseFloat(CustomerAccount.get('availablecust')) < parseFloat(Order.get('remainamount'))) {
            $('.paymentReserve').find('.font-12').html(trans.get('Reserv_money'));
        } else {
            $('.paymentReserve').find('.font-12').html(trans.get('Pay_order'));
        }
    },
    onEnrollFormShown: function (ev) {
    },
    onWithdrawFormShown: function (ev) {
    },
    mergeOrders: function () {
        var content = '<div class="editableform-loading" ></div>';
        var callback = function (body) {
            var selected = $("input[type='radio']:checked", body);
            if (selected.length > 0) {
                var orderId = Order.id;
                var order2Id = $(selected).attr('id');
                //merge orders
                $.post('?cmd=orders&do=mergeOrders',
                    {
                        'orderId': Order.id,
                        'order2Id': order2Id
                    },
                    function (data) {
                        if (!data.error) {
                            if (data.result == 'ok') {
                                //redirect
                                window.location.href = 'index.php?cmd=orders&do=list';
                            } else {
                                showError(data.result);
                            }
                        } else {
                            showError(data.message);
                            $('.confirmDialog').modal('hide');
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

        modalDialog(trans.get('Select_order_for_merge'), content, callback, {
            'confirm': trans.get('Merge'),
            'cancel': trans.get('Cancel')
        });

        $.post(
            '?cmd=orders&do=getOrdersListForMerge',
            {
                'orderId': Order.id,
                'customerId': Order.attributes.custid,
                'provider': Order.attributes.providertypeenum
            },
            function (data) {
                if (!data.error) {
                    contents = data.orders;
                    $('.confirmDialog .modal-body').html(contents);
                } else {
                    showError(data);
                    $('.confirmDialog').modal('hide');
                }
            },
            'json'
        );
    },
    /* /Действия со счетом пользователя */

    /**
     * Выводит лог действий оператора на странице заказа
     */
    showOrderLog: function () {

        $('#showOrderLogBtn').addClass('hidden');
        $('#hideOrderLogBtn').removeClass('hidden');
        $('#orderLogWrapper').removeClass('hidden');

        var options = {
            "ajax": '?cmd=reports&do=orderLog&id=' + Order.id,
            columnDefs: [
                {type: 'de_datetimesec', targets: 0}
            ],
            retrieve: true
        };

        if(currentAdminLang != 'en') {
            options.language = {
                url: 'js/vendor/DataTables/js/i18n/' + currentAdminLang + '.lang.json'
            }
        }

        $('#orderLog').dataTable(options);

        destination = $('#orderLogWrapper').offset().top;
        $('body').animate({scrollTop: destination}, 1100);

        return false;
    },

    hideOrderLog: function () {
        $('#showOrderLogBtn').removeClass('hidden');
        $('#hideOrderLogBtn').addClass('hidden');
        $('#orderLogWrapper').addClass('hidden');

        return false;
    },

    onEnterAddWeight:function(e){
        if(e.keyCode === 13) {
            $("#addWeight").click();
        }
    }
});

// OrdersPageObject всегда возвращает только один объект OrdersPage
var OrdersPageObject = (function () {
    var instance;

    return function Construct_singletone() {
        if (instance) {
            return instance;
        }
        if (this && this.constructor === Construct_singletone) {
            instance = new OrdersPage();
            return instance;
        } else {
            return new Construct_singletone();
        }
    }
}());

$(function(){
    var O = new OrdersPageObject();
});

function clone(o) {
    if(!o || 'object' !== typeof o)  {
        return o;
    }
    var c = 'function' === typeof o.pop ? [] : {};
    var p, v;
    for(p in o) {
        if(o.hasOwnProperty(p)) {
            v = o[p];
            if(v && 'object' === typeof v) {
                c[p] = clone(v);
            } else {
                c[p] = v;
            }
        }
    }
    return c;
}