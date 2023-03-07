jQuery.ajaxSetup({
    beforeSend : function (xhr, settings) {
        xhr.setRequestHeader('X-Requested-With', 'XmlHttpRequest');
        if ('undefined' !== typeof $.deparam) {
            var url = $.deparam.querystring();        
            if ('undefined' !== typeof url.debug && url.debug) {
                if (typeof settings.data === 'object' && typeof settings.data.debug === 'undefined') {
                    settings.data.debug = 1;
                } else if ('undefined' !== typeof settings.data && settings.data.indexOf('&debug=') == -1) {
                    settings.data += '&debug=1';
                }
                if ('undefined' !== typeof settings.url && settings.url.indexOf('&debug=') == -1) {
                    settings.url += (settings.url.indexOf('?') !== -1) ? '&debug=1' : '?debug=1';
                }
            }
        }
    }
});

$(document).ajaxComplete(function (event, xhr, settings) {
    try {
        var response = $.parseJSON(xhr.responseText);
        if ('object' === typeof response) {
            // TODO:
            /*if ('undefined' !== typeof response.expired) {
               if (response.expired === 1) {
                   modalLoginOpen();
                   return false;
               }
            }*/
            if ('undefined' !== typeof response.debugLog) {
                showDebugLog(response.debugLog);
            }
            if ('undefined' !== typeof response.errors) {
                $.each(response.errors, function(i, error){
                    showError(error, true);
                });
            }
        }
    } catch (error) {}
});

$(document).ajaxError(function (xhr, settings, errorThrown) {
    var errorMessage = '';
    if (settings.status == 403) {
        errorMessage = ''+trans.get('Access_forbidden')+'';
    } else {
        if ((settings.statusText !== 'error') && (settings.statusText !== 'abort') && settings.responseText) {
            errorMessage = (settings.statusText + ': ' + settings.responseText);
            errorMessage = errorMessage.replace(/__newline__/g, "\n");
        }
    }
    if (errorMessage.length) {
        showError(errorMessage, true);
    }
});

var showDebugLog = function (debugLog) {
    var moreLink = $('<a href="javascript:void(0)">'+trans.get('Show_queries_log')+'</a>');
    PNotify.notice({
        title: trans.get('Debug_log'),
        text: $('<i></i>').append(debugLog.title).append(moreLink).html(),
        history: false,
        hide: true,
        sticker: false,
        delay: 7000,
        type: 'info',
        titleTrusted: true,
        textTrusted: true,
        modules: {
            Callbacks: {
                afterOpen: function (notice) {
                    $(notice.refs.textContainer).find('a').on('click', function () {
                        modalDialog(
                            trans.get('Debug_log'),
                            debugLog.body,
                            function(){},
                            { confirm: false, cancel: trans.get('Close') },
                            function (){},
                            undefined,
                            4
                        );
                    });
                }
            }
        },
    });
};

var showMessage = function (message, isError, dontHide) {
    var title = 'undefined' !== typeof isError ? trans.get('error') : trans.get('InfoMessage');
    var type = 'undefined' !== typeof isError ? 'error' : 'success';
    PNotify.alert({
        title: title,
        text: message,
        history: false,
        hide: !! ('undefined' === typeof dontHide || !dontHide),
        sticker: false,
        delay: 7000,
        type: type,
        titleTrusted: true,
        textTrusted: true
    });
};

var showError = function (data, dontHide) {
    var errorMessage = trans.get('Notify_error');
    if ('string' === typeof data) {
        errorMessage = data;
    } else if (data.message) {
        errorMessage = data.message;
    } else if (data.errors) {
        // Это массив ошибок, который нам отдаёт Validator.
        var _errorMessages = [];
        $.each(data.errors, function(i, error){
            if ('undefined' !== typeof error.key) {
                var selector = $('#' + error.key).length ? error.key : 'ot_' + error.key;
                $("#" + selector).addClass('validation-error');
            }
            _errorMessages.push(error.message);
        });
        errorMessage = _errorMessages.join('<br/>');
    }
    showMessage(errorMessage, true, dontHide);
};

