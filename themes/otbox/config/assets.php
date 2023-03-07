<?php

$theme = basename(dirname(dirname(__FILE__)));
$themeDir = 'themes/' . $theme;

return array(
    'style.css' => array(
        '//css/style/messages.css',
        '//css/libs/jquery/jquery.ui/jquery-ui.css',
        '//css/libs/jquery/jquery.colorbox/jquery-colorbox.css',
        '//css/style.content.css',
        '//css/libs/jquery/jquery.toastmessage/jquery.toastmessage.css',
        '//css/vendor/jquery.pnotify.css',
        '//css/vendor/bootstrap.min.css',
        '//css/vendor/bootstrap-select.min.css',
        '//' . $themeDir . '/css/custom.css',
        '//' . $themeDir . '/css/so_megamenu.css',
        '//' . $themeDir . '/css/wide-grid.css',
        '//' . $themeDir . '/css/slider_style.css',
        '//' . $themeDir . '/css/animate.css',
        '//' . $themeDir . '/css/owl.carousel.css',
        '//' . $themeDir . '/css/style.css',
        '//' . $themeDir . '/css/css3.css',
        '//' . $themeDir . '/css/blog_style.css',
        '//' . $themeDir . '/css/style_render_281.css',
        '//' . $themeDir . '/css/style_render_289.css',
        '//' . $themeDir . '/css/orange.css',
        '//' . $themeDir . '/css/header24.css',
        '//' . $themeDir . '/css/footer24.css',
        '//' . $themeDir . '/css/responsive.css',
        '//' . $themeDir . '/css/filter_style.css',
        '//js/vendor/jquery-ui-1.11.4/jquery-ui.min.css',
        '//js/vendor/jquery-ui-1.11.4/jquery-ui.structure.min.css',
        '//js/vendor/jquery-ui-1.11.4/jquery-ui.theme.min.css',
        '//' . $themeDir . '/css/main.css'
    ),
    'custom.new.css' => array(
        '//css/style/paginator.css'
    ),
    'general.js' => array(
        '//js/vendor/underscore-min.js',
        '//js/vendor/backbone-min.js',
        '//js/libs/jquery/jquery.colorbox/jquery.colorbox-min.js',
        '//js/libs/jquery/jquery.ba-bbq.js',
        '//js/vendor/jquery.alphanumeric.js',
        '//js/vendor/jquery.numeric.js',
        '//js/vendor/jquery.livequery.min.js',
        '//js/vendor/jquery.toastmessage.js',
        '//js/vendor/jquery.pnotify.js',
        '//js/vendor/bootstrap.min.js',
        '//js/vendor/jquery.jcarousel.min.js',
        '//js/vendor/bootstrap-select.min.js',
        '//js/general.js',
        '//js/common.js',
        '//' . $themeDir . '/js/general.js',
        '//js/vendor/jquery.redirect.js',
        //'//' . $themeDir . '/js/lazysizes.min.js',
        '//' . $themeDir . '/js/ot-general.js',
        '//' . $themeDir . '/js/so_megamenu.js',
        '//' . $themeDir . '/js/owl.carousel.js',
        '//' . $themeDir . '/js/custom.js',
        '//' . $themeDir . '/js/so.custom.js'
    ),
);