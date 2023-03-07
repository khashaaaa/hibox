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
    $.pnotify({
        title: trans.get('Debug_log'),
        text: $('<i></i>').append(debugLog.title).append(moreLink).html(),
        history: false,
        hide: true,
        sticker: false,
        delay: 10000,
        type: 'info',
        after_open: function (pnotify) {
            pnotify.find('a').on('click', function () {
                modalDialog(
                    trans.get('Debug_log'),
                    debugLog.body,
                    function(){},
                    { confirm: false, cancel: trans.get('Close') },
                    function (){},
                    undefined,
                    3
                );
            });
        }
    });
};

var showMessage = function (message, isError, dontHide) {
    var title = 'undefined' !== typeof isError ? trans.get('error') : trans.get('InfoMessage');
    var type = 'undefined' !== typeof isError ? 'error' : 'success';
    $.pnotify({
        title: title,
        text: message,
        history: false,
        hide: !! ('undefined' === typeof dontHide || !dontHide),
        sticker: false,
        delay: 5000,
        type: type
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


$(document).on('click', '.debug-log .debug-title', function () {
    $(this).siblings('.debug-body').toggle();
});

$(function () {
    // Init tooltip
    $('body').tooltip({
        selector: '[rel="tooltip"]',
        trigger: 'hover'
    });

    $('.currency-userpreferences').selectpicker();
    $('.country-userpreferences').selectpicker();

    var photoContainer = $('#photo-search');
    var showPhotoSearch = function() {
        photoContainer.css('display','inline-block');
    };
    var hidePhotoSearch = function() {
        photoContainer.css('display','none');
    };

    // Search Bar photo upload
    var searchForm = $('form#box-search-form'),
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
                photoContainer.addClass('disabled');
                photoContainer.find('i').removeClass('glyphicon-camera').addClass('microPreLoader');
                searchForm.find('.btn').addClass('disabled');

                data.formData = {
                    language:searchLanguage,
                    fileType:'Image'
                };
            })
            .on('fileuploaddone', function (e, data) { // Загрузка завершена успешно
                $.each(data.result.files, function (index, file) {
                    if (file.error) {
                        photoContainer.removeClass('disabled');
                        photoContainer.find('i').removeClass('microPreLoader').addClass('glyphicon-camera');
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
                    photoContainer.removeClass('disabled');
                    photoContainer.find('i').removeClass('microPreLoader').addClass('glyphicon-camera');
                    searchForm.find('.btn').removeClass('disabled');

                    var errorMessage = data.files[index].name + ': ' + data._response.jqXHR.responseText;
                    showError(errorMessage);
                });
            })
            .on('fileuploadprocessalways', function (e, data) { // Завершение загрузки Успех/Ошибка на клиенте
                var index = data.index,
                    file = data.files[index];
                if (file.error) {
                    photoContainer.removeClass('disabled');
                    photoContainer.find('i').removeClass('microPreLoader').addClass('glyphicon-camera');
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

$('.menu-aside').click(function(){
    $('.accordion-menu-page').slideToggle();
});