var Sellers = new Backbone.Collection();
var SellersPage = Backbone.View.extend({
    "el": ".seller_wrapper",
    "events": {
        "click .delete-existing-image": "deleteImage",
        "click .js-save-seller-btn": "saveSeller",
    },
    sellerAlias: null,
    render: function()
    {
        return this;
    },
    initialize: function()
    {
       this.sellerAlias = $('#url').val();
       this.render();
    },
    deleteImage: function () {
        $('#existing_image').val('');
        $('.thumbnail-placeholder img').attr('src', $('#originalPicture').val());
    },
    saveSeller: function (e) {
        e.preventDefault();
        var btn = $(e.currentTarget);
        var form = $('.ot_form ')[0];
        var action = "?cmd=Sets&do=updateSetsSeller";

        if (url.length == 0) {
            showError(trans.get("Seller_url_or_id_is_required"));
            return false;
        }

        $.ajax({
            type: 'POST',
            url: action,
            data: new FormData(form),
            success: function (data) {
                if (data.error) {
                    showError(data);
                    return false;
                } else {
                    showMessage(trans.get("Data_updated_successfully"));
                    if (btn.data('redirect-url') !== undefined && btn.data('redirect-url').length) {
                        window.location.href = btn.data('redirect-url');
                    }
                }
            },
            cache: false,
            contentType: false,
            processData: false
        });

    },
});

$(function() {
    var U = new SellersPage();
});