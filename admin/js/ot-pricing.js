var Pricing = new Backbone.Collection();
var PricingPage = Backbone.View.extend({
    "el": $("#pricing-wrapper")[0],
    "events": {
        "click #add-currency": "add",
        "click #save-currency": "save",
        "click .remove-currency": "deleteCurrency",
        "click .delete-rate": "deleteRate",
        "click .delete-unsafed-rate": "deleteUnSavedRate",
        "change .rates-sync": "changeSyncMode"
    },
    "select": $('select[name="new_currency"]'),
    "firstRateSelect": $('#add-rate-from'),
    "secondRateSelect": $('#add-rate-to'),
    "select2Selector": '.select2-container',
    "addBtn" : $('#add-currency'),
    "currency_list": [],
    "currency_rates": [],
    initialize: function() {
        var chosenItems = document.getElementById('chosenItems');
        var chosenItemsSortable = new Sortable.create(chosenItems, {
            handle: 'i.icon-move',
            animation: 150
        });
        this.updateSelect();
        $('.rates-sync').each(function() {
            var currentSync = $(this).val();
            if (currentSync == 'No') {
                $(this).parent().find('input').attr('disabled', 'disabled');
            } else {
                $(this).parent().find('input').removeAttr('disabled');
            }
        });
        $('.ot_inline_popup_text_editable').editable({
            emptytext: ''+trans.get('Not_filled')+'',
            mode: 'popup',
        });
    },
    add: function(e) {
        e.preventDefault();
        if (this.addBtn.hasClass('disabled')) {
            return false;
        }
        if (this.select.find('option').length == 0) {
            showError(trans.get('Notify_error'));
            return false;
        }
        this.addBtn.attr('disabled', 'disabled');

        var self = this;
        $.get('templates/pricing/underscore_templates/currency.html?' + Math.random(), function (tpl) {
            var newItemHtml = _.template(tpl, {'Code': self.select.val()});
            self.$('#chosenItems').append(newItemHtml);
            self.select.find('option[value="' + self.select.val() + '"]').remove();
            self.$(self.select2Selector).find('a span').text(self.select.find('option:first').text());
            self.updateSelect();
        });
        if (self.select.val() != 'CNY') {
            $('<option value="' + self.select.val() + '">'+ self.select.val() + '</option>').appendTo(this.firstRateSelect);        
            $('<option value="' + self.select.val() + '">'+ self.select.val() + '</option>').appendTo(this.secondRateSelect);
        }
        this.addBtn.removeAttr('disabled');

        return false;
    },
    changeSyncMode: function(e) {
        var target = this.$(e.target);
        var currentSync = target.val();
        if (currentSync == 'No') {
            target.parent().find('input').attr('disabled', 'disabled');
        } else {
            target.parent().find('input').removeAttr('disabled');
        }
        return false;
    },
    deleteRate: function(e) {
        e.preventDefault();
        var target;
        if (this.$(e.target).hasClass('icon-remove')) {
            target = this.$(e.target).parent();
        } else {
            target = this.$(e.target);
        }        
        var firstCode = $(target).parent().attr('first');
        var secondCode = $(target).parent().attr('second');
        modalDialog(trans.get('Confirm_needed'), trans.get('Really_delete_the_selected_rate') + firstCode + ' - ' + secondCode + '?', function(){            
            $('#save-currency').attr('disabled', 'disabled');
            $(target).removeAttr('disabled').addClass('disabled');
            $.post('?cmd=pricing&do=RemoveRate', {'firstCode' : firstCode, 'secondCode' : secondCode}, function (data) {
                target.removeClass('disabled').find('i').attr('class', 'icon-remove font-14');
                if (! data.error) {
                    $(target).parent().remove();                    
                    showMessage(trans.get('Rate_was_deleted'));
                    $('#save-currency').removeAttr('disabled');
                } else {
                    showError(data);
                    $(target).removeAttr('disabled').removeClass('disabled');
                    $('#save-currency').removeAttr('disabled');
                }
            }, 'json');
        });       
        
        return false;
    },
    deleteUnSavedRate: function(e) {
        e.preventDefault();     
        var target;
        if (this.$(e.target).hasClass('icon-remove')) {
            target = this.$(e.target).parent();
        } else {
            target = this.$(e.target);
        }
        var firstCode = $(target).parent().attr('first');
        var secondCode = $(target).parent().attr('second');
        
        modalDialog(trans.get('Confirm_needed'), trans.get('Really_delete_the_selected_rate') + firstCode + ' - ' + secondCode + '?', function(){            
            $('#save-currency').attr('disabled', 'disabled');
            $(target).parent().remove();                    
            showMessage(trans.get('Rate_was_deleted'));
            $('#save-currency').removeAttr('disabled');
        });       
        
        return false;
    },    
    save: function(callback) {
        var self = this;

        this.$('#save-currency').attr('disabled', 'disabled');
        this.serialize();        
        this.$('#currency-order').empty();
        $.each(this.currency_list, function(l, name) {
            self.$('#currency-order').append($('<input>').attr({
                name: 'currency[]',
                type: 'hidden',
                value: name
            }));
        });
        $.each(this.currency_rates, function(l, name) {
            self.$('#currency-order').append($('<input>').attr({
                name: 'rates[]',
                type: 'hidden',
                value: name
            }));
        });
        var marginValue = $('#sync-margin').val();
        this.$('#currency-order').append($('<input>').attr({
            name: 'margin_value',
            type: 'hidden',
            value: marginValue
        }));
        this.$('#currency-order').append($('<input>').attr({
            name: 'syncMode',
            type: 'hidden',
            value: self.$('#syncmode').val()
        }));
        
        
        var form = this.$('#currency-order');
        $.post(
            '?cmd=pricing&do=saveCurrency',
            form.serialize(),
            function (data) {
                self.$('#save-currency').removeAttr('disabled');
                if (! data.error) {
                    self.$('#save-currency').attr('disabled', 'disabled');
                    self.$('.badge').removeClass('badge-success').addClass('badge');
                    showMessage(data.message ? data.message : trans.get('Notify_success'));
                    setTimeout(function() { window.location.reload() }, 5000);
                } else {
                    showError(data);
                }
                if ('function' === typeof callback) {
                    callback(data);
                }
            }, 'json'
        );
    },
    serialize: function() {        
        var currency_list = [];
        $.each(this.$('.ot_sortable li'), function() {
            currency_list.push($(this).attr('data-name'));
        });
        this.currency_list = currency_list;
        
        var currency_rates = [];
        $.each(this.$('.ot_currency_list li'), function() {
            currency_rates.push([$(this).attr('first'), $(this).attr('second'), $(this).find('a').text(), $(this).find('#rate-sync-mode').val(), $(this).find('#rate-sync-margin').val()]);            
        });        
        
        this.currency_rates = currency_rates;        
        this.currency_mode = this.$('#syncmode').val();        
    },
    deleteCurrency: function(ev) {
        this.serialize();
        if (this.currency_list.length < 2) {
            showError(trans.get('Currency_cant_be_deleted'));
            return false;
        }
        var item = $(ev.target).parent().parent();
        item.remove();
        $('<option value="' + item.data('name') + '">' + item.data('name') + '</option>').appendTo(this.select);
        
        if (item.data('name') != 'CNY') {
            this.firstRateSelect.find('option[value="' + item.data('name') + '"]').remove();
            this.secondRateSelect.find('option[value="' + item.data('name') + '"]').remove();
        }
        this.updateSelect();

        return true;
    },
    updateSelect: function () {
        var select2 = $(this.select2Selector);
        if (this.select.find('option').length == 0) {
            this.addBtn.removeAttr('disabled').addClass('disabled');
            select2.addClass('select2-container-disabled');
            select2.find('a').addClass('select2-default').off();
            select2.find('a span').text(trans.get('All_currencies_chosen'));
        } else {
            this.addBtn.removeAttr('disabled').removeClass('disabled');
            select2.removeClass('select2-container-disabled');
            select2.find('a').removeClass('select2-default');
            select2.find('a span').text(this.select.find('option:first').text());
            this.select.select2();
        }
    }
});

$(function() {
    new PricingPage();
});