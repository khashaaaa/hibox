var Sets = new Backbone.Collection();
var SetsPage = Backbone.View.extend({
    "el": ".sets_wrapper",
    "events": {
        "click #save-order": "saveItemsOrder",

        "click .ot_sortable_brands li .ot_show_deletion_dialog_modal": "deleteBrand",
        "click .ot_sortable_sellers li .ot_show_deletion_dialog_modal": "deleteSeller",
        "click .ot_sortable_items li .ot_show_deletion_dialog_modal": "deleteItem",

        "click #add-from-list-submit": "addFromList",
        "submit .js-add-vendor_form": "addSeller",
        "click .ot_add_brand_from_link .btn-primary" : "addBrand",
        "submit form": "addBrandFormSubmit",
        "click .ot_add_recommended_from_link .btn-primary" : "addItem",
        "click .ot_add_recommended_from_file #upload-btn" : "addItemsFromFile",       
        "click .ot_sortable_items li .ot_show_edit_selections_product_window" : "editProduct",        
        
        "click .cancel-btn": "resetForm",
        "click #load-more-items": "loadMoreItems",
        "click #load-more-sellers": "loadMoreSellers",
        "click .ot_clear_set": "clearSet"
    },

    addBrandFormSubmit: function()
    {
        return false;
    },
    resetForm: function(e)
    {
        var form = $(e.currentTarget).closest('form');
        $(form)[0].reset();
    },
    render: function()
    {
        return this;
    },
    initialize: function()
    {
        var self = this;
        this.render();

        if (document.getElementById('sellers-sortable') !== null) {
            var sortableElement = document.getElementById('sellers-sortable');
        } else if (document.getElementById('brands-sortable') !== null) {
            var sortableElement = document.getElementById('brands-sortable');
        }

        if (sortableElement && sortableElement !== null) {
            var sortableObject = new Sortable.create(sortableElement, {
                handle: 'i.icon-move',
                animation: 150,
                onMove: function() {
                    $('#save-order').removeClass('disabled');
                    $('#save-order').removeAttr('disabled');
                }
            });
        }

        if (document.getElementById('items-sortable') !== null) {
            var sortableElement = document.getElementById('items-sortable');
            var sortableObject = new Sortable.create(sortableElement, {
                handle: 'i.icon-move',
                animation: 150,
                onMove: function() {
                    $('#save-order').removeClass('disabled');
                    $('#save-order').removeAttr('disabled');
                },
                onSort: function(evt) {
                    if (evt.oldIndex == evt.newIndex) {
                        return;
                    }
                    //disable screen
                    $('ul.ot_sortable_items .items-overlay').show();

                    var type = $(sortableElement).attr('type');
                    var cid = $(sortableElement).attr('cid');
                    var id = $(evt.item).attr('id');
                    $.ajax({
                        url: '?cmd=Sets&do=setItemPosition',
                        type: 'POST',
                        data: {
                            "id": id,
                            "position": evt.newIndex+1,
                            "type": type,
                            "cid": cid
                        },
                    })
                        .success(function(data){
                            if ( data.result == 'ok') {
                                showMessage(trans.get("Items_order_saved"));
                            } else {
                                showError(data);
                            }
                            $('ul.ot_sortable_items .items-overlay').hide();
                        })
                        .error(function(jqXHR, textStatus, errorThrown){
                            showError();
                            $('ul.ot_sortable_items .items-overlay').hide();
                        });

                }
            });
        }

        $('.ot_add_brand_from_list').bind('shown', function() {
            $('.ot_add_brand_from_list').css('height', 'auto');
        });
        $('.ot_add_brand_from_link').bind('shown', function() {
            $('.ot_add_brand_from_link').css('height', 'auto');
        });
        $('.ot_add_brand_from_list').bind('show', function() {
            $('.ot_add_brand_from_link').css('height', '0px');
            $('.ot_add_brand_from_link').removeClass('in');
        });
        $('.ot_add_brand_from_link').bind('show', function() {
            $('.ot_add_brand_from_list').css('height', '0px');
            $('.ot_add_brand_from_list').removeClass('in');
        });

        $('.ot_add_recommended_from_file').bind('shown', function() {
            $('.ot_add_recommended_from_file').css('height', 'auto');
        });
        $('.ot_add_recommended_from_link').bind('shown', function() {
            $('.ot_add_recommended_from_link').css('height', 'auto');
        });
        $('.ot_add_recommended_from_file').bind('show', function() {
            $('.ot_add_recommended_from_link').css('height', '0px');
            $('.ot_add_recommended_from_link').removeClass('in');
        });
        $('.ot_add_recommended_from_link').bind('show', function() {
            $('.ot_add_recommended_from_file').css('height', '0px');
            $('.ot_add_recommended_from_file').removeClass('in');
        });

        initializeTinyMCE('#description');

        $('.ot_inline_help').clickover();
    },
    clearSet: function(e)
    {
        var target = $(e.currentTarget);
        var type = target.data('type');
        var contentType = target.data('contentType');
        var cid = target.data('cid');
        modalDialog(trans.get('Confirm_needed'), trans.get('really_clear_the_set'), function() {
            $.post(
                "?cmd=sets&do=clearSet",
                {
                    "type": type,
                    "contentType": contentType,
                    "cid": cid
                },
                function (data) {
                    if ( !data.error) {
                        showMessage(trans.get("set_cleared"));
                        location.reload();
                    } else {
                        showError(data.message);
                    }
                }, 'json'
            );

        });
    },
    addBrand: function(e)
    {
        var options = {
                success: function(data) {
                    $('.ot_add_brand_from_link form .btn').removeClass('disabled');
                    $('.ot_add_brand_from_link form .btn').removeAttr('disabled');
                    if (data.result == 'ok') {
                        var brands = data.brands;
                        for ( var i in brands) {
                            var brand = brands[i];
                            //create brand item
                            $('.ot_sortable_brands').append($('.ot_sortable_brands .ot-brand-template').html());
                            $('.ot_sortable_brands li:last').attr('id', brand.id);
                            $('.ot_sortable_brands li:last a').attr('href', brand.BrandUrl).attr('target', '_blank');;
                            $('.ot_sortable_brands li:last img').attr('src', brand.PictureUrl);
                            $('.ot_sortable_brands li:last img').attr('alt', escapeData(brand.name));
                            $('.ot_sortable_brands li:last h3').html(escapeData(brand.name));
                            $('.ot_sortable_brands li:last').removeClass('ot-brand-template');
                            $('.ot_sortable_brands li:last').show();
                            var listItem = $('.ot_add_brand_from_list .span3 input[value="' + brand.id + '"]');
                            if (listItem.length) {
                                var span3 = $(listItem).closest('.span3');
                                $(span3).remove();

                            }
                        }
                        $('.ot_add_brand_from_link form')[0].reset();
                    }
                    else {
                        showError(data.message);
                    }
                },
                'dataType':'json'
        };

        $('.ot_add_brand_from_link form .btn').addClass('disabled');
        $('.ot_add_brand_from_link form .btn').attr('disabled', 'disabled');
        $('.ot_add_brand_from_link form').ajaxSubmit(options);
        return false;
    },
    saveItemsOrder: async function(e)
    {
        var type = $(e.currentTarget).attr('itemType');
        var contentType = $(e.currentTarget).attr('itemContentType');
        var cid = $(e.currentTarget).attr('cid');
        if (! cid) {
            cid = 0;
        }

        if (contentType == 'Vendor' && $("#load-more-sellers").length > 0) {
            while ($("#load-more-sellers").css('display') != 'none') {
                await this.loadMoreSellers();
            }
        }

        var ids = [];
        $('.ot_sortable_cols li:visible').each(function() {
            ids.push($(this).attr('id'));
        });

        $('#save-order').addClass('btn_preloader');
        $('#save-order').button('loading');
        $.post(
                "?cmd=Sets&do=saveItemsOrder",
                {
                    "ids": ids.join(';'),
                    "type": type,
                    "contentType": contentType,
                    "cid": cid
                },
                function (data) {
                    $('#save-order').removeClass('btn_preloader');
                    $('#save-order').button('reset');
                    if (! data.error) {
                        showMessage(trans.get("Items_order_saved"));
                        setTimeout(function() {
                        	$('#save-order').addClass('disabled');
                        	$('#save-order').attr('disabled', 'disabled');
                        },100);
                    } else {
                        showError(data.message);
                    }
                }, 'json'
            );
    },
    deleteSeller: function(e)
    {
        var li = $(e.currentTarget).closest('li');
        var id = $(li).attr('id');

        modalDialog(trans.get('Confirm_needed'), trans.get('Really_delete_this_seller_from_sets'), function() {
            $.post(
                    "?cmd=Sets&do=deleteItem",
                    {
                        "id": id,
                        "contentType": "Vendor",
                        "type": "Best"
                    },
                    function (data) {
                        if ( !data.error) {
                            // add item to list
                            $(li).remove();
                            showMessage(trans.get("Seller_deleted"));
                        } else {
                            showError(data.message);
                        }
                    }, 'json'
                );

        });

    },
    deleteBrand: function(e)
    {
        var li = $(e.currentTarget).closest('li');
        var id = $(li).attr('id');

        modalDialog(trans.get('Confirm_needed'), trans.get('Really_delete_this_brand_from_sets'), function() {
            $.post(
                    "?cmd=Sets&do=deleteItem",
                    {
                        "id": id,
                        "contentType": "Brand",
                        "type": "Best"
                    },
                    function (data) {
                        if ( !data.error) {
                            // add item to list
                            $('span.no-more-brands').hide();
                            $('.ot_add_brand_from_list .row-fluid').append($('.ot_add_brand_from_list .brand-item-template').html());
                            $('.ot_add_brand_from_list .span3:last input').val(id);
                            $('.ot_add_brand_from_list .span3:last img').attr('src', $('img', li).attr('src'));
                            $('.ot_add_brand_from_list .span3:last .brand-name').html($('h3', li).html());
                            $('.ot_add_brand_from_list .span3:last').show();
                            $(li).remove();
                            showMessage(trans.get("Brand_deleted"));
                        } else {
                            showError(data.message);
                        }
                    }, 'json'
                );

        });
    },
    deleteItem: function(e)
    {
        var li = $(e.currentTarget).closest('li');
        var type = $(li).attr('type');
        var id = $(li).attr('id');
        var cid = $(li).attr('cid');
        if(! cid) {
            cid = 0;
        }

        modalDialog(trans.get('Confirm_needed'), trans.get('Really_delete_this_item_from_sets'), function() {
            $.post(
                    "?cmd=Sets&do=deleteItem",
                    {
                        "id": id,
                        "contentType": "Item",
                        "type": type,
                        "cid": cid
                    },
                    function (data) {
                        if ( !data.error) {
                            $(li).remove();
                            showMessage(trans.get("Item_deleted"));
                        } else {
                            showError(data.message);
                        }
                    }, 'json'
                );

        });

    },
    addFromList: function(e) {
        var form = $(e.currentTarget).closest('form');
        var ids = [];
        $('input[type="checkbox"]:checked').each(function() {
            var id = $(this).val();
            ids.push(id);
        });
        if (ids.length > 0) {
            $('#ids', form).val(ids.join(';'));
            var options = {
                    success: function(data) {
                        $('.cancel-btn', form).removeClass('disabled');
                        $('#add-from-list-submit', form).button('reset');

                        if (data.result = 'ok') {
                            var brands = data.brands;
                            for ( var i in brands) {
                                var brand = brands[i];
                                //create brand item
                                $('.ot_sortable_brands').append($('.ot_sortable_brands .ot-brand-template').html());
                                $('.ot_sortable_brands li:last').attr('id', brand.id);
                                $('.ot_sortable_brands li:last a').attr('href', brand.BrandUrl).attr('target', '_blank');
                                $('.ot_sortable_brands li:last img').attr('src', brand.PictureUrl);
                                $('.ot_sortable_brands li:last img').attr('alt', escapeData(brand.name));
                                $('.ot_sortable_brands li:last h3').html(escapeData(brand.name));
                                $('.ot_sortable_brands li:last').removeClass('ot-brand-template');
                                $('.ot_sortable_brands li:last').show();
                                var checkbox = $('.ot_add_brand_from_list input[type="checkbox"][value="'+brand.id+'"]');
                                var span3 = $(checkbox).closest('.span3');
                                $(span3).remove();
                            }
                            var cnt = $('form.well input[type="checkbox"]:visible').length;
                            if (cnt == 0) {
                                $('span.no-more-brands').show();
                                $('#add-from-list-submit', form).removeAttr('reset');
                            }
                        }
                        $(form)[0].reset();
                        $('#add-from-list-submit', form).removeAttr('reset');
                    },
                    'dataType':'json'
            };

            $('.cancel-btn', form).addClass('disabled');
            $('#add-from-list-submit', form).button('loading');

            $(form).ajaxSubmit(options);

            return true;
        } else {
            showError(trans.get('Brands_not_selected'));
            return false;
        }
    },
    addSeller: function (e)
    {
        e.preventDefault();
        $('.js-add-vendor').button('loading');
        var form = $('.js-add-vendor_form').get(0);
        var url = $('#sellerId', form).val();
        var action = "?cmd=sets&do=addSetsSeller";

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
                    $('.js-add-vendor').button('reload');
                } else {
                    if (data.result == 'ok') {
                        showMessage(trans.get('Notify_success'));
                        $('.js-add-vendor').button('reload');
                        var sellers = data.sellers;
                        for ( var i in sellers) {
                            var seller = sellers[i];
                            if ( $('.ot_sortable_sellers li[id="'+seller.id+'"]').length == 0 ) {
                                var displayName = seller.displayName ? seller.displayName : seller.name;

                                $('.ot_sortable_sellers').prepend($('.ot_sortable_sellers .ot-seller-template').html());
                                $('.ot_sortable_sellers li:first .ot_show_edit_seller').attr('href', seller.editUrl);
                                $('.ot_sortable_sellers li:first').attr('id', seller.id);
                                $('.ot_sortable_sellers li:first').attr('display-name', displayName);
                                $('.ot_sortable_sellers li:first img').attr('src', seller.PictureUrl);
                                $('.ot_sortable_sellers li:first img').attr('alt', seller.name);
                                $('.ot_sortable_sellers li:first a.img_preview').attr('href', seller.url);
                                $('.ot_sortable_sellers li:first h3').html(displayName);
                                $('.ot_sortable_sellers li:first').removeClass('ot-seller-template');
                                $('.ot_sortable_sellers li:first').show();
                            }
                        }
                        $('.ot_add_seller_to_selection form')[0].reset();
                    }

                }
            },
            cache: false,
            contentType: false,
            processData: false
        });
    },

    checkVendor: function (url, name, alias) {
        $.post(
            "?cmd=Sets&do=checkVendor",
            {
                "name" : name,
                "sellerId" : url,
                "alias" : alias,
            },
            function (data) {
                if (data.error) {
                    showError(data);
                    return true;
                } else {
                    return false;
                }
            });
    },
    addItem: function(e)
    {
        var form = $(e.currentTarget).closest('form');
        var url = $('#urlId', form).val();
        var cid = $('#cid').val(); // категория для подборки

        if (url.length == 0) {
            showError(trans.get("Item_url_or_id_is_required"));
            return false;
        }

        var options = {
                success: function(data) {
                    $('.ot_add_recommended_from_link form .btn').removeClass('disabled');
                    $('.ot_add_recommended_from_link form .btn').removeAttr('disabled');

                    if (data.items) {
                        var items = data.items;
                        for ( var i in items) {
                            var item = items[i];
                            if ( $('.ot_sortable_items li[id="'+item.id+'"]').length == 0 ) {                                
                                $('.ot_sortable_items').prepend($('.ot_sortable_items .ot-item-template').html());
                                $('.ot_sortable_items li:first').attr('id', item.id);
                                $('.ot_sortable_items li:first').attr('cid', cid);
                                $('.ot_sortable_items li:first').attr('originalPicture', item.MainPictureUrl);
                                $('.ot_sortable_items li:first img').attr('src', item.MainPictureUrl);
                                $('.ot_sortable_items li:first img').attr('alt', item.title);
                                $('.ot_sortable_items li:first h3').html(item.title);
                                $('.ot_sortable_items li:first .img_preview').attr('href', '/?p=item&id=' + item.id);
                                $('.ot_sortable_items li:first').removeClass('ot-item-template');
                                $('.ot_sortable_items li:first').show();
                            }
                        }
                        $('.ot_add_recommended_from_link form')[0].reset();
                        showMessage(trans.get('Notify_success'));
                    }

                    for (var i in data.errors) {
                        showError(data.errors[i]);
                    }
                },
                'dataType':'json'
        };
        $('.ot_add_recommended_from_link form .btn').addClass('disabled');
        $('.ot_add_recommended_from_link form .btn').attr('disabled', 'disabled');

        $('.ot_add_recommended_from_link form').ajaxSubmit(options);

        return true;
    },
    addItemsFromFile: function(e)
    {
        var form = $(e.currentTarget).closest('form');
        var file = $('#itemsFile', form).val();
        if (file.length == 0 ) {
            showError(trans.get("Items_file_required"));
            return false;
        }
        var cid = $('#cid').val(); // категория для подборки

        var options = {
                success: function(data) {
                    $('.btn', form).removeClass('disabled');
                    $('.btn', form).removeAttr('disabled');

                    if (data.items) {
                        var items = data.items;
                        for ( var i in items) {
                            var item = items[i];
                            if ( $('.ot_sortable_items li[id="'+item.id+'"]').length == 0 ) {
                                $('.ot_sortable_items').prepend($('.ot_sortable_items .ot-item-template').html());
                                $('.ot_sortable_items li:first').attr('id', item.id);
                                $('.ot_sortable_items li:first').attr('cid', cid);
                                $('.ot_sortable_items li:first img').attr('src', item.MainPictureUrl);
                                $('.ot_sortable_items li:first img').attr('alt', item.title);
                                $('.ot_sortable_items li:first h3').html(item.title);
                                $('.ot_sortable_items li:first .img_preview').attr('href', '/?p=item&id=' + item.id);
                                $('.ot_sortable_items li:first').removeClass('ot-item-template');
                                $('.ot_sortable_items li:first').show();
                            }
                        }
                        $(form)[0].reset();
                        showMessage(trans.get('Notify_success'));
                    }

                    for (var i in data.errors) {
                        showError(data.errors[i]);
                    }
                },
                'dataType':'json'
        };
        $('.btn',form).addClass('disabled');
        $('.btn',form).attr('disabled', 'disabled');

        $(form).ajaxSubmit(options);

    },
    editProduct: function(e)
    {
        var li = $(e.currentTarget).closest('li');
        var name = $('h3', li).text();
        var id = $(li).attr('id');
        var picture = $(li).attr('picture');
        var originalPicture = $(li).attr('originalPicture');
        
        var form = $('.ot_edit_selections_product_dialog_window form');
        var language = $('#currentLang').data('lang');
        $('.ot_edit_selections_product_dialog_window #currentLang').val(language);
        $('.ot_edit_selections_product_dialog_window #itemId').val(id);
        $('.ot_edit_selections_product_dialog_window #displayName').val(name);

        $('.ot_edit_selections_product_dialog_window .editableform-loading').show();
        
        $('.ot_edit_selections_product_dialog_window #originalPicture').val(originalPicture);
        if ((typeof picture !== 'undefined') && (picture.length > 0)) {
            $('.ot_edit_selections_product_dialog_window #existing_image').val(picture);            
            $('.ot_edit_selections_product_dialog_window .thumbnail-placeholder img').attr('src', picture);
        } else {
            $('.ot_edit_selections_product_dialog_window #existing_image').val('');
            $('.ot_edit_selections_product_dialog_window .thumbnail-placeholder img').attr('src', '');            
        }
        $('.ot_edit_selections_product_dialog_window .editableform-loading').show();

        $('.ot_edit_selections_product_dialog_window .btn-primary').addClass('disabled');

        $.post(
                "?cmd=Sets&do=getItemInfo",
                {
                    "id" : id,
                    "language" : language
                },
                function (data) {
                    $('.ot_edit_selections_product_dialog_window .btn-primary').removeClass('disabled');

                    if (data.result == 'ok') {
                        $('.ot_edit_selections_product_dialog_window #displayName').attr('value', data.title);
                        $('.ot_edit_selections_product_dialog_window #description').val(data.description);
                        if (tinyMCE.editors.length > 0) {
                            tinyMCE.editors[0].setContent(data.description);
                        }
                        $('.ot_edit_selections_product_dialog_window .editableform-loading').hide();

                    }
                }, 'json'
            );


        var width = jQuery(window).width();
        var left = (width-690) / 2;
        $('.ot_edit_selections_product_dialog_window').css('width', '690px');
        $('.ot_edit_selections_product_dialog_window').css('left', left+'px');
        $('.ot_edit_selections_product_dialog_window').css('margin-left', '0px');
        $('.ot_edit_selections_product_dialog_window .delete-existing-image').click(function() {            
            $('.ot_edit_selections_product_dialog_window #existing_image').val('del');            
            $('.ot_edit_selections_product_dialog_window .thumbnail-placeholder img').attr('src', '');
        });
        $('.ot_edit_selections_product_dialog_window .modal-footer .btn-primary').unbind('click').click(function() {
            $('.ot_edit_selections_product_dialog_window .btn').addClass('disabled');
            $('.ot_edit_selections_product_dialog_window .btn').attr('disabled', 'disabled');

            if (tinyMCE.editors.length > 0) {
                $('#description', form).val(tinyMCE.editors[0].getContent());
            }

            var options = {
                    success: function(data) {
                        $('.ot_edit_selections_product_dialog_window .btn').removeClass('disabled');
                        $('.ot_edit_selections_product_dialog_window .btn').removeAttr('disabled');

                        if (data.result == 'ok') {
                            var desc = $('#description', form).val();
                            var title = $('#displayName', form).val();
                            $('.item-description', li).html(desc);
                            $('h3', li).html(title);
                            $(li).attr('picture', data.picture);
                            if (data.picture != "") {
                                $('#img_preview', li).attr('src', data.picture);
                            }
                            if ($('#existing_image', li).val() == 'del') {
                                $(li).attr('picture', '');
                            }
                            $('.ot_edit_selections_product_dialog_window').modal('hide');
                            //$(form)[0].reset();
                        }
                        else {
                            showError(data.message);
                        }
                    },
                    'dataType':'json'
            };
            $(form).ajaxSubmit(options);
        });

        $('.ot_edit_selections_product_dialog_window').on('hide', function() {
            $('.ot_edit_selections_product_dialog_window form input[name!="type"]').val('');
            if (tinyMCE.editors.length > 0) {
                tinyMCE.editors[0].setContent('');
            }

        });

        $('.ot_edit_selections_product_dialog_window').modal('show');
        
    },
    loadMoreItems: function()
    {
    	var type = $('ul.ot_sortable_items').attr('type');
    	var cid = $('ul.ot_sortable_items').attr('cid');
    	var offset = $('ul.ot_sortable_items li:visible').length;
    	var language = $("#currentLang").val();
    	$('a#load-more-items').hide();
    	$('#load-more-preloader').show();
        $.post(
                "?cmd=Sets&do=moreItems",
                {
                    "type": type,
                    "cid": cid,
                    "offset": offset,
                    "language": language
                },
                function (data) {
                	$('#load-more-preloader').hide();
                    if (! data.error) {
                    	$('ul.ot_sortable_items').append(data.html);
                    	var count = $('ul.ot_sortable_items li:visible').length;
                    	var totalCount = $('#total-count').val();
                    	if (count < totalCount) {
                    		$('a#load-more-items').show();
                    	}
                    }
                }, 'json'
            );

    },
    loadMoreSellers: function()
    {
        var type = $('ul.ot_sortable_sellers').attr('type');
        var cid = $('ul.ot_sortable_sellers').attr('cid');
        var offset = $('ul.ot_sortable_sellers li:visible').length;

        $('a#load-more-sellers').hide();
        $('#load-more-preloader').show();

        return new Promise(function(succeed) { 
            $.post(
                "?cmd=Sets&do=moreSellers",
                {
                    "type": type,
                    "cid": cid,
                    "offset": offset
                },
                function (data) {
                    $('#load-more-preloader').hide();
                    if (! data.error) {
                        $('ul.ot_sortable_sellers').append(data.html);
                        var count = $('ul.ot_sortable_sellers li:visible').length;
                        var totalCount = $('#total-count').val();
                        if (count < totalCount) {
                            $('a#load-more-sellers').show();
                        }
                        succeed();
                    }
                }, 'json'
            )
        });
    }
});

$(function() {
    var U = new SetsPage();
});
