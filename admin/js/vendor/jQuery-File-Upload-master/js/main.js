$(function () {
    'use strict';

    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: 'uploader/php/',
        done: function (e, data) {
            $('#upload-img img').attr('src', data.result.files[0].thumbnail_url);
            $.post('?cmd=SiteConfiguration&do=save',{'name': 'logo', 'value': data.result.files[0].url},
                function(data){});
            $.post('?cmd=SiteConfiguration&do=save',{'name': 'thumb_logo', 'value': data.result.files[0].thumbnail_url},
                function(data){});
        },
        add: function (e, data) {
            $(this).fileupload('process', data).done(function () {
                data.submit();
            });
        }
    });
});
