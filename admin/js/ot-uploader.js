/*
 * otUploader jQuery plugin for file uploading.
 * Copyright OpenTrade Commerce
 */
(function($){
    $.fn.otUploader = function(options) {
        return this.each(function () {
            var defaultOptions = {
                autoSubmit: true,
                afterSaveCallBack: null
            };
            options = $.extend(defaultOptions, options);

            var $fileUploadContainer    = $(this),
                $file                   = $fileUploadContainer.find('.input-image'),
                preview                 = $fileUploadContainer.find('.image-preview'),
                btnNew                  = $fileUploadContainer.find('.btn-new'),
                btnExist                = $fileUploadContainer.find('.btn-exists'),
                btnRemove               = $fileUploadContainer.find('.btn-remove'),
                btnSave                 = $fileUploadContainer.find('.btn-save');

            if (options.autoSubmit) {
                btnSave.hide();
            } else {
                btnSave.show();
            }
            if (preview.attr('src') === "") {
                btnNew.show();
                btnExist.hide();
                btnRemove.hide();
            } else {
                btnNew.hide();
                btnExist.show();
                btnRemove.show();
            }

            $file.change(function() {
                var input = this;
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        preview.attr('src', e.target.result);
                        btnExist.show();
                        btnRemove.show();
                        btnNew.hide();
                        if (options.autoSubmit) {
                            btnSave.trigger('click');
                        } else {
                            btnSave.show();
                        }
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            });

            btnNew.click(function() {
                $file.trigger('click');
            });

            btnExist.click(function() {
                $file.trigger('click');
            });

            btnSave.click(function() {
                btnSave.show();
                btnSave.button('loading').siblings('button').addClass('disabled');
                btnSave.parents('form:first').ajaxSubmit({
                    url     :   btnSave.attr('action'),
                    type    :   'POST',
                    dataType:   'json',
                    success :   function(data) {
                        btnSave.button('reset').siblings('button').removeClass('disabled');
                        btnSave.hide();
                        if (data && 'undefined' !== typeof data.url) {
                            preview.attr('src', data.url);
                            $fileUploadContainer.find('input[name=delete_image]').val('0');
                            showMessage(trans.get('Data_updated_successfully'));
                            if ('function' === typeof options.afterSaveCallBack) {
                                options.afterSaveCallBack(data);
                            }
                        } else {
                            showError(data);
                        }
                    }
                });
            });

            btnRemove.click(function() {
                $fileUploadContainer.find('input[name=delete_image]').val('1');
                preview.attr('src', preview.attr('default'));
                $file.wrap('<form>').closest('form').get(0).reset();
                $file.unwrap();

                btnExist.hide();
                btnRemove.hide();
                btnNew.show();
                if (options.autoSubmit) {
                    btnSave.trigger('click');
                } else {
                    btnSave.show();
                }
            });
        });
    };
})(jQuery);