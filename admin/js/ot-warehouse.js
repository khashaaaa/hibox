var WarehouseProducts = new Backbone.Collection();
var WarehousePage = Backbone.View.extend({
    "el": ".warehouse-wrapper",
    "events": {
        "click #start-import": "startImport",
    },
    "warehouseRootCategory": 'warehouse_root_category',
    "lastLoadedNode": null,
    render: function()
    {
        return this;
    },
    initialize: function(){
        self = this;
        this.render();
        if ($("#jstree").length) {
            $("#jstree")
                .bind("before.jstree", function (e, data) {
                })
                .bind("open_node.jstree", function (e, data) {
                    data.inst.select_node("#phtml_2", true);
                })
                .jstree({
                    // Подключаем плагины
                    "plugins" : ["themes","json_data","ui","crrm","dnd","search","types"],
                    "themes" : {
                        "theme" : "classic",
                        "dots" : true,
                        "icons" : true
                    },
                    "json_data" : {
                        "data" : self.getPreparedCategories(WarehouseCategories.models),
                        'correct_state': true,
                        'progressive_render': true,
                        'progressive_unload': true,
                        "ajax" : {
                            "url" : '?cmd=Warehouse&do=getCategories',
                            // the `data` function is executed in the instance's scope
                            // the parameter is the node being loaded
                            // (may be -1, 0, or undefined when loading the root nodes)
                            "data" : function (node) {
                                // the result is fed to the AJAX request `data` option
                                return {
                                    "parentId" : node.attr ? node.attr("id") : 0
                                };
                            },
                            "success" : function (data) {
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
                .bind("create.jstree", function (e, node) {
                    var parentId = 0;
                    if (node.rslt.parent != -1){
                        parentId = node.rslt.parent.attr("id");
                    }
                    $.post(
                        "?cmd=Warehouse&do=createCategory",
                        {
                            "parentId" : parentId,
                            "position" : node.rslt.position,
                            "name" : node.rslt.name
                        },
                        function (data) {
                            if (data.newId) {
                                var li = $(node.rslt.obj);
                                $(li).attr("id", data.newId);
                                self.addJSTreeItemTaskBar(li);
                            } else {
                                $.jstree.rollback(node.rlbk);
                                showError(data.message ? data.message : trans.get('Notify_error'));
                            }
                        }, 'json'
                    );
                })
                .bind("remove.jstree", function (e, node) {
                        $.post(
                            "?cmd=Warehouse&do=removeCategory",
                            {"id" : node.rslt.obj.attr("id")},
                            function (data) {
                                if (data.error) {
                                    showError(data.message ? data.message : trans.get('Notify_error'));
                                    $.jstree.rollback(node.rlbk);
                                }
                            }, 'json'
                        );
                    return false;
                })
                .bind("rename.jstree", function (e, node) {
                    if (node.rslt.new_name == node.rslt.old_name) {
                        return;
                    }
                    $.post(
                        "?cmd=Warehouse&do=renameCategory",
                        {"id" : node.rslt.obj.attr("id"), "newName" : node.rslt.new_name},
                        function (data) {
                            if (data.error) {
                                $.jstree.rollback(node.rlbk);
                                showError(data.message ? data.message : trans.get('Notify_error'));
                            }
                        }, 'json'
                    );
                })
                .bind("move_node.jstree", function (e, node) {
                    node.rslt.o.each(function (i) {
                        $.ajax({
                            async : true,
                            type: 'POST',
                            dataType: 'json',
                            url: "?cmd=Warehouse&do=moveCategory",
                            data : {
                                "id" : $(this).attr("id").replace("node_",""),
                                "parentId" : node.rslt.cr === -1 ? 0 : node.rslt.np.attr("id"),
                                "position" : node.rslt.cp + i,
                                "copy" : node.rslt.cy ? 1 : 0
                            },
                            success : function (data) {
                                if (data.error) {
                                    $.jstree.rollback(node.rlbk);
                                    showError(data.message ? data.message : trans.get('Notify_error'));
                                } else {
                                    $(node.rslt.oc).attr("id", data.id);
                                    if (node.rslt.cy && $(node.rslt.oc).children("UL").length) {
                                        var li = node.inst._get_parent(node.rslt.oc);
                                        node.inst.refresh(li);
                                        self.addJSTreeItemTaskBar(li);
                                    }
                                }
                            }
                        });
                    });
                })
                .one("reopen.jstree", function (event, data) {
                })
                // 3) but if you are using the cookie plugin or the UI `initially_select` option:
                .one("reselect.jstree", function (event, data) {
                })
                .bind("loaded.jstree", function(e, data){
                    // байндинг на выделение нода
                    $('.warehouse-wrapper #jstree li').each(function(){
                        self.addJSTreeItemTaskBar(this);
                    });

                    $('.ot_show_crud_cat_item_window').click(function(){
                        $('.ot_crud_cat_item_window .modal-body #categoryName').attr('value', '');
                        var content = $('.ot_crud_cat_item_window .modal-body').html();
                        modalDialog(trans.get('Add_category'), content, function(body) {
                            var categoryName = $('#categoryName',body).val();
                            if (categoryName.length) {
                                $("#jstree").jstree("create", -1, 'last', categoryName, null, true);
                                return true;
                            } else {
                                showError(trans.get('Enter_category_name_promt'));
                                return false;
                            }
                            return false;

                        }, {confirm: trans.get('Save'), cancel: trans.get('Cancel') });
                    });
                })
                .bind("select_node.jstree", function (event, data) {
                })
                .bind("create_node.jstree", function (event, data) {
                })

                .bind("load_node.jstree", function (event, data) {
                    if (undefined !== data) {
                        var item = data.args[0][0];
                        if (undefined != item) {
                            $('li', item).each(function(){
                                self.addJSTreeItemTaskBar(this);
                            });
                            self.lastLoadedNode = data.rslt.obj;
                        }
                    }
                });
        }
    },
    getPreparedCategories: function (categories) {
        var preparedCategories = [];
        _.each(categories, function (item) {
            var category = item.attributes ? item.attributes : item;
            var prepared = {
                "data" : {
                    "title" : category.Name,
                    "metadata" : '<div class="actions"></div>'
                },
                "attr" : category
            };
            if (category.IsParent == 'true') {
                prepared.icon = 'folder';
                prepared.children = [];
            } else {
                prepared.icon = 'folder';
            }
            preparedCategories.push(prepared);
        });
        return preparedCategories;
    },
    assignItemHandlers: function(item){
        $('.ot_cat_actions .add_category_button', item).click(function(){
            var li = $(this).closest('li');
//          $("#jstree").jstree("create", li);
            $('.ot_crud_cat_item_window .modal-body #categoryName').attr('value', '');
            var content = $('.ot_crud_cat_item_window .modal-body').html();
            modalDialog(trans.get('Add_category'), content, function(body) {
                var categoryName = $('#categoryName',body).val();
                if (categoryName.length) {
                    $("#jstree").jstree("create", li, 'last', categoryName, null, true);
                    return true;
                } else {
                    showError(trans.get('Enter_category_name_promt'));
                    return false;
                }
                return false;

            }, {confirm: trans.get('Save'), cancel: trans.get('Cancel') });
        });
        $('.ot_cat_actions .rename_category_button', item).click(function(){
            var li = $(this).closest('li');
            var oldCategoryName = $('a:first',li).text();
            oldCategoryName = oldCategoryName.trim();
            $('.ot_crud_cat_item_window .modal-body #categoryName').attr('value', oldCategoryName);
            var content = $('.ot_crud_cat_item_window .modal-body').html();
            modalDialog(trans.get('Add_category'), content, function(body) {
                var categoryName = $('#categoryName',body).val();
                if (categoryName.length) {
                    if (categoryName != oldCategoryName ) {
                        $("#jstree").jstree("rename_node", li , categoryName);
                        $.post(
                            "?cmd=Warehouse&do=renameCategory",
                            {"id" : li.attr("id"), "newName" : categoryName},
                            function (data) {
                                if (data.error) {
                                    $.jstree.rollback(node.rlbk);
                                    showError(data.message ? data.message : trans.get('Notify_error'));
                                }
                            }, 'json'
                        );
                    }
                    return true;
                } else {
                    showError(trans.get('Enter_category_name_promt'));
                    return false;
                }
                return false;

            }, {confirm: trans.get('Save'), cancel: trans.get('Cancel') });


        });
        $('.ot_cat_actions .delete_category_button', item).click(function(){
            var li = $(this).closest('li');
            modalDialog('', trans.get('Really_delete_this_category'), function(){
                $("#jstree").jstree("remove", li);
            });
        });
        $('.ot_cat_actions .move_down_category_button', item).click(function(){
            var li = $(this).closest('li');
            var parentLi = $(li).closest('ul');
            var targetLi = $('#'+$(li).attr('id')+' ~ li',parentLi);
            $("#jstree").jstree("move", li, targetLi);
        });
        $('.ot_cat_actions .move_category_button', item).mousedown(function(e){
            var li = $(this).closest('li');
            var a = $('a',li);
            $("#jstree").jstree("start_drag", a, e  );
            return false;
        });
    },
    addJSTreeItemTaskBar: function(item){
        var html = '<span class="ot_cat_actions">'+
        '<button class="btn btn-tiny offset-right1 move_category_button" title="'+trans.get("Move_category")+'"><i class="icon-move"></i></button>'+
        '<span class="btn-group">'+
        '    <button class="btn btn-tiny rename_category_button" title="'+trans.get("Rename_category")+'"><i class="icon-pencil"></i></button>'+
        '    <button class="btn btn-tiny ot_show_add_cat_item_window add_category_button" title="'+trans.get("Add_category")+'"><i class="icon-plus"></i></button>'+
        '    <button class="btn btn-tiny ot_show_deletion_dialog_modal delete_category_button" title="'+trans.get("Delete_category")+'"><i class="icon-remove"></i></button>'+
        '</span>'+
        '</span>';

        if ($('span.ot_cat_actions', item).length == 0) {
            var id = $(item).attr('id');
            if (id != 0) {
                $(item).append(html);

                this.assignItemHandlers(item);

                $('a', item).hover(function(){
                    $('.ot_cat_actions').hide();
                    $(this).parent('li').mouseenter();
                    return false;
                });

                $(item).hover(function(){
                    $('.ot_cat_actions').hide();
                    $('.ot_cat_actions:first',this).show();
                    return false;
                }, function(){
                    $('.ot_cat_actions').hide();
                    return false;
                });
            }

        }
    },
    startImport: function (ev) {
        var self = this;
        var form = $(ev.currentTarget).closest('form');
        var button = $('button#start-import');

        if (!form.find('input[name="fileId[]"]') || !form.find('input[name="fileId[]"]').val()) {
            showError(trans.get('No_files_to_upload'));
            return false;
        }

        button.button('loading');

        $.post(
            form.attr('action'),
            form.serialize(),
            function (data) {
                if (!data.error) {
                    showMessage(data.message ? data.message : trans.get('Notify_success'));
                    var activityId = data.activityId;
                    var activityType = data.activityType;
                    openActivity(activityId, activityType);
                } else {
                    showError(data);
                }
                button.button('reset');
            }, 'json'
        );
    }
});

$(function(){
    var U = new WarehousePage();
});
