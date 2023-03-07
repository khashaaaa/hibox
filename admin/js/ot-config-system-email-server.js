var ConfigEmailServerPage = Backbone.View.extend({
    "el": "#emailServerInfo",
    "events": {
        "click .emailServer": "getEmailServerMetaUI",
        "click .testEmailServer": "testEmailServer",
        "click .selectEmailServerForBox": "selectEmailServerForBox"
    },
    elMetaUI: "#emailServerMetaUI",
    render: function()
    {
        return this;
    },
    initialize: function()
    {
        this.render();
    },
    getEmailServerMetaUI: function(ev){
        var self = this;

        ev.preventDefault();
        var btn = this.$(ev.currentTarget);
        btn.button('loading');
        $(self.elMetaUI).html('');

        $.post(btn.attr('action'), {'id': btn.attr('data-id')}, function (data) {
            btn.button('reset');
            if (! data.error) {
                $(self.elMetaUI).html(data.html);
            } else {
                showError(data);
            }
        }, 'json').error(function(xhr, ajaxOptions, thrownError){
            btn.button('reset');
            showError(xhr.responseText);
        });
    },
    testEmailServer: function(ev){
        var self = this;

        ev.preventDefault();
        var btn = this.$(ev.currentTarget);
        btn.button('loading');

        var email = btn.siblings('.email').val();

        $.post(btn.attr('action'), {'email': email}, function (data) {
            btn.button('reset');
            if (! data.error) {
                showMessage(data.message);
            } else {
                showError(data);
            }
        }, 'json').error(function(xhr, ajaxOptions, thrownError){
            btn.button('reset');
            showError(xhr.responseText);
        });
    },
    selectEmailServerForBox: function(ev){
        var self = this;

        ev.preventDefault();
        var btn = this.$(ev.currentTarget);
        btn.button('loading');

        $.post(btn.attr('action'), {}, function (data) {
            btn.button('reset');
            if (! data.error) {
                btn.removeClass('btn-primary').addClass('btn-success');
                showMessage(trans.get('Notify_success'));
                // отметим в списке выбранный smtp
                $('.emailServer').each(function(){
                    $(this).find('i').remove();
                    if ($(this).attr('data-id') == btn.attr('data-id')) {
                        $(this).prepend('<i class="icon-ok"></i>');
                    }
                });
            } else {
                showError(data);
            }
        }, 'json').error(function(xhr, ajaxOptions, thrownError){
            btn.button('reset');
            showError(xhr.responseText);
        });
    }
});

$(function(){
    var P = new ConfigEmailServerPage();	
});
