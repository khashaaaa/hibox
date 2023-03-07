var Reviews = Backbone.View.extend({
    "el": ".reviews_wrapper",
    "events": {
    	"click .accept-item-btn": "acceptItem",
    	"click .ot_show_deletion_dialog_modal": "deleteItem", 
        "click .accept-items-btn": "acceptItems",
        "click .delete-items-btn": "deleteItems",
        "change input.row-checkbox": "toggleRow",
        "change input.rows-checkbox": "toggleRows",
        "click .ot-show-review": "showReviewInfo",
        "submit .add-message-form": "addMessage",
        "click #enrollMoneyBtn": "rewardItemReview"
    },
    toggleRows: function(e){
        var self = this.$(e.target);
        self.parents('thead').next().find('input[type=checkbox]').prop('checked', self.is(':checked'));
    	if ($(e.currentTarget).is(':checked')) {
    		$('tbody tr').addClass('selected_item');
    	} else {
    		$('tbody tr').removeClass('selected_item');
    	}

        var count = $('.table-reviews').find('input[type=checkbox]:checked').length;
        if ( count > 0) {
    		$('.delete-items-btn').removeClass('active');
    		$('.accept-items-btn').removeClass('active');
    	} else {
    		$('.delete-items-btn').addClass('active');
    		$('.accept-items-btn').addClass('active');
    	}
    },	
    toggleRow: function(e)
    {
    	var tr = $(e.currentTarget).closest('tr');
    	if ($(e.currentTarget).is(':checked')) {
    		$(tr).addClass('selected_item');
    	} else {
    		$(tr).removeClass('selected_item');
    	}
    	var count = $('.table-reviews').find('input[type=checkbox]:checked').length;
    	if ( count > 0 ) {
    		$('.delete-items-btn').removeClass('active');
    		$('.accept-items-btn').removeClass('active');
    	} else {
    		$('.delete-items-btn').addClass('active');
    		$('.accept-items-btn').addClass('active');
    	}
    },
    deleteItems: function(e) {
    	var ids = [];
    	var trs = [];
    	$('input.row-checkbox:checked').each(function(){
    		var tr = $(this).closest('tr');
    		ids.push($(tr).attr('id'));
    		trs.push(tr);
    	});
    	
    	var callback = function(){
        	$('.ot_show_deletion_dialog_modal i').removeClass('icon-ok');
        	$('.ot_show_deletion_dialog_modal i').addClass('ot-preloader-micro');
        	$('.btn').button('toggle');
        	$('.accept-items-btn').button('toggle');
        	$('.delete-items-btn').button('toggle');
        	$.post(
                    "?cmd=reviews&do=deleteReview",
                    {
                        "ids": ids.join(';'),
                    },
                    function (data) {
                    	$('.accept-items-btn').button('toggle');
                    	$('.delete-items-btn').button('toggle');
                    	$('.ot_show_deletion_dialog_modal i').removeClass('ot-preloader-micro');
                    	$('.ot_show_deletion_dialog_modal i').addClass('icon-ok');
                    	$('.btn').button('toggle');
                    	if (! data.error) {
                    		showMessage(trans.get("reviews::Reviews_deleted"));
                    		location.reload();
                    	} else {
                    		showError(data);
                    	}
                    	var count = $('input[type=checkbox]:checked').length;
                    	if ( count > 0) {
                    		$('.delete-items-btn').removeClass('active');
                    		$('.accept-items-btn').removeClass('active');
                    	} else {
                    		$('.delete-items-btn').addClass('active');
                    		$('.accept-items-btn').addClass('active');
                    	}
                    }, 'json'
                );    		
    	};
    	
    	if (ids.length > 0) {
    		modalDialog(trans.get('Confirm_needed'), trans.get('reviews::Really_delete_reviews'), callback, {'confirm': trans.get('Delete'), 'cancel': trans.get('Cancel')});
    	}
    },
    deleteItem: function(e)
    {
    	var tr = $(e.currentTarget).closest('tr');
    	var id = $(tr).attr('id');
    	var ids = [];
    	ids.push(id);
    	
    	var callback = function(){
        	$('.ot_show_deletion_dialog_modal i', tr).removeClass('icon-ok');
        	$('.ot_show_deletion_dialog_modal i', tr).addClass('ot-preloader-micro');
        	$('.btn', tr).button('toggle');
        	$('.accept-items-btn').button('toggle');
        	$('.delete-items-btn').button('toggle');
        	$.post(
                    "?cmd=reviews&do=deleteReview",
                    {
                        "ids": ids.join(';'),
                    },
                    function (data) {
                    	$('.accept-items-btn').button('toggle');
                    	$('.delete-items-btn').button('toggle');
                    	$('.ot_show_deletion_dialog_modal i', tr).removeClass('ot-preloader-micro');
                    	$('.ot_show_deletion_dialog_modal i', tr).addClass('icon-ok');
                    	$('.btn', tr).button('toggle');
                    	if (! data.error) {
                    		showMessage(trans.get("reviews::Review_deleted"));
                    		$(tr).remove();
                    	} else {
                    		showError(data);
                    	}
                    	
                    }, 'json'
                );
    	} ;
    	
    	modalDialog(trans.get('Confirm_needed'), trans.get('reviews::Really_delete_review'), callback, {'confirm': trans.get('Delete'), 'cancel': trans.get('Cancel')});
    },
    acceptItems: function(e) {
        var ids = [];
        var trs = [];
        $('input.row-checkbox:checked').each(function(){
            var tr = $(this).closest('tr');
            ids.push($(tr).attr('id'));
            trs.push(tr);
        });
        if (ids.length > 0) {
            $('.accept-item-btn i').removeClass('icon-ok');
            $('.accept-item-btn i').addClass('ot-preloader-micro');
            $('.btn').button('toggle');
            $('.accept-items-btn').button('toggle');
            $('.delete-items-btn').button('toggle');
            $.post(
                "?cmd=reviews&do=acceptReview",
                {
                    "ids": ids.join(';'),
                },
                function (data) {
                    $('.accept-item-btn i').removeClass('ot-preloader-micro');
                    $('.accept-item-btn i').addClass('icon-ok');
                    $('.btn').button('toggle');
                    $('.accept-items-btn').button('toggle');
                    $('.delete-items-btn').button('toggle');
                    if (! data.error) {
                        showMessage(trans.get("reviews::Reviews_accepted"));
                        location.reload();
                    } else {
                        showError(data);
                    }
                    var count = $('input[type=checkbox]:checked').length;
                    if ( count > 0) {
                        $('.delete-items-btn').removeClass('active');
                        $('.accept-items-btn').removeClass('active');
                    } else {
                        $('.delete-items-btn').addClass('active');
                        $('.accept-items-btn').addClass('active');
                    }

                }, 'json'
            );
        }
    },
    acceptItem: function(e)
    {
    	var tr = $(e.currentTarget).closest('tr');
    	var id = $(tr).attr('id');
    	var ids = [];
    	ids.push(id);
    	$('.accept-item-btn i', tr).removeClass('icon-ok');
    	$('.accept-item-btn i', tr).addClass('ot-preloader-micro');
    	$('.btn', tr).button('toggle');
        $('.accept-items-btn').button('toggle');
        $('.delete-items-btn').button('toggle');

        $.post(
            "?cmd=reviews&do=acceptReview",
            {
                "ids": ids.join(';'),
            },
            function (data) {
                $('.btn', tr).button('toggle');
                $('.accept-items-btn').button('toggle');
                $('.delete-items-btn').button('toggle');
                if (! data.error) {
                    $(e.currentTarget).remove();
                    showMessage(trans.get("reviews::Review_accepted"));
                } else {
                    $('.accept-item-btn i', tr).removeClass('ot-preloader-micro');
                    $('.accept-item-btn i', tr).addClass('icon-ok');
                    showError(data);
                }
            }, 'json'
        );
    },
    showReviewInfo: function(e)
    {
        var self = this;
        var target = this.$(e.target);
        var reviewId = target.closest('tr').attr('id');
        $(".ot_review_view_topic .well").append('<i class="ot-preloader-medium preloader-centered"></i>');

        $.post(
            '?cmd=reviews&do=getReviewForOperator', {
                'reviewId': reviewId
            },
            function (data) {
                $(".ot_review_view_topic .well").html('');
                if (! data.error) {
                    $(".ot_review_view_topic .well").html(data.content);
                    $('.ratyScore').raty({readOnly: true});
                    $(".review-pictures").colorbox({rel: $(this).attr('rel'), maxHeight:"85%", maxWidth: "85%"});
                } else {
                    showError(data.message ? data.message : trans.get('Notify_error'));
                    $(".ot_review_view_topic .well").html(data.message);
                }
            }, 'json'
        );
    },
    addMessage: function(el)
    {
        var self = this.$(el.target);
        var targetButton = self.find('button[type="submit"]');
        var message = self.find('textarea[name="message"]').val();
        var files = $('.file-widget .files-container a');

        targetButton.button('loading');

        if (message.trim().length === 0) {
            showError(trans.get('Message_can_not_be_empty'));
            targetButton.button('reset');
            return false;
        }

        var action = self.attr('action');
        var method = self.attr('method');
        var formData = self.serializeArray();

        $.post(action, formData, function (data) {
                if (! data.error) {
                    content = renderTemplate('reviews/js_tpl/review-answer-message',{message : message, files : files});
                    $(".ot_review_view_topic .well .chat-messages").append(content);
                    self.closest('div.review-message-reply-form').collapse('toggle');
                    self.find('textarea[name="message"]').val('');
                    showMessage(trans.get('Notify_success'));
                    targetButton.button('reset');
                    $('.file-widget .files-container').empty();
                    $(".review-pictures").colorbox({rel: $(this).attr('rel'), maxHeight:"85%", maxWidth: "85%"});
                } else {
                    showError(data);
                    targetButton.button('reset');
                }
            }, 'json'
        );
        return false;
    },
    rewardItemReview: function(ev)
    {
        var self = this;
        var form = self.$(ev.target).parents('form:first');
        form.find('button').attr('disabled', 'disabled');

        $.post('?cmd=reviews&do=rewardItemReview',
            form.serialize(),
            function (data) {
                if (! data.error) {
                    form.find(':input').not(':button, :submit, :reset, :hidden').val('');
                    $('#toggleEnrollForm').trigger('click');

                    var reviewId = $(form).find('input[name="reviewId"]').val();
                    $('table.table-reviews').find('tbody').find('tr#' + reviewId).find('a.icon-gift').show();
                    $('.ot_review_view_topic aside dl .bonus_credited').show();

                    showMessage(trans.get('Notify_success'));
                } else {
                    showError(trans.get('Notify_error'));
                }
                form.find('button').removeAttr('disabled');
            }, 'json'
        );
    },
    render: function()
    {
        return this;
    },
    initialize: function() 
    {
        this.render();
		$('.delete-items-btn').addClass('active');
		$('.accept-items-btn').addClass('active');
    },
});

$(function() {
    var U = new Reviews();
});
