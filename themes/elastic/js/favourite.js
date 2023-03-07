var oData;

var checkAjax = null;

var FavouriteView = Backbone.View.extend({
    "el": ".content-block",
    "events": {
        'change .list-cart-products .checkbox-item': 'checkButtons',
        "click .custom-control-input.choose_all_items, .nav-link": "checkAllItems",
        "click .button-delete-selected": "removeSelectedItems",
        "click .button-clear-favourites": "clearFavourites",
        "click .delete-item.delete": "removeItem",
        "click .number__plus, .number__minus": "setQuantity",
        "click  input[type=checkbox]": "setCheckboxItem",
        "click  .button-favorite": "add_group_to_basket",
        "click  .basket-icon": "addToBasket",
        "click  .remove_vendor": "removeVendor",
        "focusout  .quantity": "setQuantity",
        "focusout .list-cart-products__col-3 textarea": "addCommentForItem"
    },

    render: function()
    {
        return this;
    },

    initialize: function(){
        this.checkAjax = null;
        this.render();
        this.checkButtons();
    },

    addCommentForItem: function (e) {
        var textarea = this.$(e.target);
        var action = textarea.data('action');
        var id =  textarea.closest('.list-cart-products__row-item').data('rowid');
        var comment = textarea.val();

        if(textarea.hasClass('is-invalid')){
            textarea.removeClass('is-invalid');
        }

        if(textarea.hasClass('is-valid')){
            textarea.removeClass('is-valid');
        }

        $.post(
            action,
            {
                id:id,
                comment:comment
            },
            function (data) {
                if(data.error){
                    textarea.addClass('is-invalid');
                    showError(data.error);
                } else {
                    if(textarea.val() !== '') {
                        textarea.addClass('is-valid');
                    }
                }
            }
        );
    },

    setPreloader: function (e) {
        var preloader = '<img class="preloader" src="/i/ajax-loader-transparent.gif" />';
        this.$(e.target).after(preloader);
    },

    removePreloader: function () {
        $('img.preloader').remove();
    },



    checkAllItems: function () {

        if ($(".tab-pane.fade.active.show .custom-control-input.choose_all_items").prop("checked")) {

            $.each($('.tab-pane.fade.active.show .custom-control-input.checkbox-item'), function (index, elem) {
                $(elem).prop("checked", true);
            })
        }
        if(!$(".tab-pane.fade.active.show .custom-control-input.choose_all_items").prop("checked")){

            $.each($('.tab-pane.fade.active.show .custom-control-input.checkbox-item'), function (index, elem) {
                $(elem).prop("checked", false);
            })
        }
        this.checkButtons();
    },


    removeItem: function (e) {
        var action = this.$(e.target).data('action');
        var self = this;
        modalDialog(trans.get('need_confirm'), trans.get('sure_delete'), function () {
            self.setPreloader(e);
            window.location.href = action;
        });
    },

    removeSelectedItems: function (e) {

        var items = this.getCheckedItems();
        if (items.length < 1) {            
            return false;
        }
        var checkboxes = $('input[name=deleter]').filter(':checked');

        var ids = "";
        var self = this;
        var action = $('.button.button-delete').data("action");

        checkboxes.each(function() {
            var itemid = $(this).val();
            ids = ids + itemid + ",";
        });
        modalDialog(trans.get('need_confirm'), trans.get('sure_delete_group'), function () {
            self.setPreloader(e);

            $.get(
                action,
                {
                    ids: ids,
                    delGroup: true
                },
                function () {
                    location.reload();
                }
            );
    });
    },

    clearFavourites: function (e) {
        var action = $('.button.button-delete').data("action");
        var self = this;
        var checkboxes = $('input[name=deleter]');
        var ids = "";

        checkboxes.each(function() {
            var itemid = $(this).val();
            ids = ids + itemid + ",";
        });

        modalDialog(trans.get('need_confirm'), trans.get('sure_clear_favourites'), function () {
            self.setPreloader(e);
            $.get(
                action,
                {
                    ids: ids,
                    delGroup: true
                },
                function () {
                    location.reload();
                }
            );
        })
    },



    add_group_to_basket: function(e) {

        var items = this.getCheckedItems();
        if (items.length < 1) {            
            return false;
        }

        var checkboxes = $('input[name=deleter]').filter(':checked');
        var count = checkboxes.filter(':checked').length;
        var perPage = $("select[name='per_page'] option:selected").val();
        var self = this;
        var checks = "";

        if (count) {
            modalDialog(trans.get('need_confirm'), trans.get('sure_move_favourites_in_cart'), function () {
                self.setPreloader(e);
                checkboxes.each(function() {
                    var itemid = $(this).val();
                    checks = checks + itemid + ',';
                });

                $.get(
                    "//"+window.location.hostname,
                    {
                        p: 'MoveItemsFromNoteToBasket',
                        items: checks
                    },
                    function() {
                        document.location.href = "/?p=supportlist&per_page=" + perPage;
                    }
                );
            });

        } else {
            $("#dialog-empty").dialog("open");
        }
    },

    addToBasket: function (e) {

        var perPage = $("select[name='per_page'] option:selected").val();
        var itemid = this.$(e.target).data('itemid');
        var self = this;
        modalDialog(trans.get('need_confirm'), trans.get('sure_to_basket'), function () {
            self.setPreloader(e);
            $.get(
                "//"+window.location.hostname,
                {
                    p: 'MoveItemFromNoteToBasket',
                    id: itemid,
                },
                function (data) {
                    var result = JSON.parse(data);
                    if ((result.Success) === "Ok") {
                        location.reload();
                    } else {
                        showError(trans.get('add_to_fav_error'));
                    }
                }
            );
        });
    },

    removeVendor: function (e) {
        var action = this.$(e.currentTarget).data('action');
        var id = this.$(e.currentTarget).data('id')
        var self = this;
        modalDialog(trans.get('need_confirm'), trans.get('sure_delete_vendor'), function () {
            self.setPreloader(e);
            $.get(
                action,
                {
                  ids: id
                },
                function (data) {
                    if (!data.Error) {
                        location.reload();
                    } else {
                        showError(data.Error);
                    }
                }
            );
        });
    },

    setQuantity: function (e) {
        this.removePreloader();
        this.setPreloader(e);
        if(this.checkAjax !== null){
            this.checkAjax.abort();
        }
        var id = this.$(e.target).closest(".list-cart-products__row-item").data('rowid');

        var quantity = $('#count-' + id).val();
        if (quantity == 0){
            quantity = 1;
            $('#' + id + '_quantity').val(1)
        }
        var sign = null;
        var action = this.$(e.target).data('action');

        var self = this;

        self.checkAjax =  $.get(
            action,
            {'id':id, 'num': quantity},
            function (data) {
                self.removePreloader();
                $(data).each(function(k, item){
                    sign = item.CurrencySign;
                    $('#total-price-'+item.Id)
                        .html(number_format(parseFloat(item.Cost), price_round_decimals, '&nbsp;') + '&nbsp;' + sign)
                        .attr('price', parseFloat(item.Cost));

                    var newDeliveryPricePerItem = 0;
                    if (item.GroupConvertedPriceList && item.GroupConvertedPriceList.Internal && item.GroupConvertedPriceList.Internal.Display) {
                        newDeliveryPricePerItem = item.GroupConvertedPriceList.Internal.Display;
                    }
                    $('#current-price_'+item.Id).html(item.DisplayPrice);
                });
                this.checkAjax = null;
            }, 'json'
        );


    },

    checkButtons: function () {
        console.log('checkButtons');
        var buttonFavorite = this.$('a.button-favorite');
        var buttonDelete = this.$('a.button-delete-selected');
        var favoriteNeedsDisable = true;
        var deleteNeedsDisable = true;

        var items = this.getCheckedItems();
        if (items.length > 0) {
            favoriteNeedsDisable = false;
            deleteNeedsDisable = false;
        }

        if (favoriteNeedsDisable) {
            buttonFavorite.addClass('disabled');
        } else {
            buttonFavorite.removeClass('disabled');
        }

        if (deleteNeedsDisable) {
            buttonDelete.addClass('disabled');
        } else {
            buttonDelete.removeClass('disabled');
        }
    },

    getCheckedItems: function () {
        return this.$('#favourites_items')
            .find('.list-cart-products')
            .find('input[type=checkbox]:not(:disabled):checked');
    }, 

    checkItemsEventBtn: function (items, btnSelector) {
        items = items.filter(function () {
            return $(this).closest('.list-cart-products__row-item').find('.link-group').find(btnSelector).length > 0;
        });
        return items;
    }

});

$(function(){
    var favouriteView = new FavouriteView();
});





