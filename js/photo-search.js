(function ($) {


    $(function () {
        var photoContainer = $('#photo-search');

        var showPhotoSearch = function() {
            photoContainer.css('display','inline-block');
        };
        var hidePhotoSearch = function() {
            photoContainer.css('display','none');
        };

        // Search Bar photo upload
        var searchForm = $('form#full_search_form'),
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
                            searchForm.find('input[name="search"]').val(''); // очистить поисковую фразу
                            searchForm.find('input[name="cid"]').val(''); // очистить категорию
                            searchForm.find('input[name="imageId"]').val(file.fileId); // добавить адрес файла
                            searchForm.find('input[name="SearchMethod"]').val('Image'); // установить метод поиска

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


    });


}(jQuery));