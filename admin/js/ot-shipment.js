var Shipment = new Backbone.Collection();
var ShipmentPage = Backbone.View.extend({
    "el": $("#content-wrapper")[0],
    "events": {
        "mouseup .ot_show_deletion_dialog_modal": "deleteItem",
        "mouseup .ot_show_deletion_tariff_dialog_modal": "deleteItem",
        "click #save-delivery": "saveDelivery",
        "click #save-tariff": "saveTariff",
        "click #add_tariff": "addTariff",
        "change #delivery_integration_type": "getDeliveryModes"
    },
    initialize: function(){
    },
    getDeliveryModes: function (e) {
        var value = this.$(e.target).val();
        $.get('?cmd=Shipment&do=getIntegrationDeliveryModes',
            {serviceSystem: value},
            function (data) {
                if (! data.error ) {
                    $('#delivery_integration_mode option').remove();
                    if (!$.isEmptyObject(data.modes)) {
                        $.each(data.modes, function (key, value) {
                            $('#delivery_integration_mode').append('<option value="' + key + '">'+ value +'</option>');
                        });
                        $('#delivery_integration_mode').removeAttr('disabled');
                    } else {
                        $('#delivery_integration_mode').append('<option disabled selected value>' + trans.get('Select_property_value') + '</option>');
                        $('#delivery_integration_mode').attr('disabled', 'disabled');
                    }
                } else {
                    showError(data);
                }
            }
        );
    },
    addTariff: function (e) {
        e.preventDefault();

        window.location.href = $(e.target).data('link') + '&delivery=' + this.$('#delivery').val();

        return false;
    },
    deleteItem: function (e) {
        e.preventDefault();

        var target = this.$(e.target).is('i') ? this.$(e.target).parent() : this.$(e.target);
        
        var link = target.data('link');
        var item_id = target.data('id');
        var item_name = target.data('name');
        var action = target.data('href');        
        var msg = _.template(trans.get('delete_warning'), {item: item_name});
        
        confirmDialog(msg, function(){
            target.find('i').removeClass('icon-remove').addClass('ot-preloader-micro');
            $.post(action, { id: item_id } , function(data){            
                if (! data.error) {
                    window.location.href = link;
                } else {
                    target.find('i').removeClass('ot-preloader-micro').addClass('icon-remove');
                    showError(data);
                }
            }
            , 'json');
        });
        return false;
    },
    saveDelivery: function (e) {
        e.preventDefault();

        var target = this.$(e.target);

        var $button = target.button('loading');
        var action = target.closest('form').attr('action');
		
		if (! isValidForm()) {
			$button.button('reset');
			return false;
		}        
        
        $.post(action, target.closest('form').serializeArray(), function(data) {        
			if (! data.error) {
                window.location.href = target.data('link');
			} else {
				$('.input-xlarge').css({'border-color': '#ccc'});
                $button.button('reset');
				showError(data);
			}                
        });

        return false;
    },
    saveTariff: function (e) {
        e.preventDefault();

        var target = this.$(e.target);

        var $button = target.button('loading');
        var action = target.closest('form').attr('action');			

        $.post(action, target.closest('form').serializeArray(), function(data){
            if (! data.error) {
                showMessage(trans.get('Data_save_success'));
                window.location.href = target.data('link');                
            } else {   
                showError(data);
                $('.input-xlarge').css({'border-color': '#ccc'});
                $button.button('reset');                
            }
        });
        return false;
    }    
});

function isValidForm() {
	var deliveryName = $('[data-check = delivery_name]').val();
	var minWeight = parseFloat($('[data-check = min_weight]').val());
    var maxWeight = parseFloat($('[data-check = max_weight]').val());
    var stepWeight = parseFloat($('[data-check = step_weight]').val());

	var minPriceDelivery = $('[data-check = min_price_delivery]').prop("checked");	
	var customFormula = $('[data-check = save_custom_formula]').prop("checked");

	if (deliveryName == '') {
        showError(trans.get('Must_enter_delivery_name'));                        
        return false;
    }

    if (! customFormula) {
        if (isNaN(minWeight)) {
            minWeight = 0;
        }
        if (isNaN(maxWeight)) {
            maxWeight = 999;
        }
        if ((minWeight < 0) || (maxWeight < 0)) {
            showError(trans.get('Min_weight_or_max_weight_can_not_be_minus'));                        
            return false;
        }       
        if ((maxWeight != 0) && (minWeight > maxWeight)) {
            showError(trans.get('Min_weight_can_not_be_more_than_max_weight'));            
            return false;
        }
        if (! minPriceDelivery && isNaN(stepWeight)) {
            showError(trans.get('Must_be_checked_one_of_params_minpricedelivery_or_weightstep'));
            return false;
        }
    }
 
	return true;
}

$(function(){
    new ShipmentPage();
});
