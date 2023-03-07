var WarehouseItemsPage = Backbone.View.extend({
    "el": "#warehouse-products",
    "events": {
        "click #addProductBtn": "checkCategorySelected",
        "click #searchProductsIcon": "checkCategorySelected",
        "click .ot_show_deletion_dialog_modal": "removeProduct",
        "click .groupDelete": "groupDelete",
        "click input#checkAll": "toggleCheckBoxes",
        "click #reset_filter": "resetFilter",
        "change input.SellAllowed": "changeSellAllowed"
    },
    changeSellAllowed: function(e)
    {
    	var sellAllowed = $(e.currentTarget).is(":checked");
    	var itemId = $(e.currentTarget).attr("value");
    	var tr = $(e.currentTarget).closest('tr');
    	
    	$.ajax({
            async : true,
            type: 'POST',
            dataType: 'json',
            url: "?cmd=WarehouseProducts&do=productSetAllowed",
            data : {
            	"itemId": itemId,
            	"sellAllowed": sellAllowed
            },
            success : function (data) {
            	if (data.ok) {
                	if (sellAllowed) {
                		$(tr).removeClass('SellNotAllowed');
                		$(tr).addClass('SellAllowed');
                	} else {
                		$(tr).removeClass('SellAllowed');
                		$(tr).addClass('SellNotAllowed');
                	}
            	} else {
            		showError(trans.get('Service_page_something_wrong_text'));
            		$(e.currentTarget).prop('checked', !sellAllowed);
            	}
            },
            error: function() {
            	$(e.currentTarget).prop('checked', !sellAllowed);
            	showError();
            }
        });
    	if (sellAllowed) {
    		$('#Quantity').attr('readonly', 'readonly');
    	} else {
    		$('#Quantity').removeAttr('readonly');
    	}
    },    
    resetFilter: function() {
    	$('form#filters #category_name').val('');
    	$('form#filters #category_id').val('');
    	$('form#filters #product_url').val('');
    	$('form#filters').submit();
    },
    toggleCheckBoxes: function(ev)
    {
        var self = this.$(ev.target);
        self.parents('thead').next().find('input[type=checkbox]').prop('checked', self.is(':checked'));
    },
    groupDelete: function(ev)
    {
    	var target = $(ev.target);
    	var id = [];
        $('input[name^=ids]:not(.SellAllowed):checked').each(function() {
            id.push($(this).val());
        });
        if (! id.length) {
            showError(trans.get('No_item_checked_in_the_list'));
            return;
        }
        
        var title = _.template(trans.get('Confirm_delete'));
        var msg = _.template(trans.get('Delete_products_warning'));
        var callBack = function(){
        	var url = target.data('action');
            $.ajax({
            	'type' : 'post',
                'url' : url,
                'data': {'id': id,'ajax': true},
                'dataTypes': 'json',
                'success': function (data) 
     			{
                	if (! data.error) {
                		$.each(id, function(i, val) {
                			$('tr[id="'+val+'"]').remove();
                		});
                		showMessage(trans.get('Products_removed'));
     				} else {
     					showError(data);
     				}
     			}, 
     			'error':function(){
     			}
            });
        }
        modalDialog(title, msg, callBack);
        return false;
    },
    removeProduct: function(e) 
    {
    	var tr = $(e.target).closest('tr');
    	var title = _.template(trans.get('Confirm_delete'));
        var item_name = $(e.target).closest('a').data('name');        
        item_name = escapeData(item_name);
        var msg = _.template(trans.get('delete_warning'), {item: item_name});
        var url = $(e.target).closest('a').attr('href');

        var callBack = function(modalBody){
            $(e.target).closest('a').find('i').removeClass('icon-remove').addClass('ot-preloader-micro');
            $.ajax({
            	'type' : 'get',
                'url' : url,
                'data': {'ajax': true},
                'dataTypes': 'json',
                'success': function (data) 
     			{                    
                	$(e.target).closest('a').find('i').removeClass('ot-preloader-micro').addClass('icon-remove');
                    if (! data.error) {
               			$(tr).remove();
                		showMessage(trans.get('Products_removed'));
     				} else {
     					showError(data);
     				}
     			}, 
            });
        };
        
        modalDialog(title, msg, callBack);

        return false;
    },
    checkCategorySelected: function(){
        var selectedCategory = this.$('#ex-tree').tree('selectedItems');
        var categoryId = 0;
        if (selectedCategory.length) { 
            categoryId = selectedCategory[0].additionalParameters.Id;
        }
        var url = this.$('#addProductBtn').attr('href');
        this.$('#addProductBtn').attr('href', $.param.querystring(url, {category: categoryId}));

        url = this.$('#searchProductsIcon').attr('href');
        this.$('#searchProductsIcon').attr('href', $.param.querystring(url, {category: categoryId}));

        return true;
    },
    openCurrentItem: function()
    {
        var that = this;
        that.$('#ex-tree').off('loaded');

        if (typeof currentItem === 'undefined') {
        	return;
        }

        if (currentItem.type == 'item') {
            var elem = that.$('div.tree-item-name:contains("'+ currentItem.data.Name +' ('+ currentItem.data.Id +')")').parent();
            that.$('#ex-tree').tree("selectItem", elem);
        }
        else {
            var elem = that.$('div.tree-folder-name:contains("'+ currentItem.data.Name +' ('+ currentItem.data.Id +')")').parent();
            that.$('#ex-tree').tree("selectFolder", elem);
        }
    },
    openCategory: function()
    {
        var that = this;
        that.$('#ex-tree').off('loaded');

        if (categoryPath != null && categoryPath.length) {
            var folder = categoryPath.pop();
            var elem = that.$('div.tree-folder-name:contains("'+ folder.Name +' ('+ folder.Id +')")').parent();
            that.$('#ex-tree').tree("selectFolder", elem);

            this.$('#ex-tree').on('loaded', function(e){
                that.openCategory();
            });
        }
        else{
            that.openCurrentItem();
        }
    },
    render: function()
    {
        $.fn.editable.defaults.mode = 'popup';
        $('.ot_inline_editable').editable();
        return this;
    },
    initialize: function()
    {
    	self = this;
        this.render();
        
        $('#tree-modal .btn-primary').click(function(){
        	var categoryId = $('#tree-modal #categoryId').val();
        	var categoryName = $('#tree-modal #categoryName').val();
        	if (categoryId > 0) {
        		$('#filters #category_id').val(categoryId);
        		$('#filters #category_name').val(categoryName);
        		
        		$('#tree-modal').modal('hide');
        	}
        	else {
        		showError(trans.get('Choose_category_in_the_list'));
        	}
        });
        
        $("#jstree")
        .jstree({
            "plugins" : ["themes","json_data","ui","crrm","search","types"], //,'contextmenu',"dnd"
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
                            showError(data);
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
        .bind("loaded.jstree", function(e, data){
        })
        .bind("select_node.jstree", function (event, data) {
        	var id =data.rslt.obj.attr('id');
        	var name = $('a:first', data.rslt.obj).text();
            if (id) {
            	$('#tree-modal #categoryId').val(id);
            	$('#tree-modal #categoryName').val(name);
            }
            else {
            	$('#tree-modal #categoryId').val('0');
            	$('#tree-modal #categoryName').val(name);
            }
        })
        .bind("loaded.jstree", function (event, data) { 
        })
        .bind("load_node.jstree", function (event, data) {
            self.lastLoadedNode = data.rslt.obj;
        });        
    },
    getPreparedCategories: function (categories) 
    {
    	var self = this;
        var preparedCategories = [];
        _.each(categories, function (item) {
            var category = item.attributes ? item.attributes : item;
            var prepared = {
                "data" : {
                    "title" : category.Name,
                },
                "attr" : category
            };
            if (category.IsParent == 'true') {
                prepared.icon = 'folder';
                prepared.children = self.getPreparedCategories(category.children);
            } else {
                prepared.icon = 'folder';
            }
            preparedCategories.push(prepared);
        });
        return preparedCategories;
    }
});

$(function(){
    new WarehouseItemsPage();
});
