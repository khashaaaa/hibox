/**
 * Работа с пристроем
**/
var Pristroy = new function () {

    this.dialogId = '#dialog-sell-out';
    this.dialogAddSuccessId = '#dialog-sell-out-success';
    this.dialogUpdateSuccessId = '#dialog-sell-out-update-success';
    this.dialogSetStatusSold = '#dialog-sell-out-confirm-sold';
    this.dialogRejectReason = '#dialog-sell-out-reject-reason';
    this.items = {};
    this.currentItem = {};
    this.blankImage = 'https://www.placehold.it/100x100/EFEFEF/AAAAAA';

    this.init = function(options)
    {
        $.extend(this, options);
    }

    this.fillForm = function(product)
    {
        $('.pristroy_dialog .btn span.fileupload-new').removeAttr('style');
        $('.pristroy_dialog .btn span.fileupload-exists').removeAttr('style');
        $('.pristroy_dialog a.btn.fileupload-exists').removeAttr('style');
        $('.pristroy_dialog a[data-dismiss="fileupload"]').trigger('click');
        if (! $('.pristroy_dialog #uploaded_image img').is('img')) {
            $('.pristroy_dialog #uploaded_image').html('<img src="' + this.balnkImage + '" />');
        }
        $('.pristroy_dialog #uploaded_image img').attr('src', this.blankImage);
        $('.pristroy_dialog input[name=uploaded_image]').val('');
        if ('undefined' !== typeof (product.pristroy)) {
            $('.pristroy_dialog #pristroy_title').val(product.pristroy.title);
            $('.pristroy_dialog #pristroy_price').val(product.pristroy.price);
            $('.pristroy_dialog #pristroy_quantity').val(product.pristroy.quantity);
            $('.pristroy_dialog #pristroy_desc').text(product.pristroy.description);
            var images = product.pristroy && product.pristroy.images ? product.pristroy.images : false;
            images = 'object' === typeof images ? images : $.parseJSON(images);
            if (images && 'undefined' !== typeof images[1]) {
                $('.pristroy_dialog #uploaded_image img').attr('src', images[1].replace('uploaded/pristroy', 'uploaded/pristroy/thumbnail_100_100'));
                $('.pristroy_dialog input[name=uploaded_image]').val(images[1]);
                $('.pristroy_dialog .btn span.fileupload-new').hide();
                $('.pristroy_dialog .btn span.fileupload-exists').show();
                $('.pristroy_dialog a.btn.fileupload-exists').show();
            }
            $('.pristroy_dialog input[name=action]').val('saveProduct');
        } else {
            $('.pristroy_dialog #pristroy_title').val(product.BriefDescrTrans);
            $('.pristroy_dialog #pristroy_price').val(product.AmountCust);
            $('.pristroy_dialog #pristroy_quantity').val(product.Qty);
            $('.pristroy_dialog #pristroy_desc').text(product.BriefDescrTrans);
            $('.pristroy_dialog input[name=action]').val('addProduct');
        }        
        $('.pristroy_dialog #default_image').attr('src', product.ItemImageURL + '_100x100.jpg');
        $('.pristroy_dialog input[name=default_image]').val(product.ItemImageURL);
        $('.pristroy_dialog input[name=item]').val(JSON.stringify(product));
    }

    this.openDialog = function(id)
    {
        this.currentItem = this.getItem(id);
        this.fillForm(this.currentItem);

        $(this.dialogId).dialog("open");
    };

    this.initDialogs = function()
    {
        $(this.dialogAddSuccessId + ':ui-dialog').dialog("destroy");
        $(this.dialogUpdateSuccessId + ':ui-dialog').dialog("destroy");
        $(this.dialogId + ':ui-dialog').dialog("destroy");
        $(this.dialogSetStatusSold + ':ui-dialog').dialog("destroy");
        $(this.dialogRejectReason + ':ui-dialog').dialog("destroy");

        var self = this;

        var pristroy_title      = $("#pristroy_title"),
            pristroy_price      = $("#pristroy_price"),
            pristroy_quantity   = $("#pristroy_quantity"),
            pristroy_image      = $("#pristroy_image"),
            pristroy_desc       = $("#pristroy_desc"),
            allFields = $([]).add(pristroy_title).add(pristroy_price).add(pristroy_quantity).add(pristroy_image).add(pristroy_desc);

        $(this.dialogAddSuccessId).dialog({
            autoOpen: false,
            modal: true,
            buttons: {
                Ok: function() {
                    $(this).dialog("close");
                }
            }
        });
        $(this.dialogUpdateSuccessId).dialog({
            autoOpen: false,
            modal: true,
            buttons: {
                Ok: function() {
                    $(this).dialog("close");
                }
            }
        });
        $(this.dialogRejectReason).dialog({
            autoOpen: false,
            modal: true,
            buttons: {
                Ok: function() {
                    $(this).dialog("close");
                }
            }
        });

        var buttons = {};
        buttons[trans.get('Product_is_sold')] = function() {
            self.setStatusSold($(this).data('item'));
            $(this).dialog("close");
        };
        buttons[trans.get('cancel')] = function() {
            $(this).dialog("close");
        };
        $(this.dialogSetStatusSold).dialog({
            autoOpen: false,
            modal: true,
            buttons: buttons
        });

        var buttons = {};
        buttons[trans.get('Put_up_for_sale')] = function() {
            if (! self.validate()) {
                return;
            }
            var that = this;
            $('.ui-dialog-buttonset').find('button').hide();
            $('.ui-dialog-buttonset').append('<img src="/i/ajax-loader.gif">');
            $('#pristroy_desc').val(tinymce.get('pristroy_desc').getContent());
            $(self.dialogId).find('form > fieldset').hide();
            var dialogHeight = $(self.dialogId).height();
            $(self.dialogId).height('auto');
            $(self.dialogId).dialog({ position: {'my': 'center', 'at': 'center'} });
            $(self.dialogId).find('form').ajaxSubmit({
                    url     :   '/ajax/pristroy.php'
                ,   type    :   'POST'
                ,   dataType:   'json'
                ,   success :   function(data) {
                        $(self.dialogId).find('form > fieldset').show();
                        $(self.dialogId).height(dialogHeight + 'px');
                        $(self.dialogId).dialog({ position: {'my': 'center', 'at': 'center'} });
                        $('.ui-dialog-buttonset').find('button').show();
                        $('.ui-dialog-buttonset').find('img:last').remove();
                        if (data && data.product) {
                            $(that).dialog("close");
                            if ('undefined' !== typeof (self.currentItem.pristroy)) {
                                $(self.dialogUpdateSuccessId).dialog("open");
                                $('#item' + self.currentItem.id).find('a.pristroy_item_is_sold').remove();
                            } else {
                                $(self.dialogAddSuccessId).dialog("open");
                                $('#item' + self.currentItem.id).find('a.sell_out').remove();
                                $('#item' + self.currentItem.id).find('a.edit_pristroy_item').show();
                            }
                            $('#item' + self.currentItem.id)
                                .find('.pristroy_status.on_moderation').show()
                                .siblings('.pristroy_status').hide();
                            
                            self.currentItem.pristroy = data.product;
                            self.updateItem(self.currentItem.id, self.currentItem);
                        } else {
                            show_error(data.message);
                        }
                    }
            });
        };

        buttons[trans.get('cancel')] = function() {
            $(this).dialog("close");
        };

        $(this.dialogId).dialog({
            autoOpen: false,
            height: 540,
            width: 550,
            modal: true,
            buttons: buttons,
            open: function(event, ui) {
                tinymce.get('pristroy_desc').setContent($('#pristroy_desc').text());
            },
            close: function(event, ui) {
                allFields.val("").removeClass("ui-state-error");
                $('.ui-dialog-buttonset').find('button').show();
                $('.ui-dialog-buttonset').find('img:last').remove();
            }
        });
    };

    this.setStatusSold = function(id)
    {
        var self = this;
        $.post(
            '/ajax/pristroy.php',
            {action: 'updateStatus', id: id, status: 'sold'},
            function(data){
                if (data && data.status) {
                    var item = self.getItem(id);
                    item.pristroy.status = data.status;
                    self.updateItem(id, item);
                    $('#item' + id)
                        .find('.pristroy_status.sold').show()
                        .siblings('.pristroy_status').remove();
                    $('#item' + id).find('.pristroy_info a').remove();
                	$('.pristroy_btn').hide();
                }
            }, 'json'
        );
    };

    this.updateItem = function(id, item)
    {
        var self = this;
        $.each(this.items, function(i, val){
            if (val.id == id) {
                self.items[i] = item;
                return;
            }
        });
    };

    this.showRejectReason = function(id)
    {
        var reject_reason = this.getItem(id).pristroy.reject_reason;
        reject_reason = reject_reason.length ? escapeData(reject_reason) : trans.get('Default_reject_reason');
        $(this.dialogRejectReason).html(reject_reason).dialog('open');
    };

    this.getItem = function(id)
    {
        var item = {};
        $.each(this.items, function(i, val){
            if (val.id == id) {
                item = val;
                return;
            }
        });
        return item;
    };

    this.validate = function()
    {
        var result = true;
        var Qty = parseInt($("#pristroy_quantity").val());
        if (isNaN(Qty) || Qty > this.currentItem.Qty || Qty < 1) {
            show_error(trans.get('Incorrect_quantity'));
            result = false;
        }

        return result;
    }
};

$(document).ready(function(){

    Pristroy.initDialogs();

    $(document).on('click', 'a.sell_out, a.edit_pristroy_item', function(e){
        Pristroy.openDialog($(this).data('id'));
    });

    $(document).on('click', 'a.pristroy_item_is_sold', function(e){
        $(Pristroy.dialogSetStatusSold).data('item', $(this).data('id')).dialog("open");
    });

    $(document).on('click', 'a.show_reject_reason', function(e){
        Pristroy.showRejectReason($(this).data('id'));
    });
});
