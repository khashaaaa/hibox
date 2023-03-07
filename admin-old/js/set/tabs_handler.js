function showLoader(){
    $('.ui-tabs-panel:visible').html('<p id="loader"><img src="css/i/loading.gif"></p>');
}

function refreshTab(){
    var idx = $("#tabs").tabs('option', 'selected');
    $("#tabs").tabs('load', idx);
}

$(function() {
    var sets = [
        {
            contentType: 'Item',
            itemRatingTypeId: 'Best',
            categoryId: 0
        },
        {
            contentType: 'Item',
            itemRatingTypeId: 'Popular',
            categoryId: 0
        },
        {
            contentType: 'Item',
            itemRatingTypeId: 'Last',
            categoryId: 0
        }
    ];

    $('.apply-category').live('click', function(){
        $('.apply-category').css('font-weight', 'normal');
        $(this).css('font-weight', 'bold');

        var tabIdx = $('#tabs').tabs('option', 'active');
        sets[tabIdx].categoryId = $(this).attr('categoryId');
        $('[setId="'+sets[tabIdx].contentType+tabIdx+'"]').attr('href',
            'index.php?cmd=Set2&do=show'+sets[tabIdx].contentType+'Set&type='+sets[tabIdx].itemRatingTypeId
                +'&contentType='+sets[tabIdx].contentType+'&categoryId='+encodeURI(sets[tabIdx].categoryId));

        refreshTab();
    });

    $( "#tabs" ).tabs({
        beforeLoad: function( event, ui ) {
            ui.panel.html('<p id="loader"><img src="css/i/loading.gif"></p>');
            ui.jqXHR.error(function(xhr, ajaxOptions, thrownError) {
                if(thrownError == 'SessionExpired'){
                    window.location = 'index.php?cmd=login';
                }
                else{
					if(thrownError != 'abort')                    
                    	show_error(thrownError + '<br />' + xhr.responseText);
                }
            });
        }
        ,load: function(event, ui){
            $("#loader").remove();
        }
        ,create: function(event, ui){
            ui.panel.html('<p id="loader"><img src="css/i/loading.gif"></p>');
        }
    }).addClass( "ui-tabs-vertical ui-helper-clearfix" );
    $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
});