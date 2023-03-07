let BasketView = Backbone.View.extend({
    el: '.js-basket',

    events: {
        'blur   .js-basket-comment-textarea':       'saveComment',
        'click  .js-basket-btn-delete':             'deleteChecked',
        'click  .js-basket-btn-favorite':           'moveCheckedToFavorites',
        'click  .js-basket-btn-favorite-item':      'moveItemToFavorite',
        'click  .js-basket-btn-delete-item':        'deleteItem',
        'click  .js-basket-make-order-button':      'makeOrder',
        'click  .js-basket-provider-link':          'changeProvider',
        'click  .js-basket-btn-clear':              'cleanBasket',
        'click  .js-basket-comment-btn':            'editComment',
        'click  .js-basket-config':                 'changeConfig',
        'click  .js-basket-line-quantity-btn':      'changeQuantityAct',
        'click  .js-basket-line-delivery-change':   'changeDeliveryOfLine',
        'click  .js-basket-delivery-method-choose': 'changeDelivery',
        'change .js-basket-line-quantity':          'changeQuantity',
        'change .js-basket-checkbox':               'processCheckbox',
    },

    basketCheckingMessages: {},
    basketCheckedWithWarning: false,
    noBasketCheckingErrors: true,
    noBasketCheckingWarnings: true,
    chosenDeliveryModeId: null,
    deliveryModeId: null,
    deliveryModes: null,
    totalCost: 0,

    initialize: function () {
        this.getBasket();
        this.render();
    },

    render: function () {
        return true;
    },


    getBasket: function () {
        let self = this,
            action = self.$el.data('getBasketUrl'),
            data = {};

        if (self.$('.js-basket-content').length) {
            data = {
                activeBasketLines: self.getCheckedLinesString(true),
                activeProvider: self.$('.js-basket-provider-link.active').data('alias')
            };
        }

        self.showOverlay();
        $.post(action, data, function (data) {
            if (data.error) {
                showError(data.message);
            } else {
                self.$('.js-basket-content-wrapper').html(data.content);
                self.processBasket(true);
            }
            self.hideOverlay();
        });
    },

    processBasket: function (findBasketCheckingMessage) {
        // обрабатываем все комментарии
        this.checkCommentsState();
        this.countItemsEachTotalCost();

        // включаем оверлей
        this.showSidebarOverlay();

        // если есть выбранные товары, то считаем сумму
        if (this.checkItemsQuantity()) {
            this.countItemsTotalWeight();
            this.getDeliveries();
            this.$el.find('.js-basket-panel-button:not(.js-basket-btn-clear)').removeClass('disabled');
        } else {
            // если товаров нету, то сразу отключаем оверлей
            this.hideSidebarOverlay();
            this.$el.find('.js-basket-panel-button:not(.js-basket-btn-clear)').addClass('disabled');
        }

        // вызываем подсчет итоговой суммы, даже если товаров не отмечено, что бы обновить this.totalCost
        this.countItemsTotalCost();
        // проверям минимальную сумму заказа
        this.checkMinOrderTotalCost();
        // проверяем ошибки для позиций товаров
        this.processBasketCheckingMessagesAll(this.basketCheckingMessages);

        // проверяем существование сообщений об ошибках и скролим до первого
        if (findBasketCheckingMessage) {
            this.findFirstBasketCheckingMessage();
        }
    },

    checkItemsQuantity: function () {
        if (this.getCheckedLines().length) {
            this.enableOrderButton();
            this.$('.js-basket-sidebar-body').show();
            return true;
        } else {
            this.disableOrderButton();
            this.$('.js-basket-sidebar-body').hide();
            this.$('.js-basket-total-cost').html('0');
            return false;
        }
    },

    countItemsEachTotalCost: function () {
        let self = this,
            $lines = self.getAllLines();

        $.each($lines, function (k, line) {
            let $line = self.$(line),
                $lineDeliveryPrice = $line.find('.js-basket-line-summary-price-delivery'),
                $lineItemsPrice = $line.find('.js-basket-line-summary-price-item'),
                lineItemsPrice = $lineItemsPrice.find('[itemprop=price]').attr('content'),
                currency = $lineItemsPrice.find('[itemprop=priceCurrency]').text(),
                totalCost = Number(lineItemsPrice);

            if ($lineDeliveryPrice.length) {
                totalCost += Number($lineDeliveryPrice.find('[itemprop=price]').attr('content'));
            }

            $line.find('.js-basket-line-summary-price').html(getCurrencyPrice(totalCost, currency));
        });
    },

    countItemsTotalCost: function () {
        let self = this,
            $items = self.getCheckedLines(),
            $totalCost = self.$('.js-basket-lines-total-cost'),
            checkedGroups = [],
            totalCost = 0,
            currency = '';

        $totalCost.hide();

        if ($items.length) {
            $.each($items, function (k, item) {
                let $item = self.$(item);
                let $itemPrice = $item.find('.js-basket-line-summary-price-item');
                let itemPrice = $itemPrice.find('[itemprop=price]').attr('content');
                let itemCurrency = $itemPrice.find('[itemprop=priceCurrency]').text();

                totalCost += Number(itemPrice);
                currency = itemCurrency;

                let $group = $item.closest('.js-basket-group');
                let groupId = $group.data('groupId');
                let groupPrice = $group.data('groupPrice');

                if (!checkedGroups.includes(groupId)) {
                    totalCost += Number(groupPrice);
                    checkedGroups.push(groupId);
                }
            });

            let displayTotalCost = getCurrencyPrice(totalCost, currency);
            $totalCost.show()
                .find('.js-basket-lines-total-cost-value')
                .data('totalCost', totalCost)
                .data('currency', currency)
                .html(displayTotalCost);
        }

        self.totalCost = totalCost;
    },

    countItemsTotalWeight: function () {
        let self = this,
            $items = self.getCheckedLines(),
            $totalWeight = self.$('.js-basket-lines-total-weight'),
            totalWeight = 0;

        $totalWeight.hide();

        if ($items.length) {
            $.each($items, function (k, item) {
                let $item = self.$(item),
                    $weight = $item.find('.js-basket-line-weight'),
                    quantity = $item.find('.js-item-quantity').data('quantity');

                if ($weight.length) {
                    totalWeight += Number($weight.data('itemWeight')) * quantity;
                }
            });

            if (totalWeight) {
                $totalWeight.show()
                    .find('.js-basket-lines-total-weight-value')
                    .html(number_format(totalWeight, 2));
            }
        }
    },

    countTotalCost: function () {
        let $itemsTotalCost = this.$('.js-basket-lines-total-cost-value'),
            $deliveryCost = this.$('.js-basket-lines-total-delivery-cost-value'),
            deliveryCost = $deliveryCost.data('cost'),
            itemsTotalCost = $itemsTotalCost.data('totalCost'),
            currency = $itemsTotalCost.data('currency'),
            totalCost = Number(itemsTotalCost) + Number(deliveryCost);

        totalCost = getCurrencyPrice(totalCost, currency);
        this.$('.js-basket-total-cost').html(totalCost);

        this.hideSidebarOverlay();
    },

    checkMinOrderTotalCost: function () {
        let totalCost = this.totalCost,
            $message = this.$el.find('.js-min-order-total-cost-error-message'),
            minOrderTotalCost = Number($message.data('minOrderTotalCost'));

        if (minOrderTotalCost < totalCost) {
            $message.hide();
            this.enableOrderButton();
        } else if (totalCost !== 0) {
            $message.show();
            this.disableOrderButton();
        } else {
            $message.hide();
        }
    },


    makeOrder: function (e) {
        let self = this,
            $btn = self.$(e.currentTarget);

        if ($btn.hasClass('disabled')) {
            return false;
        }

        self.showBasketCheckingProgressbar(0);
        $btn.addClass('disabled');

        let checkedLines = [],
            $checkedLines = self.getCheckedLines();
        $.each($checkedLines, function (k, item) {
            let lineId = self.$(item).data('lineId');
            checkedLines.push(lineId);
        });

        if (!checkedLines.length) {
            modalDialog('title', trans.get('Need_choose_items'));
            return false;
        }

        if (!self.basketCheckedWithWarning) {
            self.runBasketChecking($btn, checkedLines);
        } else {
            this.makeOrderRequest();
        }
    },

    makeOrderRequest: function () {
        let action = this.$el.data('makeOrderUrl'),
            data = {
                type: this.$('.js-basket-provider.active').data('alias'),
                items: this.getCheckedLinesString(),
                deliveryId: this.deliveryModeId
            };

        $.redirect(action, data);
    },

    runBasketChecking: function ($btn, checkedLines) {
        let self = this,
            action = self.$el.data('runBasketCheckingUrl');

        self.noBasketCheckingErrors = true;
        self.noBasketCheckingWarnings = true;

        $btn.removeClass('disabled');
        self.$('.js-basket-provider.active')
            .find('.js-basket-notification')
            .removeClass('alert-warning')
            .removeClass('alert-danger')
            .text('')
            .hide();

        $.post(action, {basketLines: checkedLines}, function (data) {
            if (data.error) {
                showError(data.message);
                self.hideBasketCheckingProgressbar();
            } else {
                self.basketCheckingStep(data.activityId);
            }
        });
    },

    basketCheckingStep: function (activityId) {
        let self = this,
            action = self.$el.data('basketCheckingUrl'),
            activeProvider = self.$('.js-basket-provider.active').data('name');

        $.post(action, {'activityId': activityId}, function (data) {
            if (data.error) {
                showError(data.message);
                self.hideBasketCheckingProgressbar();
            } else {
                self.processBasketCheckingStep(data);

                if (data.finished) {
                    self.basketCheckingMessages[activeProvider] = data.messages;
                    self.finishBasketChecking();
                } else {
                    setTimeout(function () {
                        self.basketCheckingStep(activityId);
                    }, 1000);
                }
            }
        });
    },

    processBasketCheckingStep: function (result) {
        let self = this;

        self.showBasketCheckingProgressbar(result.progressPercent);
        self.processBasketCheckingMessages(result.messages);
    },

    processBasketCheckingMessages: function (messages) {
        let self = this;

        $.each(messages, function (k, message) {
            let statusClass = 'alert ';

            switch (message.status) {
                case 'Warning':
                    statusClass += 'alert-warning';
                    self.noBasketCheckingWarnings = false;
                    break;
                case 'Error':
                    statusClass += 'alert-danger';
                    self.noBasketCheckingErrors = false;
                    break;
            }

            self.getBasketCheckingNotificationNode(message.lineId)
                .addClass(statusClass)
                .text(message.text)
                .show();
        });
    },

    processBasketCheckingMessagesAll: function (messagesAll) {
        let self = this;

        $.each(messagesAll, function (k, messages) {
            self.processBasketCheckingMessages(messages);
        });
    },

    removeBasketCheckingMessage: function (lineId) {
        let self = this,
            messageAlreadyFound = false;

        self.getBasketCheckingNotificationNode(lineId)
            .removeClass('alert-warning')
            .removeClass('alert-danger')
            .text('')
            .hide();

        $.each(self.basketCheckingMessages, function (p, messages) {
            $.each(messages, function (k, message) {
                if (String(lineId) === String(message.lineId)) {
                    messages.splice(k, 1);
                    messageAlreadyFound = true;
                    return false;
                }
            });

            if (messageAlreadyFound) {
                self.basketCheckingMessages[p] = messages;
                return false;
            }
        });
    },

    getBasketCheckingNotificationNode: function (lineId) {
        return this.$('.js-basket-line-' + lineId).find('.js-basket-notification');
    },

    finishBasketChecking: function () {
        if (this.findFirstBasketCheckingMessage()) {
            showMessage(trans.get('attention_changes_in_the_basket'), true);
        }

        if (this.noBasketCheckingErrors) {
            this.enableOrderButton();
        }

        if (!this.noBasketCheckingWarnings) {
            this.basketCheckedWithWarning = true;
            showError(trans.get('order_has_comments'));
        } else if (this.noBasketCheckingErrors) {
            this.makeOrderRequest();
        }

        this.hideBasketCheckingProgressbar();
    },

    findFirstBasketCheckingMessage: function () {
        let isChanges = false,
            $activeProvider = this.$('.js-basket-provider.active'),
            $firstError = $activeProvider.find('.js-basket-notification.alert-danger'),
            $firstWarning = $activeProvider.find('.js-basket-notification.alert-warning');

        if ($firstError.length) {
            isChanges = true;
            this.scrollToLine($firstError);
        }

        if (!isChanges && $firstWarning.length) {
            isChanges = true;
            this.scrollToLine($firstWarning);
        }

        return isChanges;
    },

    scrollToLine: function ($basketLine) {
        $('body, html').animate({scrollTop: $basketLine.offset().top - 300}, 500);
    },


    changeConfig: function (e) {
        let self = this,
            $item = self.$(e.currentTarget).closest('.js-basket-line'),
            itemId = $item.data('itemId'),
            $basketWrapper = self.$el,
            itemInfoAction = $basketWrapper.data('itemInfoAction');

        self.showOverlay();
        $.post(itemInfoAction, {itemId: itemId}, function (data) {
            if (data.error) {
                showError(data.message);
            } else {
                self.showChangeConfigModal($item, data.configurations);
            }

            self.hideOverlay();
        });
    },

    showChangeConfigModal: function ($line, configurations) {
        let self = this,
            $modalContent = self.$('.js-basket-line-change-configuration-modal').clone().show(),
            $skeletonSelectBlock = self.$('.js-basket-select-skeleton').clone().show(),
            $skeletonOption = $skeletonSelectBlock.find('.js-basket-select-option').remove();

        $.each(configurations, function (configId, config) {
            let $block = $skeletonSelectBlock.clone(),
                $select = $block.find('.js-basket-select');

            $block.find('.js-basket-select-skeleton-name').html(config.displayName);
            $select.attr('name', 'configurations[' + configId + ']');

            let checked = true;
            $.each(config.values, function (valueId, valueDisplayName) {
                $select.append(
                    $skeletonOption
                        .clone()
                        .attr('value', valueId)
                        .attr('checked', checked)
                        .html(valueDisplayName)
                );
                checked = false;
            });

            $modalContent.find('.js-basket-line-change-form').append($block);
        });

        $modalContent.find('.js-basket-line-change-configuration-modal-itemUrl').val($line.data('itemUrl'));
        $modalContent.find('.js-basket-line-change-configuration-modal-lineId').val($line.data('lineId'));
        $modalContent.find('.js-basket-line-change-configuration-modal-itemId').val($line.data('itemId'));
        $modalContent.find('.js-basket-line-change-configuration-modal-comment').val($line.data('itemComment'));
        $modalContent.find('.js-basket-line-change-configuration-modal-quantity').val($line.data('itemQuantity'));
        $modalContent.find('.js-basket-line-change-configuration-modal-configId').val($line.data('itemConfigId'));
        $modalContent.find('.js-basket-line-change-configuration-modal-externalDeliveryId').val($line.data('itemExternalDeliveryId'));

        modalDialog(trans.get('change_config'), $modalContent, function ($modal) {
            let action = self.$el.data('lineChangeConfigurationUrl'),
                formData = $modal.find('.js-basket-line-change-form').serializeArray();

            self.simplePostRequest(action, formData);
        });
    },


    getDeliveries: function () {
        let self = this,
            lines = self.getCheckedLinesString(),
            action = self.$el.data('getBasketDeliveriesUrl');

        if (typeof self.getDeliveriesXHR === 'function') {
            self.getDeliveriesXHR.abort();
        }

        self.getDeliveriesXHR = $.post(action, {basketLines: lines}, function (data) {
            if (data.error) {
                showError(data.message);
            } else {
                self.deliveryModes = data.deliveryModes;

                let $checkedLines = self.getCheckedLines(),
                    sameDeliveryId = $checkedLines.first().data('itemExternalDeliveryId'),
                    sameDelivery = true,
                    deliveryId = null;

                // проверяем, не используется ли для всех товаров одна и та же система доставки
                $.each($checkedLines, function (k, line) {
                    let $line = self.$(line),
                        deliveryId = $line.data('itemExternalDeliveryId');

                    if (sameDeliveryId !== deliveryId) {
                        sameDelivery = false;
                        return false;
                    }
                });

                if (sameDelivery && self.isDeliveryAvailable(sameDeliveryId, self.deliveryModes)) {
                    // система доставки из товаров
                    deliveryId = sameDeliveryId;
                } else if (self.chosenDeliveryModeId && self.isDeliveryAvailable(self.chosenDeliveryModeId, self.deliveryModes)) {
                    // система доставки выбранная пользователем
                    deliveryId = self.chosenDeliveryModeId;
                } else {
                    // самая дешевая система доставки, либо "выбрать позже"
                    let cheapestMode = null;

                    $.each(self.deliveryModes, function (id, mode) {
                        if (!mode.ErrorCode) {
                            cheapestMode = cheapestMode || mode.Id;
                            cheapestMode = mode.Cost.Val < self.deliveryModes[cheapestMode].Cost.Val ? mode.Id : cheapestMode;
                        }
                    });

                    deliveryId = cheapestMode ? cheapestMode : 0;
                }

                self.deliveryModeId = deliveryId;
                self.processDeliveryData(deliveryId, self.deliveryModes);
            }
        });
    },

    isDeliveryAvailable: function (deliveryId, deliveryModes) {
        return deliveryModes[deliveryId] && !deliveryModes[deliveryId].ErrorCode;
    },

    changeDelivery: function () {
        let self = this;

        modalDialog(
            trans.get('select_delivery'),
            self.renderChangeDeliveryTable(self.deliveryModeId, self.deliveryModes),
            function ($modal) {
                let chosenDeliveryModeId = $modal.find('.js-basket-table-delivery-mode-input:checked').data('id');

                self.chosenDeliveryModeId = chosenDeliveryModeId;
                self.deliveryModeId = chosenDeliveryModeId;
                self.processDeliveryData(chosenDeliveryModeId, self.deliveryModes);
            },
            false, false, undefined, 3
        );
    },

    processDeliveryData: function (deliveryModeId, deliveryModes) {
        if (deliveryModeId) {
            let delivery = deliveryModes[deliveryModeId];

            this.$('.js-basket-lines-total-delivery-cost').show();
            this.$('.js-basket-delivery-method-choose-input').val(delivery.Id);
            this.$('.js-basket-delivery-method-choose-text').html(delivery.Name);
            this.$('.js-basket-lines-total-delivery-cost-value')
                .attr('data-cost', delivery.Cost.Val)
                .data('cost', delivery.Cost.Val)
                .html(getCurrencyPrice(delivery.Cost.Val, delivery.Cost.Sign));
        } else {
            this.$('.js-basket-lines-total-delivery-cost').hide();
            this.$('.js-basket-delivery-method-choose-input').val(0);
            this.$('.js-basket-delivery-method-choose-text').html(trans.get('delivery_method_select_at_checkout'));
            this.$('.js-basket-lines-total-delivery-cost-value')
                .attr('data-cost', 0)
                .data('cost', 0)
                .empty();
        }

        this.processDeliveryText(deliveryModes);
        this.countTotalCost();
    },

    processDeliveryText: function (deliveryModes) {
        let $deliveryButton = $('.js-basket-delivery-method-choose-dummy'),
            $deliveryText = $('.js-basket-delivery-method-choose-text'),
            $deliveryTextClickable = $('.js-basket-delivery-method-choose-text-clickable'),
            $deliveryTextNotClickable = $('.js-basket-delivery-method-choose-text-not-clickable');

        $deliveryText.hide();
        $deliveryButton.removeClass('js-basket-delivery-method-choose');

        if (Object.values(deliveryModes).length) {
            $deliveryButton.addClass('js-basket-delivery-method-choose');
            $deliveryTextClickable.show();
        } else {
            $deliveryTextNotClickable.show();
        }
    },

    changeDeliveryOfLine: function (e) {
        e.preventDefault();

        let self = this,
            $btn = self.$(e.currentTarget),
            $line = $btn.closest('.js-basket-line'),
            $deliveryModes = $line.find('.js-basket-line-delivery-mode'),
            lineId = $line.data('lineId'),
            currentDeliveryMode = $btn.data('id'),
            deliveryModes = [];

        $.each($deliveryModes, function (id, deliveryMode) {
            deliveryModes.push(self.$(deliveryMode).data());
        });

        modalDialog(
            trans.get('select_delivery'),
            self.renderChangeDeliveryTable(currentDeliveryMode, deliveryModes),
            function ($modal) {
                let action = self.$el.data('editBasketItemFieldsUrl'),
                    data = {
                        lineId: lineId,
                        externalDeliveryId: $modal.find('.js-basket-table-delivery-mode-input:checked').data('id')
                    };

                self.simplePostRequest(action, data);
            },
            false, false, undefined, 3
        );
    },

    renderChangeDeliveryTable: function (currentDeliveryMode, deliveryModes) {
        let self = this,
            $table = self.$('.js-basket-table-delivery-skeleton').clone().show(),
            $tableBody = $table.find('.js-basket-table-delivery-body'),
            $tableBodyDelivery = $tableBody.find('.js-basket-table-delivery-mode-skeleton');

        $tableBodyDelivery
            .find('.js-basket-table-delivery-mode-input')
            .attr('id', 'delivery-later')
            .siblings('.js-basket-table-delivery-mode-label')
            .attr('for', 'delivery-later');

        $.each(deliveryModes, function (k, deliveryMode) {
            let $tmpTableBodyDelivery = $tableBodyDelivery.clone(),
                id = deliveryMode.id || deliveryMode.Id,
                name = deliveryMode.name || deliveryMode.Name,
                description = deliveryMode.description || deliveryMode.Description;

            $tmpTableBodyDelivery
                .find('.js-basket-table-delivery-mode-input')
                .attr('id', id)
                .data('id', id)
                .attr('data-id', id)
                .data('name', name)
                .attr('data-name', name)
                .attr('value', id)
                .val(id)
                .attr('checked', currentDeliveryMode === id)
                .siblings('.js-basket-table-delivery-mode-label')
                .attr('for', id)
                .html(name)
                .closest($tmpTableBodyDelivery)
                .find('.js-basket-table-delivery-mode-description-text')
                .html(description);

            if (deliveryMode.ErrorCode) {
                if ($tmpTableBodyDelivery.hasClass('js-basket-table-delivery-mode-skeleton-need-disable')) {
                    $tmpTableBodyDelivery.addClass('disabled');
                }

                $tmpTableBodyDelivery
                    .find('.js-basket-table-delivery-mode-input')
                    .attr('disabled', true)
                    .closest($tmpTableBodyDelivery)
                    .find('.js-basket-table-delivery-mode-description-error')
                    .html(deliveryMode.ErrorDescription);
            } else {
                let costVal = deliveryMode.costVal || deliveryMode.Cost.Val,
                    costSign = deliveryMode.costSign || deliveryMode.Cost.Sign;

                $tmpTableBodyDelivery
                    .find('.js-basket-table-delivery-mode-price')
                    .html(getCurrencyPrice(costVal, costSign));
            }

            $tableBody.prepend($tmpTableBodyDelivery);
        });

        if (! $tableBody.find('.js-basket-table-delivery-mode-input:checked').length) {
            $tableBodyDelivery.find('.js-basket-table-delivery-mode-input').attr('checked', true);
        }

        return $table;
    },


    getAllLines: function () {
        return this
            .$('.js-basket-provider.active')
            .find('.js-basket-checkbox-item')
            .closest('.js-basket-line');
    },

    getCheckedLines: function (getAllProviders) {
        return this
            .$('.js-basket-provider' + (getAllProviders ? '' : '.active'))
            .find('.js-basket-checkbox-item:checked')
            .closest('.js-basket-line');
    },

    getCheckedLinesString: function (getAllProviders) {
        let self = this,
            basketLines = '',
            $checkedItems = self.getCheckedLines(getAllProviders);

        $.each($checkedItems, function (k, item) {
            basketLines += self.$(item).data('lineId') + ',';
        });

        return basketLines.length ? basketLines.slice(0, -1) : basketLines;
    },


    processCheckbox: function (e) {
        let $el = this.$(e.currentTarget),
            $wrapper = $el.closest('.js-basket-checkbox-wrapper');

        this.processCheckboxChildren($el);
        this.processCheckboxParents($wrapper);
        this.processBasket();
    },

    processCheckboxChildren: function ($el) {
        let $wrapper = $el.closest('.js-basket-checkbox-wrapper'),
            $children = $wrapper.find('.js-basket-checkbox'),
            checkedState = $el.prop('checked');

        if ($el.hasClass('js-basket-checkbox-multiple')) {
            $children.prop('checked', checkedState);
        }
    },

    processCheckboxParents: function ($wrapper) {
        let $blockCheckbox = $wrapper.find('.js-basket-checkbox-main:first .js-basket-checkbox'),
            isUnchecked = !!$wrapper.find('.js-basket-checkbox-children .js-basket-checkbox:not(:checked)').length,
            $parentWrapper = $wrapper.parent().closest('.js-basket-checkbox-wrapper');

        if (isUnchecked) {
            $blockCheckbox.prop('checked', false);
        } else {
            $blockCheckbox.prop('checked', true);
        }

        if ($parentWrapper.length) {
            this.processCheckboxParents($parentWrapper);
        }
    },


    editComment: function (e) {
        let $btn = this.$(e.currentTarget),
            $item = $btn.closest('.js-basket-line'),
            $textarea = $item.find('.js-basket-comment-textarea');

        $btn.hide();
        $textarea.show().focus();
    },

    saveComment: function (e) {
        let self = this,
            $textarea = self.$(e.currentTarget),
            $line = $textarea.closest('.js-basket-line'),
            comment = $textarea.val(),
            oldComment = $line.find('.js-basket-line-comment-text').text(),
            action = self.$el.data('editBasketItemFieldsUrl'),
            data = {
                lineId: $line.data('lineId'),
                comment: comment
            };

        self.checkCommentState($line);

        if (oldComment === comment) {
            return;
        }

        $line.find('.js-basket-preloader').show();

        $.post(action, data, function (data) {
            if (data.error) {
                showError(data.message);
                self.revertCommentData($line);
            }

            $line.find('.js-basket-preloader').hide();
            self.checkCommentState($line);
        });
    },

    checkCommentsState: function () {
        let self = this,
            $lines = this.getAllLines();

        $.each($lines, function (k, line) {
            let $line = self.$(line);
            self.checkCommentState($line);
        });
    },

    checkCommentState: function ($line) {
        let $link = $line.find('.js-basket-comment-btn'),
            $textarea = $line.find('.js-basket-comment-textarea'),
            $textWrapper = $link.find('.js-basket-line-comment'),
            $text = $textWrapper.find('.js-basket-line-comment-text'),
            $add = $link.find('.js-basket-line-comment-add'),
            comment = $textarea.val();

        $link.show();
        $textarea.hide();
        $text.text(comment);

        if (comment.length) {
            $textWrapper.show();
            $add.hide();
        } else {
            $textWrapper.hide();
            $add.show();
        }
    },

    revertCommentData: function ($line) {
        let $textarea = $line.find('.js-basket-comment-textarea'),
            comment = $line.find('.js-basket-line-comment-text').text();

        $textarea.val(comment).text(comment);
    },


    cleanBasket: function (e) {
        let self = this,
            action = this.$(e.currentTarget).data('action');

        let successCallback = function () {
            $('.js-cart-amount').html(0);
        };

        modalDialog(
            trans.get('clear_cart'),
            trans.get('sure_delete_all'),
            function () {
                self.simplePostRequest(action, {}, successCallback)
            }
        );
    },

    moveItemToFavorite: function (e) {
        let self = this,
            $btn = this.$(e.currentTarget);

        let successCallback = function () {
            self.updateCartAndFavouriteAmount(-1, 1);
        };

        modalDialog(
            trans.get('to_favourites'),
            trans.get('sure_to_favourites'),
            function () {
                self.processBtnAction($btn, successCallback);
            }
        );
    },

    deleteItem: function (e) {
        let self = this,
            $btn = this.$(e.currentTarget);

        let successCallback = function () {
            self.updateCartAndFavouriteAmount(-1, 0)
        };

        modalDialog(
            trans.get('delete_from_cart'),
            trans.get('Cart_remove_confirmation'),
            function () {
                self.processBtnAction($btn, successCallback);
            }
        );
    },

    processBtnAction: function ($btn,  successCallback) {
        let action = $btn.data('action'),
            lineId = $btn.data('lineId'),
            data = {
                basketLines: String(lineId)
            };

        this.simplePostRequest(action, data, successCallback);
    },

    deleteChecked: function (e) {
        let self = this,
            $btn = this.$(e.currentTarget);

        if ($btn.hasClass('disabled')) {
            return;
        }

        let successCallback = function () {
            let checkedQuantity = self.getCheckedLines().length;
            self.updateCartAndFavouriteAmount(checkedQuantity * -1, 0)
        };

        modalDialog(
            trans.get('delete_selected'),
            trans.get('sure_delete_selected'),
            function () {
                self.processCheckedItemsAction($btn, successCallback);
            }
        );
    },

    moveCheckedToFavorites: function (e) {
        let self = this,
            $btn = this.$(e.currentTarget);

        if ($btn.hasClass('disabled')) {
            return;
        }

        let successCallback = function () {
            let checkedQuantity = self.getCheckedLines().length;
            self.updateCartAndFavouriteAmount(checkedQuantity * -1, checkedQuantity);
        };

        modalDialog(
            trans.get('move_selected_to_favorites'),
            trans.get('sure_favourite_multi'),
            function () {
                self.processCheckedItemsAction($btn, successCallback);
            }
        );
    },

    processCheckedItemsAction: function ($btn, successCallback) {
        let action = $btn.data('action'),
            data = {
                basketLines: this.getCheckedLinesString()
            };

        this.simplePostRequest(action, data, successCallback);
    },

    updateCartAndFavouriteAmount: function (cartAdd, favoriteAdd) {
        let $cartAmount = $('.js-cart-amount'),
            $favoritesAmount = $('.js-favorite-amount'),
            cartAmount = Number($cartAmount.text()),
            favoriteAmount = Number($favoritesAmount.text());

        $cartAmount.text(cartAmount + cartAdd);
        $favoritesAmount.text(favoriteAmount + favoriteAdd);
    },


    changeQuantity: function (e) {
        let self = this,
            $input = self.$(e.currentTarget),
            quantity = $input.val(),
            lineId = $input.closest('.js-basket-line').data('lineId'),
            action = self.$el.data('changeQuantityUrl'),
            data = {
                quantity: quantity,
                lineId: lineId,
            };

        let successCallback = function () {
            self.removeBasketCheckingMessage(lineId);
        };

        self.simplePostRequest(action, data, successCallback);
    },

    changeQuantityAct: function (e) {
        let $btn = this.$(e.currentTarget),
            $quantity = $btn.closest('.js-basket-line').find('.js-basket-line-quantity'),
            quantity = Number($quantity.val()),
            action = $btn.data('action'),
            add = (action === 'plus') ? 1 : (action === 'minus') ? -1 : 0;

        $quantity.val(quantity + add).trigger('change');
    },


    showOverlay: function () {
        let $overlay = $('.js-overlay-no-preloader');

        $overlay.find('.js-overlay-no-preloader-message').html('');
        $overlay.show();
    },

    hideOverlay: function () {
        $('.js-overlay-no-preloader').hide();
    },

    showSidebarOverlay: function () {
        this.$('.js-basket-aside-preloader').show();
    },

    hideSidebarOverlay: function () {
        this.$('.js-basket-aside-preloader').hide();
    },

    showBasketCheckingProgressbar: function (progress) {
        progress = progress + '%';
        this.$('.js-basket-progressbar')
            .show()
            .find('.js-basket-progressbar-progress')
            .css('width', progress)
            .html(progress);
    },

    hideBasketCheckingProgressbar: function () {
        this.$('.js-basket-progressbar')
            .hide()
            .find('.js-basket-progressbar-progress')
            .css('width', '0%')
            .html('0%');
    },

    disableOrderButton: function () {
        this.$('.js-basket-make-order-button').addClass('disabled');
    },

    enableOrderButton: function () {
        this.$('.js-basket-make-order-button').removeClass('disabled');
    },


    simplePostRequest: function (action, data, successCallback) {
        let self = this;

        self.showOverlay();
        $.post(action, data, function (answer) {
            if (answer.error) {
                showError(answer.message);
            } else if (typeof successCallback === 'function') {
                successCallback();
            }
            self.getBasket();
        });
    },


    changeProvider: function (e) {
        e.preventDefault();
        let $btn = this.$(e.currentTarget),
            providerContainer = $btn.data('container');

        this.$('.js-basket-provider').removeClass('active').removeClass('show');
        this.$('.js-basket-provider-link').removeClass('active');
        this.$(providerContainer).addClass('active').addClass('show');
        $btn.addClass('active');

        this.processBasket();
    },
});

$(function () {
    basketView = new BasketView();
});