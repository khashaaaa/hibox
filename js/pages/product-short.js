var ProductShortView = Backbone.View.extend({
    el: 'body',
    events: {
        'click .js-product-item .js-product-btn-quick-look': 'showProductModal'
    },

    initialize: function () {
        this.render();
    },

    render: function () {
        return true;
    },

    showProductModal: function (e) {
        e.preventDefault();
        var self = this;
        var $wrapper = this.$(e.currentTarget).closest('.js-product-item');
        var productId = $wrapper.data('productId');
        var productUrl = $wrapper.data('productUrl');
        modalDialog('', '<div class="spinner"></div>', false,
            {
                confirm: false,
                cancel: trans.get('close')
            },
            function () {
                self.xhr = $.post(productUrl, {'id': productId},
                    function (data) {
                        if (data.error) {
                            showError(data);
                        } else {
                            var $modalContent = $('.confirmDialog.show .modal-dialog .modal-body');
                            $modalContent.html('');
                            $modalContent.append($(data.answer));
                            var $productWrapper = $modalContent.find('.js-product');
                            window.productView.renderProduct($productWrapper);
                        }
                    }, 'json'
                );
            },
            undefined, 4,
            function () {
                if (typeof self.xhr !== 'undefined') {
                    self.xhr.abort();
                }
            }
        );
    }
});

$(function () {
    var productShortView = new ProductShortView();
});