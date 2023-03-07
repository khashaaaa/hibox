var SearchMethodsSettingsPage = Backbone.View.extend({
    "el": "#deliverySystems",
    "events": {
        "click .event": "getEventMetaUI",
    },
    elMetaUI: "#deliverySystemsMetaUI",
    render: function()
    {
        return this;
    },
    initialize: function()
    {
        this.render();
    },
    getEventMetaUI: function(ev){
        var self = this;

        ev.preventDefault();
        var btn = this.$(ev.currentTarget);
        btn.button('loading');
        $(self.elMetaUI).html('');

        $.post(btn.attr('action'), {'type': btn.attr('data-type')}, function (data) {
            btn.button('reset');
            if (! data.error) {
                $(self.elMetaUI).html(data.html);
                $(self.elMetaUI).attr('reload-url', btn.attr('action') + '&type=' + btn.attr('data-type'));
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
    var P = new SearchMethodsSettingsPage();	
});
