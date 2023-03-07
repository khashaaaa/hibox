$(function(){
    $('.delete-from-item-set').live('click', function(){
        var m_this = this;
        $.ajax({
            url: 'index.php',
            data: {
                cmd: 'Set2',
                'do': 'deleteFromRatingList',
                'itemList': $(m_this).attr('itemId'),
                'itemRatingTypeId': $(m_this).attr('itemRatingTypeId'),
                'contentType': $(m_this).attr('contentType'),
                'categoryId': $(m_this).attr('categoryId')
            },
            beforeSend: function(){
                $(m_this).closest('li').empty().html('<p id="loader"><img src="css/i/loading.gif"></p>');
            }
        })
            .success(function(data){
                var idx = $("#tabs").tabs('option', 'selected');
                $("#tabs").tabs('load', idx);
            })
            .error(function(xhr, ajaxOptions, thrownError){
                if(thrownError == 'SessionExpired'){
                    window.location = 'index.php?cmd=login';
                }
                else{
                    show_error(thrownError + '<br />' + xhr.responseText);
                }
                var idx = $("#tabs").tabs('option', 'selected');
                $("#tabs").tabs('load', idx);
            });
        return false;
    });
});