var level = 1;
//onShowCallback - функция вызываемая после открытия окна без подтвеождения пользователем
//level - уровень окна над другими подобными окнами
var modalDialog = function (title, content, onConfirmCallback, buttons, onShowCallback, levelManual, size, onCancelCallback) {
    if (typeof levelManual === 'undefined') {
        levelManual = level;
    }

    var element = $(".confirmDialog-tpl").clone().removeClass("confirmDialog-tpl").addClass(levelManual + '-confirmDialog').addClass('level-' + levelManual);
    var dialog = element;
    var self = this;
    self.isConfirm = false;

    if (size !== undefined) {
        switch (size) {
            case 1:
                dialog.find('.modal-dialog').addClass('modal-sm');
                break;
            case 3:
                dialog.find('.modal-dialog').addClass('modal-lg');
                break;
            case 4:
                dialog.find('.modal-dialog').addClass('modal-xl');
                break;
        }
    }

    dialog.find('.modal-header h5').html(title);
    dialog.find('.modal-body').html(content);

    if ('object' === typeof buttons) {
        if ('undefined' !== typeof buttons.confirm) {
            if (false !== buttons.confirm) {
                dialog.find('#confirm').html(buttons.confirm).show();
            } else {
                dialog.find('#confirm').hide();
            }
        }
        if ('undefined' !== typeof buttons.cancel) {
            dialog.find('#cancelBtn').html(buttons.cancel);
        }
    }

    dialog.find('#confirm').off().on('click', function () {
        self.isConfirm = true;
        if ('function' === typeof onConfirmCallback) {
            var callbackResult = onConfirmCallback(dialog.find('.modal-body'));
            // Если колбэк вернул false, то считаем,
            // что произошла ошибка, которая показана пользователю.
            // Поэтому окно не закрываем, чтобы дать пользователю возможность исправить ошибку
            if (false !== callbackResult) {
                dialog.find('.close').trigger('click');
            } else {
                self.isConfirm = false;
            }
        } else {
            dialog.find('.close').trigger('click');
        }
    });

    // При скрытии диалогового окна восстановим его начальное состояние.
    dialog.off('hidden.restoreDefaults').on('hidden.restoreDefaults', function(){
        dialog.find('#confirm').html(trans.get('Yes')).show();
        dialog.find('#cancelBtn').html(trans.get('Cancel')).show();
        dialog.find('.modal-header h5, .modal-body').empty();
        element.remove();
    });

    if ('function' === typeof onShowCallback) {
        dialog.on('shown.bs.modal', onShowCallback(dialog.find('.modal-body')));
    }

    dialog.on('hidden.bs.modal', function (e) {
        if (! self.isConfirm && typeof onCancelCallback === 'function') {
            onCancelCallback(dialog.find('.modal-body'));
        }
        if ($('.confirmDialog.show').length > 0) {
            $('body').addClass('modal-open');
        }
        dialog.remove();
    });

    level++;
    return dialog.modal();
};

$(document).on('click', '.debug-log .debug-title', function () {
    $(this).siblings('.debug-body').toggle();
});

