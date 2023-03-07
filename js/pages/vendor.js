var VendorView = Backbone.View.extend({
    el: 'body',

    events: {
        'click .js-vendor-add-favorites': 'addVendorToFavourite',
        'click .js-vendor-remove-favorites': 'removeVendorFromFavourites'
    },

    initialize: function() {
        this.initializeRatyScore();
        this.render();
    },

    render: function() {
        return this;
    },

    initializeRatyScore: function () {
        let $raty = this.$el.find('.js-favourite-vendors-item-raty-score');
        if ($raty.length) {
            $raty.raty({
                readOnly: true
            });
        }
    },

    addVendorToFavourite: function () {
        var vendorId = $('.js-vendor-add-favorites').data('vendorid');
        var action = $('.js-vendor-add-favorites').data('action');
        var btn = $('.js-vendor-add-favorites');
        btn.button('loading');
        $.post(
            action,
            {
                id: vendorId
            }, function (data) {
                if (!data.error) {
                    showMessage(trans.get('vendor_added_to_favourites_txt'));
                } else {
                    showError(data.message);
                }

            })
        .done(function() {
            btn.button('reset');
        });
    },

    removeVendorFromFavourites: function (e) {
        e.preventDefault();
        let self = this,
            $btn = self.$el.find(e.currentTarget),
            $vendor = $btn.closest('.js-favourite-vendors-item'),
            action = $btn.data('action'),
            id = $btn.data('id');
        modalDialog(
            trans.get('drop_vendor_from_fav'),
            trans.get('sure_delete_vendor'),
            function () {
                $.post(action, {ids : id}, function (answer) {
                    if (answer.error) {
                        showError(answer.message);
                    } else {
                        $vendor.remove();
                    }
                });
            }
        );
    },
});

$(function () {
    var vendorView = new VendorView();
});