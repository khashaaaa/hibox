var UsersForm = new Backbone.Collection();
var RolesFormPage = Backbone.View.extend({
    "el": ".roles-form-wrapper",
    "events": {
        "click button.cancel" : "cancelEdit",
        'click div.tree_rights input[type="checkbox"]' : "autoCheck",
        "change .template_role select" : "changedTemplateRole",
        "keyup input#RoleName": "onKeyUpInput",
    },
    render: function()
    {
        return this;
    },
    initialize: function(){
        this.render();
    },
    changedTemplateRole: function(ev){
        var target = this.$(ev.currentTarget);
        var button = $('.btn-primary');
        if (target.val()) {
            button.addClass('disabled');
            $('#fieldsetRoleName').hide();
            if (target.val() == "SuperAdmin") {
                $('input.not_from_template').prop('checked', true);
                button.removeClass('disabled');
            } else {
                $('input.not_from_template').prop('checked', false);
                
                $(".roles-form-wrapper .well").append('<i class="ot-preloader-medium preloader-centered"></i>');
                $.post(
                    '?cmd=roles&do=getRightsList',
                    {
                        'rolename': target.val()
                    },
                    function (data) {  
                        $('.ot-preloader-medium').remove();              
                        if (! data.error) {
                            $.each( data.ids, function( key, value ){
                                $('input.right-' + value).prop('checked', true);
                            });                  
                            button.removeClass('disabled');
                        } else {
                            showError(data.message ? data.message : trans.get('Notify_error'));
                        }
                    }, 'json'
                );
            }
        } else {
            $('#fieldsetRoleName').show();
        }
    },
    cancelEdit: function(ev){
        ev.preventDefault();
        var target = this.$(ev.currentTarget);
        location.href = target.data('action');
    },
    autoCheck: function(e) {
        var target = $(e.currentTarget);
        var checked = target.is(':checked');
        var isRootNode = !!target.parent('h4').length;

        if (isRootNode) {
            target.closest('.right_group').find('input[type="checkbox"]').prop('checked', checked);
        } else {
            var currentNode = target.closest('li');
            // авто выбор/сброс вниз
            currentNode.children('ul').find('input[type="checkbox"]').prop('checked', checked);
            // авто выбор вверх
            if (checked) {
                var parent = currentNode.parent().parent('li');
                while (parent.length) {
                    parent.children('label').find('input[type="checkbox"]').prop('checked', checked);
                    parent = parent.parent().parent('li');
                }
                currentNode.closest('div.right_group').children('h4').children('input[type="checkbox"]').prop('checked', checked);
            }
        }
    },
    onKeyUpInput: function(ev){
        var target = this.$(ev.currentTarget);
        var value = target.val();
        target.val(value.replace(/[^A-zА-яЁё0-9]+/g,''));
    }
});

$(function(){
    var UF = new RolesFormPage();
});
