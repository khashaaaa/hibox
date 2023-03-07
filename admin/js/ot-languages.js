var Languages = new Backbone.Collection();
var LanguagesPage = Backbone.View.extend({
    "el": $("#lang-wrapper")[0],
    "events": {
        "mouseup #add-lang": "add",
        "click #save-lang": "save",
        "click .icon-remove": "deleteLang"
    },
    "select": $('select[name="new_language"]'),
    "select2Selector": '.select2-container',
    "addBtn" : $('#add-lang'),
    "languages": [],
    initialize: function(){
        var chosenItems = document.getElementById('chosenItems');
        var chosenItemsSortable = new Sortable.create(chosenItems, {
            handle: 'i.icon-move',
            animation: 150
        });

        this.updateSelect();
    },
    save: function (callback) {
        this.serialize();
        var form = this.$('#lang-order');
        var button = this.$('#save-lang').button('loading');
        form.find('input[name^=langs]').remove();
        _.each(this.languages, function (item) {
            form.append($('<input>').attr({
                name: 'langs[]',
                type: 'hidden',
                value: item
            }));
        });
        $.post(
            '?cmd=MultilingualSettings&do=saveLangOrder',
            form.serialize(),
            function (data) {
                button.button('reset');
                if (! data.error) {
                    self.$('.badge').removeClass('badge-success').addClass('badge');
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
        var languages = [];
        $.each(this.$('.ot_sortable li'), function(){
            if ($(this).find('span.badge').is('span')) {
                languages.push($(this).attr('data-name'));
            }
        });
        this.languages = languages;
    },
    deleteLang: function (ev) {
        this.serialize();
        if (this.languages.length < 2) {
            showError(trans.get('Language_cant_be_deleted'));
            return false;
        }

        var item = $(ev.target).parent().parent();
        $('<option value="'+item.data('name')+'">'+item.text()+'</option>').appendTo(this.select);
        item.remove();

        this.updateSelect();
    },
    add: function(ev){
        ev.preventDefault();

        if (this.addBtn.hasClass('disabled')) {
            return false;
        }

        if (this.select.find('option').length == 0) {
            showError(trans.get('Notify_error'));
            return false;
        }

        var self = this;
        $.get('templates/lang/item.html?' + Math.random(), function (tpl) {
            var option = self.select.find('option[value="'+self.select.val()+'"]');
            var lang = {'Name': option.first().attr('value'), 'Description': option.first().text()};
            var newItemHtml = _.template(tpl, {'lang': lang});
            option.remove();
            self.$('#chosenItems').append(newItemHtml);
            self.$(self.select2Selector).find('a span').text(self.select.find('option:first').text());
            self.updateSelect();
        });

        return false;
    },
    updateSelect: function () {
        var select2 = $(this.select2Selector);
        if (this.select.find('option').length == 0) {
            this.addBtn.removeAttr('disabled').addClass('disabled');
            select2.addClass('select2-container-disabled');
            select2.find('a').addClass('select2-default').off();
            select2.find('a span').text(trans.get('All_languages_chosen'));
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
    new LanguagesPage();
});
