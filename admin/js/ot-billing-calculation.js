var BillingPage = Backbone.View.extend({
    "el": $("#billing-view-wrapper")[0],
    "events": {
        "click .export-calculation": "exportFinanceCalculation",
        "click .collapseTr": "collapseData",
        "click #apply_button": "checkFilter",
    },
    checkFilter: function() {
    	var start = parseDMYDate($('#date-start-display').val());
    	var end = parseDMYDate($('#date-end-display').val());
    	if (end < start) {
    		showError(''+trans.get('End_date_must_be_greater')+'.');
    		return false;
    	}
    	if (end - start > 365*24*3600*1000) {
    		showError(''+trans.get('Max_date_range_year')+'.');
    		return false;
    	}
    	return true;
    },
    updateTable: function(data) {
    	for ( var m in data) {
    		var month = data[m];
    		$('tr[data-month="' + month.id + '"] td.middleIncomeAmount').text(month.middleIncomeAmount);
    		$('tr[data-month="' + month.id + '"] td.middleOrdersReservedAmount').text(month.middleOrdersReservedAmount);
    		$('tr[data-month="' + month.id + '"] td.middlePurchaseAmount').text(month.middlePurchaseAmount);
    		$('tr[data-month="' + month.id + '"] td.middleExternalDeliveryAmount').text(month.middleExternalDeliveryAmount);
    		$('tr[data-month="' + month.id + '"] td.middleEarningsAmount').text(month.middleEarningsAmount);
    		$('tr[data-month="' + month.id + '"] td.middleEarningsPercent').text(month.middleEarningsPercent);
    		for ( var d in month.days) {
    			var day = month.days[d];
        		$('tr[data-month="' + month.id + '"][data-day="' + d + '"] td.incomeAmount').text(day.incomeAmount);
        		$('tr[data-month="' + month.id + '"][data-day="' + d + '"] td.ordersReservedAmount').text(day.ordersReservedAmount);
        		$('tr[data-month="' + month.id + '"][data-day="' + d + '"] td.purchaseAmount').text(day.purchaseAmount);
        		$('tr[data-month="' + month.id + '"][data-day="' + d + '"] td.externalDeliveryAmount').text(day.externalDeliveryAmount);
        		$('tr[data-month="' + month.id + '"][data-day="' + d + '"] td.earningsAmount').text(day.earningsAmount);
        		$('tr[data-month="' + month.id + '"][data-day="' + d + '"] td.earningsPercent').text(day.earningsPercent);
    			for ( var p in day.providers) {
					var provider = day.providers[p];
	        		$('tr[data-month="' + month.id + '"][data-day="' + d + '"][data-provider="' + p + '"] .incomeAmount').text(provider.incomeAmount);
	        		$('tr[data-month="' + month.id + '"][data-day="' + d + '"][data-provider="' + p + '"] .ordersReservedAmount').text(provider.ordersReservedAmount);
	        		$('tr[data-month="' + month.id + '"][data-day="' + d + '"][data-provider="' + p + '"] .purchaseAmount').text(provider.purchaseAmount);
	        		$('tr[data-month="' + month.id + '"][data-day="' + d + '"][data-provider="' + p + '"] .externalDeliveryAmount').text(provider.externalDeliveryAmount);
	        		$('tr[data-month="' + month.id + '"][data-day="' + d + '"][data-provider="' + p + '"] .earningsAmount').text(provider.earningsAmount);
	        		$('tr[data-month="' + month.id + '"][data-day="' + d + '"][data-provider="' + p + '"] .earningsPercent').text(provider.earningsPercent);
	        		
	        		$('tr[data-month="' + month.id + '"][data-day="' + d + '"][data-provider="' + p + '"] .providerType').text(provider.providerType);
	        		$('tr[data-month="' + month.id + '"][data-day="' + d + '"][data-provider="' + p + '"] .providerCurrencyCode').text(provider.providerCurrencyCode);
	        		$('tr[data-month="' + month.id + '"][data-day="' + d + '"][data-provider="' + p + '"] .exchangeRate').text(provider.exchangeRate);
				}
    		}
		}
    },
    render: function()
    {
    	var self = this;
        $.fn.editable.defaults.mode = 'popup';
        $.fn.editableform.buttons += '<button type="button" class="btn editable-reset"><i class="icon-undo" aria-hidden="true"></i></button>'
        $('.ot_inline_editable').on('save', function(e, params) {
        	if (params.response.ok && params.response.data) {
        		self.updateTable(params.response.data);
        	}
                
        });        	
        $('.ot_inline_editable').editable({
            success: function(response, newValue) {
                if(response.error) {
                    return response.message;
                }
                return {newValue: parseFloat(newValue)};
            },
            validate: function(value) {
                if ($.trim(value) == '') {
                    return trans.get('Value_must_not_be_empty');
                }
                if (! value.match(/^-?\d*(\.\d+)?$/)) {
                    return trans.get('Field_incorrect'); 
                }
            }
        });
        $(document).on('click', '.btn.editable-reset', function(){
        	var td = $(this).closest('td');
        	var a = $('a', td);
        	var url = $(a).data('url');
        	var pk = $(a).data('pk');
        	var name = $(a).data('name');
        	$(this).addClass('btn_preloader');
        	$(this).addClass('disabled');
        	$.ajax({
                async : true,
                type: 'POST',
                dataType: 'json',
                url: url,
                data : {
                	"pk": pk,
                	"name": name,
                	"reset": true
                },
                success : function (data) {
                	if (data.ok) {
                		 $(a).editable('toggle');
                		 if (data.data) {
                			 self.updateTable(data.data);
                		 }
                	} else {
                		showError(trans.get('Service_page_something_wrong_text'));
                	}
                },
                error: function() {
                	showError();
                }
            });

        });
        return this;
    },
    initialize: function()
    {
        var self = this;
        this.render();
    },
    collapseData: function (e) {
        e.preventDefault();
        var cId = $(e.target).attr('data-collapse');
        var i = $(e.target).find('i');
        if (typeof cId === 'undefined') {
            cId = $(e.target).parent().attr('data-collapse');
            i = $(e.target).parent().find('i');
        }
        if (typeof cId === 'undefined') {
            cId = $(e.target).parent().parent().attr('data-collapse');
            i = $(e.target).parent().parent().find('i');
        }
        if (! $("." + cId).is(":visible")) {
            i.removeClass('icon-caret-down').addClass('icon-caret-up');
            $("." + cId).show();
        } else {
            i.removeClass('icon-caret-up').addClass('icon-caret-down');
            $("." + cId).hide();
            $(".hide-" + cId).hide();
            $("tr[hide='" + cId + "']").find('i').removeClass('icon-caret-up').addClass('icon-caret-down');
        }
        return false;
    },
    exportFinanceCalculation: function (e) {
    	if (! this.checkFilter()) {
    		return false;
    	}
        e.preventDefault();
        var form = $("#apply-filter-finance-details");
        window.location = '?cmd=Reports&do=exportCalculation&filter[fromdate]=' + form.find('#date-start-display').val() + '&filter[todate]=' + form.find('#date-end-display').val();
        return false;
    }
});

$(function(){
    new BillingPage();	
});
