var Banker = new Backbone.Collection();
var BankerPage = Backbone.View.extend({
    "el": $("#banker-wrapper")[0],
    "events": {
        "click .remove-price-group": "removePriceGroup",
        "click a.btn_save": "savePriceGroup",
        "click .remove-group": "removePriceGroupInterval",
        "click #add-interval-btn": "addPriceGroupInterval",
        "change #price-group-type": "priceGroupTypeChange",
        "change #price-provider-type": "priceProviderTypeChange",
        "change .interval-low-edge" : "updateIntervalHighEdge",        
        "click #add-delivery": "addDelivey",
        "click .remove-delivery": "deleteDelivey",
        "submit #priceGroupForm": "savePriceGroup",
    },
    "select": $('select[name="new_delivery"]'),
    "addBtn" : $('#add-currency'),
    "select2Selector": '.select2-container',
    initialize: function() 
    {
        this.checkAbility4Delete();
        this.priceProviderTypeChange();
        this.updateIntervalHighEdge();
        this.priceGroupTypeChange();
    },
    addDelivey: function(e) {
        e.preventDefault();
        if (this.addBtn.hasClass('disabled')) {
            return false;
        }
        if (this.select.find('option').length == 0) {
            showError(trans.get('Notify_error'));
            return false;
        }
        this.addBtn.attr('disabled', 'disabled');
        var name = this.select.find('option[value="' + this.select.val() + '"]').text();
        var code = this.select.val();
        var self = this;
        $.get('templates/pricing/underscore_templates/delivery.html?' + Math.random(), function (tpl) {
            var newItemHtml = _.template(tpl, {'Code': code, 'Name': name});
            self.$('#chosenItems').append(newItemHtml);
            self.select.find('option[value="' + self.select.val() + '"]').remove();
            self.$(self.select2Selector).find('a span').text(self.select.find('option:first').text());
            self.updateSelect();
        });
        this.addBtn.removeAttr('disabled');

        return false;
    },
    deleteDelivey: function(ev) {
        var item = $(ev.target).closest('li');

        $('<option value="' + item.data('code') + '">' + item.data('name') + '</option>').appendTo(this.select);
        item.remove();
        this.updateSelect();

        return true;
    },
    updateSelect: function () {
        var select2 = $(this.select2Selector);
        if (this.select.find('option').length == 0) {
            this.addBtn.removeAttr('disabled').addClass('disabled');
            select2.addClass('select2-container-disabled');
            select2.find('a').addClass('select2-default').off();
            select2.find('a span').text(trans.get('All_deliveries_are_chosen'));
        } else {
            this.addBtn.removeAttr('disabled').removeClass('disabled');
            select2.removeClass('select2-container-disabled');
            select2.find('a').removeClass('select2-default');
            select2.find('a span').text(this.select.find('option:first').text());
            this.select.select2();
        }
    },
    updateIntervalHighEdge: function() {
        $('.interval-low-edge').each(function(){
        	var value = $(this).val();
        	var tr = $(this).closest('tr');
        	var prevTr = $(tr).prev();
        	$('.interval-high-edge', prevTr).val(value);
        });
    },
    priceGroupTypeChange: function(e) {
    	var description = $('#price-group-type option:selected').data('description');
    	$('div.price-group-type-description').text(description);
    },
    priceProviderTypeChange: function(e) {
        /* find available strategies */
        var provider = $('#price-provider-type').val();
        if (typeof strategiesByProviders !== 'undefined') {
            var currentStrategies = [];
            if (typeof strategiesByProviders[provider] !== 'undefined') { // provider strategies
                currentStrategies = currentStrategies.concat(strategiesByProviders[provider]);
            }
            if (typeof strategiesByProviders['All'] !== 'undefined') { // general strategies
                currentStrategies = currentStrategies.concat(strategiesByProviders['All']);
            }
            if (currentStrategies.length !== 0) {
                $('#price-group-type').empty(); // remove old strategies

                var currentStrategyExists = false;
                $(currentStrategies).each(function(key, val) {
                    var option = $('<option/>', {
                        val:  val['Name'],
                        text: val['Name']
                    }).data('description', val['Description']);

                    if (typeof currentStrategy !== 'undefined' && val['Name'] === currentStrategy.strategyType) { // select current strategy
                        currentStrategyExists = true;
                        option.attr("selected", "selected");
                    }
                    option.appendTo('#price-group-type');
                });
                 // add the current strategy, if it isn't added
                if (!currentStrategyExists && typeof currentStrategy !== 'undefined') {
                    $('<option/>', {
                        val:  currentStrategy.strategyType,
                        text: currentStrategy.strategyType
                    }).data('description', currentStrategy.description).attr("selected", "selected").prependTo('#price-group-type');
                }
            }
            this.priceGroupTypeChange();
        }

        var currencyCode = $('#price-provider-type option:selected').attr('currency-code');
        $('.provider-currency').each(function(){
            $(this).text(currencyCode);
        });    	
    },      
    addPriceGroupInterval: function(e)
    {
    	var html = $('table#intervals tr.price-group-interval:last').html();
    	$('table#intervals').append('<tr style="border-top: 1px dotted #D3D3D3; height: 40px;" class="price-group-interval">' + html + '</tr>');
    	$('table#intervals tr.price-group-interval:last input[type="text"]').val('');
    	$('table#intervals tr.price-group-interval:last input[type="hidden"]').val('');
    	$('table#intervals tr.price-group-interval:last input:radio:first').attr('checked','checked');
        this.checkAbility4Delete();
    },
    removePriceGroupInterval: function(e)
    {
    	var count = $('table#intervals tr.price-group-interval').length;
    	var tr = $(e.currentTarget).closest('tr');
    	if (count==1) {
    		showError(trans.get('Price_group_must_have_at_least_one_interval'));
    	}
    	else {
    		$(tr).remove();
    	}
        this.checkAbility4Delete();
        this.updateIntervalHighEdge();
    	
    },
    checkAbility4Delete: function(){
        var count = $('table#intervals tr.price-group-interval').length;
        if(count == 1) {
            $('i.remove-group').hide();
        } else {
            $('i.remove-group').show();
        }
    },
    removePriceGroup: function(e)
    {
    	var tr = $(e.currentTarget).closest('tr');
    	var groupId = $(tr).attr('id');
    	
    	modalDialog(trans.get('Confirm_needed'), trans.get('Really_remove_this_price_group'), function() {
    		$.post('?cmd=pricing&do=deletePriceGroup', { 'id' : groupId}, function (data) {
                if (data.error) {
                    showError(data);
                } else {
                    showMessage(trans.get('Price_group_deleted_successfully'));
                	$(tr).remove();
                }
            }, 'json');    		
        });
    	
    	return false;
    },
    savePriceGroup: function(e) 
    {
    	var isValid = true;
    	var validation = function() {
    		var value = $(this).val();
    		if (value == '')
    			return true;
    		var value = parseFloat(value);
    		if (isNaN(value)) {
    			isValid = false;
    			return false;
    		}
    		if (!isNaN(value) && value < 0) {
    			isValid = false;
    			return false;
    		}
    		return true;
    	};
    	$('input[name="margin[]"]').each(validation);
    	$('input[name="margin_fixed[]"]').each(validation);
    	$('input[name="limit[]"]').each(validation);
    	$('input[name="delivery[]"]').each(validation);
    	$('input[name="delivery-all"]').each(validation);
    	
    	var messages = [];
    	if(!isValid) {
    		messages.push(trans.get('Price_group_values_must_be_greater_than_zero'));
    	}

        var name = $('#name').val();
    	if (name.trim() == '') {
    		messages.push(trans.get('Must_be_enter_price_group_name'));
    		isValid = false;
    	}
        var description = $('#description').val();
    	if (description.trim() == '') {
    		messages.push(trans.get('Must_be_enter_price_group_description'));
    		isValid = false;
    	}
        
    	if(!isValid) {
    		showError(messages.join('<br/>'));
    		return false;
    	}

        $('#feature_list input[name="features[]"]').remove();

        $('span.tag').each(function() {
            $('<input/>', {
                name:  "features[]",
                value: $(this).text(),
                type: "hidden"
            }).appendTo('#feature_list');
        });

    	$('#priceGroupForm').ajaxSubmit({
            url     :   $(this).attr('action'),
            type    :   'POST',
            dataType:   'json',
            success :   function(data) {
            	if(data && data.result && data.result == 'ok') {
            		showMessage(trans.get('Price_group_saved_successfully'));
            		document.location.href = 'index.php?cmd=pricing&do=banker';
            	} else {
            		showError(data);
            	}
             }
        });
        return false;
    }
});

$(function() {
    new BankerPage();
});