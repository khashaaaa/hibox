var Sets = new Backbone.Collection();
var RestrictionPage = Backbone.View.extend({
    "el": "body",
    "events": {
        "click .show-categories": "showCategories",
        "click .ot_add_category_from_link .btn-primary": "addCategory",
        "click .ot_show_deletion_dialog_modal": "deleteCategory",
    },
    "rootCategory": 'Root_category',
    "lastLoadedNode": null,    
    deleteCategory: function(e)
    {
    	var tr = $(e.currentTarget).closest('tr');
    	var categoryId = $(tr).attr('id');
    	var groupId = $('#groupId').val();
    	
    	modalDialog(trans.get('Confirm_needed'), trans.get('Really_remove_this_price_group_category'), function() {
    		$.post('?cmd=pricing&do=deletePriceGroupCategory', { 'groupId' : groupId, "categoryId": categoryId}, function (data) {
                if (data.result != 'error') {
                	$(tr).remove();
                }
            }, 'json');    		
        });
    	
    	return false;    	
    },
    addCategory: function() {
    	var form = $('.ot_add_category_from_link form');
    	$(form).ajaxSubmit({
            url     :   $(form).attr('action'),
            type    :   'POST',
            dataType:   'json',
            success :   function(data) {
            	if(data && data.result && data.result == 'ok') {
            		showMessage(trans.get('Price_group_category_added_successfully'));
            		document.location.reload();
            	} else {
            		showError(data);
            	}
             }
        });    	
    	return false;
    },
    
    render: function()
    {
        return this;
    },
    initialize: function() 
    {
        var self = this;
        this.render();
    },
    initJSTree: function(body) 
    {        
        var self = this;        
        if (typeof(CategoriesCategories) != 'undefined') {
          $("#jstree", body)
            .bind("before.jstree", function(e, data) {
            })
            .bind("open_node.jstree", function(e, data) {
                data.inst.select_node("#phtml_2", true);
            })
            .jstree({
                // Подключаем плагины
                "plugins" : ["themes","json_data","ui","crrm","dnd","search","types","sort"], 
                "themes" : {
                    "theme" : "classic",
                    "dots" : true,
                    "icons" : true
                },
                "sort": function (a, b) {
                    return parseInt($(a).attr('i')) > parseInt($(b).attr('i')) ? 1 : -1; 
                },
                "json_data" : {
                    "data" : self.getPreparedCategories(CategoriesCategories.models), // 1
                    'correct_state': true,
                    'progressive_render': true,
                    'progressive_unload': true,
                    "ajax" : {
                        "url" : '?cmd=Categories&do=getCategories',
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
            .one("reopen.jstree", function (event, data) {
            })
            // 3) but if you are using the cookie plugin or the UI `initially_select` option:
            .one("reselect.jstree", function (event, data) {
            })
            .bind("loaded.jstree", function(e, data) { 
                
            })
            .bind("select_node.jstree", function (event, data) {
                var selectedCat = $('.jstree-clicked').parent().attr('id');
                $('.category-url').val(selectedCat);
                
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
    showCategories: function(e)
    {
    	var self = this;
		e.preventDefault();
        var content = '<div id="jstree"></div>';
        modalDialog(trans.get('Check_category'), content, function(body) {            
        },
        {confirm: trans.get('Check'), cancel: trans.get('Cancel') }, function(body){ self.initJSTree(body) });
		return false;
    },
    getPreparedCategories: function (categories, addRootCategory) 
    {
        var preparedCategories = [];
        _.each(categories, function (item) {
            var category = item.attributes ? item.attributes : item;
            var prepared = {
                "data" : {
                    "title" : category.Name,
                    "metadata" : '<div class="actions"></div>',
                },
                "attr" : category
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
                'data' : {
                    'title' : trans.get('Root_category')
                },
                'attr' : {
                    'id'    : 0,
                    'class' : self.rootCategory
                },
                'state' : 'open',
                'children' : [preparedCategories]
            }];
        }
        
        return preparedCategories;
    }
});

$(function() {
    var R = new RestrictionPage();
});
