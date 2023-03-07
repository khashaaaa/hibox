var Items = new Backbone.Collection();
var ItemsPage = Backbone.View.extend({
    "el": ".item_wrapper",
    "events": {
    	"click #edit-item-btn": "editItem",
    	"click .save-item-btn": "saveItem",
        "click .delete-existing-image": "delImage",
        "click #removeFromSelector": "removeFromSelector",
        "click .reset-item-btn": "resetItem",
        "change input": "changeInputValue"
    },
    changeInputValue: function(e)
    {
    	var ctrlGroup = $(e.currentTarget).closest('.control-group');
    	var btn = $('button.btn-primary', ctrlGroup);
    	$(btn).removeClass('btn-primary');
    	$(btn).addClass('btn-warning');
    },
    resetItem: function(e) 
    {
    	var self = this;
    	var field = $(e.currentTarget).data('field');
    	var form = $(e.currentTarget).closest('form');
    	
    	modalDialog(trans.get('Confirm_needed'), trans.get('Really_reset_this_item_field_value'), function() {
	    	$(e.currentTarget).button('toggle');
	    	$(e.currentTarget).button('loading');
	    	$(e.currentTarget).button('toggle');
	    	$('.save-item-btn').button('toggle');
	    	$('.reset-item-btn').button('toggle');
	    	
	        $.ajax({
	            async : true,
	            type: 'POST',
	            dataType: 'json',
	            url: "?cmd=Items&do=resetItem",
	            data : {
	                "id" : $('#id').val(),
	                "field" : field,
	                "language" : $('#currentLang').val()
	            },
	            success : function (data) {
	            	if (data.resetValue) {
	            		if (field == 'description') {
	            			tinyMCE.activeEditor.setContent(data.resetValue);
	            		} else {
	            			$('#' + field).val(data.resetValue);            			
	            		}
	            	}
	            	$(e.currentTarget).button('toggle');
	            	$(e.currentTarget).button('reset');
	            	$(e.currentTarget).button('toggle');
	            	$('.save-item-btn').button('toggle');
	            	$('.reset-item-btn').button('toggle');
	            	$('.item-title-edited-flag').hide();
	            },
	            error: function () {
	            	$(e.currentTarget).button('toggle');
	            	$(e.currentTarget).button('reset');
	            	$(e.currentTarget).button('toggle');
	            	$('.save-item-btn').button('toggle');
	            	$('.reset-item-btn').button('toggle');
	            }
	        }); 
    	});
    	
    },
    editItem: function(e)
    {
    	var form = $(e.currentTarget).closest('form');
    	var id = $('#item-id', form).val();
    	if (id == "") {
            showError(trans.get('Value_must_not_be_empty'));
            return;
        }
    	$('#edit-item-btn').button('loading');
    	$('#edit-item-btn').button('toggle');
    	$('#edit-item-btn').addClass('toggle');
    	
    	$(form).ajaxSubmit({
            type    :   'POST',
            dataType:   'json',
            success :   function(data) {
            	if (data && data.result && data.result == 'ok') {
            		window.open(
            			'index.php?cmd=items&do=edit&id=' + data.id,
						'_blank'
					);
            	} else {
            		showError(data);
            	}
            	$('#edit-item-btn').button('reset');
            	$('#edit-item-btn').button('toggle');
            	$('#edit-item-btn').addClass('toggle');
            },
            error: function(data) {
                $('#edit-item-btn').button('reset');
                $('#edit-item-btn').button('toggle');
                $('#edit-item-btn').addClass('toggle');
            }
        });
    	return false;
    },
    saveItem: function (e)
    {
    	var field = $(e.currentTarget).data('field');
    	var form = $(e.currentTarget).closest('form');
    	$('#field', form).val(field);
    	var description = tinyMCE.editors[0].getContent(); 
    	$('#description', form).val(description);
    	var title = $('#title', form).val();

    	var ctrlGroup = $(e.currentTarget).closest('.control-group');
    	var btn = $('button.save-item-btn', ctrlGroup);
    	
    	$(e.currentTarget).button('toggle');
    	$(e.currentTarget).button('loading');
    	$(e.currentTarget).button('toggle');
    	
    	$('.save-item-btn').button('toggle');
    	
    	$(form).ajaxSubmit({
            url     :   $(this).attr('action'),
            type    :   'POST',
            dataType:   'json',
            success :   function(data) {
            	if (data && data.result && data.result == 'ok') {
            		showMessage(trans.get('Item_saved_successfully'));
            	} else {
            		showError(data);
            	}
            	$(e.currentTarget).button('toggle');
            	$(e.currentTarget).button('reset');
            	$(e.currentTarget).button('toggle');
            	$('.save-item-btn').button('toggle');
            	
            	$(btn).removeClass('btn-warning');
            	$(btn).addClass('btn-primary');
            },
            error: function(data) {
            	$(e.currentTarget).button('toggle');
            	$(e.currentTarget).button('reset');
            	$(e.currentTarget).button('toggle');
            	$('.save-item-btn').button('toggle');
            }
        });
    	return false;
    },
    initialize: function()
    {
        var self = this;
        this.render();

        initializeTinyMCE('#description', {
        	height: 400,
			width: 610,
			setup: function (ed) {
        		ed.on('change', function (e) {
        			e.add(function (ed, el) {
						$('button[name="submit-description"]').removeClass('btn-primary');
						$('button[name="submit-description"]').addClass('btn-warning');
					});
				});
			}
		});
        $('.ot_inline_help').clickover();
        
        $('#ot_filters_rename_accordion .ot_inline_editable').editable().on('shown', function() {
            var a = $(this).closest('a');
            var form = ('.editableform',a);
            var submitFunction = function(event) {
                form.submit();
                event.stopPropagation();
            };
            $('.btn-primary',form).unbind('click').unbind('submit');
            $('.btn-primary',form).click(submitFunction);
        });
    },
    delImage: function() {
        console.log($('#originalPicture').val());
        $('#existing_image').val('del');
        $('.thumbnail-placeholder img').attr('src', $('#originalPicture').val());
    },
    removeFromSelector: function() {
        var el = $('#removeFromSelector');
        var type = $(el).data('type');
        var id = $(el).data('id');
        var cid = $(el).data('cid');

        var url = '/admin/?cmd=items&do=edit&id=' + id;

        if(! cid) {
            cid = 0;
        }

        modalDialog(trans.get('Confirm_needed'), trans.get('Really_delete_this_item_from_sets'), function() {
            $.post(
                "?cmd=Sets&do=deleteItem",
                {
                    "id": id,
                    "contentType": "Item",
                    "type": type,
                    "cid": cid
                },
                function (data) {
                    if ( !data.error) {
                        showMessage(trans.get("Item_deleted"));
                        $('#selectorActions').remove();
                        location.href = url;
                    } else {
                        showError(data.message);
                    }
                }, 'json'
            );

        });

        return false;
    }
});

$(function() {
    var U = new ItemsPage();
});
