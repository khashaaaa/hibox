var OrderItemsUploadBlock = Backbone.View.extend({
    "el": ".order-view-wrapper",
    "events": {
        "click .addOrderItemPhotosBtn"          : "addOrderItemPhotosAction",
        "mouseup .removeGalleryImageBtn"        : "removeOrderItemImageAction",
        "click .carousel-inner .ot_delete_img"  : "removeOrderItemImageAction",
        "click a[data-gallery=gallery]"         : "changeCurrentViewableItem",
    },
    'currentViewableItem': null,
    'webcam': null,
    'isWebcamReady': false,
    render: function()
    {
        this.initWebcam();

        return this;
    },
    initialize: function()
    {
        this.render();
    },
    initWebcam: function(){
        if ('undefined' === typeof window.webcam) {
            showError('Webcam object has not been set yet. Make sure js/ot-order-view/webcam/webcam.js is included.');
        }
        var self = this;
        self.webcam = window.webcam;
        self.webcam.set_swf_url('js/ot-order-view/webcam/webcam.swf');
        self.webcam.set_shutter_sound(true, 'js/ot-order-view/webcam/shutter.mp3');
        self.webcam.set_quality(80); // Качество фотографий JPEG
        var webcamTab = this.$('#web-cam-photo');
        var screen = webcamTab.find('#screen');
        var shootBtn = webcamTab.find('#shootButton');
        var uploadBtn = webcamTab.find('#uploadButton');
        var cancelBtn = webcamTab.find('#cancelButton');
        var preloader = webcamTab.find('.preloader');
        screen.html(
            self.webcam.get_html(screen.width(), screen.height())
        );
        shootBtn.on('click', function(){
            if (! self.isWebcamReady) {
                return false;
            }
            self.webcam.freeze();
            shootBtn.addClass('hide');
            uploadBtn.removeClass('hide');
            cancelBtn.removeClass('hide');
            return false;
        });
        uploadBtn.on('click', function(){
            if (! self.isWebcamReady) {
                return false;
            }
            shootBtn.addClass('hide');
            uploadBtn.addClass('hide');
            cancelBtn.addClass('hide');
            preloader.removeClass('hide');
            self.webcam.set_api_url(
                '?cmd=orders&do=uploadOrderItemImage&orderId=' + Order.id +
                '&itemId=' + webcamTab.find('input[name=itemId]').val() + '&type=webcam'
            );
            self.webcam.upload();
            self.webcam.reset();
            return false;
        });
        cancelBtn.on('click', function(){
            self.webcam.reset();
            shootBtn.removeClass('hide');
            cancelBtn.addClass('hide');
            uploadBtn.addClass('hide');
            return false;
        });
        webcamTab.find('.settings').click(function(){
            if (! self.isWebcamReady) {
                return false;
            }
            self.webcam.configure('camera');
        });
        self.webcam.set_hook('onLoad', function () {
            self.isWebcamReady = true;
            shootBtn.removeClass('hide');
            cancelBtn.addClass('hide');
            uploadBtn.addClass('hide');
        });
        self.webcam.set_hook('onComplete', function (data) {
            data = $.parseJSON(data);
            if (! data.error) {
                self.updateImageCarousel(data.urls, Order.id, webcamTab.find('input[name=itemId]').val());
                showMessage(trans.get('Image_shot_was_uploaded_successfully'));
            } else {
                showError(data);
            }
            preloader.addClass('hide');
            shootBtn.removeClass('hide');
        });
        self.webcam.set_hook('onError', function (e) {
            screen.html(e);
        });
    },
    changeCurrentViewableItem: function(ev){
        var itemRow = $(ev.target).parents('.ot_order_product_item:first');
        this.currentViewableItem = OrderItems.get(itemRow.data('id'));
    },
    removeOrderItemImageAction: function(ev){
        ev.preventDefault();
        var target = $(ev.target);
        if (target.hasClass('ot_delete_img')) {
            var imageUrl = target.parent().find('a').attr('href');
            this.changeCurrentViewableItem(ev);
        } else {
            var btn = $(ev.target).is('button') ? $(ev.target) : $(ev.target).parents('button:first');
            var imageUrl = btn.parents('.modal-gallery:first').find('.modal-image img:last').attr('src');
        }
        if ('object' === typeof this.currentViewableItem) {
            imageUrl = decodeURIComponent(imageUrl);
            var item = this.currentViewableItem;
            modalDialog(
                trans.get('Removing_item_images'),
                trans.get('Removing_item_images_confirmation'),
                function (dialog) {
                    if ('undefined' !== typeof btn) {
                        btn.button('loading').find('i').attr('class', 'ot-preloader-micro');
                    }
                    $.post(
                        '?cmd=orders&do=removeOrderItemImage',
                        {
                            'itemId'    : item.id,
                            'orderId'   : Order.id,
                            'imageUrl'  : imageUrl,
                            'comment'   : item.get('operatorcomment'),
                            'quantity'  : item.get('qty'),
                            'status'    : item.get('statusid')
                        },
                        function (data) {
                            if ('undefined' !== typeof btn) {
                                btn.button('reset').find('i').attr('class', 'icon-remove-sign');
                            }
                            if (! data.error) {
                                var itemRow = $('.ot_order_product_item[data-id=' + item.id + ']');
                                var carousel = itemRow.find('.ot_product_img_carousel');
                                if (carousel.find('li').length == 1) {
                                    carousel.empty();
                                    if ($('#modal-gallery').is(':visible')) {
                                        $('#modal-gallery').find('[data-dismiss="modal"]').trigger('click');
                                        $('.modal-backdrop').remove();
                                    }
                                } else {
                                    var imageToRemove = carousel.find('a[href="' + imageUrl + '"]').closest('li');
                                    var newActiveImage = imageToRemove.next().is('li') ?
                                        imageToRemove.next() :
                                        imageToRemove.prev();
                                    newActiveImage.addClass('active');
                                    imageToRemove.remove();
                                    if ($('#modal-gallery').is(':visible')) {
                                        $('#modal-gallery').find('[data-dismiss="modal"]').trigger('click');
                                        $('.modal-backdrop').remove();
                                        setTimeout(function(){
                                            newActiveImage.find('a').trigger('click');
                                        }, 200);
                                    }
                                }
                                if ('undefined' !== typeof data.comment) {
                                    item.set('operatorcomment', data.comment);
                                    $.get('templates/orders/item/comments.html?' + Math.random(), function (tpl) {
                                        var comment = {
                                            'name': trans.get('Operator'),
                                            'text': escapeData(data.comment)
                                        };
                                        var commentHtml = _.template(tpl, {
                                            'comments': [comment], 'useWrapper': false, 'operatorComment': true, 'itemId': item.id
                                        });
                                        var commentsBlock = itemRow.find('.item-comments-block');
                                        var commentsList = commentsBlock.find('.comments-list');
                                        commentsList.find('blockquote:last').remove();
                                        commentsList.append(commentHtml);
                                        commentsBlock.find('.addOperatorCommentBtn').remove();
                                        commentsBlock.find('textarea').val(comment.text)
                                    });
                                }
                                showMessage(data.message ? data.message : trans.get('Notify_success'));
                            } else {
                                showError(data);
                            }
                        },
                        'json'
                    );
                },
                { confirm: trans.get('Remove') }
            );
        }
        return false;
    },
    addOrderItemPhotosAction: function(ev){
        ev.preventDefault();
        var self = this;
        var target = $(ev.target);
        var itemRow = target.parents('.ot_order_product_item:first');
        var item = OrderItems.get(itemRow.data('id'));
        var modal = this.$('.addOrderItemPhotos').modal();
        var uploadPhotoTab = modal.find('#upload-photo');
        var linkPhotoTab = modal.find('#link-photo');
        modal.find('input[name=itemId]').val(item.id);
        linkPhotoTab.find('input[name^=imagesLinks]').off().on('blur', function(){
            if ($(this).val()) {
                $(this).next().html('<img src="'+$(this).val()+'">').show();
            }
        });
        linkPhotoTab.find('span[type=button]').off().on('click', function(){
            var lastBlock = $(this).parent().find('.imageLinkBlock:last');
            var clone = lastBlock.clone(true);
            clone.find('input').val('');
            clone.find('.thumbnail').empty().hide();
            lastBlock.after(clone);
        });
        linkPhotoTab.find('button[type=button]').off().on('click', function(){
            var btn = $(this);
            if (btn.hasClass('disabled')) {
                return;
            }
            var form = linkPhotoTab.find('form');
            var emptyForm = true;
            var emptyFields = [];
            var imagesLinks = [];
            form.find('input[name^=imagesLinks]').each(function(i, val){
                if ($.trim($(this).val()).length) {
                    emptyForm = false;
                    imagesLinks.push($.trim($(this).val()));
                } else {
                    emptyFields.push(val);
                }
            });
            if (emptyForm) {
                showError(trans.get('No_links_to_upload'));
                return;
            } else {
                $.each(emptyFields, function(){
                    $(this).parent().remove();
                });
            }
            btn.button('loading');
            $.post(
                form.attr('action'),
                {
                    'type'          : 'link',
                    'imagesLinks'   : imagesLinks,
                    'orderId'       : Order.id,
                    'itemId'        : item.id,
                    'comment'       : item.get('operatorcomment'),
                    'quantity'      : item.get('qty'),
                    'status'        : item.get('statusid')
                },
                function (data) {
                    btn.button('reset');
                    if (! data.error) {
                        linkPhotoTab.find('.imageLinkBlock:gt(0)').remove();
                        linkPhotoTab.find('.imageLinkBlock input').val('');
                        linkPhotoTab.find('.imageLinkBlock .thumbnail').empty().hide();
                        if ('undefined' !== typeof data.comment) {
                            item.set('operatorcomment', data.comment);
                            $.get('templates/orders/item/comments.html?' + Math.random(), function (tpl) {
                                var comment = {
                                    'name': trans.get('Operator'),
                                    'text': escapeData(data.comment)
                                };
                                var commentHtml = _.template(tpl, {
                                    'comments': [comment], 'useWrapper': false, 'operatorComment': true, 'itemId': item.id
                                });
                                var commentsBlock = itemRow.find('.item-comments-block');
                                var commentsList = commentsBlock.find('.comments-list');
                                commentsList.find('blockquote:last').remove();
                                commentsList.append(commentHtml);
                                commentsBlock.find('.addOperatorCommentBtn').remove();
                                commentsBlock.find('textarea').val(comment.text)
                            });
                            self.updateImageCarousel(data.urls, Order.id, item.id);
                        }
                        showMessage(trans.get('Links_saved_successfully'));
                    } else {
                        showError(data);
                    }
                }, 'json'
            );
            return false;
        });
        uploadPhotoTab.find('#uploadMoreBtn').off().on('click', function(){
            var button = $(this);
            $.get('templates/upload/file.html?' + Math.random(), function (tpl) {
                button.parent().find('.uploadbox:last').after(_.template(tpl));
            });
        });
        uploadPhotoTab.find('#sendToServerBtn').off().on('click', function(){
            var btn = $(this);
            if (btn.hasClass('disabled')) {
                return;
            }
            var form = uploadPhotoTab.find('form');
            form.find('input[name=itemId]').val(item.id);
            form.find('input[name=orderId]').val(Order.id);
            var emptyForm = true;
            var emptyFields = [];
            form.find('input[name^=files]').each(function(i, val){
                if ($(this).val().length) {
                    emptyForm = false;
                } else {
                    emptyFields.push(val);
                }
            });
            if (emptyForm) {
                showError(trans.get('No_files_to_upload'));
                return;
            } else {
                $.each(emptyFields, function(){
                    $(this).parents('.uploadbox:first').remove();
                });
            }
            btn.button('loading');
            form.ajaxSubmit({
                url     :   $(this).attr('action'),
                type    :   'POST',
                dataType:   'json',
                success :   function(data) {
                    btn.button('reset');
                    if (! data.error) {
                        if (data.urls) {
                            self.updateImageCarousel(data.urls, Order.id, item.id);
                        }
                        form.find('.uploadbox').removeAttr('data-provides').find('.btn').remove();
                        form.find('.uploadbox').find('.fileupload-new').remove();
                        form.find('.uploadbox').filter(':not(:last)').removeClass('clearfix');
                        showMessage(trans.get('Files_uploaded_successfully'));
                    } else {
                        showError(data);
                    }
                 }
            });
        });
        modal.off('hidden').on('hidden', function(){
            uploadPhotoTab.find('.fileupload').remove();
            $.get('templates/upload/file.html?' + Math.random(), function (tpl) {
                uploadPhotoTab.find('form').append(_.template(tpl));
            });
            linkPhotoTab.find('.imageLinkBlock:gt(0)').remove();
        }).off('shown').on('shown', function(){
            uploadPhotoTab.find('.uploadbox:last').addClass('clearfix');
        });
        return false;
    },
    updateImageCarousel: function (urls, orderId, itemId) {
        $.get('templates/orders/item/image-carousel.html?' + Math.random(), function (tpl) {
            var itemRow = $('.ot_order_product_item[data-id=' + itemId + ']');
            var carousel = itemRow.find('.ot_product_img_carousel');
            var oneImageOnly = !! carousel.children().length;
            var imagesHtml = _.template(tpl, {
                'urls': urls, 'oneImageOnly': oneImageOnly, 'orderId': orderId, 'itemId': itemId
            });
            if (oneImageOnly) {
                carousel.find('ul').append(imagesHtml);
            } else {
                carousel.replaceWith(imagesHtml);
                carousel = itemRow.find('.ot_product_img_carousel');
                carousel.find('li:first').addClass('active');
                carousel.carousel({ interval: false });
            }
        });
    }
});

$(document).ready(function(){
    new OrderItemsUploadBlock();
});
