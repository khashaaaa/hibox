var Sets = new Backbone.Collection();
var RestrictionPage = Backbone.View.extend({
    "el": "body",
    "events": {
    	"click .add-restriction": "addRestriction", 
        "click .itemCheck": "checkAndGetSelectedItems", 
    	"click .delete-restrictions": "deleteRestriction",
        "change .per-page-item": "changePerPage",
        "click .show-categories": "showCategories",
        "click #checkAll": "toggleCheckAll",
        "submit form.form-inline": "addRestriction"
    },
    "rootCategory": 'Root_category',
    "lastLoadedNode": null,    
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
                $('.add-restriction-data').val(selectedCat);
                
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
        {confirm: trans.get('Check'), cancel: trans.get('Cancel') }, function(body) { self.initJSTree(body) });
		return false;
    },
    addRestriction: function(e)
    {
		e.preventDefault();
        var target = this.$(e.target);        
        var restrictionData = $('.add-restriction-data').val();
        if (restrictionData == '') {
            showError(trans.get('Item_not_entered'));
            return false;
        }         
        var $button = target.button('loading');
        var action = target.data('action');        
        var type = target.data('type');                
        $.post(
            action,
            { 
                restrictionData : restrictionData, 
                type : type 
            },
            function (data) {
                if (! data.error) {			  
				    showMessage(trans.get('Restriction_added'));
                    window.location.href = target.data('link');
				} else {
				    $button.button('reset');
                    showError(data.message);
				}
            }, 'json'
        );         
		return false;
    },
    deleteRestriction: function(e)
    {
		e.preventDefault();
        var restrictionData = this.checkAndGetSelectedItems();
        if (restrictionData.length > 0) {
            var target = this.$(e.target);
            var $button = target.button('loading');
            var action = target.data('action');
            var type = target.data('type');            
            var msg = _.template(trans.get('delete_warning'), {item: trans.get('checked_group')});        
            modalDialog(trans.get('Confirm_needed'), msg, function(){
                $.post(
                    action,
                    { 
                        restrictionData : restrictionData, 
                        type : type 
                    },
                    function (data) {
                        if (! data.error) {			  
                            showMessage(trans.get('Restrictions_deleted'));
                            window.location.href = target.data('link');
                        } else {
                            $button.button('reset');
                            showError(data.message);
                        }
                    }, 'json'
                ); 	
            });            
        } 
		return false;
    },
    changePerPage: function(e)
    {
		e.preventDefault();
        var target = this.$(e.target);
        var perPage = target.val();
        var action = target.data('action');
        window.location.href = action + '&perpage=' + perPage;        
		return false;
    },
    checkAndGetSelectedItems: function()
    {   
        var checkedItems = [];
		$('.itemCheck').each(function(i,elem) {
            if ($(elem).prop('checked')) {
                $(elem).parent().parent().addClass('selected_item');
                checkedItems.push($(elem).attr('data-id'));                
            } else {
                $(elem).parent().parent().removeClass('selected_item');
            }
        });
        if (checkedItems.length > 0) {
            $('.delete-restrictions').removeClass('disabled');
        } else {
            $('.delete-restrictions').addClass('disabled');
        }
		return checkedItems;
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
    },
    toggleCheckAll: function (ev) 
    {
        var self = this.$(ev.target);
        $('.itemCheck').each(function(i,elem) {
            $(elem).prop('checked', self.is(':checked'));             
        });
        this.checkAndGetSelectedItems();
    }
});

$(function() {
    var R = new RestrictionPage();
});
