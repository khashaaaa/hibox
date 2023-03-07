var Brands = new Backbone.Collection();
var BrandsPage = Backbone.View.extend({
    "el": ".brands_wrapper",
    "events": {
    	"click #save-order": "saveItemsOrder",
    	"click .ot_sortable_brands li .ot_show_deletion_dialog_modal": "deleteBrand",
    	"click .ot_sortable_brands li .ot-hide-brand": "hideBrand",
    	"click .ot_sortable_brands li .ot-show-brand": "showBrand",
    	"click a.ot-edit-brand": "editBrand",
    	"click #save-brand-btn": "saveBrand",
    	"click #save-brand-add-btn": "saveBrandAdd",
    	"click #add-from-list-submit": "addFromList",
    },
    addFromList: function(e) {
    	var form = $(e.currentTarget).closest('form');
    	var ids = [];
    	$('input[type="checkbox"]:checked').each(function() {
    		var id = $(this).val();
    		ids.push(id);
    	});
    	if (ids.length > 0) {
    		var ids = ids.join(';');
    		
    		$('#add-from-list-submit', form).button('loading');
    		$('.cancel-btn', form).addClass('disabled');
    		
            $.post(
                    "?cmd=brands&do=addBrands",
                    {
                    "ids": ids,
                    },
                    function (data) {
                 	   if (data.result == 'ok') {
                 		   var brands = data.brands;
                 		   for ( var i in brands) {
                 			   var brand = brands[i];
                 			   //create brand item
                 			   $('.ot_sortable_brands').append($('.ot_sortable_brands .ot-brand-template').html());
                 			   $('.ot_sortable_brands li:last').attr('id', brand.id);
                 			   $('.ot_sortable_brands li:last img').attr('src', brand.PictureUrl);
                 			   $('.ot_sortable_brands li:last img').attr('alt', escapeData(brand.name));
                 			   $('.ot_sortable_brands li:last h3').html(escapeData(brand.name));
                 			   $('.ot_sortable_brands li:last').removeClass('ot-brand-template');
                 			   $('.ot_sortable_brands li:last').show();
                 			   $('.ot_sortable_brands li:last .ot-show-brand').hide();
                 		   }
                 		   form.find('input[type=checkbox]').prop('checked', false);
                 	   } else {
                 	   }
                 	   $('#add-from-list-submit', form).button('reset');
                  	   $('.cancel-btn', form).removeClass('disabled');

                    }, 'json'
             );

    		return true;
    	} else {
    		showError(trans.get('brands::Brands_not_selected'));
    		return false;
    	}
    },
    saveBrand: function(e)
    {
    	var form = $(e.currentTarget).closest('form');
    	var description = tinyMCE.editors[0].getContent(); 
    	$('#brand-description', form).val(description);
    	
    	$('#save-brand-btn').button('loading');
    	$('#save-brand-add-btn').button('toggle');
    	$('#cancel-btn').addClass('toggle');
    	
    	$(form).ajaxSubmit({
            url     :   $(this).attr('action'),
            type    :   'POST',
            dataType:   'json',
            success :   function(data) {
            	if (data && data.result && data.result == 'ok') {
            		showMessage(trans.get('brands::Brand_saved_successfully'));
            		document.location.href = 'index.php?cmd=brands&do=default';
            	} else {
            		showError(data);
            	}
            	$('#save-brand-btn').button('reset');
            	$('#save-brand-add-btn').button('toggle');
            	$('#cancel-btn').addClass('toggle');
             }
        });
    	return false;
    },
    saveBrandAdd: function (e)
    {
    	var form = $(e.currentTarget).closest('form');
    	var description = tinyMCE.editors[0].getContent(); 
    	$('#brand-description', form).val(description);
    	
    	$('#save-brand-btn').button('toggle');
    	$('#save-brand-add-btn').button('loading');
    	$('#cancel-btn').button('toggle');

    	
    	$(form).ajaxSubmit({
            url     :   $(this).attr('action'),
            type    :   'POST',
            dataType:   'json',
            success :   function(data) {
            	if (data && data.result && data.result == 'ok') {
            		showMessage(trans.get('brands::Brand_saved_successfully'));
            		$('img', form).remove();
            		$('input', form).val('');
            		$('textarea', form).val('');
            		$('textarea', form).html('');
            		tinyMCE.editors[0].setContent('');
            		$('#id', form).val(0);
            	} else {
            		showError(data);
            	}
            	$('#save-brand-btn').button('toggle');
            	$('#save-brand-add-btn').button('reset');
            	$('#cancel-btn').button('toggle');
             }
        });
    	return false;
    },
    hideBrand: function(e) 
    {
        var li = $(e.currentTarget).closest('li');
        var id = $(li).attr('id');
        
        $('.ot-preloader', li).show();
        $('.ot-hide-brand', li).hide();
        $('.ot-show-brand', li).hide();
        
        $.post(
               "?cmd=brands&do=hideBrand",
               {
               "id": id,
               },
               function (data) {
            	   if (data.result == 'ok') {
	            	   $(li).addClass('disabled_item');
	            	   $('.ot-hide-brand', li).hide();
	            	   $('.ot-show-brand', li).show();
            	   } else {
            	        $('.ot-hide-brand', li).show();
            	        $('.ot-show-brand', li).hide();
            	   }
            	   $('.ot-preloader', li).hide();
               }, 'json'
        );
    },
    showBrand: function(e) 
    {
        var li = $(e.currentTarget).closest('li');
        var id = $(li).attr('id');
        
        $('.ot-preloader', li).show();
        $('.ot-hide-brand', li).hide();
        $('.ot-show-brand', li).hide();
        $.post(
               "?cmd=brands&do=showBrand",
               {
               "id": id,
               },
               function (data) {
            	   if (data.result == 'ok') {
	            	   $(li).removeClass('disabled_item');
	            	   $('.ot-hide-brand', li).show();
	            	   $('.ot-show-brand', li).hide();
            	   } else {
	            	   $('.ot-hide-brand', li).hide();
	            	   $('.ot-show-brand', li).show();
            	   }
            	   $('.ot-preloader', li).hide();
               }, 'json'
        );
    },
    editBrand: function(e)
    {
        var li = $(e.currentTarget).closest('li');
        var id = $(li).attr('id');
    	document.location.href = 'index.php?cmd=brands&do=editBrand&id='+id;
    	return false;
    },
    render: function()
    {
        return this;
    },
    initialize: function() 
    {
        var self = this;
        this.render();

		initializeTinyMCE('#brand-description');
        
        $('#external-id').typeahead({
        	source: function (query, process) 
        	{
        		$('#external-id').addClass('preloader');
        		return $.get('?cmd=brands&do=searchBrands&name='+query, {}, function (response) {
        			var data = new Array();
        			$.each(response.options, function(i, item)
        			{
        				data.push(item.id + '|' + item.name + '|' + item.externalid + '|' + item.pictureurl + '|' + item.description + '|' + item.providerIconImage);
                    });	
        			$('#external-id').removeClass('preloader');
        			return process(data);
        		}, 'json');
        	},
        	//output in list
        	highlighter: function(item) 
        	{
        		$('#external-id').removeClass('preloader');
        		var parts = item.split('|');
				let brandName = parts[0] === parts[1] ? parts[0] :  parts[0] + ' (' + parts[1] + ')';

        		return '<img src="' + parts[5] + '">' + ' ' + brandName;
        	},
            //select in list
        	updater: function(item) 
        	{
        		var parts = item.split('|');
        		$('#external-id').val(parts[0]);
				$('#brand-name').val(parts[1]);
                $('.thumbnail-placeholder img').attr('src', parts[3]);
                $('#old-image').val(parts[3]);
                return parts[0];
        	},         	        	
        });
        
        $('#brand-name').typeahead({
        	source: function (query, process) 
        	{
        		$('#brand-name').addClass('preloader');
//        		$('div.ot-preload-hide').css('visibility','hidden');
        		return $.get('?cmd=brands&do=searchBrands&name='+query, {}, function (response) {
        			var data = new Array();
        			$.each(response.options, function(i, item)
        			{
        				data.push(item.id + '|' + item.name + '|' + item.externalid + '|' + item.pictureurl + '|' + item.description);
                    });	
        			$('#brand-name').removeClass('preloader');
        			return process(data);
        		}, 'json');
        	},
        	//output in list
        	highlighter: function(item) 
        	{
        		$('#brand-name').removeClass('preloader');
//        		$('div.ot-preload-hide').css('visibility','visible');
        		var parts = item.split('|');
        		return parts[1];
        	},
            //select in list
        	updater: function(item) 
        	{
        		var parts = item.split('|');
                $('#external-id').val(parts[0]);
                $('#old-image').val(parts[3]);
                $('.thumbnail-placeholder img').attr('src', parts[3]);
                return parts[1];
        	},         	        	
        });

        $('#display-name').change(function(){
        	var seoTitle = $('#pagetitle').val();
        	if (seoTitle.length == 0) {
        		$('#pagetitle').val($('#display-name').val());
        	} 
        });
    },
    deleteBrand: function(e)
    {
        var li = $(e.currentTarget).closest('li');
        var id = $(li).attr('id');
        
        var onConfirmCallback = function() {
            $.post(
                    "?cmd=brands&do=deleteBrand",
                    {
                        "id": id,
                    },
                    function (data) {
                    	if (data.result == 'ok') {
                    		$(li).remove();
                    	}

                    }, 'json'
                );
            
        }; 
        modalDialog(trans.get('Confirm_needed'), trans.get('brands::Really_delete_this_brand'), onConfirmCallback, {'confirm':trans.get('Delete'), 'cancel': trans.get('Cancel')});
    },
});

$(function() {
    var U = new BrandsPage();
});
