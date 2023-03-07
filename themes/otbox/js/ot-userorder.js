	// Check min order sum
function checkMinOrderSum() {
	if (minOrderPrice > 0 ) {
	    var totalPrice = $('span.products-price').data('value');
	    var priceSign = $('span.products-price').data('sign');
	    if (totalPrice < minOrderPrice) {
	    	showError(trans.get('min_cost') + ' ' + '<b>' + sdf_FTS(minOrderPrice, PRICE_ROUND_DECIMALS, ' ') + ' ' + priceSign + '</b>');
	    	return false;
	    }
	}
	return true;
}

// Refresh order information: price, weight, delivery info
function updateOrderSummary(ev, updatePrice) {
    var totalPrice = 0;
    calculateTotalWeightByRows();
    var totalWeight = $('span#total-weight').data('weight') ? $('span#total-weight').data('weight') : 0;
    var priceSign = '';
    var items = [];
    
    var calcTotalOrderCost = function(totalPrice) {
        // Delivery info
        var deliveryPrice = 0;
        var deliverySign = '';
        var deliveryName = trans.get('Not_selected');
        if ($('#externalDeliveries').length > 0
            && $('#externalDeliveries option:selected').length > 0
            && $('#externalDeliveries option:selected').val() !== '') {
            deliveryPrice = parseFloat($('#option_' + $('#externalDeliveries option:selected').val()).data('price'));
            deliverySign = $('#option_' + $('#externalDeliveries option:selected').val()).data('sign');
            deliveryName = $('#option_' + $('#externalDeliveries option:selected').val()).data('name');
            $('span#delivery-price').html(deliveryPrice + ' ' + deliverySign);
        } else {
            $('span#delivery-price').html(trans.get('Not_selected'));
        }
        
        $('span#externalDeliveryMode').html(deliveryName);
        $('span#order-price').html(sdf_FTS(totalPrice + deliveryPrice, PRICE_ROUND_DECIMALS, ' ') + ' ' + priceSign);
    };    
    
    var getTotalCost = function() {
        $.ajax({
            async : true,
            type: 'POST',
            dataType: 'json',
            url: getTotalCostsUrl,
            data : {
                items: items
            },
            success : function (data) {
                if (data.TotalCost && data.TotalCost.value && data.TotalCost.sign) {
                    $('span.products-price').html(sdf_FTS(data.TotalCost.value, PRICE_ROUND_DECIMALS, ' ') + ' ' + data.TotalCost.sign);
                    calcTotalOrderCost(data.TotalCost.value);
                    $('span.products-price').data('value', data.TotalCost.value);
                    $('span.products-price').data('sign', data.TotalCost.sign);
                }
            }
        });
    };
    
    if (updatePrice) {
        getTotalCost();
    } else {
        var totalCost = parseFloat($('span.products-price').data('value'));
        if (totalCost) {
            calcTotalOrderCost(totalCost);
        } else {
            getTotalCost();
        }
    }
        
    $('.total-weight')
        .data('weight', sdf_FTS(totalWeight, 3, ' '))
        .text(sdf_FTS(totalWeight, 3, ' '));
    $('input#totalOrderWeight').val(sdf_FTS(totalWeight, 3, ' '));
}

// Remove product item remove with сonfirmation
function confirmRemoveOrderItem(id) {
    var html = trans.get('sure_delete');
    modalDialog(trans.get('delete_item'), html,
        function (body) {
            var tr = $('tr[data-id="' + id + '"]');
            // удаляем дополнительную стоимость, если она есть
            if (tr.prev('.order-item-group-data').length) {
                tr.prev('.order-item-group-data').remove();
            }

            // удаляем сам товар
            var $items = $('input[name="order\[items\]"]'),
                items = $items.val();

            items = items.split(',');
            var itemIndex = $.inArray(id + "", items);
            items.splice(itemIndex, 1);
            items = items.join(',');
            $items.val(items);

            $(tr).remove();
            if ($('table.basket tr.order-item').length==0) {
                $('#order-overlay').show();
                window.location.replace('/?p=basket');
            }

            updateOrderSummary({}, true);
        },
        { confirm: trans.get('delete'), cancel: trans.get('cancel') }
    );
}

