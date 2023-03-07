function InitDataTable (url) {
    var params = {
        //"bFilter": false,
        "bDestroy": true,
        "bProcessing": true,
        "scrollX": true,
        "sAjaxSource": url
    };
    if (currentAdminLang && currentAdminLang === 'ru') {
        params["oLanguage"] = { // localization
            "sProcessing":   "Загрузка...",
            "sLengthMenu":   "Вывести _MENU_ записей на страницу",
            "sZeroRecords":  "Записи отсутствуют.",
            "sInfo":         "Показано с _START_ по _END_ из _TOTAL_ записей",
            "sInfoEmpty":    "",
            "sInfoFiltered": "(отфильтровано из _MAX_ записей)",
            "sInfoPostFix":  "",
            "sSearch":       "Поиск по содержанию:",
            "sUrl":          "",
            "oPaginate": {
                "sFirst": "Первая",
                "sPrevious": "Предыдущая",
                "sNext": "Следующая",
                "sLast": "Последняя"
            }
        };
    }
    oTable = $('#translations_table').dataTable(params);
}

function ReInitDataTable(){
    InitDataTable("?cmd=Translations&do=getTranslationsJSON&lang=" + $('#languages-filter').val() + "&sort=" + $('#translations-type-filter').val());
}

$(function(){
        $(document).on('click', 'a[data-action="delete"]',function(){
        var key = $(this).attr('data-key');
        var loader = $(this).find('i.icon-trash img');
        loader.show();

        $.get('?cmd=Translations&do=delete', {
            'key': key
        }, function(data){
            if (! data.error) {
                $('.label-success[data-key="'+key+'"]').removeClass('label-success').addClass('label-warning');
                loader.hide();
                ReInitDataTable();
            } else {
                loader.hide();
                showError(data);
            }
        });
        return false;
    });

    InitDataTable("?cmd=Translations&do=getTranslationsJSON");


    $('#translations-type-filter').change(function(){
        setTimeout(ReInitDataTable, 10);
    });

    $('#languages-filter').change(function(){
        setTimeout(ReInitDataTable, 10);
    });
});

var TranslationsUtilPage = Backbone.View.extend({
    "el": ".translations-view-wrapper",
    "events": {
        "submit #search-form": "search",
        "change #TranslatableContent": "changeTranslatableContent",
        "keyup .translationText": "changeTranslationText",
        "input .translationText": "changeTranslationText",
        "click .translationSave": "translationSave",
        "click .translationDelete": "translationDelete",
        "click .searchTranslationSave": "searchTranslationSave",
        "click .searchTranslationDelete": "searchTranslationDelete"
    },
    render: function()
    {
        return this;
    },
    initialize: function()
    {
        this.render();
        // логика disable для кнопок
        $('.translationSave').each(function(){
            $(this).prop('disabled', true);
        });
        $('.translationDelete').each(function(){
            var text = $(this).siblings('input[name="text"]').val();
            if (text === '') $(this).prop('disabled', true);
        });
    },
    search: function(ev){
        if ($('#TranslatableContent').val() === null) {
            showError(trans.get('Service_translation_translatable_content_select'));
            return false;
        }

        return true;
    },
    changeTranslatableContent: function(ev){
        var select = this.$(ev.target);
        var form = select.closest('form');
        form.submit();
    },
    changeTranslationText: function(ev){
        var text = this.$(ev.target);
        var btn = text.siblings('.translationSave');
        if (btn.prop('disabled')) btn.prop('disabled', false);
        if (text.val() === '') btn.prop('disabled', true);
    },
    translationSave: function(ev){
        ev.preventDefault();
        var btn = this.$(ev.currentTarget);
        btn.button('loading');

        var form = btn.closest('form');
        var text = form.find('input[name="text"]').val();
        var translatableContent = $('#TranslatableContent').val();
        $.post(form.attr('action'), {'translationId': form.data('id'), 'translatableContent': translatableContent, 'Text': text}, function (data) {
            btn.button('reset');
            if (! data.error) {
                showMessage(trans.get('Notify_success'));
                form.find('.translationDelete').prop('disabled', false);
                setTimeout(function() {
                    btn.prop("disabled", true);
                }, 0);
            } else {
                showError(data);
            }
        }, 'json').error(function(xhr, ajaxOptions, thrownError){
            showError(xhr.responseText);
        });
    },
    translationDelete: function(ev){
        ev.preventDefault();
        var btn = this.$(ev.currentTarget);
        var form = btn.closest('form');

        var content = trans.get('Service_translation_confirm_delete');
        modalDialog(trans.get('Confirm_needed'), content, function(){
            btn.button('loading');

            var translatableContent = $('#TranslatableContent').val();
            $.post(form.attr('action'), {'translationId': form.data('id'), 'translatableContent': translatableContent, 'ResetTranslation': 'true'}, function (data) {
                btn.button('reset');
                if (! data.error) {
                    form.find('input[name="text"]').val('');
                    showMessage(trans.get('Notify_success'));
                    form.find('.translationSave').prop('disabled', true);
                    setTimeout(function() {
                        btn.prop("disabled", true);
                    }, 0);
                } else {
                    showError(data);
                }
            }, 'json').error(function(xhr, ajaxOptions, thrownError){
                showError(xhr.responseText);
            });
        }, {'confirm':trans.get('Revert'), 'cancel': trans.get('Cancel')});
    },

    searchTranslationDelete: function(ev){
        ev.preventDefault();
        var btn = this.$(ev.currentTarget);
        var form = btn.closest('form');
        var id = form.data('id');
        var content = trans.get('Confirm_needed');
        modalDialog(trans.get('Confirm_needed'), content, function(){
            btn.button('loading');

            $.post(form.attr('action'), {'translationId': id, 'ResetTranslation': 'true'}, function (data) {
                btn.button('reset');
                if (! data.error) {
                    $('tr[data-id="'+ id +'"]').remove();
                    form.find('input[name="text"]').val('');
                    showMessage(trans.get('Notify_success'));
                    form.find('.translationSave').prop('disabled', true);
                    setTimeout(function() {
                        btn.prop("disabled", true);
                    }, 0);
                } else {
                    showError(data);
                }
            }, 'json').error(function(xhr){
                showError(xhr.responseText);
            });
        }, {'confirm':trans.get('confirm'), 'cancel': trans.get('Cancel')});
    },

    searchTranslationSave: function(ev){
        ev.preventDefault();
        var btn = this.$(ev.currentTarget);
        btn.button('loading');

        var form = btn.closest('form');
        var text = form.find('input[name="text"]').val();
        $.post(form.attr('action'), {'translationId': form.data('id'), 'Text': text}, function (data) {
            btn.button('reset');
            if (! data.error) {
                showMessage(trans.get('Notify_success'));
                form.find('.translationDelete').prop('disabled', false);
                setTimeout(function() {
                    btn.prop("disabled", true);
                }, 0);
            } else {
                showError(data);
            }
        }, 'json').error(function(xhr){
            showError(xhr.responseText);
        });
    },


});

$(function(){
    var P = new TranslationsUtilPage();	
});
