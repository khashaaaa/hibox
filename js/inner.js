"use strict";

$(function(){

	var $cat = $('.category');
	var $drop = $('.uluserbar').find('.dropdown').not('.dropdown-search');
	var $searchDropdown = $('.field .dropdown');
    
    $('.showHideFilter').on('click',function(){
		var $OpenCloseFilter = $(this),
            $parentBlock = $OpenCloseFilter.parent(),
            $icons = $('.i-new', $OpenCloseFilter),
            $icons_type_open = $OpenCloseFilter.data('type-open'),
            $icons_type_close =  $OpenCloseFilter.data('type-close');    
    
    
        if($parentBlock.hasClass('closed')){
            $parentBlock.addClass('opened');
            $parentBlock.removeClass('closed');
            $icons.removeClass('' + $icons_type_close);            
            if (name == 'filter-subtitle') {
                document.cookie = "ftitle = opened";
            }
            
        } else {
            $parentBlock.removeClass('opened');
            $parentBlock.addClass('closed');
            $icons.addClass('' + $icons_type_close);
            if(name == 'filter-subtitle') {
                document.cookie = "ftitle = closed";
            }
        }
	});
    
	$cat.on('click',function(e){
		$(document).unbind('click.myEvent');
		if($searchDropdown+':visible'){ $searchDropdown.hide(); }
		e.preventDefault();
                if($(this).hasClass('active'))
                    $(this).removeClass('active');
                else
                    $(this).addClass('active');
		$drop.toggle();
        var firstClick = true;
        $(document).bind('click.myEvent', function(e) {
            if (!firstClick && $(e.target).closest('.selection-search').length == 0) {
                $drop.hide();
				 $cat.removeClass('active');
                $(document).unbind('click.myEvent');
            }
            firstClick = false;
        });		
	})
	
	$drop.click(function(e){
        e.stopPropagation();
    })

	var $tabs = $('#product-tabs li');

	$tabs.click(function(e){
		var $tabs_active = $('.tabs li.active');
		var $blockContent = $('.tabs li.active').attr('tab');
		$blockContent = '#' + $blockContent;
		e.preventDefault();
		if(!$(this).hasClass('active')){
			$($blockContent).hide();
			$blockContent = $(this).attr('tab');
			$('#'+$blockContent).show();
			$tabs_active.removeClass('active');
			$(this).addClass('active');
		}
	})
	
    
});


$(function(){

	var $tabs = $('.tabs li');

	$tabs.click(function(e){
                if($(this).parent().hasClass('tabs1')){
                    return true;
                }
		var $tabs_active = $('.tabs li.active');
		var $blockContent = $('.tabs li.active').attr('tab');
		$blockContent = '#' + $blockContent;
		e.preventDefault();
		if(!$(this).hasClass('active')){
			$($blockContent).hide();
			$blockContent = $(this).attr('tab');
			$('#'+$blockContent).show();
			$tabs_active.removeClass('active');
			$(this).addClass('active');
			
		}
	})
});
