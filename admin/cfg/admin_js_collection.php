<?php
/*
 * global vars:
 * $lang
 * $version
 */

$sources[] = '/js/libs/jquery/jquery.cookie.js';

$sources[] = '/js/libs/jquery/jquery.ba-bbq.js';
$sources[] = '/admin/js/vendor/jquery.treeview.js';
$sources[] = '/admin/js/vendor/jquery.treeview.edit.js';
$sources[] = '/admin/js/vendor/jquery.treeview.async.js';
$sources[] = '/js/vendor/bootstrap-tagsinput.min.js';

//<!-- bootstrap -->
//$sources[] = 'js/vendor/bootstrap.min.js';
//<!-- Polifills -->
$sources[] = '/admin/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js';
//<!-- fixing elements to the top when scroling content -->
$sources[] = '/admin/js/vendor/portamento.js';
//<!-- fixing elements to the top when scroling content -->
$sources[] = '/admin/js/vendor/waypoints.min.js';
$sources[] = '/admin/js/vendor/waypoints-sticky.min.js';
//<!-- datatables plugin — powerfull extention for tables -->
$sources[] = '/admin/js/vendor/DataTables/js/jquery.dataTables.js';
$sources[] = '/admin/js/vendor/DataTables/js/plugins/date-de.js';
$sources[] = '/admin/js/vendor/dataTables-bootstrap.js';
$sources[] = '/admin/js/vendor/FixedHeader.js';
//<!-- Making long tabs into dropdown one -->
$sources[] = '/admin/js/vendor/bootstrap-tabdrop.js';
//<!-- inline-editable fields -->
$sources[] = '/admin/js/vendor/bootstrap-editable.js';
$sources[] = '/admin/js/vendor/moment.min.js';
//<!-- clickable tooltips -->
$sources[] = '/admin/js/vendor/bootstrapx-clickover.js';
$sources[] = '/admin/js/vendor/jQuery-File-Upload-master/js/vendor/jquery.ui.widget.js';
//<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
$sources[] = '/admin/js/vendor/jQuery-File-Upload-master/js/jquery.iframe-transport.js';
//<!-- The basic File Upload plugin -->
$sources[] = '/admin/js/vendor/jQuery-File-Upload-master/js/jquery.fileupload.js';
//<!-- The File Upload file processing plugin -->
$sources[] = '/admin/js/vendor/jQuery-File-Upload-master/js/jquery.fileupload-fp.js';
//<!-- The File Upload user interface plugin -->
$sources[] = '/admin/js/vendor/jQuery-File-Upload-master/js/jquery.fileupload-ui.js';
//<!-- The main application script -->
$sources[] = '/admin/js/vendor/jQuery-File-Upload-master/js/main.js';

//<!--  replacement for selects -->
$sources[] = '/admin/js/vendor/select2.js';

if ($lang == 'ru') {
    $sources[] = '/admin/js/vendor/select2_locale_ru.js';
}

//<!-- datepicker -->
$sources[] = '/admin/js/vendor/bootstrap-datepicker.js';
$sources[] = '/admin/js/vendor/bootstrap-datepicker.ru.js';

//<!-- lightbox -->
$sources[] = '/admin/js/vendor/bootstrap-lightbox.js';

//<!--  files uploads -->
$sources[] = '/admin/js/vendor/bootstrap-fileupload.js';

//<!-- new look of default selects -->
$sources[] = '/admin/js/vendor/bootstrap-select.min.js';

//<!-- bootstrap-image-gallery -->
$sources[] = '/admin/js/vendor/load-image.js';
$sources[] = '/admin/js/vendor/bootstrap-image-gallery.min.js';

//<!-- bootstrap-dropdown plugin -->
$sources[] = '/admin/js/vendor/bootstrap-dropdown-ext.js';

//<!-- OT custom -->
$sources[] = '/admin/js/plugins.js';
$sources[] = '/admin/js/ot-app.js';
$sources[] = '/admin/js/ot-common.js';
$sources[] = '/admin/js/ot-topmenu.js';

//<!-- js system notifications -->
$sources[] = '/admin/js/vendor/jquery.pnotify.js';

//<!-- improve preloading plugin -->
$sources[] = '/admin/js/vendor/spin.min.js';
$sources[] = '/admin/js/vendor/ladda.min.js';

$sources[] = '/js/vendor/jquery.alphanumeric.js';
$sources[] = '/js/vendor/jquery.livequery.min.js';