var Contents = new Backbone.Collection();
var ContentsPage = Backbone.View.extend(
{
    "el": ".sets_wrapper",
    "events": {
		"click .ot_sortable_list i.icon-remove": "removeSiteCategory",
		"click #all-categories li i.icon-plus": "addSiteCategory",
		"click #all-categories li i.icon-search": "openSiteCategory",
		"click button.save-menu": "saveSiteCategories",
		"click div.pagination ul li": "loadCategories"
    },
    render: function()
    {
        return this;
    },
    loadCategories: function(ev){
    	ev.preventDefault();
    	var li = $(ev.currentTarget);
    	if (undefined !== $(li).attr('page')) {
    		var page = $(li).attr('page');
    		this.loadAllCategories(page);
    	}
    	return false;
    },
    initialize: function()
    {
        this.render();
        var a = $('#activeLanguagesContainer ul.dropdown-menu li a[data-value=""]');
        var li = $(a).closest('li');
        $(li).remove();
        
        this.loadAllCategories(1);

    },
    loadAllCategories: function(page){
    	$('#all-categories-preloader').show();
		$.post('?cmd=sets&do=getAllCategories&page=' + page, {}, function (data) {
            if (data.result == 'ok') {
            	$('#all-categories-container').html(data.list);
            }
            $('#all-categories-preloader').hide();
        }, 'json');    		
    },
    removeSiteCategory: function(e)
    {
    	var li = $(e.currentTarget).closest('li');
    	var id = $(li).attr('id');
    	$(li).remove();
    	$('#all-categories li[id="' + id +'"] i.icon-plus').show();
    	return false;
    },
    addSiteCategory: function(e) 
    {
    	var a = $(e.currentTarget).parent().find('a');
    	var id = $(a).attr('id');
    	var href = $(a).attr('href');
    	var categoryName = $(a).text();
    	var title = trans.get('Go_edit_sets_products');

    	if (id == undefined) {
            return false;
    	}

    	var li = $(a).closest('li');
    	$('i.icon-plus', li).hide();
    	//$(li).hide();
    	//add to menu
    	var template = $('.menu-item-template').html();
    	$('.ot_sortable_list').append(template);
    	$('.ot_sortable_list li:last').attr('id', id);
    	$('.ot_sortable_list li:last span.title').html('<a href="' + href +'">' + categoryName + '</a>');
    	$('.ot_sortable_list li:last span.badge').attr('title', title);
    	$('.ot_sortable_list li:last span.badge').addClass('badge-success');
    	return false;
    },
    openSiteCategory: function(e)
	{
        var url = $(e.currentTarget).attr('data-url');
        window.open(url);
	},
    saveSiteCategories: function(e)
    {
    	var ids = new Array();
    	var language = $('#currentLang').data('lang');
    	$('.ot_sortable_list li:visible').each(function(){
    		ids.push($(this).attr('id'));
    	});
    	
    	$('button.save-menu').addClass('disabled');
    	$('button.save-menu').html(trans.get('Saving'));
    	//save to server
		$.post('?cmd=sets&do=saveSiteCategories&language=' + language, {"ids":ids}, function (data) {
            if (data.result == 'ok') {
            	showMessage(trans.get('Site_categories_saved'));
            	$('.ot_sortable_list li span.badge').removeClass('badge-success');
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
