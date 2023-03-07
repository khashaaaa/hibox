$(function(){
	var $all = $('.allplace');
    var $searchDropdown = $('.field .dropdown');
	var $drop = $('.uluserbar').find('.dropdown').not('.dropdown-search');
	var $cat = $('.category');
    $all.on('click', function(e){
		if($drop+':visible'){ $cat.addClass('active'); $drop.show(); }
    })
})