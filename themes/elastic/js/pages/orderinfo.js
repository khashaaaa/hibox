var OrderInfoView = Backbone.View.extend({
    el: '.main-order-block',
    events: {
        'change .checkAll': 'checkAll',
        'change .list-products__col-checkbox': 'checkButtons',
        'click .button-cancel': 'cancelOrder',
        'click .button-confirm-items': 'confirmSelectedPrice',
        'click .button-delete-items': 'confirmSelectedRemove',
        'click .button-confirm-price': 'confirmPrice',
        'click .button-delete-line': 'confirmRemove',
        'click .button-confirm-receipt-of-goods': 'closeLineOrder',
        'click .button-my-review': 'getReview',
        'click .button-add-comment': 'addComment',
        'click .js-pay-order': 'payOrder',
        'click .js-package-tracking': 'getPackageTracking'
    },

    initialize: function(majorObject) {
        this.render();
        if (majorObject) {
            this.checkDefaultCheckboxes();
            this.checkNeedConfirm();
        }
        this.checkButtons();
    },

    render: function() {
        return this;
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
                    let tracking = trans.get('no_tracking');

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

                    modalDialog(trans.get('tracking'), tracking, null, {'cancel': trans.get('close'), 'confirm': false});
                }
            }
        });
    },

    checkDefaultCheckboxes: function () {
        var availableCheckboxes = this.$('.list-products')
            .children('.list-probutton-cancelducts__row-item')
            .find('.list-products__checkbox')
            .find('input[type=checkbox]:not(:disabled)');
        var confirmButtons = this.$('.list-products')
            .children('list-products__row-item')
            .find('.list-products__link-group')
            .find('a.button-confirm-price');

        if (availableCheckboxes.length === confirmButtons.length) {
            this.$('.checkAll').click();
        } else {
            confirmButtons
                .closest('.list-products__row-item')
                .find('.list-products__checkbox')
                .find('input[type=checkbox]')
                .prop('checked', true);
        }
    },

    checkNeedConfirm: function () {
        var confirmButtons = this.$('.list-products__row-item .button-confirm-price');
        if (confirmButtons.length > 0) {
            $('html, body').animate({ scrollTop: this.$('.need-confirm-item:first').offset().top }, 500);
            showError(trans.get('need_confirm'), true);
        }
    },

    checkAll: function(e) {
        var toggleButton = this.$(e.currentTarget);
        this.$(e.currentTarget)
            .closest('.list-products')
            .children('.list-products__row-item')
            .find('.list-products__checkbox')
            .find('input[type=checkbox]:not(:disabled)')
            .prop('checked', toggleButton.is(':checked'));
        this.checkButtons();
    },

    checkButtons: function () {
        var buttonConfirmAll = this.$('a.button-confirm-items');
        var buttonDeleteAll = this.$('a.button-delete-items');
        var confirmNeedsDisable = true;
        var deleteNeedsDisable = true;

        var items = this.getCheckedItems();
        if (items.length > 0) {
            if (this.checkItemsEventBtn(items, 'a.button-confirm-price').length >= 1) {
                confirmNeedsDisable = false;
            }
            if (this.checkItemsEventBtn(items, 'a.button-delete-line').length >= 1) {
                deleteNeedsDisable = false;
            }
        }

        if (confirmNeedsDisable) {
            buttonConfirmAll.addClass('disabled');
        } else {
            buttonConfirmAll.removeClass('disabled');
        }

        if (deleteNeedsDisable) {
            buttonDeleteAll.addClass('disabled');
        } else {
            buttonDeleteAll.removeClass('disabled');
        }
    },

    getCheckedItems: function() {
        return this.$('.list-products')
            .children('.list-products__row-item')
            .find('input[type=checkbox]:not(:disabled):checked');
    },

    checkItemsEventBtn: function(items, btnSelector) {
        items = items.filter(function() {
            return $(this).closest('.list-products__row-item').find('.list-products__link-group').find(btnSelector).length > 0;
        });
        return items;
    },

    confirmSelectedPrice: function(e) {
        var items = this.getCheckedItems();
        if (items.length < 1) {
            showError(trans.get('not_selected_products'));
            return true;
        }

        items = this.checkItemsEventBtn(items, 'a.button-confirm-price');
        if (items.length < 1) {
            showError(trans.get('operation_is_not_allowed_for_products'));
            return true;
        }

        modalDialog(trans.get('need_confirm'), trans.get('sure_confirm_price'),
            function () {
                showOverlay();
                var product, orderId;
                var salesLineId = [];
                items.each(function () {
                    product = $(this).closest('.list-products__row-item');
                    orderId = product.data('order');
                    salesLineId.push(product.data('sales-line-id'));
                });

                var strSalesLineIds = salesLineId.join(';');
                var pageView = new OrderInfoView(false);
                pageView.makeConfirm(orderId, strSalesLineIds);
            },
            {
                confirm: trans.get('confirm_new_price'),
                cancel: trans.get('cancel')
            },
            function(body) {
                $(body).closest('.confirmDialog').addClass('order-confirm-dialog');
            }
        );
    },

    confirmSelectedRemove: function (e) {
        var items = this.getCheckedItems();
        if (items.length < 1) {
            showError(trans.get('not_selected_products'));
            return true;
        }

        items = this.checkItemsEventBtn(items, 'a.button-delete-line');
        if (items.length < 1) {
            showError(trans.get('operation_is_not_allowed_for_products'));
            return true;
        }

        modalDialog(trans.get('need_confirm'), trans.get('sure_delete'),
            function () {
                showOverlay();
                var product, orderId;
                var salesLineId = [];
                items.each(function () {
                    product = $(this).closest('.list-products__row-item');
                    orderId = product.data('order');
                    salesLineId.push(product.data('sales-line-id'));
                });

                var strSalesLineIds = salesLineId.join(';');
                var pageView = new OrderInfoView(false);
                pageView.makeRemove(orderId, strSalesLineIds);
            },
            {
                confirm: trans.get('delete'),
                cancel: trans.get('cancel')
            },
            function(body) {
                $(body).closest('.confirmDialog').addClass('order-confirm-dialog');
            }
        );
    },

    confirmPrice: function (e) {
        var confirmButton = this.$(e.currentTarget);

        modalDialog(trans.get('need_confirm'), trans.get('sure_confirm_price'),
            function () {
                showOverlay();
                var product = confirmButton.closest('.list-products__row-item');
                var orderId = product.data('order');
                var salesLineId = product.data('sales-line-id');

                $('html, body').animate({ scrollTop: $('.list-products__row-item[data-sales-line-id="' + salesLineId + '"]').offset().top - 5 }, 500);
                var pageView = new OrderInfoView(false);
                pageView.makeConfirm(orderId, salesLineId);
            },
            {
                confirm: trans.get('confirm_new_price'),
                cancel: trans.get('cancel')
            },
            function(body) {
                $(body).closest('.confirmDialog').addClass('order-confirm-dialog');
            }
        );
    },

    confirmRemove: function (e) {
        var removeButton = this.$(e.currentTarget);

        modalDialog(trans.get('need_confirm'), trans.get('sure_delete'),
            function () {
                showOverlay();
                var product = removeButton.closest('.list-products__row-item');
                var orderId = product.data('order');
                var salesLineId = product.data('sales-line-id');

                $('html, body').animate({ scrollTop: $('.list-products__row-item[data-sales-line-id="' + salesLineId + '"]').offset().top - 5 }, 500);
                var pageView = new OrderInfoView(false);
                pageView.makeRemove(orderId, salesLineId);
            },
            {
                confirm: trans.get('delete'),
                cancel: trans.get('cancel')
            },
            function(body) {
                $(body).closest('.confirmDialog').addClass('order-confirm-dialog');
            }
        );
    },

    makeRemove: function (orderId, salesLineId) {
        $.ajax({
            async : true,
            type: 'POST',
            dataType: 'json',
            url: this.$('.button-delete-line').eq(0).data('action'),
            data : {
                "orderId" : orderId,
                "salesLineId" : salesLineId
            },
            success: function (data) {
                if (data) {
                    if (!data.result) {
                        hideOverlay();
                        showMessage(data.message, true);
                    } else {
                        window.location.reload();
                    }
                }
            }
        });
    },

    makeConfirm: function (orderId, salesLineId) {
        $.ajax({
            async : true,
            type: 'POST',
            dataType: 'json',
            url: this.$('.button-confirm-price').eq(0).data('action'),
            data : {
                "orderId" : orderId,
                "salesLineId" : salesLineId
            },
            success: function (data) {
                if (data) {
                    if (!data.result) {
                        hideOverlay();
                        showMessage(data.message, true);
                    } else {
                        window.location.reload();
                    }
                }
            }
        });
    },

    cancelOrder: function (e) {
        var buttonCancel = this.$(e.currentTarget);
        modalDialog(trans.get('need_confirm'), trans.get('sure_cancel'),
            function (body) {
                $.ajax({
                    async : true,
                    type: 'POST',
                    dataType: 'json',
                    url: buttonCancel.data('action'),
                    data : {
                        "orderId" : buttonCancel.data('order-id')
                    },
                    success: function (data) {
                        if (data && data.result) {
                            location.reload();
                        }
                    }
                });
            },
            {
                confirm: trans.get('cancel_order'),
                cancel: trans.get('cancel')
            },
            function(body) {
                $(body).closest('.confirmDialog').addClass('order-confirm-dialog');
            }
        );
    },

    closeLineOrder: function (e) {
        var orderLine = this.$(e.currentTarget).closest('.list-products__row-item');
        var orderId = orderLine.data('order');
        var salesLineId = orderLine.data('sales-line-id');

        showOverlay();
        $.post('index.php?p=closeLineOrder', {'orderId':orderId, 'salesLineId':salesLineId}, function(data){
            hideOverlay();
            if (data.error) {
                showError(data);
                return false;
            }
            $('#confirmReceiptGoods-' + salesLineId).css('display', 'none');
            orderLine.find('.button-add-review').css('display', 'inline-block');
            showMessage(trans.get('saved'));
        });
    },

    getReview: function (e) {
        e.preventDefault();
        showOverlay();

        var reviewId = this.$(e.currentTarget).data('review-id');
        var url = this.$(e.currentTarget).attr('href');

        $.get(url, {'reviewId':reviewId}, function(data){
            if (data.error) {
                $('a.button-my-review').removeAttr('disabled');
                showError(data);
                return false;
            }
            var reviewBlock = $('#myReview');
            reviewBlock.html(data.review).show();

            modalDialog(
                trans.get('your_review'),
                reviewBlock,
                function (body) {},
                {
                    confirm: false,
                    cancel: trans.get('cancel')
                },
                function(body) {
                    $(body).closest('.confirmDialog').addClass('order-confirm-dialog');
                }
            );
        }, 'json').complete(function() {
            hideOverlay();
        });
        $('body').on('hide.bs.modal', '.order-confirm-dialog', function() {
            var reviewForm = $('#myReview');
            $('.modal-body').html(reviewForm.show());
            reviewForm.find('textarea[name="text"]').val('');
            reviewForm.find('input[name="score"]').val('');
            reviewForm.find('.file-container').remove();
            $('.button-add-review').removeAttr('disabled');
            $('.button-my-review').removeAttr('disabled');
        });
    },

    addComment: function (e) {
        var form = this.$(e.currentTarget).closest('.message-form');
        var msg = this.$('.message-text', form).val();
        var orderId = this.$(form).data('orderid');
        var ticketId = this.$(form).data('ticketid');

        if (msg.length < 1) {
            return false;
        }

        $.ajax({
            async : true,
            type: 'POST',
            dataType: 'json',
            url: $('.button-add-comment').eq(0).data('action'),
            data : {
                "orderId" : orderId,
                "ticketId": ticketId,
                "message": msg
            },
            success: function (data) {
                if (data && data.result) {
                    $('.message-text', form).val('');
                    $(form).closest('.messages').find('.answer-messages').prepend(
                        '<div class="message"><span class="message-sender">' + data.user + '</span>' +
                        '<span class="message-text">' + msg + '</span></div>'
                    );
                }
            }
        });
    },

    payOrder: function (e) {
        let $btn = $(e.currentTarget),
            formUrl = $btn.data('formUrl'),
            $overlay = $('.js-overlay-no-preloader');
    
        $overlay.find('.js-overlay-no-preloader-message').html('');
        $overlay.show();
        $.post(
            formUrl, {},
            function (data) {
                if (data.error) {
                    showError(data.message);
                } else {
                    modalDialog(trans.get('payment'), data, false, {'confirm': false}, false, false, 3);
                }
                $overlay.hide();
            }
        );
    },

    /* TODO: добавить инициализацию колорбокса */
});

$(function () {
    var orderInfoView = new OrderInfoView(true);
});