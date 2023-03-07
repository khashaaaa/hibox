var RolesForm = new Backbone.Collection();
var AdminsFormPage = Backbone.View.extend({
    "el": ".admins-wrapper",
    "events": {
        "click .adminsActions a.remove_admin": "removeAdminAction",
    },
    render: function()
    {
        return this;
    },
    initialize: function(){
        this.render();
    },
    removeAdminAction: function(ev){
        ev.preventDefault();
        var target = this.$(ev.currentTarget);
        modalDialog(trans.get('Confirm_needed'), trans.get('Really_remove_these_users'), function(){
            $.post(target.data('action'), {'login': target.data('login')}, function (data) {
            }).success(function(){
                location.reload();
            }).error(function(xhr, ajaxOptions, thrownErro){
                showError(xhr.responseText);
            });
            
        }); 
        return false;
    }
});

$(function(){
    var UF = new AdminsFormPage();
});
