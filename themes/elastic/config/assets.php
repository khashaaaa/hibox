<?php

$theme = basename(dirname(dirname(__FILE__)));
$themeDir = 'themes/' . $theme;

return array(
    'general.css' => array(
        '//' . $themeDir . '/css/vendor/font-awesome-4.7.0/css/font-awesome.min.css',
        '//js/vendor/jquery-ui-1.11.4/jquery-ui.min.css',
        '//js/vendor/jquery-ui-1.11.4/jquery-ui.structure.min.css',
        '//js/vendor/jquery-ui-1.11.4/jquery-ui.theme.min.css',
        '//css/libs/jquery/jquery.colorbox/jquery-colorbox.css',
        '//' . $themeDir . '/css/vendor/chosen.css',
        '//' . $themeDir . '/css/vendor/PNotifyBrightTheme.css',
        '//' . $themeDir . '/css/vendor/slick.css',
        '//' . $themeDir . '/css/vendor/jquery.rateyo.min.css',
        '//js/vendor/cloud-zoom/cloud-zoom.css',
        '//' . $themeDir . '/css/vendor/raiting.css',
        '//css/vendor/jquery.pnotify.css',
        '//' . $themeDir . '/css/screen.css',
        '//css/vendor/font-icomoon-categories.css',
    ),
    'general.js' => array(
        '//js/vendor/underscore-min.js',
        '//js/vendor/backbone-min.js',
        '//' . $themeDir . '/js/vendor/popper.min.js',
        '//' . $themeDir . '/js/vendor/bootstrap.min.js',

        '//js/vendor/jquery.numeric.js',
        '//js/vendor/jquery.livequery.min.js',
        '//js/libs/jquery/jquery.colorbox/jquery.colorbox-min.js',
        '//js/vendor/slick.min.js',
        '//' . $themeDir . '/js/vendor/chosen.jquery.min.js',
        '//' . $themeDir . '/js/vendor/chosenImage.jquery.js',
        '//' . $themeDir . '/js/vendor/jquery.rateyo.min.js',
        '//js/vendor/cloud-zoom/cloud-zoom.js',
        '//' . $themeDir . '/js/vendor/jquery.expander.min.js',
        '//' . $themeDir . '/js/vendor/PNotify.js',
        '//' . $themeDir . '/js/vendor/PNotifyButtons.js',
        '//' . $themeDir . '/js/vendor/PNotifyCallbacks.js',
        '//js/common.js',
        '//' . $themeDir . '/js/general.js',
        '//' . $themeDir . '/js/vendor/popper.min.js',
        '//js/vendor/jquery.redirect.js',

        '//' . $themeDir . '/js/script.js', // TODO: отказаться от файла дизайнера
        '//' . $themeDir . '/js/header.js',
        '//js/pages/authentication.js',
        '//js/pages/product.js',
        '//js/pages/product-short.js',
        '//js/pages/vendor.js',
    ),
    'uploader.js' => array(
        '//js/vendor/blueimp/jQuery-File-Upload/js/vendor/jquery.ui.widget.js',
        '//js/vendor/blueimp/jQuery-File-Upload/js/jquery.iframe-transport.js',
        '//js/vendor/blueimp/jQuery-File-Upload/js/jquery.fileupload.js',
        '//js/vendor/blueimp/jQuery-File-Upload/js/jquery.fileupload-process.js',
        '//js/vendor/blueimp/jQuery-File-Upload/js/jquery.fileupload-validate.js'
    ),
);