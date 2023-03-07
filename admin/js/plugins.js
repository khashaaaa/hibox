// Avoid `console` errors in browsers that lack a console.
(function() {
    var method;
    var noop = function () {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());

// scrolling event
$(window).scroll(function() {

    // this for hide/show button to-top
    if($(this).scrollTop() > 200) {
        $('a[rel=go_to_top]').fadeIn('slow');
    } else {
        $('a[rel=go_to_top]').fadeOut('slow');
    }

});

// scroll to top
$('a[rel=go_to_top]').click(function(e) {
    e.preventDefault();
    $('body,html').animate({
        scrollTop:0
    }, 'slow');
});



$(document).ready( function () {

    // fixing sub nav to the top
    /*$('.ot_sub_nav').waypoint('sticky', {
        wrapper: '<div class="sticky_ot_sub_nav" />',
        stuckClass: 'stuck',
        offset: 75 // Apply "stuck" when element 75px from top
    });*/

    /* tabdrop */
    $('.ot_sub_nav .nav-tabs').tabdrop()

    /* fix tables header to the top  */
    var tableElement = $('#data_table, #data_table_sorting');
    if(tableElement.length){
        //var oTable = tableElement.dataTable();
    }

    if(typeof oTable !== 'undefined'){
        new FixedHeader( oTable, {
            "offsetTop": 120
        } );
    }

} );

//bootstrap-editable init
$.fn.editable.defaults.mode = 'inline'; //turn to inline mode
$(document).ready( function () {

    $('.ot_inline_text_editable').editable({
        emptytext: trans.get('Not_filled')
    });

    $('.ot_inline_select_editable').editable({
        type: 'select',
        value: 1,
        source: [
            {value: 1, text: trans.get('Show')},
            {value: 2, text: trans.get('Hide')},
        ]
    });

    $('.ot_inline_textarea_editable').editable({
        url: '/post',
        title: 'Enter comments',
        rows: 5,
        emptytext: trans.get('Not_filled')
    });

    $(function(){
        $('.ot_inline_date_editable').editable({
                format: 'yyyy-mm-dd',
                viewformat: 'dd/mm/yyyy',
                emptytext: trans.get('Not_set'),
                datepicker: {
                    weekStart: 1
                }
            }
        );
    });

    $(function(){
        $('.ot_inline_checklist_editable').editable({
            emptytext: trans.get('Not_set'),
            source: [
                {value: 1, text: '1 ' + trans.get('level')},
                {value: 2, text: '2 ' + trans.get('level')},
                {value: 3, text: '3 ' + trans.get('level')}
            ]
        });
    });

    /* adding user to discount list */
    $(function(){
        /*$.fn.editableform.buttons =
            '<button class="btn btn-mini btn-primary editable-submit" type="submit"><i class="icon-ok icon-white"></i></button>'+
            '<button class="btn btn-mini editable-cancel" type="button"><i class="icon-remove"></i></button>';
*/
        $('.ot-typehead-users').editable({
            mode: 'popup',
            title: trans.get('Start_typing_login') + '...',
            placeholder: trans.get('Start_typing_login'),
//            clear: true,
            emptytext : '',
            inputclass: 'input-medium',
            source: [
                {value: 'gb', text: 'VasiaPupkin'},
                {value: 'us', text: 'NikitaGigurda'},
                {value: 'ru', text: 'qweqwe-385'},
                {value: 'cn', text: 'ChuckNorris'},
                {value: 'em', text: 'EdwardMurphy'}
            ]
        });

        var sortableContainers = document.getElementsByClassName('ot_sortable');
        Object.keys(sortableContainers).forEach(function(sortableContainerNum){
            var sortableContainer = sortableContainers[sortableContainerNum];
            sortableContainerNum = new Sortable.create(sortableContainer, {
                handle: 'i.icon-move',
                animation: 150
            });
        });

        var sortableWithDropContainers = document.getElementsByClassName('ot_sort_n_drop');
        Object.keys(sortableWithDropContainers).forEach(function(sortableWithDropContainerNum){
            var sortableWithDropContainer = sortableWithDropContainers[sortableWithDropContainerNum];
            sortableWithDropContainerNum = new Sortable.create(sortableWithDropContainer, {
                handle: 'i.icon-move',
                animation: 150
            });
        });
    });
});

/* select2 */
var preload_data = [
    { id: 'ru', text: 'Русский (Russian)', locked: true},
    { id: 'en', text: 'English (English)'},
    { id: 'mn', text: 'Монгол хэл (Mongolian)'},
    { id: 'cn', text: '中国的 (Chinese)'},
    { id: 'es', text: 'Español (Spanish)'},
    { id: 'de', text: 'Deutsch (German)' },
    { id: 'pt', text: 'Português (Portuguese)' },
    { id: 'bg', text: 'Български (Bulgarian)' },
    { id: 'il', text: 'עברית (Hebrew)'},
    { id: 'am', text: 'հայերէն (Armenian)' },
    { id: 'saha', text: 'Саха тыла (Yakut)' },
    { id: 'pl', text: 'Jezyk polski, polszczyzna (Polish)' },
    { id: 'ge', text: 'ქართული ენა (Georgian)' },
];

$(document).ready(function () {
    $('#showcase_languages').select2({
        multiple: true,
        placeholder: 'Select a State',
        query: function (query){
            var data = {results: []};

            $.each(preload_data, function(){
                if(query.term.length == 0 || this.text.toUpperCase().indexOf(query.term.toUpperCase()) >= 0 ){
                    data.results.push({id: this.id, text: this.text });
                }
            });

            query.callback(data);
        }
    });
    $('#showcase_languages').select2('data', preload_data )
});

//$(document).ready(function() { $(".ot_form select").select2(); }); // Это бы для внешнего вида только, без поиска
