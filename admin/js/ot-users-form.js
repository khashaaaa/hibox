var UsersForm = new Backbone.Collection();
var UsersFormPage = Backbone.View.extend({
    "el": ".users-form-wrapper",
    "events": {
        "click #addRecipientLink": "toggleRecipientForm",
        "click [type='submit']": "saveUser"
    },
    render: function() 
    {
        this.toggleRecipientForm(1);
        return this;
    },
    initialize: function(){
        this.render();
    },
    toggleRecipientForm: function (initial) {
        var form = this.$('.new-user-delivery-form');
        if ('undefined' !== typeof initial || !this.$('#addRecipientLink').hasClass('collapsed')) {
            form.find('input[required="required"]').removeAttr('required').attr('wasrequired', 'wasrequired');
        } else {
            form.find('input[wasrequired="wasrequired"]').removeAttr('wasrequired').attr('required', 'required');
        }
    },
    saveUser: function (ev) {
        var button = $(ev.target);
        button.attr('disabled', 'disabled');
        var form = $(ev.target).closest('form');
        $.post(form.attr('action'), form.serializeArray(), function (data) {
            button.removeAttr('disabled');
            if (data.error) {
                showError(data.message);
            } else {
                showMessage(data.message);
                document.location.href = '?cmd=users&do=profile&id=' + data.id;
            }
        }, 'json');
        return false;
    }
});

$(function(){
    var UF = new UsersFormPage();
});
