var ProductView = Backbone.View.extend({
    el: 'body',
    events: {
        'click .js-product .js-product-configurator-image': 'changeConfiguratorImage',
        'click .js-product .js-reviews-tab': 'getReviewsProxy',
        'click .js-product .js-product-quick-order-btn': 'quickOrder',
        'click .js-product .js-product-btn-in-garbage': 'addItemsToBasket',

        'click .js-product .js-product-btn-in-favorite': 'addItemsToFavourite',

        'click .js-product .js-product-add-vendor-to-favorites': 'addVendorToFavourite',
        'click .js-product .js-product-choose-delivery': 'showDeliveryModal',
        'change .js-product .js-product-configurator': 'getConfigurationInfoProxy',
        'change .js-product .js-product-quantity': 'getConfigurationInfoProxy',
        'change .js-product .js-product-multi-input-quantity': 'changeMultiInputQuantity',
        'change .js-reviews-source-checkbox': 'getReviewsProxy',
        'mouseover .js-product .js-product-btn-in-garbage': 'checkSelectedConfigsBasket',
        'mouseover .js-product .js-product-quick-order-btn': 'checkSelectedConfigsBasket',

        'mouseover .js-product js-product-btn-in-favorite': 'checkSelectedConfigsBasket',

        'mouseenter .js-product .js-product-slide-photo': 'showPhoto',
        'mouseenter .js-product .js-product-slide-video': 'showVideo',
    },

    /* необходимо для одновременной работы с несколькими товарами */
    products: {},

    initialize: function() {
        var $wrapper = this.$('.js-product');

        if ($wrapper.length) {
            this.bindGetFullInfoListener($wrapper);
            this.renderProduct($wrapper);
        }

        this.render();
    },

    render: function () {
        return this;
    },

    renderProduct: function ($wrapper) {
        var productId = $wrapper.data('productId');

        this.products[productId] = {
            configurationCurrent: {
                property: {}
            },
            configurationSelected: {},
            getConfigurationInfoXHR: null,
            currentDeliveryMode: null,
            currentTotalCost: null,
            currentTotalCostWithDelivery: null,
            currency: null,
        };

        $wrapper.find('.js-tooltip').tooltip();

        this.getConfigurationInfo($wrapper);

        var $reviewsTab = $wrapper.find('.js-nav-item .js-reviews-tab');
        if ($reviewsTab.length && $reviewsTab.hasClass('active') && $reviewsTab.hasClass('show')) {
            this.getReviews($wrapper);
        }
    },

    /* исполняется только для странитцы карточки товара */
    blockY: null, // координата самого верхнего блока, при достижении которой будет вызван getFullInfo
    fullInfoListener: false,
    bindGetFullInfoListener: function ($wrapper) {
        var self = this;

        // проверим наличие блоков, требующих отдельной загрузки
        var blocks = [];
        $wrapper.find('[data-full-info-need-request="1"]').each(function (i, el) {
            blocks.push($(el).attr('data-full-info-block'));
            if (self.blockY === null || self.blockY > $(el).offset().top) {
                self.blockY = $(el).offset().top;
            }
        });

        if (blocks.length) {
            _.bindAll(this, 'getFullInfoListener');
            $(window).scroll(this.getFullInfoListener);
            this.fullInfoListener = true;
        }
    },

    /* исполняется только для странитцы карточки товара */
    getFullInfoListener: function () {
        if (window.pageYOffset + window.innerHeight >= this.blockY) {
            $(window).off('scroll', '', this.getFullInfoListener);
            if (this.fullInfoListener) {
                this.fullInfoListener = false;
                this.getFullInfo();
            }
        }
    },

    /* исполняется только для странитцы карточки товара */
    getFullInfo: function() {
        var blocks = [];
        this.$('[data-full-info-need-request="1"]').each(function (i, el) {
            blocks.push($(el).attr('data-full-info-block'));
        });

        if (blocks.length) {
            var action = this.$('.js-product').data('actionGetFullInfo');
            $.post(action, {blocks: blocks}, function(data){
                if (data.error) {
                    showError(data.message);
                } else if (data.blocks) {
                    $.each(data.blocks, function(key, value) {
                        if (blocks.indexOf(key) != -1 && $('[data-full-info-block="' + key + '"]').length) {
                            $('[data-full-info-block="' + key + '"]').html(value.html);
                            if (value.html) {
                                $('[data-full-info-block="' + key + '"]').attr('data-full-info-need-request', 0);
                            }
                        }
                    });
                }
            }, 'json');
        }
    },

    checkSelectedConfigsBasket: function (e) {
        var $wrapper = this.$(e.currentTarget).closest('.js-product');
        var id = $wrapper.data('productId');

        if (Object.keys((this.products[id].configurationSelected)).length !== 0) {
            this.hideAllTooltips($wrapper);
        }
    },

    hideAllTooltips: function ($wrapper) {
        let $tooltips = $wrapper.find('.js-panel-buttons .js-tooltip, .js-panel-add .js-tooltip');
        $tooltips.tooltip('hide').tooltip('dispose');
    },

    disableButtons: function($wrapper) {
        var id = $wrapper.data('productId');

        if (Object.keys(this.products[id].configurationSelected).length === 0) {
            $wrapper.find('.js-product-btn-in-garbage, .js-product-btn-in-favorite, .js-product-quick-order-btn')
                .attr('disabled', 'disabled')
                .addClass('disabled_button');
            $wrapper.find('.js-tooltip').tooltip();
        } else {
            $wrapper.find('.js-product-btn-in-garbage, .js-product-btn-in-favorite, .js-product-quick-order-btn')
                .removeAttr('disabled')
                .removeClass('disabled_button');
        }
    },

    setActiveVideo: function ($el) {
        var $wrapper = $el.closest('.js-product');
        var src = $el.data('src');
        var poster = $el.data('poster');

        $wrapper.find('.js-product-main-video').attr({'src': src, 'poster': poster});
        this.showVideoBlock($wrapper);
    },

    showVideo: function (e) {
        var $el = this.$(e.target);
        var $wrapper = $el.closest('.js-product');

        if ($el.data('src') !== $wrapper.find('.js-product-main-video').attr('src')) {
            this.setActiveVideo($el);
        } else {
            this.showVideoBlock($wrapper);
        }
    },

    showPhoto: function (e) {
        var $el = this.$(e.currentTarget);
        var $wrapper = $el.closest('.js-product');
        var largeImgSrc = $el.data('srcLarge');
        var fullImgSrc = $el.data('srcUrl');

        this.setMainPhoto($wrapper, largeImgSrc, fullImgSrc);
    },

    setMainPhoto: function ($wrapper, largeImgSrc, fullImgSrc) {
        $wrapper.find('.js-product-main-photo').attr('src', largeImgSrc);
        $wrapper.find('.js-product-main-photo-link').attr('href', fullImgSrc);

        this.showPhotoBlock($wrapper);
    },

    showVideoBlock: function ($wrapper) {
        $wrapper.find('.js-product-main-photo-wrapper').hide();
        $wrapper.find('.js-product-main-video-wrapper').show();
    },

    showPhotoBlock: function ($wrapper) {
        $wrapper.find('.js-product-main-video-wrapper').hide();
        $wrapper.find('.js-product-main-photo-wrapper').show();
    },

    showItemsToBakset: function ($wrapper) {
        var id = $wrapper.data('productId');

        if (JSON.stringify(this.products[id].configurationSelected) === '{}') {

        } else {
            var total = 0;
            var totalCost = 0;
            var img = null;

            var $mainPhoto = $wrapper.find('.js-product-main-photo');
            var mainPhotoSrc = $mainPhoto.attr('src');

            if (mainPhotoSrc !== '' && mainPhotoSrc !== '/i/noimg.png') {
                img = mainPhotoSrc;
            } else {
                img = $wrapper.find('.js-product-slide-photo').first().data('srcLarge');
            }

            for (var key in this.products[id].configurationSelected) {
                total = total + this.products[id].configurationSelected[key];
            }

            if (this.products[id].currentTotalCostWithDelivery !== null) {
                totalCost = getCurrencyPrice(this.products[id].currentTotalCostWithDelivery, this.products[id].currency);
            } else {
                totalCost = getCurrencyPrice(this.products[id].currentTotalCost, this.products[id].currency);
            }

            $wrapper.find('.js-product-total-amount').text(total);
            $wrapper.find('.js-product-total-cost').html(totalCost);
            $wrapper.find('.js-products-modal-add-done-img').attr('src', img);
        }
    },

    changeConfiguratorImage: function (e) {
        e.preventDefault();

        var $elProperty = this.$(e.currentTarget);
        var $configuratorWrapper = $elProperty.closest('.js-product-configurator-images');
        var $wrapper = $elProperty.closest('.js-product');
        var isPropertyActive = $elProperty.hasClass('active');
        var needGetConfig = !$elProperty.closest('.js-product-multi-input-table').length;

        /* ничего не делаем, если конфигурация недоступна */
        if ($elProperty.hasClass('disabled')) {
            return false;
        }

        if (!needGetConfig) {
            $elProperty
                .closest('.js-product-multi-input-table')
                .find('.js-product-configurator-image')
                .removeClass('active');
        }

        if (isPropertyActive) {
            /* если конфигурация уже активна, то деактивируем ее */
            $elProperty.removeClass('active');
        } else {
            /* иначе активируем */
            $configuratorWrapper.find('.js-product-configurator-image').removeClass('active');
            $elProperty.addClass('active');
        }

        /* меняем главное изображение */
        this.changeMainImageByProperty($elProperty, isPropertyActive);

        if (needGetConfig) {
            /* получаем id конфигурации и значения */
            var propertyId = $configuratorWrapper.data('propertyId');
            var propertyValueId = $elProperty.data('propertyValueId');

            if (isPropertyActive) {
                /* если конфигурация уже активна, то деактивируем ее */
                $wrapper.find('.js-product-configurator[data-property-id="' + propertyId + '"]').val('');
            } else {
                /* иначе активируем */
                $wrapper.find('.js-product-configurator[data-property-id="' + propertyId + '"]').val(propertyValueId);
            }

            this.runPreloader($wrapper);
            this.getConfigurationInfo($wrapper);
        }

        $elProperty.trigger('afterChangeConfiguratorImage');
    },

    changeMainImageByProperty: function ($elProperty, isPropertyActive) {
        var $wrapper = $elProperty.closest('.js-product');
        var $elPropertyImg = $elProperty.find('.js-product-config-image');
        var largeImgConfig = $elPropertyImg.data('imgLarge');
        var fullImgConfig = $elPropertyImg.data('imgUrl');

        if (isPropertyActive) {
            /* если конфигурация уже активна, то деактивируем ее */
            $wrapper.find('.js-product-slide-photo').first().trigger('mouseover');
        } else {
            /* иначе активируем */
            this.setMainPhoto($wrapper, largeImgConfig, fullImgConfig);
        }
    },

    buildConfigurationCurrent: function ($wrapper) {
        var id = $wrapper.data('productId');
        var current = {
            property: {},
        };

        // если есть configurationId в uri и если это не короткая карточка
        if (! $wrapper.data('isShort')) {
            var configurationId = window.location.hash.replace('#', '');
            if (configurationId) {
                current.configurationId = configurationId;
            }
        }

        // поле ввода Quantity появляется только если нет MultiInputPropertyId
        var $productQuantity = $wrapper.find('.js-product-quantity');
        if ($productQuantity.length) {
            current.quantity = Number($productQuantity.val());
        }

        // шлем current с текущими выбранными Property
        var self = this;
        $wrapper.find('.js-product-configurator').each(function (i, select) {
            var $select = self.$(select);
            if ($select.val()) {
                current.property[$select.data('propertyId')] = $select.val();
            }
        });

        this.products[id].configurationCurrent = current;
    },

    getConfigurationInfoProxy: function (e) {
        var $wrapper = this.$(e.currentTarget).closest('.js-product');
        this.getConfigurationInfo($wrapper);
    },

    getConfigurationInfo: function($wrapper) {
        var id = $wrapper.data('productId');
        var notAvailableId = $wrapper.data('productNotAvailableId');
        var isIncomplete = $wrapper.data('productIsIncomplete');
        var self = this;

        if (isIncomplete) {
            this.dataReload($wrapper);
        }

        // check available product
        $wrapper.find('.js-row-item-price').show();
        $wrapper.find('.js-row-quantity').show();
        if (notAvailableId) {
            $wrapper.find('.js-row-item-price').hide();
            $wrapper.find('.js-row-quantity').hide();
            return;
        }

        this.runPreloader($wrapper);

        if (this.products[id].getConfigurationInfoXHR) {
            this.products[id].getConfigurationInfoXHR.abort();
        }

        self.buildConfigurationCurrent($wrapper);

        var action = $wrapper.data('actionGetConfigurationInfo');
        this.products[id].getConfigurationInfoXHR = $.post(
            action,
            {
                current: self.products[id].configurationCurrent,
                selected: self.products[id].configurationSelected
            },
            function(data){
                if (data.error) {
                    showError(data.message);
                    $wrapper.find('.js-product-configuration-current-price').html(trans.get('error'));
                } else if (data.Result) {
                    self.handleConfigurationInfo(data.Result, $wrapper);
                }
            }, 'json'
        );
    },

    handleConfigurationInfo: function(result, $wrapper) {
        var self = this;
        var issetDelivery = false;
        var id = $wrapper.data('productId');

        $wrapper
            .find('.js-product-delivery-method[data-delivery-id=' + this.products[id].currentDeliveryMode + ']')
            .trigger('click');

        var Configuration = result.Configuration;
        var Currency = result.Currency;
        var AdditionalPrices = result.AdditionalPrices;
        this.products[id].currency = Currency.DisplayName;

        this.disableButtons($wrapper);
        $wrapper.find('.js-additional-price-header').hide();

        if (Configuration) {
            var $priceNew = $wrapper.find('.js-product-price-new');

            if (Configuration.TotalCost) {
                $priceNew.html(getCurrencyPrice(Configuration.TotalCost, result.Currency.DisplayName));
                $priceNew.closest('.js-product-price-new-row').show();
                this.products[id].currentTotalCost = Configuration.TotalCost;
            } else {
                $priceNew.closest('.js-product-price-new-row').hide();
                this.products[id].currentTotalCost = null;
            }

            if (Configuration.Current) {
                if (! $wrapper.data('isShort')) {
                    var url = window.location.href.split('#')[0];

                    if (Configuration.Current.IsFullConfiguration && typeof Configuration.Current.ConfigurationId  !== 'undefined') {
                        // добавляем configurationId в uri
                         url = url + '#' + Configuration.Current.ConfigurationId;
                        window.history.replaceState(url, null, url);
                    } else if (window.location.href.split('#').length) {
                        // убираем configurationId из uri
                        window.history.replaceState(url, null, url);
                    }
                }

                // для не MultiInput случая отдельно сохраняем configurationSelected
                var $productQuantity = $wrapper.find('.js-product-quantity');

                if ($productQuantity.length) {
                    if (Configuration.Current.IsFullConfiguration && typeof Configuration.Current.ConfigurationId  !== 'undefined') {
                        this.products[id].configurationSelected = {};
                        this.products[id].configurationSelected[Configuration.Current.ConfigurationId] = Number($productQuantity.val());
                    } else {
                        this.products[id].configurationSelected = {};
                    }
                }

                self.disableButtons($wrapper);

                if (Configuration.Current.Price) {
                    $wrapper
                        .find('.js-product-configuration-current-price')
                        .html(getCurrencyPrice(Configuration.Current.Price, result.Currency.DisplayName));
                }

                if (Configuration.Current.OldPrice) {
                    $wrapper
                        .find('.js-product-configuration-current-old-price')
                        .html(getCurrencyPrice(Configuration.Current.OldPrice, result.Currency.DisplayName));
                }

                if (Configuration.Current.DiscountPercent) {
                    $wrapper
                        .find('.js-product-configuration-current-discount-percent')
                        .html('-&nbsp;' + Configuration.Current.DiscountPercent + '%')
                        .show();
                } else {
                    $wrapper.find('.js-product-configuration-current-discount-percent').hide();
                }

                if (Configuration.Current.InternalDelivery && Configuration.Current.InternalDelivery > 0 ) {
                    var internalDeliveryText = '' +
                        trans.get('internal_delivery') + ': ' +
                        getCurrencyPrice(Configuration.Current.InternalDelivery, Currency.DisplayName);
                    if ($('.js-product-configuration-internal-delivery').length) {
                        $wrapper.find('.js-tooltip.internal_delivery_tooltip').show();
                        $wrapper.find('.js-product-configuration-internal-delivery').html(internalDeliveryText).show();
                    }
                }

                var $confQtyRange = $wrapper.find('.js-product-configuration-quantity-range');
                var $confQtyRangeHead = $confQtyRange.find('.js-product-quantity-range-head-piece');
                var $confQtyRangeBody = $confQtyRange.find('.js-product-quantity-range-body-piece');

                $confQtyRangeHead.empty();
                $confQtyRangeBody.empty();

                if (Configuration.QuantityRanges) {
                    Configuration.QuantityRanges.Range.forEach(function (Range) {
                        if (Range.MaxQuantity === undefined) {
                            $confQtyRangeHead.append(
                                '<th scope="col">' +
                                    '≥' + Range.MinQuantity + ' ' + trans.get('pcs') +
                                '</th>'
                            );
                        } else {
                            $confQtyRangeHead.append(
                                '<th scope="col">' +
                                    Range.MinQuantity + '-' + Range.MaxQuantity + ' ' + trans.get('pcs') +
                                '</th>'
                            );
                        }

                        $confQtyRangeBody.append('<td>' + getCurrencyPrice(Range.Price, Currency.DisplayName) + '</td>');
                    });
                }

                if (typeof Configuration.Current.AvailableQuantity !== 'undefined') {
                    var $availableQuantity = $wrapper.find('.js-product-available-quantity');
                    if ($availableQuantity) {
                        $availableQuantity.html(
                            trans.get('in_existence') + ': ' +
                            Configuration.Current.AvailableQuantity + ' ' +
                            trans.get('pcs')
                        );
                    }
                }
            }

            if (Configuration.Properties.length !== 0) {
               $wrapper.find('.js-product-configurator-image').removeClass('disabled');

                var ctext = '';
                Configuration.Properties.Property.forEach(function (Property) {
                    Property.Value.forEach(function (PropertyValue) {
                        if (PropertyValue.Disabled === 'true') {
                            $wrapper
                                .find('.js-product-configurator-images[data-property-id="' + Property.Id + '"]')
                                .find('.js-product-configurator-image[data-property-value-id="' + PropertyValue.Id + '"]')
                                .addClass('disabled');
                        }

                        if (PropertyValue.Current === 'true') {
                            $wrapper
                                .find('.js-product-configurator[data-property-id="' + Property.Id + '"]')
                                .val(PropertyValue.Id);

                            var $conf = $wrapper
                                .find('.js-product-configurator-images[data-property-id="' + Property.Id + '"]')
                                .find('.productConfiguratorImage[data-property-value-id="' + PropertyValue.Id + '"]');

                            $conf.addClass('active');
                            if ($conf.find('.js-product-config-image').length) {
                                self.changeMainImageByProperty($conf, false);
                            }

                            ctext += $wrapper.find('li[data-property-value-id="' + PropertyValue.Id + '"]').attr('title') + '; ';
                            $wrapper.find('.js-chosen-config').html(ctext);
                        }

                        if (PropertyValue.Selected === 'true') {
                            $wrapper
                                .find('.js-product-configurator-images[data-property-id="' + Property.Id + '"]')
                                .find('.js-product-configurator-image[data-property-value-id="' + PropertyValue.Id + '"]')
                                .addClass('selected');
                        }

                        if ((PropertyValue.Selected === 'false')) {
                            var $confImage = $wrapper
                                .find('.js-product-configurator-images[data-property-id="' + Property.Id + '"]')
                                .find('.js-product-configurator-image[data-property-value-id="' + PropertyValue.Id + '"]');

                            if ($confImage.hasClass('selected')) {
                                $confImage.removeClass('selected');
                            }
                        }
                    });
                });
            }

            var $multiInputTable = $wrapper.find('.js-product-multi-input-table');
            if ($multiInputTable.length) {
                self.hideConfigTable($wrapper);

                var $multiInputTableRow = $multiInputTable.find('.js-product-multi-input-table-body-row');

                $multiInputTableRow.removeClass('disabled');
                $multiInputTableRow
                    .find('.js-product-multi-input-quantity')
                    .prop('disabled', false)
                    .removeClass('disabled');

                // сбрасываем информацию в таблице
                $multiInputTable.find('.js-product-multi-input-configurations-price').html('');
                $multiInputTable.find('.js-product-multi-input-configurations-available-quantity').html('');
                $multiInputTable.find('.js-product-multi-input-quantity').prop('disabled', true).attr('ConfigurationId', '').val(0);
                $multiInputTable.find('.js-product-qty-minus, .js-product-qty-plus').addClass('disabled_button');

                // заполняем таблицу информацией
                if (Configuration.MultiInputConfigurations && Configuration.MultiInputConfigurations.Configuration) {
                    Configuration.MultiInputConfigurations.Configuration.forEach(function(configuration) {
                        $tr = $multiInputTable.find('[data-property-value-id="' + configuration.InputPropertyValueId + '"]');
                        if ($tr.length) {
                            $tr.find('.js-product-multi-input-configurations-price').html(
                                getCurrencyPrice(configuration.Price, result.Currency.DisplayName)
                            );
                            $tr.find('.js-product-multi-input-configurations-available-quantity').html(
                                configuration.AvailableQuantity + '&nbsp' + trans.get('pcs') // TODO:
                            );

                            $tr.find('.js-product-multi-input-quantity')
                                .prop('disabled', false)
                                .attr('ConfigurationId', configuration.ConfigurationId);
                            $tr.find('.js-product-qty-minus').removeClass('disabled_button');
                            $tr.find('.js-product-qty-plus').removeClass('disabled_button');

                            if (self.products[id].configurationSelected[configuration.ConfigurationId]) {
                                $tr.find('.js-product-multi-input-quantity')
                                    .val(self.products[id].configurationSelected[configuration.ConfigurationId]);
                            }
                        }

                        Configuration.Properties.Property.forEach(function (Property) {
                            Property.Value.forEach(function (PropertyValue) {
                                if (PropertyValue.Disabled === 'true') {
                                    var $propertyValue = $wrapper
                                        .find('.js-product-property-value-id[data-property-value-id="' + PropertyValue.Id + '"]');

                                    $propertyValue
                                        .addClass('disabled')
                                        .find('.js-product-multi-input-quantity[configurationid="' + configuration.ConfigurationId + '"]')
                                        .prop('disabled', true)
                                        .addClass('disabled');
                                    $propertyValue
                                        .find('.js-product-qty-plus, .js-product-qty-minus')
                                        .addClass('disabled_button');
                                }
                            });
                        });
                    });
                }

                this.showConfigTable($wrapper);
            }
        }

        if (AdditionalPrices !== undefined && AdditionalPrices.length > 0) {
            $wrapper.find('.js-additional-price-header').show();
            $wrapper.find('.js-product-additional-price-row .js-product-additional-price-config').empty();

            AdditionalPrices.forEach(function (Value) {
                for (var k in Value) {
                    $wrapper
                        .find('.js-product-additional-price-row .js-product-additional-price-config')
                        .append(Value[k]['ShortDisplayName'] + ': ' + getCurrencyPrice(Value[k]['Price']['Price'], Currency.DisplayName));
                }
            });
        }

        if (result.DeliveryModes && result.DeliveryModes.Mode && result.DeliveryModes.Mode.length) {
            var deliveryPrice = $wrapper.find('.js-product-delivery-method:checked').data('deliveryPrice');
            var fullPrice = this.products[id].currentTotalCost;
            var minPrice = null;
            var deliveryName = null;
            var deliveryId = null;

            $wrapper.find('.js-product-table-delivery-body').empty();

            result.DeliveryModes.Mode.forEach(function (deliveryMethod) {
                if (self.products[id].currentDeliveryMode === deliveryMethod.Id) {
                    issetDelivery = true;
                    return false;
                }
            });

            var randId = Math.floor(Math.random() * 10000);

            result.DeliveryModes.Mode.forEach(function (deliveryMethod) {
                if (issetDelivery === false) {
                    self.products[id].currentDeliveryMode = null;

                    if (minPrice === null && self.products[id].currentDeliveryMode === null) {
                        minPrice = deliveryMethod.Price.Price;
                        deliveryName = deliveryMethod.Name;
                        deliveryId = deliveryMethod.Id;
                    }

                    if (minPrice > deliveryMethod.Price.Price && self.products[id].currentDeliveryMode === null) {
                        minPrice = deliveryMethod.Price.Price;
                        deliveryName = deliveryMethod.Name;
                        deliveryId = deliveryMethod.Id;
                    }
                }

                $wrapper.find('.js-product-table-delivery-body').append(
                    '<tr class="delivery_list js-product-delivery-list" data-deliveryid="' + deliveryMethod.Id + '" data-currency="' + Currency.DisplayName + '">' +
                        '<td class="td-1">' +
                            '<div class="custom-control custom-radio">' +
                                '<input type="radio" id="' + deliveryMethod.Id + '-' + randId + '" data-delivery-id="' + deliveryMethod.Id + '" data-delivery-price="' + deliveryMethod.Price.Price + '" data-delivery-name="' + deliveryMethod.Name + '"  name="deliveryMethod" class="custom-control-input deliveryMethod js-product-delivery-method">' +
                                '<label class="custom-control-label deliveryName" for="' + deliveryMethod.Id + '-' + randId + '">' + deliveryMethod.Name + '</label>' +
                            '</div>' +
                        '</td>' +
                        '<td class="td-2">' + deliveryMethod.Description + '</td>' +
                        '<td class="td-3">' + getCurrencyPrice(deliveryMethod.Price.Price, Currency.DisplayName) + '</td>' +
                    '</tr>'
                );
            });

            $wrapper.find('.js-product-table-delivery-body').append(
                '<tr class="js-product-delivery-list">' +
                    '<td class="td-1">' +
                        '<div class="custom-control custom-radio">' +
                            '<input type="radio" id="chooseLater-' + randId + '" data-delivery-id="chooseLater"  data-delivery-price="0" data-delivery-name="' + trans.get('select_later') + '"  name="deliveryMethod" class="custom-control-input deliveryMethod js-product-delivery-method js-product-delivery-method-later">' +
                            '<label class="custom-control-label deliveryName" for="chooseLater-' + randId + '">' + trans.get('select_later') + '</label>' +
                        '</div>' +
                    '</td>' +
                    '<td class="td-2"></td>' +
                    '<td class="td-3"></td>' +
                '</tr>'
            );

            if (this.products[id].currentDeliveryMode !== null) {
                $wrapper.find('.js-product-delivery-method[data-delivery-id=' + this.products[id].currentDeliveryMode + ']').trigger('click');

                deliveryPrice = $wrapper.find('.js-product-delivery-method:checked').data('deliveryPrice');
                fullPrice = parseFloat(fullPrice) + parseFloat(deliveryPrice);

                $wrapper.find('.js-product-choose-delivery-price').html(getCurrencyPrice(deliveryPrice, Currency.DisplayName));
                $wrapper.find('.js-product-price-new').html(getCurrencyPrice(fullPrice, Currency.DisplayName));

                this.products[id].currentTotalCostWithDelivery = fullPrice;

                return false;
            } else {
                $wrapper.find('.js-product-delivery-method[data-delivery-id=' + deliveryId + ']').trigger('click');
            }

            if (minPrice !== null) {
                fullPrice = parseFloat(fullPrice) + parseFloat(minPrice);
            }

            if (this.products[id].currentDeliveryMode === null) {
                this.products[id].currentDeliveryMode = deliveryId;
                $wrapper.find('.js-product-config-delivery').show();
                $wrapper.find('.js-product-choose-delivery-price').html(getCurrencyPrice(minPrice, Currency.DisplayName));
                $wrapper.find('.js-product-choose-delivery-name').html(deliveryName);
                $wrapper.find('.js-product-price-new').html(getCurrencyPrice(fullPrice, Currency.DisplayName));
            }
        }

        if (AdditionalPrices !== undefined && AdditionalPrices.length > 0) {
            var $additionalPriceConfig = $wrapper.find('.js-product-additional-price-config');

            $additionalPriceConfig.empty();
            $wrapper.find('.js-additional-price-header').show();

            AdditionalPrices.forEach(function (Value) {
                for (var k in Value) {
                    $additionalPriceConfig.append(Value[k]['ShortDisplayName'] + ': ' + getCurrencyPrice(Value[k]['Price']['Price'], Currency.DisplayName));
                }
            });
            $additionalPriceConfig.find('.js-tooltip').tooltip();
        }
    },

    chooseDelivery: function ($wrapper) {
        var id = $wrapper.data('productId');

        var fullPrice = this.products[id].currentTotalCost;
        var currency = $wrapper.find('.js-product-delivery-list').data('currency');
        var $checkedDeliveryMethod = this.$('.js-product-delivery-method:checked');
        var deliveryPrice = $checkedDeliveryMethod.data('deliveryPrice');
        var deliveryId = $checkedDeliveryMethod.data('deliveryId');
        var deliveryName = $checkedDeliveryMethod.data('deliveryName');

        fullPrice = parseFloat(fullPrice) + parseFloat(deliveryPrice);

        this.products[id].currentDeliveryMode = deliveryId;
        this.products[id].currentTotalCostWithDelivery = fullPrice;

        if (deliveryId === 'chooseLater') {
            $wrapper.find('.js-product-choose-delivery-price').hide();
        } else {
            $wrapper.find('.js-product-choose-delivery-price').show();
        }

        $wrapper.find('.js-product-config-delivery').show();
        $wrapper.find('.js-product-choose-delivery-price').html(getCurrencyPrice(deliveryPrice, currency));
        $wrapper.find('.js-product-choose-delivery-name').html(deliveryName);
        $wrapper.find('.js-product-price-new').html(getCurrencyPrice(fullPrice, currency));
    },

    getReviewsProxy: function (e) {
        var $wrapper = this.$(e.currentTarget).closest('.js-product');
        this.getReviews($wrapper);
    },

    getReviews: function ($wrapper) {
        var source = null;
        var provider = $('.js-reviews-tab').data('provider');

        if ($("#reviewsSourceCheckbox").length > 0 && (typeof $wrapper.find('.js-tab-reviews').attr('data-type') !== typeof undefined)) {
            var checkbox = $("#reviewsSourceCheckbox").prop('checked');
            if (checkbox === true) {
                source = "Internal";
            } else {
                source = "Provider";
            }
        }

        if (($('.paymshipReviews').length > 0) && ($wrapper.find('.js-tab-reviews').attr('data-type') === source)) {
            return false;
        }

        var $reviewsTab = $wrapper.find('.js-reviews-tab');
        var itemId = $reviewsTab.data('itemId');
        var action = $reviewsTab.data('action');

        $('.paymshipReviews').addClass('paymshipReviewsOverlay');
        $('.noReviews').addClass('paymshipReviewsOverlay');

        $.ajax({
            url: action,
            data: {itemId: itemId, source: source, provider: provider},
            success: function(data) {
                $wrapper.find('.js-tab-reviews .js-reviews-container').html(data.reviews);
                $wrapper.find('.js-tab-reviews').attr('data-type', data.source);
            }
        });
    },

    addItemsToBasket: function (e) {
        var $el = this.$(e.currentTarget);
        var $wrapper = $el.closest('.js-product');
        var id = $wrapper.data('productId');

        if ($el.hasClass('disabled_button')) {
            return false;
        }

        showOverlay();
        this.toggleBtnDisable($el);

        var self = this;
        var itemUrl = $wrapper.data('productUrl');
        var action = $el.data('action');
        var itemId = $el.data('itemId');
        var externalDeliveryId = $wrapper
            .find('.js-product-delivery-method:checked')
            .data('deliveryId');

        $.post(action, {
                itemId: itemId,
                itemUrl: itemUrl,
                externalDeliveryId: externalDeliveryId,
                selected: this.products[id].configurationSelected
            }, function (data) {
                self.$('.js-cart-amount').text(data);
                self.$('.js-full_cart_amount').text(data);

                if (! data.error) {
                    self.showItemsToBakset($wrapper);
                    var $modalContent = $wrapper.find('.js-product-modal-add-done').clone().removeClass('hidden').show();

                    modalDialog(
                        trans.get('good_added_to_cart'),
                        $modalContent,
                        function () {
                            document.location.href = $modalContent.data('makeOrderLink');
                        },
                        {
                            confirm: trans.get('make_order'),
                            cancel: trans.get('continue_shopping'),
                        },
                        undefined, undefined, undefined,
                        function () {
                            self.toggleBtnDisable($el);
                            hideOverlay();
                        }
                    );
                } else {
                    self.toggleBtnDisable($el);
                    showError(data.message);
                }
            }
        );
    },

    hideConfigTable: function ($wrapper) {
        $wrapper.find('.js-animate-fade').each(function () {
            $(this).fadeOut(200);
        });
    },

    showConfigTable: function ($wrapper) {
        $wrapper.find('.js-animate-fade').each(function () {
            $(this).fadeIn(200);
        });
    },

    addItemsToFavourite: function (e) {
        var $el = this.$(e.currentTarget);
        var $wrapper = $el.closest('.js-product');
        var id = $wrapper.data('productId');

        this.hideAllTooltips($wrapper);

        if ($el.attr('disabled')) {
            return false;
        }

        showOverlay();
        var itemUrl = $wrapper.data('productUrl');
        var action = $el.data('action');
        var itemId = $el.data('itemId');
        var externalDeliveryId = $wrapper
            .find('.js-product-delivery-method:checked')
            .data('deliveryId');

        var self = this;
        $.post(action, {
                itemId: itemId,
                itemUrl: itemUrl,
                externalDeliveryId: externalDeliveryId,
                selected: this.products[id].configurationSelected
            }, function (data) {
                if (!data.error) {
                    showMessage(trans.get('good_added_to_fav'));
                    self.$('.js-favorite-amount').html(data);
                } else {
                    showError(data.message);
                }
                hideOverlay();
            }
        );
    },

    addVendorToFavourite: function (e) {
        var $el = this.$(e.currentTarget);
        var vendorId = $el.data('vendorId');
        var action = $el.data('action');

        $el.button('loading');

        $.post(action, {id: vendorId},
            function (data) {
                if (!data.error) {
                    showMessage(trans.get('vendor_added_to_favourites_txt'));
                } else {
                    showError(data.message);
                }
                $el.button('reset');
            }
        );
    },

    changeMultiInputQuantity: function(e) {
        var $input = this.$(e.currentTarget);
        var $wrapper = $input.closest('.js-product');
        var id = $wrapper.data('productId');

        this.runPreloader($wrapper);

        e.preventDefault();

        if ($input.attr('ConfigurationId')) {
            if (Number($input.val())) {
                this.products[id].configurationSelected[$input.attr('ConfigurationId')] = Number($input.val());
            } else {
                delete this.products[id].configurationSelected[$input.attr('ConfigurationId')];
            }
        }

        this.getConfigurationInfo($wrapper);
    },

    runPreloader: function ($wrapper) {
        $wrapper.find('.js-product-configuration-current-price').html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
        $wrapper.find('.js-product-configuration-current-old-price').empty();
        $wrapper.find('.js-product-configuration-current-discount-percent').hide();
    },

    quickOrder: function (e) {
        var $btn = this.$(e.currentTarget);
        var $wrapper = $btn.closest('.js-product');
        var id = $wrapper.data('productId');

        if ($btn.hasClass('disabled_button')) {
            return false;
        }

        this.toggleBtnDisable($btn);

        if (! $btn.data('is-authenticated')) {
            let $modal = this.$('.js-modal-login');

            if ($modal.length) {
                this.$('.js-modal-login').modal('show');
            } else {
                window.location.href = $btn.data('authenticationAction');
            }
            return;
        }

        if (this.isBasketDisabled($wrapper)) {
            showError(trans.get('sell_not_allowed_without_config'));
            return false;
        }

        if (Object.keys(this.products[id].configurationSelected).length === 0) {
            showError(trans.get('no_items_selected'));
            return false;
        }

        var self = this;
        var params = this.getQuickOrderParams($wrapper);

        showOverlay();
        $btn.button('loading');
        $.get($btn.data('action'), params,
            function(data) {
                if (data.error) {
                    showError(data);
                    hideOverlay();
                    return false;
                }

                modalDialog(trans.reg_order, data.layout,
                    function (body) {
                        userOrderView.createOrder();
                        return false;
                    },
                    {
                        confirm: trans.get('make_order'),
                        cancel: trans.get('cancel')
                    },
                    function () {
                        $btn.button('reset');
                    },
                    undefined, 3,
                    function () {
                        self.toggleBtnDisable($btn);
                        hideOverlay();
                    }
                );
            }, 'json'
        );
    },

    getQuickOrderParams: function ($wrapper) {
        var $btn = $wrapper.find('.js-product-quick-order-btn');
        var id = $wrapper.data('productId');
        var itemsQuantity = 0;
        var items = [];

        $.each(this.products[id].configurationSelected, function (configuration, quantity) {
            itemsQuantity += quantity;
            items.push({
                'id': $btn.data('item-id'),
                'configurationId': configuration,
                'quantity': quantity
            });
        });

        return {
            'items': items,
            'totalPrice': this.products[id].currentTotalCost,
            'itemWeight': $btn.data('weight'),
            'quantity': itemsQuantity,
            'type': $btn.data('provider'),
            'promotionId': 0 /* заглушка */
        };
    },

    isBasketDisabled: function ($wrapper) {
        return !!$wrapper.find('.js-product-btn-in-garbage').hasClass('disabled');
    },

    toggleBtnDisable: function ($btn) {
        var isBtnDisabled = $btn.attr('disabled') || $btn.hasClass('disabled_button');

        if (isBtnDisabled) {
            $btn.removeAttr('disabled').removeClass('disabled_button');
        } else {
            $btn.attr('disabled', 'disabled').addClass('disabled_button');
        }
    },

    showDeliveryModal: function (e) {
        e.preventDefault();
        var self = this;
        var $el = this.$(e.currentTarget);
        var $wrapper = $el.closest('.js-product');
        var $tableWrapper = $wrapper.find('.js-product-modal-delivery');
        var $modalContent = $tableWrapper.find('.js-product-table-delivery').remove();
        var $predefinedModalContent = $modalContent.clone();

        modalDialog(
            trans.get('delivery_modes'),
            $modalContent,
            function () {
                $tableWrapper.append($modalContent);
                self.chooseDelivery($wrapper);
            },
            {
                confirm: trans.get('ok'),
                cancel: trans.get('cancel')
            },
            undefined, undefined, undefined,
            function () {
                $tableWrapper.append($predefinedModalContent);
            }
        );
    },

    reloadCount: 0,
    reloadMaxCount: 10,
    reloadTimeout: 5000,
    dataReload: function ($wrapper) {
        var self = this;

        this.reloadCount++;
        if (this.reloadCount > this.reloadMaxCount) {
            self.showDataReloadError($wrapper);
            return;
        }

        self.showDataReloadPreloader($wrapper);

        setTimeout(() => {
            var productUrl = $wrapper.data('actionReload');
            $.post(productUrl,
                function (data) {
                    self.hideDataReloadPreloader($wrapper);

                    if (data.error) {
                        self.dataReload($wrapper);
                    } else {
                        $newWrapper = $(data.answer);

                        // заменяем ранее полученные блоки
                        $wrapper.find('[data-full-info-need-request="0"]').each(function (i, el) {
                            var $el = $(el);
                            $newWrapper.find('[data-full-info-block="' + $el.attr('data-full-info-block') + '"][data-full-info-need-request="1"]').replaceWith($el);
                        });

                        $wrapper.replaceWith($newWrapper);
                        self.bindGetFullInfoListener($newWrapper);
                        self.renderProduct($newWrapper);

                        if (data.crumbs) {
                            $('.js-breadcrumbs').html(data.crumbs);
                        }
                        this.reloadCount = 0;
                    }
                }, 'json'
            ).fail(function() {
                self.hideDataReloadPreloader($wrapper);
                self.showDataReloadError($wrapper);
                this.reloadCount = 0;
            });
        }, this.reloadTimeout);
    },

    showDataReloadPreloader: function ($wrapper) {
        $wrapper.find('.js-product-reload-start').show();
    },

    hideDataReloadPreloader: function ($wrapper) {
        $wrapper.find('.js-product-reload-start').hide();
    },

    showDataReloadError: function ($wrapper) {
        $wrapper.find('.js-product-reload-error').show();
    },
});

$(function () {
    window.productView = new ProductView();
});