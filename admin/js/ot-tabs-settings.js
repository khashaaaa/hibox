var ProviderSettingsPage = Backbone.View.extend({
    "el": "#providersInfo",
    "events": {
        "click .providerUI": "getProviderMetaUI",
    },
    elMetaUI: "#providerMetaUI",
    render: function()
    {
        return this;
    },
    initialize: function()
    {
        this.render();
    },
    getProviderMetaUI: function(ev){
        var self = this;

        ev.preventDefault();
        var btn = this.$(ev.currentTarget);
        btn.button('loading');
        $(self.elMetaUI).html('');

        $.post(btn.attr('action'), {'type': btn.attr('data-id')}, function (data) {
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
    }
});

$(function(){
    var P = new ProviderSettingsPage();
});