function calculateTotalWeightByRows() {
    var $rows = $('tr.order-item'),
        $total = $('span#total-weight');

    if ($rows.length) {
        var totalWeight = 0;
        $rows.each(function() {
            var weight = parseFloat($(this).data('weight'));
            var qty = parseFloat($('.item-qty', this).text());
            if (isNaN(weight)) {
                weight = 0;
            }
            totalWeight += (weight * qty);
        });

        $total
            .data('weight', sdf_FTS(totalWeight, 3, ' '))
            .text(sdf_FTS(totalWeight, 3, ' '));

        return totalWeight;
    }

    return false;
}

// Validate delivery profile fields
function validateDeliveryAddress()
{
    var noError = true;
    var requiredFields = $('.required').find(':input').filter(function () {
        var id = $(this).attr('id'),
            exceptionWord = "selectized";
        return id.indexOf(exceptionWord) === -1;
    });

    requiredFields.each(function (idx) {
        if ($.trim($(this).val()) == '') {
            var control = $(this).closest('.form-group');
            control.addClass('has-error');
            setTimeout(function () {
                control.removeClass('has-error');
            }, 10000);
            noError = false;
        }
    });
    if (!noError) {
        showError(trans.get('not_filled_required_field'));
        $('#createorder').removeClass('btn-success').addClass('btn-danger');
    }

    return noError;
}

// Activate next section
function nextStep(btn) {
	var group = $(btn).closest('.accordion-group');
	var nextGroup = $(group).next();
    group.children('.accordion-body').collapse('hide');
    nextGroup.children('.accordion-body').collapse('show');
	$(group).addClass('done');
}

// Update changed delivery profile in internal store
function updateDeliveryProfiles(profile) {
	var found = false;
	$.each(profiles, function(i, item){
		if (item.Id === profile.Id) {
            profile.CountryName = profile.CountryCode;
            $.each(countries, function(i, country){
                if (profile.CountryCode === country.Id) {
                    profile.CountryName = country.Name
                }
            });
            // Если город введен вручную, CityCode может быть
            // не задан, задать его равным City
            if (typeof profile.CityCode === 'undefined') {
                profile.CityCode = profile.City;
            }
            if (typeof profile.MiddleName === 'undefined') {
                profile.MiddleName = '';
            }
			profiles[i] = profile;
			found = true;
		}
	});
	if (! found) {
        profile.CountryName = profile.CountryCode;
        $.each(countries, function(i, country){
            if (profile.CountryCode === country.Id) {
                profile.CountryName = country.Name
            }
        });
        // Если город введен вручную, CityCode может быть
        // не задан, задать его равным City
        if (typeof profile.CityCode === 'undefined') {
            profile.CityCode = profile.City;
        }
		profiles.push(profile);
	}
}

