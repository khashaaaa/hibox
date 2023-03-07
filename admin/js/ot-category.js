var Sets = new Backbone.Collection();
var RestrictionPage = Backbone.View.extend({
    "el": "body",
    "events": {
        "click .add-recommend-category": "addRecommendedCategory",
        "click .ot_show_deletion_dialog_modal": "deleteRecommendedCategory",
        "click .ot_show_edit_category": "editCategory",
        "click .show-categories": "showCategories",
        "click #load-more-categories": "moreRecommendedCategories",
        "click .ot_clear_set": "clearSetRecommendedCategories",
        "click .delete-existing-image": "delImageRecommendedCategory"
    },
    "rootCategory": 'Root_category',
    "lastLoadedNode": null,
    render: function () {
        return this;
    },
    initialize: function () {
        var self = this;
        this.render();
        if (document.getElementById('category-sortable') !== null) {
            var sortableElement = document.getElementById('category-sortable');
            var sortableObject = new Sortable.create(sortableElement, {
                handle: 'i.icon-move',
                animation: 150,
                onMove: function () {
                    $('#save-order').removeClass('disabled');
                    $('#save-order').removeAttr('disabled');
                },
                onSort: function (evt) {
                    if (evt.oldIndex == evt.newIndex) {
                        return;
                    }
                    //disable screen
                    $('ul.ot_sortable_items .items-overlay').show();

                    var type = $(sortableElement).attr('type');
                    var cid = $(sortableElement).attr('cid');
                    var id = $(evt.item).attr('id');
                    $.ajax({
                        url: '?cmd=Sets&do=setRecommendedCategoryPosition',
                        type: 'POST',
                        data: {
                            "id": id,
                            "position": evt.newIndex + 1,
                            "type": type,
                            "cid": cid
                        }
                    })
                        .success(function (data) {
                            if (data.result == 'ok') {
                                showMessage(trans.get("Items_order_saved"));
                            } else {
                                showError(data);
                            }
                            $('ul.ot_sortable_items .items-overlay').hide();
                        })
                        .error(function () {
                            showError();
                            $('ul.ot_sortable_items .items-overlay').hide();
                        });
                }
            });
        }
        $('.btn.btn-success.btn-tiny.btn-file').click(function () {
            $('.fileupload-preview.fileupload-exists.thumbnail.thumbnail-mini').show();
            $('.fileupload-preview.fileupload-exists.thumbnail.thumbnail-mini').css('display', 'inline-block');
            $('.fileupload-new.thumbnail.thumbnail-mini').css('display', 'none');
        });

        $('.ot_edit_selections_category_dialog_window').on('hide', function () {
            $('.ot_edit_selections_category_dialog_window form input[name!="type"]').val('');
          $('.fileupload-preview.fileupload-exists.thumbnail.thumbnail-mini').find('img').attr('src','');
          $('.fileupload-preview.fileupload-exists.thumbnail.thumbnail-mini').hide();
          $('.fileupload-new.thumbnail.thumbnail-mini').css('display', 'inline-block');
        });
    },
    initJSTree: function (body) {
        var self = this;
        if (typeof(CategoriesCategories) != 'undefined') {
            $("#jstree", body)
                .bind("before.jstree", function (e, data) {
                })
                .bind("open_node.jstree", function (e, data) {
                    data.inst.select_node("#phtml_2", true);
                })
                .jstree({
                    // Подключаем плагины
                    "plugins": ["themes", "json_data", "ui", "crrm", "dnd", "search", "types", "sort"],
                    "themes": {
                        "theme": "classic",
                        "dots": true,
                        "icons": true
                    },
                    "sort": function (a, b) {
                        return parseInt($(a).attr('i')) > parseInt($(b).attr('i')) ? 1 : -1;
                    },
                    "json_data": {
                        "data": self.getPreparedCategories(CategoriesCategories.models), // 1
                        'correct_state': true,
                        'progressive_render': true,
                        'progressive_unload': true,
                        "ajax": {
                            "url": '?cmd=Categories&do=getCategories',
                            // the `data` function is executed in the instance's scope
                            // the parameter is the node being loaded
                            // (may be -1, 0, or undefined when loading the root nodes)
                            "data": function (node) {
                                // the result is fed to the AJAX request `data` option
                                return {
                                    "parentId": node.attr ? node.attr("id") : 0
                                };
                            },
                            "success": function (data) {
                                if (data.categories) {
                                    if (data.categories.length) {
                                        return self.getPreparedCategories(data.categories);
                                    } else if (self.lastLoadedNode) {
                                        self.lastLoadedNode.removeClass('jstree-open').addClass('jstree-leaf');
                                    }
                                } else {
                                    showError(data.message ? data.message : trans.get('Notify_error'));
                                }
                            }
                        }
                    }
                })
                .one("reopen.jstree", function (event, data) {
                })
                // 3) but if you are using the cookie plugin or the UI `initially_select` option:
                .one("reselect.jstree", function (event, data) {
                })
                .bind("select_node.jstree", function (event, data) {
                    var selectedCat = $('.jstree-clicked').parent().attr('name');
                    var selectedCatId = $('.jstree-clicked').parent().attr('id');
                    $('.add-recommend-categories').val(selectedCat);
                    $('.recommend-categories_id').val(selectedCatId);
                })
                .bind("load_node.jstree", function (event, data) {
                    if (undefined !== data) {
                        var item = data.args[0][0];
                        if (undefined != item) {
                            self.lastLoadedNode = data.rslt.obj;
                        }
                    }
                });
        }
    },
    showCategories: function (e) {
        var self = this;
        e.preventDefault();
        var content = '<div id="jstree"></div>';
        modalDialog(trans.get('Check_category'), content, function (body) {
            },
            {confirm: trans.get('Check'), cancel: trans.get('Cancel')}, function (body) {
                self.initJSTree(body)
            });
        return false;
    },
    addRecommendedCategory: function (e) {
        e.preventDefault();
        var target = this.$(e.target);
        var categoryId = $('.recommend-categories_id').val();
        if (categoryId == '') {
            showError(trans.get('Item_not_entered'));
            return false;
        }
        var $button = target.button('loading');
        var action = target.data('action');
        var type = target.data('type');
        $.post(
            action,
            {
                categoryId: categoryId,
                type: type
            },
            function (data) {
                if (!data.error) {
                    showMessage(trans.get('Category_added'));
                    location.reload();
                    // window.location.href = target.data('link');
                } else {
                    $button.button('reset');
                    showError(data.message);
                }
            }, 'json'
        );
        return false;
    },
    clearSetRecommendedCategories: function () {
        modalDialog(trans.get('Confirm_needed'), trans.get('really_clear_the_set'), function () {
            $.post(
                "?cmd=sets&do=clearSetRecommendedCategories",
                function (data) {
                    if (!data.error) {
                        showMessage(trans.get("set_cleared"));
                        location.reload();
                    } else {
                        showError(data.message);
                    }
                }, 'json'
            );

        });
    },
    moreRecommendedCategories: function () {
        var offset = $('ul.ot_sortable_categories li:visible').length;
        var language = $('#activeLanguage').val();
        var action = "?cmd=Sets&do=moreRecommendedCategories";

        $('#load-more-categories').hide();
        $('#load-more-preloader').show();
        $.post(
            action,
            {
                "offset": offset,
                "language":language
            },
            function (data) {
                $('#load-more-preloader').hide();
                if (!data.error) {
                    $('ul.ot_sortable_categories').append(data.html);
                    var count = $('ul.ot_sortable_categories li:visible').length;
                    var totalCount = $('#total-count').val();
                    if (count < totalCount) {
                        $('a#load-more-categories').show();
                    }
                }
                else{
                    showError(data.message);
                }
            }, 'json'
        );

    },
    editCategory: function (e) {
        var li = $(e.currentTarget).closest('li');
        var id = $(li).attr('id');
        var picture = $(li).attr('picture');
        var originalPicture = $(li).attr('originalPicture');
        var form = $('.ot_edit_selections_category_dialog_window form');
        var language = $('#currentLang').data('lang');
        var categoryName = li.attr('value');
        $('.modal-header h3').text(trans.get('Edit_category')+' '+categoryName);
        if (picture === "/i/noimg.png") {
            $("#deleteImage").hide();
        }
        $('.ot_edit_selections_category_dialog_window #currentLang').val(language);
        $('.ot_edit_selections_category_dialog_window #itemId').val(id);

        $('.ot_edit_selections_category_dialog_window .editableform-loading').show();

        $('.ot_edit_selections_category_dialog_window #originalPicture').val(originalPicture);
        if ((typeof picture !== 'undefined') && (picture.length > 0)) {
            $('.ot_edit_selections_category_dialog_window #existing_image').val(picture);
            $('.ot_edit_selections_category_dialog_window .thumbnail-placeholder img').attr('src', picture);
        } else {
            $('.ot_edit_selections_category_dialog_window #existing_image').val('');
            $('.ot_edit_selections_category_dialog_window .thumbnail-placeholder img').attr('src', "/i/noimg.png");
        }
        $('.ot_edit_selections_category_dialog_window .editableform-loading').show();

        var width = jQuery(window).width();
        var left = (width - 690) / 2;
        $('.ot_edit_selections_category_dialog_window').css('width', '690px');
        $('.ot_edit_selections_category_dialog_window').css('left', left + 'px');
        $('.ot_edit_selections_category_dialog_window').css('margin-left', '0px');
        $('.ot_edit_selections_category_dialog_window .delete-existing-image').click(function () {
            $('.ot_edit_selections_category_dialog_window #existing_image').val('del');
            $('.ot_edit_selections_category_dialog_window .thumbnail-placeholder img').attr('src', "/i/noimg.png");
        });
        $('.ot_edit_selections_category_dialog_window .modal-footer .btn-primary').unbind('click').click(function () {
            $('.ot_edit_selections_category_dialog_window .btn').addClass('disabled');
            $('.ot_edit_selections_category_dialog_window .btn').attr('disabled', 'disabled');

            var options = {
                success: function (data) {
                    $('.ot_edit_selections_category_dialog_window .btn').removeClass('disabled');
                    $('.ot_edit_selections_category_dialog_window .btn').removeAttr('disabled');
                    if (data.result == 'ok') {

                        if (data.newImage != "") {
                            $('.img_preview img', li).attr('src', data.newImage);
                        }
                        if (data.picture == 'del') {
                            $(li).attr('picture', '');
                            $('.img_preview img', li).attr('src', '/i/noimg.png');

                        }
                        $('.ot_edit_selections_category_dialog_window').modal('hide');
                        showMessage(trans.get("Category_edited"));
                    }
                    else {
                        showError(data.message);
                    }
                },
                'dataType': 'json'
            };
            $(form).ajaxSubmit(options);
        });
        $('.ot_edit_seller_dialog_modal .btn-primary').unbind('click').click(function () {
            $('.ot_edit_seller_dialog_modal .btn').addClass('disabled');
            $('.ot_edit_seller_dialog_modal .btn').attr('disabled', 'disabled');

            $('.ot_edit_seller_dialog_modal form').ajaxSubmit(options);
        });
        $('.ot_edit_selections_category_dialog_window').modal('show');
    },
    deleteRecommendedCategory: function (e) {
        var li = $(e.currentTarget).closest('li');
        var id = $(li).attr('id');
        var action = "?cmd=Sets&do=deleteRecommendedCategory";

        modalDialog(trans.get('Confirm_needed'), trans.get('Really_delete_this_category_from_sets'), function () {
            $.post(
                action,
                {
                    "categoryId": id
                },
                function (data) {
                    if (!data.error) {
                        // add item to list
                        $(li).remove();
                        showMessage(trans.get("Category_deleted"));
                        location.reload();
                    } else {
                        showError(data.message);
                    }
                }, 'json'
            );
        });
    },
    delImageRecommendedCategory: function (e) {
        var li = $(e.currentTarget).closest('li');
        $('#existing_image').val('del');
        $('.img_preview img', li).attr('src', '/i/noimg.png');

    },
    getPreparedCategories: function (categories, addRootCategory) {
        var preparedCategories = [];
        _.each(categories, function (item) {
            var category = item.attributes ? item.attributes : item;
            var prepared = {
                "data": {
                    "title": category.Name,
                    "metadata": '<div class="actions"></div>',
                },
                "attr": category
            };
            if (category.IsParent == 'true') {
                prepared.icon = 'folder';
                prepared.children = [];
            } else {
                prepared.icon = 'folder';
            }
            if (category.DeleteStatusUI == 'true') {
                prepared.data.icon = 'folderr';
            }
            preparedCategories.push(prepared);
        });
        if ('undefined' !== typeof addRootCategory) {
            preparedCategories = [{
                'data': {
                    'title': trans.get('Root_category')
                },
                'attr': {
                    'id': 0,
                    'class': self.rootCategory
                },
                'state': 'open',
                'children': [preparedCategories]
            }];
        }
        return preparedCategories;
    }
});
$(function () {
    var R = new RestrictionPage();
});
