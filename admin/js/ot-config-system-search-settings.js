var SearchMethodsSettingsPage = Backbone.View.extend({
    "el": "#searchMethodsInfo",
    "events": {
        "click .searchMethod": "getSearchMethodMetaUI",
    },
    elMetaUI: "#searchMethodsMetaUI",
    render: function()
    {
        return this;
    },
    initialize: function()
    {
        this.render();
    },
    getSearchMethodMetaUI: function(ev){
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
    }
});

$(function(){
    var P = new SearchMethodsSettingsPage();	
});
