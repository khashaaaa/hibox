var UsersForm = new Backbone.Collection();
var AdminsFormPage = Backbone.View.extend({
    "el": ".admins-form-wrapper",
    "events": {
        "click button.cancel": "cancelEdit",
        "blur input#Login": "onKeyUpInput",
    },
    render: function()
    {
        return this;
    },
    initialize: function(){
        this.render();
    },
    cancelEdit: function(ev){
        ev.preventDefault();
        var target = this.$(ev.currentTarget);
        location.href = target.data('action');
    },
    onKeyUpInput: function(ev){
        var target = this.$(ev.currentTarget);
        var value = target.val();
        target.val(value.replace(/[^A-zА-яЁё0-9]+/g,''));
    }
});

$(function(){
    var UF = new AdminsFormPage();
});
