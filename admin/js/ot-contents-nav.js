var Contents = new Backbone.Collection();
var ContentsPage = Backbone.View.extend(
{
    "el": ".contents-wrapper",
    "events": {
		"click .ot_sortable_list i.icon-remove": "removePageFromMenu",
		"click button.add-page-to-menu": "addPageToMenu",
		"click button.save-menu": "saveMenu",
    },
    render: function()
    {
        return this;
    },
    initialize: function()
    {
        this.render();
        var a = $('#activeLanguagesContainer ul.dropdown-menu li a[data-value=""]');
        var li = $(a).closest('li');
        $(li).remove();

    },
    removePageFromMenu: function(e)
    {
    	var section = $(e.currentTarget).closest('.tab-pane');
    	var li = $(e.currentTarget).closest('li');
    	var id = $(li).attr('id');
    	var title = $('span.badge',li).attr('title');
    	$(li).remove();
    	$('select.select_searched_list', section).append('<option id="' + id + '" value="' + id +'">'+title+'</option>');
    	return false;
    },
    addPageToMenu: function(e) 
    {
    	var section = $(e.currentTarget).closest('.tab-pane');
    	var id = $('.select_searched_list option:selected', section).attr('id');
    	var title = $('.select_searched_list option:selected', section).text();
    	if (id == undefined) {
    		return false;
    	}
    		
    	$('.select_searched_list option:selected', section).remove();
    	$('.select_searched_list option:first', section).select(); 
    	//add to menu
    	var template = $('.menu-item-template').html();
    	$('.ot_sortable_list', section).append(template);
    	$('.ot_sortable_list li:last', section)
    	$('.ot_sortable_list li:last', section).attr('id', id);
    	$('.ot_sortable_list li:last span.title', section).html(title);
    	$('.ot_sortable_list li:last span.badge', section).attr('title', title);
    	$('.ot_sortable_list li:last span.badge', section).addClass('badge-success');
    	$(".select_searched_list", section).trigger('change.select2');
    	return false;
    },
    saveMenu: function(e)
    {
    	var section = $(e.currentTarget).closest('.tab-pane');
    	var menuType = $(section).attr('type');
    	var ids = new Array();
    	$('.ot_sortable_list li:visible', section).each(function(){
    		ids.push($(this).attr('id'));
    	});
    	
    	$('button.save-menu').addClass('disabled');
    	$('button.save-menu').html(trans.get('Saving'));
    	//save to server
		$.post('?cmd=contents&do=saveMenu', {"ids":ids, 'type' : menuType}, function (data) {
            if (data.result == 'ok') {
            	showMessage(trans.get('contents::Menu_saved_successfully'));
            	$('.ot_sortable_list li span.badge', section).removeClass('badge-success');
            }
            $('button.save-menu').removeClass('disabled');
            $('button.save-menu').html(trans.get('Save'));
        }, 'json');    		
    	
    	
    	return false;
    }
});

$(function()
{
    var P = new ContentsPage();
});
