var CacheSettingsView = Backbone.View.extend({
    "el": ".cache-settings",
    "events": {
        "click #cacheClean": "cacheClean"
    },
    cacheClean: function(ev){
        var button = self.$(ev.target);
        $(button).button('loading');

        $.post('?cmd=CacheSettings&do=cacheClean',
            {},
            function (data){
                $(button).button('reset');
                if (! data.error) {
                    if (data.needRestart) {
                        showMessage(data.message);
                        $("#cacheClean").click();
                    } else {
                        showMessage(data.message);
                    }
                } else {
                    showError(data.message);
                }
            }, 'json'
        );
    }
});

$(function(){
    var CacheSettings = new CacheSettingsView();
});
