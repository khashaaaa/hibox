var PluginsUtilPage = Backbone.View.extend({
    "el": ".plugins-control-view-wrapper",
    "events": {
        "click .installPlugin": "installPlugin",
        "click .updatePlugin": "updatePlugin",
        "click .deletePlugin": "deletePlugin",
        "click .showDescription": "showDescription"
    },
    render: function()
    {
        return this;
    },
    initialize: function()
    {
        this.render();
    },
    installPlugin: function(ev){
        ev.preventDefault();
        var btn = this.$(ev.target);
        var content = trans.get('Plugins_confirm_install') + ' "' + btn.attr('data-title') + '"?';
        modalDialog(trans.get('Confirm_needed'), content, function(){
            btn.button('loading');
            $.post(btn.attr('action'), {'name': btn.attr('data-name'), 'downloadUrl': btn.attr('data-download')}, function (data) {
                if (! data.error) {
                    location.reload();
                } else {
                    showError(data);
                }
                btn.button('reset');
            }, 'json').error(function(xhr, ajaxOptions, thrownErro){
                showError(xhr.responseText);
            });;
        });
    },
    updatePlugin: function(ev){
        ev.preventDefault();
        var btn = this.$(ev.target);
        var content = trans.get('Plugins_confirm_update') + ' "' + btn.attr('data-title') + '"?';
        modalDialog(trans.get('Confirm_needed'), content, function(){
            btn.button('loading');
            $.post(btn.attr('action'), {'name': btn.attr('data-name'), 'downloadUrl': btn.attr('data-download')}, function (data) {
                if (! data.error) {
                    location.reload();
                } else {
                    showError(data);
                }
                btn.button('reset');
            }, 'json').error(function(xhr, ajaxOptions, thrownErro){
                showError(xhr.responseText);
            });;
        });
    },
    deletePlugin: function(ev){
        ev.preventDefault();
        var btn = this.$(ev.target);
        var content = trans.get('Plugins_confirm_delete') + ' "' + btn.attr('data-title') + '"?';
        modalDialog(trans.get('Confirm_needed'), content, function(){
            btn.button('loading');
            $.post(btn.attr('action'), {'name': btn.attr('data-name')}, function (data) {
                if (! data.error) {
                    location.reload();
                } else {
                    showError(data);
                }
                btn.button('reset');
            }, 'json').error(function(xhr, ajaxOptions, thrownErro){
                showError(xhr.responseText);
            });;
        });
    },
    showDescription: function(ev){
        var target = this.$(ev.target);
        var content = $(target).siblings('.pluginDescription').html();

        modalDialog(trans.get('Description'), content);
    }
});

$(function(){
    var P = new PluginsUtilPage();	
});
