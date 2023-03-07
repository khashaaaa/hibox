var Internals = new Backbone.Collection();
var InternalsPage = Backbone.View.extend({
    "el": $("#internal-wrapper")[0],
    "events": {
        "mouseup #internal-add": "add",
        "click .icon-remove": "deleteInternal",
        "click .save-case" : "save"
    },
    "select": $('select[name="new_internal"]'),
    "select2Selector": '.select2-container',
    "addBtn" : $('#internal-add'),
    "internals": [],
    initialize: function(){
        this.updateSelect();
    },
    save: function (callback) {
        this.serialize();
        var form = this.$('#case-settings');
        var button = this.$('.save-case').button('loading');
        form.find('input[name^=DeliveryTypes]').remove();
        _.each(this.internals, function (item) {
            form.append($('<input>').attr({
                name: 'DeliveryTypes[]',
                type: 'hidden',
                value: item
            }));
        });
        $.post(
            '?cmd=shipment&do=saveInternal',
            form.serialize(),
            function (data) {
                button.button('reset');
                if (! data.error) {
                    self.$('.badge').removeClass('badge-success').addClass('badge-info');
                    showMessage(data.message ? data.message : trans.get('Notify_success'));
                } else {
                    showError(data.message ? data.message : trans.get('Notify_error'));
                }
                if ('function' === typeof callback) {
                    callback(data);
                }
            }, 'json'
        );
    },
    serialize: function(){
        var internals = [];
        $.each(this.$('.ot_sortable li'), function(){
            if ($(this).find('span.badge').is('span')) {
                internals.push($(this).attr('data-name'));
            }
        });
        this.internals = internals;
    },
    deleteInternal: function (ev) {
        this.serialize();
        if (this.internals.length < 2) {
            showError(trans.get('Internal_delivery_method_cant_be_deleted'));
            return false;
        }

        var item = $(ev.target).parent().parent();
        $('<option value="'+item.data('name')+'">'+item.data('name')+'</option>').appendTo(this.select);
        item.remove();

        this.updateSelect();

        return true;
    },
    add: function (ev) {
        ev.preventDefault();

        if (this.addBtn.hasClass('disabled')) {
            return false;
        }

        if (this.select.find('option').length == 0) {
            showError(trans.get('Notify_error'));
            return false;
        }

        var self = this;
        $.get('templates/shipment/internal/item.html?' + Math.random(), function (tpl) {
            var newItemHtml = _.template(tpl, {'itemName': self.select.val()});
            self.$('#chosenItems').append(newItemHtml);

            self.select.find('option[value="'+self.select.val()+'"]').remove();
            self.$(self.select2Selector).find('a span').text(self.select.find('option:first').text());

            self.updateSelect();
        });

        return false;
    },
    updateSelect: function () {
        var select2 = $(this.select2Selector);
        if (this.select.find('option').length == 0) {
            this.addBtn.attr('disabled', 'disabled').addClass('disabled');
            select2.addClass('select2-container-disabled');
            select2.find('a').addClass('select2-default').off();
            select2.find('a span').text(trans.get('All_deliveries_chosen'));
        } else {
            this.addBtn.removeAttr('disabled').removeClass('disabled');
            select2.removeClass('select2-container-disabled');
            select2.find('a').removeClass('select2-default');
            select2.find('a span').text(this.select.find('option:first').text());
            this.select.select2();
        }
    }
});

$(function(){
    new InternalsPage();
});