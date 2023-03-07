var UserOrderView = Backbone.View.extend({
    el: '#new-order-create, .quick-order',
    events: {
        'click .createOrder': 'createOrder',
        'change .list-products .weight-value': 'changeItemWeight',
        'change .list-products .item-comment': 'changeItemComment',
        'click #order-new': 'newOrder',
        'click #order-existing': 'showSelectOrderModal',
        'click #merge-cancel': 'selectOrderCancel',
        'click #merge-orders': 'selectOrder',
        'click .list-data-orders__line': 'changeOrder',
        'click .externalDeliveryMode': 'checkExternalDeliveryMode',
        'click .externalDeliveryMode input[type="radio"]': 'checkExternalDeliveryModeRadio',
        'change #Country': 'changeCountry',
        'click #addProfile': 'addProfile',
        'click #editProfile': 'editProfile',
        'click #cancelSaveProfile': 'cancelSaveProfile',
        'click #saveProfile': 'saveProfile',
        'click .button-delete-line': 'confirmRemoveOrderItem',
        'change .aside-right input[type="checkbox"]': 'changeAsideCheckbox',
    },


    initialize: function() {
        this.render();
        this.initializeCitiesSelect();
        this.fillProfileControlls();
        this.initializeProfileSelect();
        this.bindCreateOrderButtonChange();
        this.triggerOrderChange();
        if (profiles.length === 0) {
            this.loadExternalDeliveries($('#Country').val());
        }
        this.updateOrderSummary();
        this.updateMobileAsideHeight();
    },

    render: function() {
        var self = this;
        $('#modal-select-order').on('hide.bs.modal', function () {
            $('#order-new').trigger('click');
        });
        return this;
    },

    updateMobileAsideHeight: function() {
        var wrapper = $('.aside-right .sticky-top-mobile-wrap');
        var stickyContent = wrapper.find('.sticky-top-mobile');

        stickyContent.addClass('show');
        var stickyContentHeight = stickyContent.height();
        stickyContent.removeClass('show');

        wrapper.height(stickyContentHeight);
    },

    changeAsideCheckbox: function (e) {
        var checkbox = $(e.currentTarget).closest('.custom-checkbox').find('input[type="checkbox"]');
        var checkboxName = checkbox.data('input-class');

        checkbox.closest('.aside-right').find('.' + checkboxName + ' label.aside-checkbox').each(function (i, label) {
            label = $(label);
            if (label.attr('for') !== checkbox.attr('id')) {
                label.trigger('click');
            }
        });
        $('#order-delivery-form').find('.' + checkboxName).val(checkbox.prop('checked') ? 'on' : undefined);
    },

    checkMinOrderSum: function () {
        if (minOrderPrice > 0 ) {
            var totalPrice = this.$('.products-price').data('value');
            var priceSign = this.$('.products-price').data('sign');
            if (totalPrice < minOrderPrice) {
                showError(trans.get('min_cost') + ' ' + '<b>' + number_format(minOrderPrice, PRICE_ROUND_DECIMALS, ' ') + ' ' + priceSign + '</b>');
                return false;
            }
        }
        return true;
    },

    updateOrderSummary: function (ev, updatePrice) {
        var totalPrice = 0;
        this.calculateTotalWeightByRows();
        var totalWeight = this.$('.total-weight').first().data('weight') ? this.$('.total-weight').first().data('weight') : 0;
        var priceSign = '';
        var $items = $('input[name="order\[items\]"]');
        var items = $items.val();

        if (updatePrice) {
            this.getTotalCost(items);
        } else {
            var totalCost = parseFloat($('.products-price').data('value'));
            if (totalCost) {
                this.calcTotalOrderCost(totalCost);
            } else {
                this.getTotalCost(items);
            }
        }

        $('.total-weight')
            .data('weight', number_format(totalWeight, 2, ''))
            .text(number_format(totalWeight, 2, '') + ' ' + trans.get('weight_kg'));
        $('input#totalOrderWeight').val(number_format(totalWeight, 2, ''));
        this.updateMobileAsideHeight();
    },

    calcTotalOrderCost: function (totalPrice) {
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
            $('.delivery-price').html(number_format(deliveryPrice, PRICE_ROUND_DECIMALS, '&nbsp;') + ' ' + deliverySign);
        } else {
            $('.delivery-price').html(trans.get('Not_selected'));
        }

        $('.externalDeliveryMode').html(deliveryName);
        $('.order-price').html(number_format(totalPrice + deliveryPrice, PRICE_ROUND_DECIMALS, '&nbsp;') + '&nbsp;' + priceSign);
        this.updateMobileAsideHeight();
    },

    getTotalCost: function (items) {
        var self = this;
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
                    $('.products-price').html(number_format(data.TotalCost.value, PRICE_ROUND_DECIMALS, ' ') + ' ' + data.TotalCost.sign);
                    self.calcTotalOrderCost(data.TotalCost.value);
                    $('.products-price').data('value', data.TotalCost.value);
                    $('.products-price').data('sign', data.TotalCost.sign);
                }
            }
        });
    },

    confirmRemoveOrderItem: function (e) {
        var id = this.$(e.target).data('id');
        var html = trans.get('sure_delete');
        var self = this;
        modalDialog(trans.get('delete_item'), html,
            function (body) {
                var product = $('.list-products__row-item[data-id="' + id + '"]');
                // удаляем дополнительную стоимость, если она есть
                if (product.prev('.order-item-group-data-line').length) {
                    product.prev('.order-item-group-data-line').remove();
                }

                // удаляем сам товар
                var $items = $('input[name="order\[items\]"]'),
                    items = $items.val();

                items = items.split(',');
                var itemIndex = $.inArray(id + "", items);
                items.splice(itemIndex, 1);
                items = items.join(',');
                $items.val(items);

                $(product).remove();
                if ($('.list-products .list-products__row-item').length == 0) {
                    showOverlay();
                    window.location.replace('/?p=basket');
                }

                self.updateOrderSummary({}, true);
            },
            {
                confirm: trans.get('delete'),
                cancel: trans.get('cancel')
            }
        );
    },

    calculateTotalWeightByRows: function () {
        var $rows = $('.list-products__row-item'),
            $total = $('.total-weight');

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

            $total.data('weight', number_format(totalWeight, 2, '')).text(number_format(totalWeight, 2, '') + ' ' + trans.get('weight_kg'));

            return totalWeight;
        }

        return false;
    },

    validateDeliveryAddress: function () {
        var noError = true;
        var requiredFields = $('.list-data-delivery .form-group').find(':input').filter(function () {
            var id = $(this).attr('id'),
                exceptionWord = "selectized";
            return id.indexOf(exceptionWord) === -1;
        });

        requiredFields.each(function (idx) {
            var input = $(this);
            var inputBlock = input;
            if (input.hasClass('selectized')) {
                inputBlock = input.next('.selectize-control').find('.selectize-input');
                inputBlock.addClass('form-control');

            }
            var required = input.closest('.form-group').hasClass('required');

            if (required && $.trim(input.val()) === '') {
                inputBlock.addClass('is-invalid');
                noError = false;
            } else {
                inputBlock.addClass('is-valid');
            }
            setTimeout(function () {
                inputBlock.removeClass('is-invalid').removeClass('is-valid');
            }, 10000);
        });
        if (!noError) {
            $('.createOrder').addClass('button-red');
            setTimeout(function () {
                $('.createOrder').removeClass('button-red');
            }, 10000);
        }

        return noError;
    },

    updateDeliveryProfiles: function (profile) {
        var found = false;

        $.each(profiles, function(i, item){
            if (item.Id === profile.Id) {
                profile.CountryName = profile.CountryCode;
                $.each(countries, function(i, country){
                    if (profile.CountryCode === country.Id) {
                        profile.CountryName = country.Name;
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
                    profile.CountryName = country.Name;
                }
            });
            // Если город введен вручную, CityCode может быть
            // не задан, задать его равным City
            if (typeof profile.CityCode === 'undefined') {
                profile.CityCode = profile.City;
            }
            profiles.push(profile);
        }
    },

    fillProfileControlls: function (profileId) {
        if (typeof profileId !== 'undefined') {
            $.each(profiles, function (i, item) {
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
                $('#PassportIssueDate').val(profile.PassportIssueDate);

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
                var option = {'cityCode': cityCode, 'cityName': profile.City, 'regionName': profile.region};

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
    },

    loadExternalDeliveries: function (countryCode, cityCode = '') {
        if (typeof predefinedTotalWeight !== 'undefined') {
            var weight = predefinedTotalWeight;
        } else {
            this.calculateTotalWeightByRows();
            var weight = $('.total-weight').data('weight') ? $('.total-weight').data('weight') : 0;
        }

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
        if (! this.cityExists(cityCode) && cityCode !== '') {
            cityCode = '';
        }

        externalDeliveriesContainer.html('<div class="delivery_preload"/>');
        var selectedDelivery = (typeof selectedExternalDeliveryMode !== 'undefined') ? selectedExternalDeliveryMode : 0;
        var self = this;
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
                            $('#addressRow').removeClass('hidden');
                            $('#pickupPoints').html('');
                            selectedExternalDeliveryMode = value;
                            var isPickupPointMode = $('#option_' + value).data('pickuppointmode');
                            if (isPickupPointMode === true) {
                                $('#addressRow').addClass('hidden');
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
                            self.updateOrderSummary();
                        },
                        render: {
                            option: function (data, escape) {
                                return '<div id="option_' + data.id + '" data-price="' + data.price + '" data-sign="' + data.CurrencySign + '" data-name="' + data.name + '" data-pickuppointmode="' + data.isPickupPointMode + '" ><table>'
                                    + '<tr>'
                                    + '<td class="title">'
                                    + escape(data.name)
                                    + '</td>'
                                    + '<td class="title" rowspan="2" style="text-align: right">'
                                    + getCurrencyPrice(escape(data.price), escape(data.CurrencySign))
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
                                    + getCurrencyPrice(escape(data.price), escape(data.CurrencySign))
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
                self.updateOrderSummary();
            },
            error: function () {
                externalDeliveriesContainer.html('');
            }
        });
    },

    loadCities: function (countryCode, query = '') {
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
    },

    cityExists: function (cityCode) {
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
    },

    validateOrder: function () {
        // если выбран ПВЗ подменить адрес и индекс получателя
        var selectedPickupPoint = $('[name=Profile\\[PickupPointCode\\]]:checked');
        if (selectedPickupPoint.length) {
            var deliveryAddress = selectedPickupPoint.data('address');
            var deliveryPostalCode = selectedPickupPoint.data('postalcode');
            $('#Address').val(deliveryAddress);
            $('#PostalCode').val(deliveryPostalCode);
        }

        if (! this.validateDeliveryAddress()) {
            return false;
        }

        // не отправлять на сервер CityCode если город введен вручную
        if (! this.cityExists($('#CityCode').val())) {
            $('#CityCode').prop('disabled', true);
        }

        return true;
    },

    createOrder: function (e) {
        if (!this.checkMinOrderSum() || !this.validateOrder()) {
            return false;
        }

        var form = this.$('#order-delivery-form');
        var totalWeight = this.$('.total-weight').first().data('weight') ? $('.total-weight').first().data('weight') : 0;

        $('input#totalOrderWeight', form).val(number_format(totalWeight, 2, ' '));

        showOverlay();
        form.ajaxSubmit({
            url: $(form).attr('action'),
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                if (! data.error) {
                    if (data.redirectUrl) {
                        if (window.location.href.indexOf('userorder') === -1) {
                            window.location.assign(data.redirectUrl);
                        } else {
                            window.location.replace(data.redirectUrl);
                        }
                    }
                } else {
                    showError(data);
                    hideOverlay();
                }
            },
            error: function () {
                hideOverlay();
            },
        });
    },

    changeItemWeight: function (e) {
        var product = $(e.currentTarget).closest('.list-products__row-item');
        var input = e.currentTarget;
        var itemId = $(input).attr('itemid');
        var itemWeight = $(input).val();
        itemWeight = parseFloat(itemWeight.replace(',', '.'));
        if (isNaN(itemWeight)) {
            itemWeight = 0;
        }
        if (itemWeight < 0) {
            itemWeight = 0;
        }
        $(product).data('weight', itemWeight);
        var itemQty = parseFloat($('.item-qty', product).text());
        $(input).val(itemWeight);
        $(input).attr('disabled', 'disabled');
        showOverlay();

        var self = this;
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
                hideOverlay();
                $('.item-weight', product).text(number_format(itemQty * itemWeight, 2, ' '));
                self.loadExternalDeliveries($('#Country').val());
            }
        });
    },

    changeItemComment: function (e) {
        var input = e.currentTarget;
        var itemId = $(input).attr('itemid');
        var comment = $(input).val();
        $(input).attr('disabled', 'disabled');
        showOverlay();

        var self = this;
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
                hideOverlay();
                self.updateOrderSummary();
            }
        });
    },

    selectOrder: function (e) {
        var checkedRadio = $('table.list-data-orders input[type="radio"]:checked');
        if (checkedRadio.length == 0) {
            alert(trans.get('select_order_to_merge'));
            return false;
        }

        var orderId = checkedRadio.val();
        var orderData = checkedRadio.closest('.list-data-orders__line').data();

        $('#modal-select-order').modal('hide');
        $('#order-existing').prop('checked', true);

        var orderDataHtml = $('#selected-order-data');
        orderDataHtml.find('#selected-order-id').attr('href', orderData.link).html(orderData.id);
        orderDataHtml.find('#selected-order-creation-date').html(orderData.creationDate);
        orderDataHtml.find('#selected-order-items-count').html(orderData.itemsCount);
        orderDataHtml.find('#selected-order-total-amount').html(orderData.totalAmount);

        $('#baseOrder').val(orderId);
        $('#old-order-id').html(trans.get('merge_order_with') + ': <br/>' + orderDataHtml.html());
        $('.createOrder').data('merge', true).text(trans.get('merge_order'));
        $('.list-data-delivery').hide();
        $('.list-parcels__body').hide();
        $('.sticky-top-mobile-wrap .additional_options').hide();
        this.updateMobileAsideHeight();
    },

    selectOrderCancel: function (e) {
        $('#modal-select-order').modal('hide');
    },

    newOrder: function () {
        $('#old-order-id').html(trans.get('select_from_exists'));
        $('#baseOrder').val('new');
        $('.createOrder').data('merge', false).text(trans.get('make_order'));
        $('.list-data-delivery').show();
        $('.list-parcels__body').show();
        $('.sticky-top-mobile-wrap .additional_options').show();
        this.updateMobileAsideHeight();
    },

    showSelectOrderModal: function () {
        $('#modal-select-order').modal('show');
    },

    changeOrder: function (e) {
        var tr = $(e.currentTarget);
        var radio = tr.find('input[type="radio"]');
        var radioButtons = tr.closest('.list-data-orders').find('input[type="radio"]');

        radioButtons.prop('checked', false).closest('.list-data-orders__line').removeClass('active');
        radio.prop('checked', true).closest('.list-data-orders__line').addClass('active');
    },

    initializeCitiesSelect: function () {
        var self = this;
        this.citiesSelect = $('#CityCode').selectize({
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
                    /* проверить будет ли работать без инициализации нового объекта */
                    self.loadExternalDeliveries(countryCode);
                }
            },
            onChange: function(value) {
                var countryCode = $('#Country').find('option:selected').val();
                if (value === '') {
                    self.loadCities(countryCode); // получить основные города при удалении города
                } else {
                    self.loadExternalDeliveries(countryCode, value);
                }

                // В зависимости от кода города заполнить название города и название региона
                if (cities.length) {
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
    },

    initializeProfileSelect: function () {
        var self = this;
        this.profileSelect = $('#delivery_profile').selectize({
            valueField: 'Id',
            options: profiles,
            onChange: function(value) {
                self.fillProfileControlls(value);
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
        if (this.profileSelect.length) {
            this.profileSelect[0].selectize.setValue(profiles[0].id);
        }
    },

    checkExternalDeliveryMode: function (e) {
        $(e.currentTarget).find('input[type="radio"]').prop("checked", true);
    },

    checkExternalDeliveryModeRadio: function (e) {
        $(e.currentTarget).prop("checked", true);
        e.stopPropagation();
    },

    changeCountry: function (e) {
        var countryCode = $(e.currentTarget).find('option:selected').val();
        $('#Region').val('');
        this.loadCities(countryCode);
        this.loadExternalDeliveries(countryCode);
    },

    addProfile: function (e) {
        $("#delivery1").collapse('toggle');
        this.fillProfileControlls();

        $('#delivery_profile')[0].selectize.disable();
        $(e.currentTarget).parent().toggleClass('hidden');
        $('#profileSaveButtons').toggleClass('hidden');
    },

    editProfile: function (e) {
        var profileId = $('#delivery_profile').find('option:selected').val();
        $(e.currentTarget).append($('<input type="hidden" name="Profile[Id]" value="' + profileId + '">'));
        $('#delivery_profile')[0].selectize.disable();

        $("#delivery1").collapse('toggle');
        $(e.currentTarget).parent().toggleClass('hidden');
        $('#profileSaveButtons').toggleClass('hidden');
    },

    cancelSaveProfile: function (e) {
        $('input[name="Profile[Id]"]').remove();
        $("#delivery1").collapse('toggle');
        $('#delivery_profile')[0].selectize.enable();

        delete selectedExternalDeliveryMode;
        var profileId = $('#delivery_profile').find('option:selected').val();
        this.fillProfileControlls(profileId);

        $(e.currentTarget).parent().toggleClass('hidden');
        $('#profileActionButtons').toggleClass('hidden');
    },

    saveProfile: function (e) {
        // если выбран ПВЗ подменить адрес и индекс получателя
        var selectedPickupPoint = $('[name=Profile\\[PickupPointCode\\]]:checked');
        if (selectedPickupPoint.length) {
            var deliveryAddress = selectedPickupPoint.data('address');
            var deliveryPostalCode = selectedPickupPoint.data('postalcode');
            $('#Address').val(deliveryAddress);
            $('#PostalCode').val(deliveryPostalCode);
        }

        if (! this.validateDeliveryAddress()) {
            return false;
        }
        showOverlay();

        // не отправлять на сервер CityCode если город введен вручную
        if (! this.cityExists($('#CityCode').val())) {
            $('#CityCode').prop('disabled', true);
        }

        var self = $(e.currentTarget);
        var form = $(e.currentTarget).closest('form');
        var backboneObj = this;
        form.ajaxSubmit({
            url: $('#saveProfile').data('action'),
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (! data.error) {

                    backboneObj.updateDeliveryProfiles(data.profile);
                    backboneObj.updateOrderSummary();

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
                hideOverlay();
            },
            error: function() {
                hideOverlay();
            }
        });
        $('#CityCode').prop('disabled', false);
    },

    triggerOrderChange: function () {
        $('.list-data-order_select input[type="radio"]').change();
    },

    bindCreateOrderButtonChange: function () {
        $('.required')
            .find(':input').filter(function () {
                var id = $(this).attr('id'),
                    exceptionWord = "selectized";
                return id.indexOf(exceptionWord) === -1;
            })
            .on('change', function (e) {
                $('createOrder').removeClass('button-red');
            });
    },
});

$(function () {
    userOrderView = new UserOrderView();
});