// Fill in the profile fields
function fillProfileControlls (profileId) {
    if (typeof profileId !== 'undefined') {
        $.each(profiles, function(i, item){
            if (item.Id == profileId) {
                profile = item;
            }
        });
        if (profile) {
            var oldCountry = $("#Country option:selected").val();

            $('#LastName').val(profile.LastName);
            $('#FirstName').val(profile.FirstName);
            $('#MiddleName').val(profile.MiddleName);
            $('#INN').val(profile.INN);
            if ($('#PassportNumber').length) $('#PassportNumber').val(profile.PassportNumber);
            if ($('#RegistrationAddress').length) $('#RegistrationAddress').val(profile.RegistrationAddress);

            $('#Phone').val(profile.Phone);
            $('#Address').val(profile.Address);
            $('#PostalCode').val(profile.PostalCode);

            $("#Country option").removeAttr('selected');
            $("#Country option:selected").prop('selected', false);
            $("#Country option[value='" + profile.CountryCode + "']").attr("selected", "selected");
            $("#Country option[value='" + profile.CountryCode + "']").prop("selected", true);

            var citiesSelectize = $('#CityCode')[0].selectize;
            if (oldCountry !== profile.CountryCode) {
                citiesSelectize.clearOptions(true);
                cities = [];
            }
            var cityCode = (profile.CityCode !== '') ? profile.CityCode : profile.City;
            var option = {'cityCode':cityCode, 'cityName':profile.City, 'regionName':profile.region};

            /* Добавить город в хранилище если еще не добавлен */
            var existsInStorage = false;
            for (var i = 0; i < cities.length; i++) {
                if (cities[i].cityCode === cityCode) {
                    existsInStorage = true;
                }
            }
            if (!existsInStorage) {
                cities.push(option);
            }

            citiesSelectize.addOption(option);
            citiesSelectize.setValue(option.cityCode);
            citiesSelectize.refreshOptions(false);
            $('#City').val(profile.City);
            // Установить регион после установки города
            // т.к. изменение города установит регион от поиска
            $('#Region').val(profile.Region);

        } else {
            var countriesCount = $('#Country option').length;
            if (countriesCount == 1) {
                var code = $('#Country').val();
                $("#Country option").removeAttr('selected');
                $("#Country option:selected").prop('selected', false);
                $("#Country option[value='" + code + "']").attr("selected", "selected");
                $("#Country option[value='" + code + "']").prop("selected", true);
            }
            var citiesSelectize = $('#CityCode')[0].selectize;
            citiesSelectize.clearOptions();
            citiesSelectize.refreshOptions(false);
        }
    } else {
        if (profiles.length === 0) {
            // автозаполнение из данных пользователя
            $('#LastName').val($('#LastName').data('default'));
            $('#FirstName').val($('#FirstName').data('default'));
            $('#MiddleName').val($('#MiddleName').data('default'));
            $('#Phone').val($('#Phone').data('default'));
        } else {
            $('#LastName').val('');
            $('#FirstName').val('');
            $('#MiddleName').val('');
            $('#Phone').val('');
        }

        $('#INN').val('');
        if ($('#PassportNumber').length) $('#PassportNumber').val('');
        if ($('#RegistrationAddress').length) $('#RegistrationAddress').val('');
        $('#PostalCode').val('');
        var citiesSelectize = $('#CityCode')[0].selectize;
        citiesSelectize.clearOptions();
        citiesSelectize.refreshOptions(false);
        $('#Region').val('');
        $('#City').val('');
        $('#Address').val('');
    }
}

