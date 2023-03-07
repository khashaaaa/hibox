var Pristroy = new Backbone.Collection();
var PristroyPage = Backbone.View.extend(
{
    "el": ".pristroy-wrapper",
    "events": {
        "change #perpage": "changePerPageLimit",
        "click #checkAll input": "toggleCheckAll",
        
		"click .top-actions button.approveItem": "approveButtonItemBulkAction",
		"click .top-actions button.rejectItem": "rejectButtonItemBulkAction",
		"click .top-actions button.removeItem": "removeButtonItemBulkAction",
		"click table tr button.approveItem" : "approveButtonItemAction",
		"click table tr button.rejectItem": "rejectButtonItemAction",
		"click table tr button.removeItem": "removeButtonItemAction",
    },
    render: function()
    {
        return this;
    },
    initialize: function()
    {
        this.render();
        $('#ot_user_login_filter').typeahead({
            source: function (query, process) {
                return $.get('?cmd=referral&do=searchUsers&login='+query, {}, function (data) {
                    return process(data.options);
                }, 'json');
            }
        });
        
    },
    changePerPageLimit: function(ev)
    {
        this.$('input[name=perpage]').val(this.$('#perpage').find('option:selected').val());
        this.$('#filters').submit();
    },
	updateItemStatus: function(ev, mixed, bulk)
    {
        ev.preventDefault();
		var target = $(ev.target).closest('button'); 
		
        if (target.hasClass('disabled')) {
            return;
        }

        var id = [];
        var params = {};
        if ('undefined' !== typeof bulk) {
            var error = false;
            $('input[name^=ids]:checked').each(function() {
            	var tr = $(this).closest('tr');
                id.push($(this).val());
                if ('function' === typeof mixed.uiCheckRowCallback) {
                	if (!mixed.uiCheckRowCallback(id, tr, params)) {
                		error = true;	
                		return;
                	} 
                }
            });
            if (error) {
            	return;
            }
            if (! id.length) {
                showError(trans.get('No_item_checked_in_the_list'));
                return;
            }
        } else {
        	var tr = $(target).closest('tr');
            var rid = $(tr).attr('id');
            id.push(rid);
            if ('function' === typeof mixed.uiCheckRowCallback) {
	            if (!mixed.uiCheckRowCallback(id, tr, params)) 
	            	return;
            }
            if (! id.length) {
                showError(trans.get('No_item_checked_in_the_list'));
                return; 
            }
        }

        var ielement =  target.find('i');
        target.addClass('disabled');
        $(ielement).attr('prev-class', $(ielement).attr('class'));
        $(ielement).attr('class', 'ot-preloader-micro');
        
		$('.actionButton').addClass('disabled');

        var self = this;
		var url = target.data('action');
        $.post(
            url,
            {'id': id,
             'params':params},
            function (data) 
            {
            	var ielement =  target.find('i');
                target.removeClass('disabled')
                $(ielement).attr('class', $(ielement).attr('prev-class'));
				$('.actionButton').removeClass('disabled');
                if (! data.error) {
                    if ('function' === typeof mixed) {
                        mixed($.extend(data, {'id': id}));
                    } else {
                        if ('undefined' !== typeof bulk) {
                            $.each(id, function(i, val) {
                                $('#' + val).find('.itemStatus span').attr('class', mixed.statusClass).text(mixed.statusText);
                                self.toggleItemAction($('#' + val));
                            });
                        } else {
                            target.parents('tr').find('.itemStatus span').attr('class', mixed.statusClass).text(mixed.statusText);
                            self.toggleItemAction(target.parent());
                        }
                        showMessage(mixed.statusMessage);
                        if ('function' === typeof  mixed.uiUpdateRowCallback ) {
            				if (id.length > 0) {
            					for ( var i = 0; i < id.length; i++) {
            						var rowId = id[i];
            						var tr = $('input[name^=ids][value="'+rowId+'"]').closest('tr');
            						mixed.uiUpdateRowCallback(id, tr);
            					}	
            				}
            				else {
            					var rowId = id;
            					var tr = $('input[name^=ids][value="'+rowId+'"]').closest('tr');
        						mixed.uiUpdateRowCallback(id, tr);
            				}
                        }
                    }
                } else {
                    showError(data.message ? data.message : trans.get('Notify_error'));
                }
            }, 
            'json'
        );
        return false;
    },
    toggleCheckAll: function(ev)
    {
        var self = this.$(ev.target);
        self.parents('thead').next().find('input[type=checkbox]').prop('checked', self.is(':checked'));
    },
    toggleItemAction: function (li) 
    {
    },
	approveButtonItemAction: function(ev)
	{
        this.updateItemStatus(ev, {
            statusClass: 'label label-success weight-normal',
            statusText: trans.get('Pristroy_status_approved'),
            statusMessage: trans.get('Product_is_approved'),
            uiUpdateRowCallback: function(id, tr)
            {
            	$('.rejectForm', tr).show();
				$('.rejectedMessage', tr).hide();
				$('.approveItem', tr).hide();
				$('.rejectedMessage span', tr).text('');
				$('.rejectForm textarea', tr).val('');
            }
        });
	},
    approveButtonItemBulkAction: function(ev) {
        this.updateItemStatus(ev, {
            statusClass: 'label label-success weight-normal',
            statusText: trans.get('Pristroy_status_approved'),
            statusMessage: trans.get('Product_is_approved'),
            uiUpdateRowCallback: function(id, tr)
            {
            	$('.rejectForm', tr).show();
				$('.rejectedMessage', tr).hide();
				$('.approveItem', tr).hide();
				$('.rejectedMessage span', tr).text('');
				$('.rejectForm textarea', tr).val('');
            }
            
        }, 1);
	},
	rejectButtonItemBulkAction: function(ev)
	{
        this.updateItemStatus(ev, {
            statusClass: 'label label-important weight-normal',
            statusText: trans.get('Pristroy_status_rejected'),
            statusMessage: trans.get('Product_is_rejected'),
            uiUpdateRowCallback: function(id, tr)
            {
            	$('.rejectForm', tr).hide();
				$('.rejectedMessage', tr).show();
				$('.approveItem', tr).show();
				$('.rejectedMessage span', tr).text($('.rejectForm textarea', tr).val());
            },
            uiCheckRowCallback: function(id, tr, params)
            {
            	var rejectReason = $('.reject_reason', tr).val();
            	if ('' === $.trim(rejectReason)) {
            		showError(trans.get('Enter_reject_reason'));
            		return false;
            	}
            	params[id] = rejectReason; 
            	return true;
            }
        },1);
	},
	rejectButtonItemAction: function(ev)
	{
        this.updateItemStatus(ev, {
            statusClass: 'label label-important weight-normal',
            statusText: trans.get('Pristroy_status_rejected'),
            statusMessage: trans.get('Product_is_rejected'),
            uiUpdateRowCallback: function(id, tr) {
            	$('.rejectForm', tr).hide();
				$('.rejectedMessage', tr).show();
				$('.rejectedMessage span', tr).text($('.rejectForm textarea', tr).val());
				$('.approveItem', tr).show();
            },
            uiCheckRowCallback: function(id, tr, params) {
            	var rejectReason = $('.reject_reason', tr).val();
            	if ('' === $.trim(rejectReason)) {
            		showError(trans.get('Enter_reject_reason'));
            		return false;
            	}
            	params[id] = rejectReason;
            	return true;
            }            
        });
	},
	removeButtonItemBulkAction: function(ev)
	{
	    ev.preventDefault();
        var self = this;
        var selectedItemsCount = $('input[name^=ids]:checked').length;
        if (selectedItemsCount == 0) {
        	showError(trans.get('No_item_checked_in_the_list'));
            return;
        }
        
        confirmDialog(trans.get('Really_remove_this_product_from_sale'), function() {
            self.updateItemStatus(ev, function(data) {
				var id = data.id;
				if (id.length > 0) {
					for ( var i = 0; i < id.length; i++) {
						var rowId = id[i];
						var tr = $('input[name^=ids][value="'+rowId+'"]').closest('tr');
						$(tr).remove();
					}	
				}
				else {
					var rowId = id;
					var tr = $('input[name^=ids][value="'+rowId+'"]').closest('tr');
					$(tr).remove();
				}
				
				showMessage(trans.get('Product_is_removed_from_sale'));
            },1);
        });
        return false;
	},
	removeButtonItemAction: function(ev)
	{
        ev.preventDefault();
        var self = this;
        confirmDialog(trans.get('Really_remove_this_product_from_sale'), function()
        {
	        self.updateItemStatus(ev, function(data)
	        {
				var id = data.id;
				if (id.length>0) {
					for ( var i = 0; i < id.length; i++) {
						var rowId = id[i];
						var tr = $('input[name^=ids][value="'+rowId+'"]').closest('tr');
						$(tr).remove();
					}	
				}
				else {
					var rowId = id;
					var tr = $('input[name^=ids][value="'+rowId+'"]').closest('tr');
					$(tr).remove();
				}
				
				showMessage(trans.get('Product_is_removed_from_sale'));
	        });
        });
        return false;
	},
}
);

$(function()
{
    var P = new PristroyPage();
});
