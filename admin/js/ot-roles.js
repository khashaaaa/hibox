var RolesForm = new Backbone.Collection();
var RolesFormPage = Backbone.View.extend({
    "el": ".roles-wrapper",
    "events": {
        "click .rolesActions a.remove_role": "removeRoleAction",
    },
    render: function()
    {
        return this;
    },
    initialize: function(){
        this.render();
    },
    removeRoleAction: function(ev){
        ev.preventDefault();
        var target = this.$(ev.currentTarget);
        modalDialog(trans.get('Confirm_needed'), trans.get('Really_remove_these_roles'), function(){
            target.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
            $.post(target.data('action'), {'name': target.data('name')}, function (data) {
                    if (! data.error) {
                        showMessage(trans.get('Role_is_deleted'));
                        target.parent().parent().remove();
                    } else {                        
                        showError(data.message);
                        target.removeClass('disabled').find('i').attr('class', 'icon-remove');
                    }
                }, 'json'
            );            
        });
        return false;
    }
});

$(function(){
    var UF = new RolesFormPage();
});