// Load external deliveries from server for order products
function loadExternalDeliveries(countryCode, cityCode = '') {
    calculateTotalWeightByRows();
    var weight = $('span#total-weight').data('weight') ? $('span#total-weight').data('weight') : 0;
    
    var provider = $('#Provider').val();
    var externalDeliveriesContainer = $('#externalDeliveriesContainer');
    var deliveryId = externalDeliveriesContainer.data('deliveryid');
    var profileDeliveryId = 0;
    var profilePickupPointCode = 0;
    if (typeof profile !== 'undefined') {
        if (profile.ExternalDeliveryId) {
            profileDeliveryId = profile.ExternalDeliveryId;
        }
        if (profile.PickupPointCode) {
            profilePickupPointCode = profile.PickupPointCode;
        }
    }

    // не отправлять на сервер CityCode если город введен вручную
    if (!cityExists(cityCode) && cityCode !== '') {
        cityCode = '';
    }

    externalDeliveriesContainer.html('<div class="delivery_preload"/>');
    var selectedDelivery = (typeof selectedExternalDeliveryMode !== 'undefined') ? selectedExternalDeliveryMode : 0;
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: getExternalDeliveriesUrl,
        data : {
            "country" : countryCode,
            "city" : cityCode,
            "weight" : weight,
            "type" : provider,
            "deliveryId": deliveryId,
            "profileDeliveryId" : profileDeliveryId,
            "selectedDeliveryId": selectedDelivery
        },
        success : function (data) {
            if (data.error) {
                showError(data);
            }

            if ((typeof data.countryPrices !== 'undefined') && (data.countryPrices.length)) {
                var externalDeliveriesSelect = $('<select id="externalDeliveries" name="Profile[ExternalDeliveryId]"><select/>');
                externalDeliveriesContainer.html(externalDeliveriesSelect);

                externalDeliveriesSelect.selectize({
                    valueField: 'id',
                    options: data.countryPrices,
                    onChange: function(value) {
                        // очистка ранее полученных ПВЗ
                        $('#adressRow').removeClass('hidden');
                        $('#pickupPoints').html('');
                        selectedExternalDeliveryMode = value;
                        var isPickupPointMode = $('#option_' + value).data('pickuppointmode');
                        if (isPickupPointMode === true) {
                            $('#adressRow').addClass('hidden');
                            $('#pickupPoints').html('<div class="delivery_preload"/>');

                            $.ajax({
                                type: 'POST',
                                dataType: 'json',
                                url: getPickupPointsUrl,
                                data : {
                                    "country" : countryCode,
                                    "city" : cityCode,
                                    "deliveryMode" : value,
                                    "profilePickupPointCode" : profilePickupPointCode,
                                },
                                success : function (data) {
                                    if (! data.error && data.pickupPoints.length) {
                                        var pickupPointListItem = '';
                                        var pickupPointListItemContent = '';
                                        $.each(data.pickupPoints, function( index, point ) {
                                            var checked = '';
                                            var active = '';
                                            if (point.PickupPointCode === data.defaultPickupPointCode) {
                                                checked = 'checked="checked"';
                                                active = 'active';
                                            }
                                            pickupPointListItem += '<li class="' + active + '"><a href="#' + point.PickupPointCode + '" class="externalDeliveryMode" data-toggle="tab">'
                                                + '<input type="radio" '+ checked + ' name="Profile[PickupPointCode]" value="' + point.PickupPointCode + '" data-address="' + point.Address + '" data-postalcode="' + point.PostalCode + '"/>'
                                                + point.DisplayName + '</a></li>';
                                            pickupPointListItemContent += '<div class="tab-pane ' + active + '" id="' + point.PickupPointCode + '">'
                                                +'<div>' + point.Description + '</div></div>';
                                        });
                                        var navTabs = '<ul class="nav nav-tabs">' + pickupPointListItem + '</ul>';
                                        var tabContent = '<div class="tab-content">' + pickupPointListItemContent + '</div>';
                                        var pickupPointList = '<div class="order-subheading"><span>' + trans.get('pickup_points') + '</span></div>' +
                                            '<div class="tabbable tabs-right">'
                                            + navTabs
                                            + tabContent
                                            + '</div>';
                                        $('#pickupPoints').html(pickupPointList);
                                    } else {
                                        showError(data);
                                        $('#pickupPoints').html('');
                                    }

                                },
                                error: function () {
                                }
                            });
                        }
                        updateOrderSummary();
                    },
                    render: {
                        option: function (data, escape) {
                            return '<div id="option_' + data.id + '" data-price="' + data.price + '" data-sign="' + data.CurrencySign + '" data-name="' + data.name + '" data-pickuppointmode="' + data.isPickupPointMode + '" ><table>'
                                + '<tr>'
                                + '<td class="title">'
                                + escape(data.name)
                                + '</td>'
                                + '<td class="title" rowspan="2" style="text-align: right">'
                                + sdf_FTS(escape(data.price), PRICE_ROUND_DECIMALS, ' ') + ' ' + escape(data.CurrencySign)
                                + '</td>'
                                + '</tr>'
                                + '<tr>'
                                + '<td class="description">'
                                + escape(data.description)
                                + '</td>'
                                + '</tr>'
                                + '</table></div>';
                        },
                        item: function (data, escape) {
                            return '<div data-price="' + data.price + '" data-sign="' + data.CurrencySign + '" data-name="' + data.name + '" data-pickupPointMode="' + data.pickuppointmode + '" style="width: 100%"><table>'
                                + '<tr>'
                                + '<td class="title">'
                                + escape(data.name)
                                + '</td>'
                                + '<td class="title" rowspan="2" style="text-align: right">'
                                + sdf_FTS(escape(data.price), PRICE_ROUND_DECIMALS, ' ') + ' ' + escape(data.CurrencySign)
                                + '</td>'
                                + '</tr>'
                                + '<tr>'
                                + '<td class="description">'
                                + escape(data.description)
                                + '</td>'
                                + '</tr>'
                                + '</table></div>';
                        }
                    }
                });

                externalDeliveriesSelect[0].selectize.setValue(data.defaultDelivery);
            } else {
                externalDeliveriesContainer.html('<div class="alert alert-error">' + trans.get('no_deliver_for_place_error') + '</div>');
            }
            updateOrderSummary();
        },
        error: function () {
            externalDeliveriesContainer.html('');
        }
    });
}

function loadCities(countryCode, query = '') {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: getCitiesUrl,
        data : {"country" : countryCode, "query" : query},
        success : function (data) {
            // обновление списка городов
            cities = data.cities;
            var citiesSelectize = $('#CityCode')[0].selectize;
            citiesSelectize.clearOptions(true);
            citiesSelectize.addOption(cities);
            citiesSelectize.refreshOptions(false);
        },
        error: function () {
        }
    });
}

