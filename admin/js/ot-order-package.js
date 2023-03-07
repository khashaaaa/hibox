var OrderPackagePage = Backbone.View.extend({
    "el": ".order-package-wrapper",
    "events": {
        "submit #packageEditForm" : "checkPackageManualPrice",
        "change #DeliveryCountryCode" : "getDeliveryModes",
        "change #DeliveryModeId" : "checkPackagePrice",
        "change #Weight" : "getDeliveryModes",
        "change #user-package-profiles": "changePackageAddress",
        "change .item-check": "calculateWeight",
        "click #calculate-weight-btn": "replacePackageWeight"
    },
    "deliveryModes": null,
    render: function()
    {
        // не даем редактировать адрес, при наличии ПВЗ
        if ($('#DeliveryPickupPointCode').val()) {
            $('.AddressData').each(function(){
                $(this).find('input, select').prop('disabled', true);
            });
        }

        this.calculateWeight();
        return this;
    },
    initialize: function(){
        this.render();
    },
    calculateWeight: function()
    {
        var weight = 0;
        var items = $('.item-check');
        items.each(function (idx, element) {
            if ($(element).prop("checked")) {
                weight = weight + ($(this).data('weight-per-item') * $(this).data('item-count'));
            }
        });
        $('#calculate-weight').val(weight);
    },
    replacePackageWeight: function()
    {
        $('#Weight')
            .val($('#calculate-weight').val())
            .trigger("change");
    },
    changePackageAddress: function(ev)
    {
    	var profiles = $(ev.currentTarget);
    	var currentProfile = $('option:selected', profiles);
    	if ($(currentProfile).val() != 'default-user-profile') {
    		$('#DeliveryCountryCode option').removeAttr('selected');
    		$('#DeliveryCountryCode option[value="' + $(currentProfile).data('country')+ '"]').attr('selected','selected');
    		$('#DeliveryRegionName').val($(currentProfile).data('region'));
    		$('#DeliveryCity').val($(currentProfile).data('city'));	
    		$('#DeliveryAddress').val($(currentProfile).data('address'));
    		$('#DeliveryPostalCode').val($(currentProfile).data('postalcode'));
    		$('#DeliveryContactFirstname').val($(currentProfile).data('firstname'));
    		$('#DeliveryContactLastname').val($(currentProfile).data('lastname'));
    		$('#DeliveryContactMiddlename').val($(currentProfile).data('middlename'));
    		$('#DeliveryContactPhone').val($(currentProfile).data('phone'));
            $('#DeliveryContactINN').val($(currentProfile).data('inn'));
            $('#DeliveryContactPassportNumber').val($(currentProfile).data('passportnumber'));
            $('#DeliveryContactRegistrationAddress').val($(currentProfile).data('registrationaddress'));
    	} 
    },
    getDeliveryModes: function(ev){        
        var countryCode = this.$('#DeliveryCountryCode').val();
        var weight = this.$('#Weight').val();
        var cityCode = this.$('#DeliveryCityCode').val();
        var currentMode = this.$('#DeliveryModeId').val();
        var providerTypeEnum = this.$('#ProviderTypeEnum').val();
        var self = this;
        this.$("#DeliveryModeId").prepend($('<option value="0">'+trans.get('logo_label')+'</option>'));
        this.$("#DeliveryModeId :nth-child(1)").attr("selected", "selected");
        $.post(
            '?cmd=shipment&do=searchDeliveryModes',
            {   
                countryCode : countryCode, 
                weight : weight,
                cityCode : cityCode,
                providerTypeEnum : providerTypeEnum
            },
            function (data) {
                if (! data.error) {	
                    $('#DeliveryModeId').empty();
                    $.each(data.deliveryModes, function( index, item ) {
                        $("#DeliveryModeId").prepend($('<option value="' + item.id + '">' + item.name + '</option>'));
                    });
                    self.deliveryModes = data.deliveryModes;
                    $('#DeliveryModeId').val(currentMode);
                    self.checkPackagePrice();
                    $("#DeliveryModeId [value='" + currentMode + "']").attr("selected", "selected");                    				    
				} else {			
                    $("#DeliveryModeId [value='0']").remove();
                    showError(data.message);
				}
            }, 'json'
        );
        return true;
    },
    checkPackagePrice: function(ev) {
        var currentMode = this.$('#DeliveryModeId').val();
        if (currentMode == 0) {
            return false;
        }
        if (this.deliveryModes == null) {
            return false;
        }
        $.each(this.deliveryModes, function( index, item ) {
            if (item.id == currentMode) {
                $('#PriceInternal').val(item.Price);
            }
        });
    },
    checkPackageManualPrice: function(ev){
        var currentMode = this.$('#DeliveryModeId').val();
        if (currentMode == 0) {
            return false;
        }

        var currentPrice = this.$('.packagePriceBlock').find('input[name=CurrentPriceInternal]').val();
        var mayBeNewPrice = this.$('.packagePriceBlock').find('input[name=PriceInternal]').val();
		if (parseInt(mayBeNewPrice) < 0) {
			showError(trans.get('Price_can_not_be_below_zero'))
			return false;
		}
        if (parseFloat(currentPrice) != parseFloat(mayBeNewPrice)) {
            this.$('.packagePriceBlock').find('input[name=ManualPrice]').val('true');
        }
        return true;
    }
});

$(function(){
    var OPP = new OrderPackagePage();
});
