$(function(){
    $('.delete-from-item-set').live('click', function(){
        var m_this = this;	
		IDParent = '';
		CatCount = '999';
		NoneCpec = '';
		if (hierarchy.length>0) {
			var HactiveCategory =  activeCategory.replace(/\ /g, "_");
		    	HactiveCategory =  HactiveCategory.replace(/\&/g, "-and-");				
			var CatCount = $('.'+HactiveCategory).attr('CatCount');	
			var IDParent = $('.'+HactiveCategory).attr('idparent').replace(/\_/g, " ");
			NoneCpec = $(m_this).attr('categoryId').replace(/\&/g, "-and-");			
		}
        $.ajax({
            url: 'index.php',
            data: {
                cmd: 'Set2',
                'do': 'deleteFromRatingList',
                'itemList': $(m_this).attr('itemId'),
                'itemRatingTypeId': $(m_this).attr('itemRatingTypeId'),
                'contentType': $(m_this).attr('contentType'),
                'categoryId': NoneCpec,
				'PcatId': IDParent,
				'CatCount': CatCount
            },
            beforeSend: function(){
                $(m_this).closest('li').empty().html('<p id="loader"><img src="css/i/loading.gif"></p>');
            }
        })
            .success(function(data){
                location.reload();
				//alert(data);

            })
            .error(function(xhr, ajaxOptions, thrownError){
                if(thrownError == 'SessionExpired'){
                    window.location = 'index.php?cmd=login';
                }
                else{
                    show_error(thrownError + '<br />' + xhr.responseText);
                }
				alert(xhr.responseText);
                location.reload();
				//alert('WOW Error');

            });
        return false;
    });
});