function cityExists(cityCode)
{
    var exists = false;
    $.each(cities, function (key, city) {
        if (city.cityCode === cityCode) {
            if (city.cityCode !== city.cityName) {
                exists = true;
                return false;
            }
        }
    });
    return exists;
}

var validateOrder = function(){
    // если выбран ПВЗ подменить адрес и индекс получателя
    var selectedPickupPoint = $('[name=Profile\\[PickupPointCode\\]]:checked');
    if (selectedPickupPoint.length) {
        var deliveryAddress = selectedPickupPoint.data('address');
        var deliveryPostalCode = selectedPickupPoint.data('postalcode');
        $('#Address').val(deliveryAddress);
        $('#PostalCode').val(deliveryPostalCode);
    }

    if (! validateDeliveryAddress()) {
        return false;
    }

    // не отправлять на сервер CityCode если город введен вручную
    if (!cityExists($('#CityCode').val())) {
        $('#CityCode').prop('disabled', true);
    }

    return true;
};

$(document).ready(function(){
	// Create order button handler
	$('#createorder').click(function(e){
        if (!validateOrder()) {
            return false;
        }

		var form = $(e.currentTarget).closest('form');
        var totalWeight = $('span#total-weight').data('weight') ? $('span#total-weight').data('weight') : 0;

	    $('input#totalOrderWeight', form).val(sdf_FTS(totalWeight, 3, ' '));

	    $('#order-overlay').show();
		form.ajaxSubmit({
	        url: $(form).attr('action'),
	        type: 'POST',
	        dataType: 'json',
	        success: function(data) {
	            if (! data.error) {
	            	if (data.redirectUrl) {
	            		window.location.replace(data.redirectUrl);
	            	}
	            } else {
	                showError(data);
	                $('#order-overlay').hide();
	            }
	        },
	        error: function() {
	        	$('#order-overlay').hide();
	        }
	    });	
	});	

	$('#nextstep1').click(function(e) {
		if (! allowMerge || ! ordersCount) {
			if (! checkMinOrderSum()) {
				return false;
			};
		}
		nextStep(e.currentTarget);
	});

	$('#nextstep2').click(function(e){
		if ($(e.currentTarget).data('merge')) {
			// Merge orders if order selected
			$('#createorder').trigger('click');
		} else {
			if (! checkMinOrderSum()) {
				return false;
			}
			nextStep(e.currentTarget);
		}
	});

	$('.back-button').click(function(e){
	    var groups = $(e.currentTarget).parents('.accordion-group');
	    var group = $(groups[groups.length - 1]);
	    var prevGroup = $(group).prev();

        group.children('.accordion-body').collapse('hide');
        prevGroup.children('.accordion-body').collapse('show');
	});


	// Item weight change handler. Send new value to server.
	$('.notepad input.weight-value').change(function(){
		var tr = $(this).closest('tr');
	    var input = this;
	    var itemId = $(input).attr('itemid');
	    var itemWeight = $(input).val();
	    itemWeight = parseFloat(itemWeight.replace(',', '.'));
        if (isNaN(itemWeight)) {
	        itemWeight = 0;
	    }
        if (itemWeight < 0) {
	        itemWeight = 0;
	    }
	    $(tr).data('weight', itemWeight);
	    var itemQty = parseFloat($('.item-qty', tr).text()); 
	    $(input).val(itemWeight);
	    $(this).attr('disabled', 'disabled');
	    $('#order-overlay').show();
	    
	    $.ajax({
	        async : true,
	        type: 'POST',
	        dataType: 'json',
	        url: $('.weight-value').eq(0).data('action'),
	        data : {
	            "id" : itemId,
	            "weight" : itemWeight,
	        },
	        success : function (data) {
	            $(input).removeAttr('disabled');
	            $('#order-overlay').hide();
	            $('.item-weight', tr).text(sdf_FTS(itemQty * itemWeight, 3, ' '));
	            loadExternalDeliveries($('#Country').val());
	        }
	    });
	});
	// Item commend change handler. Send new value to server.
	$('.notepad textarea.item-comment').change(function(){
	    var input = this;
	    var itemId = $(input).attr('itemid');
	    var comment = $(input).val();
	    $(this).attr('disabled', 'disabled');
	    $('#order-overlay').show();
	    
	    $.ajax({
	        async : true,
	        type: 'POST',
	        dataType: 'json',
	        url: $('.item-comment').eq(0).data('action'),
	        data : {
	            "id" : itemId,
	            "comment" : comment,
	        },
	        success : function (data) {
	            if (data.error) {
	            } else {
	            }

	            $(input).removeAttr('disabled');
	            $('#order-overlay').hide();
	            updateOrderSummary();
	        }
	    });
	});


	// Select order to merge.
	var selectOrder = function() {
	    var html = $('#orders-list').html();
	    modalDialog(trans.get('click_order'), html, function (body) {
		        if ($('input[type="radio"]:checked', body).length == 0) {
		            alert(trans.get('select_order_to_merge'));
		            return false;
		        }
		        
		        var orderId  = $('input[type="radio"]:checked', body).val();
		        $('#baseOrder').val(orderId);
		        var tr = $('input[type="radio"]:checked', body).closest('tr');
		        var order = $('td:eq(1)', tr).html();
		        $('#old-order-id').html(trans.get('merge_order_with') + ': <br/><br/>' + order);
		        $('a#nextstep2').data('merge', true);
		        $('#nextstep2').text(trans.get('merge_order'));
		        $('div.accordion-group.new-order').hide();        
		        
		    },{ confirm: trans.get('merge_order'), cancel: trans.get('cancel') }, function(body) {
	            var dialog = $(body).closest('.confirmDialog');
	            $(dialog).addClass('order-list-dialog');
	            var cancelDialog = function(ev) {
	                if (! ev.isTrigger ) {
	                    $('#new-order').trigger('click');
	                }
	            }; 
	            $('#cancelBtn', dialog).click(cancelDialog);
	            $('button.close', dialog).click(cancelDialog);
	        });  
	};	
	
	// Reset to new order, no merge.
	var newOrder = function(){
	    $('#old-order-id').html(trans.get('select_from_exists'));
	    $('#baseOrder').val('new');
	    $('a#nextstep2').data('merge', false);
	    $('#nextstep2').text(trans.get('continue'));
	    $('div.accordion-group.new-order').show();
	};

	$('#existing-order').click(selectOrder);
	$('#existing-order').on('selectOrder', selectOrder);
	$('#new-order').click(newOrder);
	$('#new-order').on('newOrder', newOrder);


	// Process click on table row to activate row radio option
	$(document).on('click', 'td.radio-option', function(){
	    var table = $(this).closest('table');
	    var tr = $(this).closest('tr');
	    var radio = $('input[type="radio"]', tr);
	    $('input[type="radio"]', table).removeAttr('checked');
	    $('input[type="radio"]', table).prop('checked', false);
	    $('tr', table).removeClass('active-option');
	    $(radio).prop('checked', true);
	    $(radio).trigger('selectOrder');
	    $(radio).trigger('newOrder');
	    $(tr).addClass('active-option');
	});
	// Change radio selection handler. Highlight selected option.
	$(document).on('change', 'input[type="radio"]', function(){
	    if (!$(this).is(':checked')) {
	        return false;
	    }
	    var table = $(this).closest('table');
	    var tr = $(this).closest('tr');
	    var radio = $(this);
	    $('tr', table).removeClass('active-option');
	    $(radio).attr('checked', 'checked');
	    $(radio).prop('checked', true);
	    $(tr).addClass('active-option');
	});

    var citiesSelect = $('#CityCode')
        .selectize({
            valueField: 'cityCode',
            labelField: 'cityName',
            searchField: 'cityName',
            create: true,
            // createOnBlur: true,
            diacritics: true,
            options: cities,
            onBlur: function() {
                var citiesSelectize = $('#CityCode')[0].selectize;
                var currentValue = citiesSelectize.getValue();
                if (currentValue === '') {
                    var countryCode = $('#Country').find('option:selected').val();
                    loadExternalDeliveries(countryCode);
                }
            },
            onChange: function(value) {
                var countryCode = $('#Country').find('option:selected').val();
                if (value === '') {
                    loadCities(countryCode); // получить основные города при удалении города
                    loadExternalDeliveries(countryCode, value);
                } else {
                    loadExternalDeliveries(countryCode, value);
                }

                // В зависимости от кода города заполнить название города и название региона
                if  (cities.length) {
                    $.each(cities, function(i, item){
                        if (item.cityCode === value) {
                            $('#Region').val(item.regionName);
                            $('#City').val(item.cityName);
                            return false;
                        } else {
                            $('#City').val(value);
                        }
                    });
                } else {
                    $('#City').val(value);
                }
            },
            load: function(query, callback) {
                var countryCode = $('#Country').find('option:selected').val();
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: getCitiesUrl,
                    data : {"country" : countryCode, "query" : query},
                    success : function (data) {
                        cities = data.cities;

                        var citiesSelectize = $('#CityCode')[0].selectize;
                        // если значение уже выбрано
                        var currentValue = citiesSelectize.getValue();
                        if (currentValue) {
                            var foundCity = {};
                            $.each(cities, function (idx, city) {
                                if (currentValue.trim().toLowerCase() === city.cityName.trim().toLowerCase()
                                || currentValue.trim().toLowerCase() === city.cityCode.trim().toLowerCase()) {
                                    foundCity = city;
                                    return false;
                                }
                            });
                            citiesSelectize.clearOptions(true);
                            if (!$.isEmptyObject(foundCity)) {
                                citiesSelectize.addOption(foundCity);
                                citiesSelectize.setValue(foundCity.cityCode);
                            } else {
                                citiesSelectize.addOption({'cityCode':currentValue, 'cityName':currentValue, 'regionName':$('#Region').val()});
                                citiesSelectize.setValue(currentValue);
                            }
                        } else { // если город еще не выбран, до нажатия Enter

                            var foundCity = {};
                            $.each(cities, function (idx, city) {
                                if (query.toLowerCase() === city.cityName.trim().toLowerCase()) {
                                    foundCity = city;
                                    return false;
                                }
                            });

                            citiesSelectize.clearOptions(true);
                            callback(cities);

                            // скрыть кнопку добавления города, если такой уже есть в списке
                            var addCityButton = $('#CityCode').siblings('.selectize-control').find('.create');
                            (!$.isEmptyObject(foundCity)) ? addCityButton.hide() : addCityButton.show();
                        }

                        /* Добавить город в хранилище если еще не добавлен */
                        var option = {'cityCode':query, 'cityName':query, 'regionName':$('#Region').val()};
                        var existsInStorage = false;
                        for (var i = 0; i < cities.length; i++) {
                            if (cities[i].cityCode === query) {
                                existsInStorage = true;
                            }
                        }
                        if (!existsInStorage) {
                            cities.push(option);
                        }
                    },
                    error: function () {
                        callback();
                    }
                });
            },
            render: {
                option_create: function(data, escape) {
                    return '<div class="create">'+ trans.get('add') + ' <strong>' + escape(data.input) + '</strong>&hellip;</div>';
                },
                option: function (data, escape) {
                    if (typeof data.regionName === 'undefined') {
                        data.regionName = '';
                    }
                    return '<div class="profile-option">'
                        + '<div class="title">'
                        + escape(data.cityName)
                        + '</div>'
                        + '<div class="description">'
                        + escape(data.regionName)
                        + '</div>'
                        + '</div>';
                }
            }
        });

    fillProfileControlls();
	var profileSelect = $('#delivery_profile')
        .selectize({
            valueField: 'Id',
            options: profiles,
            onChange: function(value) {
                fillProfileControlls(value);
            },
            render: {
                option: function (data, escape) {
                    return '<div class="profile-option">'
                        + '<div class="title">'
                        + escape(data.LastName) + ' ' + escape(data.FirstName) + ' ' + escape(data.MiddleName)
                        + '</div>'
                        + '<div class="description">'
                        + escape(data.CountryName) + ' ' + escape(data.City) + ' ' + escape(data.Region) + ' ' + escape(data.Address) + ' ' + escape(data.PostalCode)
                        + '</div>'
                        + '</div>';
                },
                item: function (data, escape) {
                    return '<div class="profile-option">'
                        + '<div class="title">'
                        + escape(data.LastName) + ' ' + escape(data.FirstName) + ' ' + escape(data.MiddleName)
                        + '</div>'
                        + '<div class="description">'
                        + escape(data.CountryName) + ' ' + escape(data.City) + ' ' + escape(data.Region) + ' ' + escape(data.Address) + ' ' + escape(data.PostalCode)
                        + '</div>'
                        + '</div>';
                }
            }
        });
	if (profileSelect.length) {
        profileSelect[0].selectize.setValue(profiles[0].id);
    }

    $(document).on('click', '.externalDeliveryMode', function (e) {
        $(this).find('input[type="radio"]').prop("checked", true);
    });
    $(document).on('click', '.externalDeliveryMode input[type="radio"]', function (e) {
        $(this).prop("checked", true);
        e.stopPropagation();
    });

	$(document).on('change', '#Country', function (e) {
	    var countryCode = $(this).find('option:selected').val();
        $('#Region').val('');
        loadCities(countryCode);
        loadExternalDeliveries(countryCode);
    });

    $('#addProfile').on('click', function (e) {
        $("#delivery1").collapse('toggle');
        fillProfileControlls();

        $('#delivery_profile')[0].selectize.disable();
        $(this).parent().toggleClass('hidden');
        $('#profileSaveButtons').toggleClass('hidden');
    });

    $('#editProfile').on('click', function (e) {
        var profileId = $('#delivery_profile').find('option:selected').val();
        $(this).append($('<input type="hidden" name="Profile[Id]" value="' + profileId + '">'));
        $('#delivery_profile')[0].selectize.disable();

        $("#delivery1").collapse('toggle');
        $(this).parent().toggleClass('hidden');
        $('#profileSaveButtons').toggleClass('hidden');
    });

    $('#cancelSaveProfile').on('click', function (e) {
        $('input[name="Profile[Id]"]').remove();
        $("#delivery1").collapse('toggle');
        $('#delivery_profile')[0].selectize.enable();

        delete selectedExternalDeliveryMode;
        var profileId = $('#delivery_profile').find('option:selected').val();
        fillProfileControlls(profileId);

        $(this).parent().toggleClass('hidden');
        $('#profileActionButtons').toggleClass('hidden');
    });

    $('#saveProfile').on('click', function (e) {
        // если выбран ПВЗ подменить адрес и индекс получателя
        var selectedPickupPoint = $('[name=Profile\\[PickupPointCode\\]]:checked');
        if (selectedPickupPoint.length) {
            var deliveryAddress = selectedPickupPoint.data('address');
            var deliveryPostalCode = selectedPickupPoint.data('postalcode');
            $('#Address').val(deliveryAddress);
            $('#PostalCode').val(deliveryPostalCode);
        }

        if (! validateDeliveryAddress()) {
            return false;
        }
        $('#order-overlay').show();

        // не отправлять на сервер CityCode если город введен вручную
        if (!cityExists($('#CityCode').val())) {
            $('#CityCode').prop('disabled', true);
        }

        var self = $(this);
        var form = $(e.currentTarget).closest('form');
        form.ajaxSubmit({
            url: $('#saveProfile').data('action'),
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (! data.error) {
                    updateDeliveryProfiles(data.profile);
                    updateOrderSummary();

                    // обноаление списка профилей
                    var profileSelectize = $('#delivery_profile')[0].selectize;
                    var currentValue = profileSelectize.getValue();
                    if (currentValue !== data.profile.Id) {
                        // профиль добавлен
                        profileSelectize.addOption(data.profile);
                        if (profiles.length === maxUserProfilesCount) {
                            $('#profileActionButtons').find('#addProfile').remove();
                        }
                    } else {
                        // профиль изменен
                        profileSelectize.updateOption(data.profile.Id, data.profile);
                    }
                    profileSelectize.setValue(data.profile.Id, true);
                    profileSelectize.enable();

                    $('input[name="Profile[Id]"]').remove();
                    $("#delivery1").collapse('toggle');
                    self.parent().toggleClass('hidden');
                    $('#profileActionButtons').toggleClass('hidden');
                } else {
                    showError(data);
                }
                $('#order-overlay').hide();
            },
            error: function() {
                 $('#order-overlay').hide();
            }
        });
        $('#CityCode').prop('disabled', false);
    });

    $('.required')
        .find(':input').filter(function () {
            var id = $(this).attr('id'),
                exceptionWord = "selectized";
            return id.indexOf(exceptionWord) === -1;
        })
        .on('change', function (e) {
        $('#createorder').removeClass('btn-danger').addClass('btn-success');
    });

    $('.notepad.orders input[type="radio"]').change();

    if (profiles.length === 0) {
        loadExternalDeliveries($('#Country').val());
    }
	updateOrderSummary();
});
