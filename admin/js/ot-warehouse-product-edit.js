var WarehouseItemsPage = Backbone.View.extend({
    "el": "#warehouse-product",
    "properties": undefined,
    "allProperties": undefined,
    "allValues": undefined,
    "configurations": undefined,
    "events": {
        "click #add-product": "checkCategorySelected",
        "mousedown #CategoryName": "showCategoryHint",
        "click #submit_btn1": "saveProduct",
        "click #submit_btn2": "saveProduct",
        "click #product-configurations": "editConfigs",
        "click #add-property-btn": "addProperty",
        "change #SellAllowed": "changeSellAllowed",
        "click .add-image": "addImageByUrl",
        "click .upload-image": "uploadImage",
        "click button.ot_show_deletion_dialog_modal": "removeImage",
        "change #WeightReset": "weightResetChange"
    },
    weightResetChange: function(){
    	var weightReset = $('#WeightReset').prop('checked');
    	$('#Weight').prop('disabled', weightReset);
    },
    uploadImage: function(e)
    {
    	var html = $('ul.ot_image_items .template-upload-image').html();
        $('ul.ot_image_items').append(html);
        $('ul.ot_image_items li:last input[type="hidden"]').attr('type', 'file');
        $('ul.ot_image_items li:last input[type="file"]').trigger('click');
    },
    removeImage: function(e)
    {
    	var li = $(e.currentTarget).closest('li');
    	$(li).remove();
    	return false;
    },
    addImageByUrl: function(e)
    {
        $('.ot_crud_custom_picture_window .modal-body #dataId').attr('value', '');
        var content = $('.ot_crud_custom_picture_window .modal-body').html();
        var modal = modalDialog(trans.get('Logo'), content, function(body) {
            var newImageUrl = $('#dataId', body).val();
            var html = $('ul.ot_image_items .template-image').html();
            $('ul.ot_image_items').append(html);
            $('ul.ot_image_items li:last img').attr('src', newImageUrl);
            $('ul.ot_image_items li:last input[type="hidden"]').val(newImageUrl);
            $('ul.ot_image_items li:last input[type="hidden"]').attr('name', 'images[]');
            modal.toggle();
            $(".modal-backdrop").remove();
            return false;
          }, {confirm: trans.get('Add'), cancel: trans.get('Cancel') }, initPopoverInsideDialog);
    	
    },
    changeSellAllowed: function(e)
    {
    	var sellAllowed = $(e.currentTarget).is(":checked");
    	var itemId = $(e.currentTarget).attr("itemId");
    	if (itemId == undefined || itemId == '') {
    		if (sellAllowed) {
    			$('#Quantity').attr('readonly', 'readonly');
			} else {
				$('#Quantity').removeAttr('readonly');
			}
    		return false;
    	}
    	$('#overlay').show();
    	
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
                		$('#Quantity').attr('readonly', 'readonly');
                	} else {
                		$('#Quantity').removeAttr('readonly');
                	}
            		window.location.reload();
            	} else {
            		showError(data);
            		$(e.currentTarget).prop('checked', !sellAllowed);
                	$('#overlay').hide();
            	}
            },
            error: function() {
            	showError(trans.get('Service_page_something_wrong_text'));
            	$(e.currentTarget).prop('checked', !sellAllowed);
            	$('#overlay').hide();
            }
        });
    	if (sellAllowed) {
    		$('#Quantity').attr('readonly', 'readonly');
    	} else {
    		$('#Quantity').removeAttr('readonly');
    	}
    },
    removeGeneralPropertyValue: function(e, self) 
    {
    	$(e.currentTarget).closest('button').hide();
    	var body = $(e.currentTarget).closest('.modal-body');
    	var id = $($(e.currentTarget).closest('li')).val();
    	var li = $(e.currentTarget).closest('li');
		$(li).css('opacity', '0.5');
		var modal = $(body).closest('.modal');
		$('.modal-footer .btn.btn-primary', modal).attr('disabled', 'disabled');
		

		$.ajax({
            async : true,
            type: 'POST',
            dataType: 'json',
            url: "?cmd=WarehouseProducts&do=removePropertyValue",
            data : {
            	"valueId": id,
            },
            success : function (data) {
            	if (data.ok) {
            		$(li).remove();	
            		self.allValues = data.allValues;
            	} else {
            		$(li).css('opacity', '1');
            		showError(data);                		
            	}
            },
            error: function() {
            	$(li).css('opacity', '1');
            }
        });
    },
    removeGeneralProperty: function(e, self) 
    {
    	$(e.currentTarget).closest('button').hide();
    	var body = $(e.currentTarget).closest('.modal-body');
    	var id = $($(e.currentTarget).closest('li')).val();
    	var li = $(e.currentTarget).closest('li');
		$(li).css('opacity', '0.5');
		var modal = $(body).closest('.modal');
		$('.modal-footer .btn.btn-primary', modal).attr('disabled', 'disabled');

		$.ajax({
            async : true,
            type: 'POST',
            dataType: 'json',
            url: "?cmd=WarehouseProducts&do=removeProperty",
            data : {
            	"id": id,
            },
            success : function (data) {
            	if (data.ok) {
            		$(li).remove();	
            		self.allProperties = data.allProperties;
            	} else {
            		$(li).css('opacity', '1');
            		showError(data);                		
            	}
            },
            error: function() {
            	$(li).css('opacity', '1');
            }
        });
    },
    
    addGeneralPropertyValue: function(e, self, propertyId)
    {
    	var body = $(e.currentTarget).closest('.modal-body');
    	var value = $('#new-property-value', body).val();
    	if ('' != value) {
    		$(e.currentTarget).button('loading');
    		$.ajax({
                async : true,
                type: 'POST',
                dataType: 'json',
                url: "?cmd=WarehouseProducts&do=addPropertyValue",
                data : {
                	"value": value,
                	"propertyId": propertyId
                },
                success : function (data) {
                	if (data.ok) {
                		var itemHtml = '<li value="' + data.id + '" prop-id="' + propertyId + '"><a href="#"><i class="icon-file"></i> ' + value;
                		itemHtml += ' <span class="property-value-actions"><button class="btn btn-tiny offset-right1"><i class="icon-remove" title="' + trans.get('Delete') + '"></i></button></span>';
                		itemHtml += '</a></li>';
                		$('ul', body).append(itemHtml);
                		self.allValues = data.allValues;
                		$('#new-property-value', body).val('');
                	} else {
                		showError(data);                		
                	}
                	$(e.currentTarget).button('reset');
                },
                error: function() {
                	$(e.currentTarget).button('reset');
                }
            });
    	} else {
    		showError(trans.get('Property_value_is_empty'));    		
    	}
    },
    addGeneralProperty: function(e, self) 
    {
    	var body = $(e.currentTarget).closest('.modal-body');
    	var name = $('#new-property', body).val();
    	if ('' != name) {
    		$(e.currentTarget).button('loading');
    		$.ajax({
                async : true,
                type: 'POST',
                dataType: 'json',
                url: "?cmd=WarehouseProducts&do=addProperty",
                data : {
                	"name": name
                },
                success : function (data) {
                	if (data.ok) {
                		var itemHtml = '<li value="' + data.id + '"><a href="#"><i class="icon-file"></i> ' + name;
                		itemHtml += ' <span class="property-actions"><button class="btn btn-tiny offset-right1"><i class="icon-remove" title="' + trans.get('Delete') + '"></i></button></span>';
                		itemHtml += '</a></li>';
                		$('ul', body).append(itemHtml);
                		self.allProperties = data.allProperties;
                		$('#new-property', body).val('');
                	} else {
                		showError(data);                		
                	}
                	$(e.currentTarget).button('reset');
                },
                error: function() {
                	$(e.currentTarget).button('reset');
                }
            });
    		
    	} else {
    		showError(trans.get('Property_name_is_empty'));    		
    	}
    },
    saveItemProperties: function(e, self)
    {	
    	$(e.currentTarget).button('loading');
		$('button#save-properties-btn').removeClass('btn-success');
    	var body = $(e.currentTarget).closest('.modal-body');
		var configuratorsContent = $('#configurators-content', body);
		var itemId = $(configuratorsContent).attr('item-id')
		var data = [];
		$('.property-section.processed', configuratorsContent).each(function(){
			var name = $(this).attr('prop-name');
			var id = $(this).attr('prop-id');
			var values = [];
			$('.property-value-section.processed', this).each(function(){
    			var value = $(this).attr('value-value');
    			var id = $(this).attr('value-id');
    			values.push({'id': id, 'value': value});
			});
			var prop = { 'name': name, 'id': id, 'values': values };
			data.push(prop);
		});
		console.log(data);
		
        $.ajax({
            async : true,
            type: 'POST',
            dataType: 'json',
            url: "?cmd=WarehouseProducts&do=saveItemConfigurators",
            data : {
            	"itemId": itemId,
                "properties" : data,
            },
            success : function (data) {
            	if (data.ok) {
            		self.properties = data.properties;
            		self.configurations = data.configurations;
            		self.showConfigurations(body, self);
            		$('button#save-properties-btn').addClass('hide');
            		$('button#save-properties-btn').addClass('btn-success');
            		var sellAllowed = $('#SellAllowed').is(":checked");
        			if (self.configurations != undefined && self.configurations.length > 0) {
        				$('#Quantity').attr('readonly', 'readonly');
        				$('input#configurations').val(1);
        			} else {
        				$('input#configurations').val(0);
        				if (! sellAllowed) {
        					$('#Quantity').removeAttr('readonly');
        				}
        			}
        			if (undefined != data.generalQuantity) {
        				$('#Quantity').val(data.generalQuantity);
        			}
            	} else {
            		showError(data);	
            	}
            	$(e.currentTarget).button('reset');
            },
            error: function () {
            	$(e.currentTarget).button('reset');
            }
        });
    },
    removePropertyValue: function(e)
    {
    	var propertyValueSection = $(e.currentTarget).closest('.property-value-section');
    	$(propertyValueSection).remove();
    	$('button#save-properties-btn').removeClass('hide');
		$('button#save-properties-btn').addClass('btn-success');
    },
    removeProperty: function(e)
    {
    	var propertySection = $(e.currentTarget).closest('.property-section');
    	$(propertySection).remove();    	
    	$('button#save-properties-btn').removeClass('hide');
		$('button#save-properties-btn').addClass('btn-success');
    },
    addPropertyValue: function(e, self)
    {
    	var propertySection = $(e.currentTarget).closest('.property-section');
    	var propertyId = $(propertySection).attr('prop-id');
    	var propertyName = $(propertySection).attr('prop-name');
    	var onConfirmCallback = function(body){
    		var valueId = $('#property-value li a.selected', body).closest('li').val();
    		var valueValue = $('#property-value li a.selected', body).text();
    		var section = $('.property-value-template', propertySection).html();
    		if (valueId) {
    			$('.property-values', propertySection).append(section);
    			$('.property-value-section:last', propertySection).addClass('processed');
    			$('.property-value-section:last', propertySection).attr('value-value', valueValue);
    			$('.property-value-section:last', propertySection).attr('value-id', valueId);
    			$('.property-value-section:last span.property-value', propertySection).text(valueValue);
    			$('button#save-properties-btn').removeClass('hide');
    			$('button#save-properties-btn').addClass('btn-success');
    		}
    	};
    	var content = $('#add-property-value').html();
    	modalDialog(trans.get('Add_property_value_for') + propertyName, content, onConfirmCallback, false, function(body){
    		var modal = $(body).closest('.modal');
    		$('.modal-footer .btn.btn-primary', modal).attr('disabled', 'disabled');
    		
    		if (self.allValues != undefined) {
    			$('ul li', body).remove();
    			for ( var i in self.allValues) {
    				var value = self.allValues[i];
            		var itemHtml = '<li value="' + value.id + '" prop-id="' + value.propertyId + '"><a href="#"><i class="icon-file"></i> ' + value.value;
            		itemHtml += ' <span class="property-value-actions"><button class="btn btn-tiny offset-right1"><i class="icon-remove" title="' + trans.get('Delete') + '"></i></button></span>';
            		itemHtml += '</a></li>';
            		$('ul', body).append(itemHtml);
    			}
    		}
    		
    		$('#property-value li', body).show();
    		$('#property-value li[prop-id!="' + propertyId + '"]', body).hide();
    		
    		$(body).on('click', 'ul li a', function(e) {
    			var li = e.currentTarget;
    			var id = $(li).val();
    			$('ul li[value!="'+ id +'"] a', body).removeClass('selected');
    			if ($(li).hasClass('selected')) {
    				$(li).removeClass('selected');
    				$('.modal-footer .btn.btn-primary', modal).attr('disabled', 'disabled');
    			} else {
    				$(li).addClass('selected');
    				$('.modal-footer .btn.btn-primary', modal).removeAttr('disabled');
    			}
    		});
    		$('#add-new-property-value-btn', body).unbind('click');
    		$('#add-new-property-value-btn', body).click(function(e){self.addGeneralPropertyValue(e, self, propertyId)});
    		$('i.icon-remove', body).unbind('click');
    		$(body).on('click', 'i.icon-remove', function(e){self.removeGeneralPropertyValue(e, self); return false;});
    	}, 2);    	
    },
    addProperty: function(e, self)
    {
    	var onConfirmCallback = function(body){
    		var propertyName = $('ul li a.selected', body).text();
    		var propertyId = $('ul li a.selected', body).closest('li').val();
    		//self.properties.push({id :0, name: propertyName});
    		if (propertyId) {
    			var section = $('#product-conf-content #configurators-content .property-template').html();
    			$('#product-conf-content #configurators-content').append(section);
    			$('#product-conf-content #configurators-content .property-section:last').addClass('processed');
    			$('#product-conf-content #configurators-content .property-section:last').attr('prop-name', propertyName);
    			$('#product-conf-content #configurators-content .property-section:last').attr('prop-id', propertyId);
    			$('#product-conf-content #configurators-content .property-section:last label span.property-name').text(propertyName);
    			$('button#save-properties-btn').removeClass('hide');
    		}
    	};
    	var content = $('#add-property').html();
    	modalDialog(trans.get('Add_property'), content, onConfirmCallback, false, function(body){
    		var modal = $(body).closest('.modal');
    		$('.modal-footer .btn.btn-primary', modal).attr('disabled', 'disabled');
    		
    		if (self.allProperties != undefined) {
    			$('ul li', body).remove();
    			for ( var i in self.allProperties) {
    				var property = self.allProperties[i];
            		var itemHtml = '<li value="' + property.id + '"><a href="#"><i class="icon-file"></i> ' + property.name;
            		itemHtml += ' <span class="property-actions"><button class="btn btn-tiny offset-right1"><i class="icon-remove" title="' + trans.get('Delete') + '"></i></button></span>';
            		itemHtml += '</a></li>';
            		$('ul', body).append(itemHtml);
    			}
    		}
    		
    		$(body).on('click', 'ul li a', function(e) {
    			var li = e.currentTarget;
    			var id = $(li).val();
    			$('ul li[value!="'+ id +'"] a', body).removeClass('selected');
    			if ($(li).hasClass('selected')) {
    				$(li).removeClass('selected');
    				$('.modal-footer .btn.btn-primary', modal).attr('disabled', 'disabled');
    			} else {
    				$(li).addClass('selected');
    				$('.modal-footer .btn.btn-primary', modal).removeAttr('disabled');
    			}
    		});
    		$('#add-new-property-btn', body).unbind('click');
    		$('#add-new-property-btn', body).click(function(e){self.addGeneralProperty(e, self)});
    		$(body).on('click', 'i.icon-remove', function(e){self.removeGeneralProperty(e, self); return false;});
    	}, 2);    	
    },
    showConfigurations: function(body, self)
    {
		if (self.configurations != undefined) {
			$('.configuration.processed', body).remove();
			for ( var i in self.configurations) {
				var conf = self.configurations[i];
				var html = $('.configuration-template', body).html();
				$('#configurations-content', body).append(html);
	    		$('#configurations-content .configuration:last', body).addClass('processed');
	    		$('#configurations-content .configuration:last', body).attr('id', conf.id);
	    		$('#configurations-content .configuration:last div.conf-name', body).text(conf.displayname);
	    		$('#configurations-content .configuration:last label.control-label.quantity', body).attr('for', 'quantity-'+conf.id);
	    		$('#configurations-content .configuration:last label.control-label.price', body).attr('for', 'price-'+conf.id);
	    		$('#configurations-content .configuration:last input.quantity', body).attr('id', 'quantity-'+conf.id);
	    		$('#configurations-content .configuration:last input.quantity', body).attr('name', 'quantity-'+conf.id);
	    		$('#configurations-content .configuration:last input.quantity', body).val(conf.quantity);
	    		$('#configurations-content .configuration:last input.price', body).attr('id', 'quantity-'+conf.id);
	    		$('#configurations-content .configuration:last input.price', body).attr('name', 'quantity-'+conf.id);
	    		$('#configurations-content .configuration:last input.price', body).val(conf.price);
			}
		}
    },
    editConfigs: function()
    {
    	var self = this;
    	var onConfirmCallback = function(body){
    		var confs = [];
    		// Save item configuration
    		var itemId = $('#configurations-content', body).attr('item-id');
    		var sellAllowed = $('#configurations-container').attr('sellAllowed');
    		$('#configurations-content .configuration.processed', body).each(function(){
    			var id = $(this).attr('id');
    			var price = $('input.price', this).val();
    			var qty = $('input.quantity', this).val();
    			var prop = { 'id': id, 'price': price, 'quantity': qty };
    			confs.push(prop);
    		});
    		console.log(confs);
    		$.ajax({
                async : true,
                type: 'POST',
                dataType: 'json',
                url: "?cmd=WarehouseProducts&do=saveItemConfigurations",
                data : {
                	"itemId": itemId,
                    "configurations" : confs,
                    "sellAllowed": sellAllowed
                },
                success : function (data) {
                	if (data.ok) {
                		self.configurations = data.configurations;
            			self.showConfigurations(body, self);
            			if (undefined != data.generalQuantity) {
            				$('#Quantity').val(data.generalQuantity);
            			}
            			if (self.configurations != undefined && self.configurations.length > 0) {
            				$('input#configurations').val(1);
            			} else {
            				$('input#configurations').val(0);
            			}            			
                	} else {
                		showError(data);
                	}
                },
                error: function () {
                }
            });
    	};
    	var content = $('#conf-content').html();
    	modalDialog(trans.get('Configurations'), content, onConfirmCallback, false, function(body){
    		$(body).attr('id','product-conf-content');
    		$('#add-property-btn', body).unbind('click');
    		$('#add-property-btn', body).click(function(e){self.addProperty(e, self)});
    		$('#save-properties-btn', body).unbind('click');
    		$('#save-properties-btn', body).click(function(e){self.saveItemProperties(e, self)});
    		$('button#save-properties-btn').addClass('hide');
    		$(body).on('click', 'button.add-property-value', function(e) {self.addPropertyValue(e, self)});
    		$(body).on('click', 'span.remove-property', self.removeProperty);
    		$(body).on('click', 'span.remove-property-value', self.removePropertyValue);
    		//fill properties
    		if (self.properties != undefined) {
    			$('.property-section.processed',body).remove();
    			for ( var i in self.properties) {
    				var property = self.properties[i];
    	    		var section = $('#configurators-content .property-template', body).html();
    	    		$('#configurators-content', body).append(section);
    	    		var curPropSection = $('#configurators-content .property-section:last', body);
    	    		$(curPropSection).addClass('processed');
    	    		$(curPropSection).attr('prop-name', property.name);
    	    		$(curPropSection).attr('prop-id', property.id);
    	    		$('label span.property-name', curPropSection).text(property.name);
    	    		for ( var j in property.values) {
    	    			var value = property.values[j];
    	    			var section = $('.property-value-template', curPropSection).html();
    	        		$('.property-values', curPropSection).append(section);
    	        		$('.property-value-section:last', curPropSection).addClass('processed');
    	        		$('.property-value-section:last', curPropSection).attr('value-value', value.value);
    	        		$('.property-value-section:last', curPropSection).attr('value-id', value.id);
    	        		$('.property-value-section:last span.property-value', curPropSection).text(value.value);    	    			
    	    		}
				}
    		}
    		self.showConfigurations(body, self);
    	}, 1);    	
    },
    saveProduct: function(ev, reloadPage)
    {
        ev.preventDefault();

        var price = $('#Price').val();
        var qty = $('#Quantity').val();
        var cid = $('#CategoryId').val();
        var sellAllowed = $('#SellAllowed').is(":checked");
        var exit = $(ev.currentTarget).hasClass('save_and_exit');
        var saveAndContinueBtn = $(ev.currentTarget).hasClass('save_and_continue');

        price = parseFloat(price);
        qty = parseFloat(qty);
        cid = parseInt(cid); 
        
        var errorMessage = '';
        if ( isNaN(price) || (!isNaN(price) && price < 0) ) {
        	errorMessage += trans.get('Price_must_be_positive') + '<br>';
        }
        if ( ! sellAllowed && (isNaN(qty) || (!isNaN(qty) && qty<=0)) ) {
        	errorMessage += trans.get('Incorrect_quantity') + '<br>';
        }
        if ( isNaN(cid) || (!isNaN(cid) && cid<=0) ) {
        	errorMessage += trans.get('Category_not_selected') + '<br>';
        }
        
        if (errorMessage.length > 0) {
        	showError(errorMessage);
        	return false;
        }
       
    	$('#overlay').show();
        var btn = $(ev.currentTarget);
        btn.button('loading');
        btn.siblings('button, a');
        btn.addClass('disabled');
        btn.closest('form').ajaxSubmit({
            url     :   $(this).attr('action'),
            type    :   'POST',
            dataType:   'json',
            success :   function(data) {
                if (! data.error) {
                    showMessage(trans.get('Data_save_success'));
                } else {
                    showError(data);
                    btn.button('reset');
                    btn.siblings('button, a');
                    btn.removeClass('disabled');
                    $('#overlay').hide();
                    return false;
                }
                if (saveAndContinueBtn) {
                    $('#overlay').hide();
                    btn.button('reset');
                    btn.prop('disabled', false);
                    btn.removeClass('disabled');
                    if (data.newItemId) {
						$('#newItemId').val(data.newItemId);
					}
                    return;
                }
                if ('undefined' !== typeof reloadPage) {
                    window.location.reload();
                    return;
                }
                if (data.redirectUrl && exit) {
                    window.location.replace(data.redirectUrl);
                    return;
                } else {
                	window.location.reload();
                }
                //btn.button('reset').siblings('button, a').removeClass('disabled');
             }
        });
        return false;
    },
    checkCategorySelected: function()
    {
        var selectedCategory = this.$('#ex-tree').tree('selectedItems');
        var categoryId = 0;
        if (selectedCategory.length) {
            categoryId = selectedCategory[0].additionalParameters.Id;
        }
        var url = this.$('#add-product').attr('href');
        this.$('#add-product').attr('href', url + '&category=' + categoryId);

        return true;
    },
    setSelectedCategory: function(categoryInfo)
    {
        this.$('#CategoryName').val(categoryInfo[0].additionalParameters.Name);
        this.$('#CategoryId').val(categoryInfo[0].additionalParameters.Id);
    },
    setUnselectedCategory: function()
    {
        this.$('#CategoryName').val(trans.get('Category_not_selected'));
        this.$('#CategoryId').val(0);
    },
    showCategoryHint: function(e)
    {
        if ($(e.target).attr('disabled')) {
            showMessage(trans.get('Choose_category_in_the_list'));
        }
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
        else{
            var elem = that.$('div.tree-folder-name:contains("'+ currentItem.data.Name +' ('+ currentItem.data.Id +')")').parent();
            that.$('#ex-tree').tree("selectFolder", elem);
        }
    },
    openCategory: function()
    {
        var that = this;
        that.$('#ex-tree').off('loaded');

        if (categoryPath && categoryPath.length) {
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
    initGetItemInfoField: function()
    {
        var self = this;

        self.$('.get-item-info').editable().on('save', function(e, data) {
            if (data.error) {
                showError(data);
                return;
            }

            var json = data.response;

			self.$('#Name').val(json.Name);
			self.$('#Vendor').val(json.Vendor);
			self.$('#Price').val(json.Price);
			self.$('#Quantity').val(json.Quantity);
			self.$('#Weight').val(json.Weight);

            var body = '';
            var imageChanged = false;

            if (json.Config && json.Config.length) {
				for (var i in json.Config) {
					body += json.Config[i].Name + ": " + json.Config[i].Value + "<br />";

					var configImageUrl = json.Config[i].ImageUrl;
					if (!imageChanged && configImageUrl && configImageUrl.length) {
						self.setProductImage(configImageUrl);
						imageChanged = true;
					}
				}
			}

			if (!imageChanged && json.MainImageUrl) {
				self.setProductImage(json.MainImageUrl);
			}

			$.get(
				'?cmd=Warehouse&do=getItemDescription&itemId='+json.Id
			).success(function(data){
				tinyMCE.editors[0].setContent(body + data.description);
				$('#newItemId').val('');
			}).error(function(xhr, ajaxOptions, thrownError){
				handleAjaxError(xhr.responseText, thrownError);
			});
        });
    },
	setProductImage: function (imageUrl) {
		var html = this.$('ul.ot_image_items .template-image').html();
		this.$('ul.ot_image_items > :not(.hide)').remove();
		this.$('ul.ot_image_items').append(html);
		this.$('ul.ot_image_items li:last img').attr('src', imageUrl);
		this.$('ul.ot_image_items li:last input[type="hidden"]').val(imageUrl);
		this.$('ul.ot_image_items li:last input[type="hidden"]').attr('name', 'images[]');
	},
    render: function()
    {
        var self = this;

        this.initGetItemInfoField();
            $('.confirmDialog .mceEditor').remove();

        initializeTinyMCE('#Description', {
        	height: 500
		});
        
        function afterTinyMceInit(){            
            if ('undefined' !== typeof tinyMCE.get('Description')) {
                tinyMCE.get('Description').setContent($('#item-description').html());
                return true;
            }
            return false;
        }
        //Ждем инициализации редактора
        var intervalID = setInterval(function(){
            if (afterTinyMceInit()) {
                clearInterval(intervalID);
            }
        }, 1000);

        return this;
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
                // `state` and `children` are only used for NON-leaf nodes
                //"state" : "", // or "open", defaults to "closed"
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
    },
    initialize: function(){
        var self = this;
        this.render();

        $('#tree-modal .btn-primary').click(function() {
            var categoryId = $('#tree-modal #categoryId').val();
            var categoryName = $('#tree-modal #categoryName').val();
            if (categoryId > 0) {
                $('.ot_form #CategoryId').attr('value', categoryId);
                $('.ot_form #CategoryName').attr('value', categoryName);

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
                    "data" : function (node)
                    {
                        // the result is fed to the AJAX request `data` option
                        return {
                            "parentId" : node.attr ? node.attr("id") : 0
                        };
                    },
                    "success" : function (data)
                    {
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
             $("#jstree").jstree('open_all');
        })
        .bind("load_node.jstree", function (event, data) {
            self.lastLoadedNode = data.rslt.obj;
        });
        $('#tree-modal').on('shown', function () {
            var categoryName = $('.ot_form #CategoryName').val();
            var categoryId = $('.ot_form #CategoryId').val();
            $('#tree-modal #categoryId').val(categoryId);
            $('#tree-modal #categoryName').val(categoryName);
            $("#jstree").jstree("select_node","#"+categoryId);
        });
        $(".input_numeric_only").keydown(function(event) {
            if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 ||
                (event.keyCode == 65 && event.ctrlKey === true) ||
                (event.keyCode >= 35 && event.keyCode <= 39)) {
					return;
            }
            else {
                if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                    event.preventDefault();
                }
            }
        });

        var imageItems = document.getElementById('image-items-sortable');
        var imageItemsSortable = new Sortable.create(imageItems, {
            handle: 'i.icon-move',
            animation: 150,
            filter: '.modified'
        });
    }
});

$(function(){
    new WarehouseItemsPage();
});
