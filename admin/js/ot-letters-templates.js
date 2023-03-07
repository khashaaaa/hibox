var SearchMethodsSettingsPage = Backbone.View.extend({
    "el": "#eventsTemplates",
    "events": {
        "click .event": "getEventMetaUI",
        "click .testTemplate": "testTemplate",
    },
    elMetaUI: "#eventTemplateMetaUI",
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
    },
    testTemplate: function(ev){
        var self = this;

        ev.preventDefault();
        var btn = this.$(ev.currentTarget);
        btn.button('loading');

        var recipient = btn.siblings('.recipient').val();

        $.post(btn.attr('action'), {'recipient': recipient}, function (data) {
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
});

$(function(){
    var P = new SearchMethodsSettingsPage();	
});