$(function () {
    var photoContainer = $('#photo-search');
    var showPhotoSearch = function() {
        photoContainer.css('display','inline-block');
    };
    var hidePhotoSearch = function() {
        photoContainer.css('display','none');
    };

    // Search Bar photo upload
    var searchForm = $('form.js-image-search-form'),
        searchLanguage = searchForm.data('lang'),
        imageUploadUrl = searchForm.data('img-upload-url'),
        photoSearchInput = photoContainer.find('input[type="file"]').eq(0);

    if (photoContainer.length) {
        photoSearchInput
            .fileupload({
                url: imageUploadUrl,
                dataType: 'json',
                autoUpload: true,
                acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
                disableImagePreview: true
            })
            .on('fileuploadadd', function (e, data) { // Добавление файла
                searchForm.find('.file-upload label').hide();
                searchForm.find('.photo-search-preloader').show();
                searchForm.find('.btn').addClass('disabled').attr('disabled', 'true');

                data.formData = {
                    language:searchLanguage,
                    fileType:'Image'
                };
            })
            .on('fileuploaddone', function (e, data) { // Загрузка завершена успешно
                $.each(data.result.files, function (index, file) {
                    if (file.error) {
                        searchForm.find('.file-upload label').show();
                        searchForm.find('.photo-search-preloader').hide();
                        searchForm.find('.btn').removeClass('disabled');

                        showError(file.error);
                    } else if (file.fileId) {
                        searchForm.find('#box-search-form_search').val(''); // очистить поисковую фразу
                        searchForm.find('input[name="imageId"]').val(file.fileId); // добавить адрес файла

                        searchForm.submit();
                    }
                });
            })
            .on('fileuploadfail', function (e, data) { // Загрузка прервана, ошибка от сервера
                $.each(data.files, function (index) {
                    searchForm.find('.file-upload label').show();
                    searchForm.find('.photo-search-preloader').hide();
                    searchForm.find('.btn').removeClass('disabled');

                    var errorMessage = data.files[index].name + ': ' + data._response.jqXHR.responseText;
                    showError(errorMessage);
                });
            })
            .on('fileuploadprocessalways', function (e, data) { // Завершение загрузки Успех/Ошибка на клиенте
                var index = data.index,
                    file = data.files[index];
                if (file.error) {
                    searchForm.find('.file-upload label').show();
                    searchForm.find('.photo-search-preloader').hide();
                    searchForm.find('.btn').removeClass('disabled');

                    var errorMessage = file.error;
                    showError(errorMessage);
                }
            });
    }

    // Switching tabs providers
    $('#box-search-form_providers a[data-toggle="tab"]').click(function(){
        var self = this;
        var alias = $(self).data('alias');

        if ($(this).data('image-search')) {
            showPhotoSearch();
        } else {
            hidePhotoSearch();
        }

        searchForm.find('select[name="Provider"]').val(alias);

        // если пользователь меняет провайдера - делаем автосабмит
        if (searchForm.find('#box-search-form_search').val() || ($(this).data('image-search') && searchForm.find('input[name="imageId"]').val())) {
            searchForm.submit();
        }
    });

    searchForm.on('submit', function () {
        if (searchForm.find('#box-search-form_search').val()) {
            searchForm.find('input[name="imageId"]').val('');
        }
    });

});

function showOverlay(showProgress = false){
    var h = $('#overlay').parent().height();
    $('#overlay').css({
        height: h+'px',
        display: 'block'
    });
    if (showProgress) {
        $('#overlay-progressbar').css({
            display: 'block'
        });
    }
}

function hideOverlay(){
    var h = $('#overlay').parent().height();
    $('#overlay-progressbar').hide();
    $('#overlay').hide();
}

// disabling form submissions if there are invalid fields
$('form.needs-validation').submit(function(e){
    var $form = $(this);
    if ((typeof($form[0].checkValidity) == "function") && !$form[0].checkValidity()) {
        e.preventDefault();
        e.stopImmediatePropagation();
    }
    $form.addClass('was-validated');
});

  $("input[name='save']").click (function(e) {
    var noError = true;
    var requiredFields = $(this).closest('form').find('input');

    requiredFields.each(function () {
        var input = $(this);
        var inputBlock = input;
        var required = input.hasClass('required');

        if (required && $.trim(input.val()) === '') {
            e.preventDefault();
            e.stopImmediatePropagation();
            inputBlock.addClass('is-invalid');
            noError = false;
        } else {
            inputBlock.removeClass('is-invalid');
            inputBlock.addClass('is-valid');
        }
    });
});

