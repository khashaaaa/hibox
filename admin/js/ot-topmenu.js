var TopmenuView = Backbone.View.extend({
    "el": ".topmenu",
    "events": {
        "click .ot_globall_settings .cacheClean": "cacheClean",
        "click .ot_globall_settings:not(.open)": "cacheSize"
    },
    cacheClean: function(ev){
        var globalSettingSelector = $('.ot_globall_settings').find('.cacheClean');

        if (!globalSettingSelector.hasClass('.js-clean-sent')) {
            $.post('?cmd=CacheSettings&do=cacheClean',
                {},
                function (data) {
                    globalSettingSelector.removeClass('.js-clean-sent');
                    if (!data.error) {
                        if (data.needRestart) {
                            showMessage(data.message);
                            $(".ot_globall_settings .cacheClean").click();
                        } else {
                            showMessage(data.message);
                            console.log('Cache clean');
                            $('.ot_globall_settings').find('.cacheClean').find('span.label-info')
                                .removeClass('label-info').removeClass('label').addClass('ot-preloader-micro').html('');
                        }
                    } else {
                        showError(data.message);
                    }
                    $('.ot_globall_settings.open').click();
                }, 'json'
            );
            globalSettingSelector.addClass('.js-clean-sent');
        }
    },
    cacheSize: function(ev){
        var globalSettingSelector = $('.ot_globall_settings').find('.cacheClean');

        if (globalSettingSelector.find('span.ot-preloader-micro').length > 0 && !globalSettingSelector.hasClass('.js-size-sent')) {
            $.post('?cmd=CacheSettings&do=cacheSize',
                {},
                function (data){
                    globalSettingSelector.find('.label-info').remove();
                    globalSettingSelector.append('<span class="label label-info">' + data.message + '</span>')
                    globalSettingSelector.find('.ot-preloader-micro').remove();
                    globalSettingSelector.removeClass('.js-size-sent');
                }, 'json'
            );
            globalSettingSelector.addClass('.js-size-sent');
        }
    }
});

$(function(){
    var Topmenu = new TopmenuView();
});
