$(function(){
    $('.ot_show_deletion_dialog_modal').click(function(e){
        e.preventDefault();
        $('#item_for_delete').html($(this).attr('q_text'));
        $('.ot_deletion_dialog_modal #confirm').attr('href', $(this).attr('href'));
        $('.ot_deletion_dialog_modal').modal('show');
        return false;
    });

	$('.ot_show_3answer_dialog_modal').click(function(e){
        e.preventDefault();		        
        $('#delete-link-confirm-ref').attr('href', $(this).attr('href'));	
		$('#replace-link-confirm-ref').attr('href', $(this).attr('2_url'));
		$('#users_in_group').html('--');
        $('.ot_3answer_dialog_modal').modal('show');
		$.get('?cmd=referral&do=getCount&id='+$(this).attr('cat_id'), {}, function (data) {
		   $('#users_in_group').html(data);
        });
		
        return false;
    });

    $('.ot-add-users-typeahead').each(function(){
        var that = this;
        $(this).popover({
            html: true,
            content: renderInlineEditableElement('popover-typeahead', {id: 'add-referral-user'})
        }).
            click(function(){
                var addUserTextEl = $('#add-referral-user');
                addUserTextEl.typeahead({
                    source: function (query, process) {
                        if(!addUserTextEl.hasClass('searching')){
                            addUserTextEl.addClass('searching');
                            return $.get('?cmd=referral&do=searchUsers&login='+query, {}, function (data) {
                                addUserTextEl.removeClass('searching');
                                return process(data.options);
                            }, 'json');
                        }
                    }
                });
                $('#add-referral-user-submit').click(function(){
                    var userToAdd = $('#add-referral-user').val();                    
                    if ($.trim(userToAdd) == '') {
                        showError(trans.get('User_login_can_not_be_empty'));
                        return false;
                    }
                    $('#add-referral-user-form').hide();
                    $('#add-referral-user-loader').show();
                    $.post($(that).attr('data-url'), {login: userToAdd}, function (data) {
                        if (! data.error) {
                            $('#add-referral-user-form').show();
                            $('#add-referral-user-loader').hide();
                            var tpl = _.template($('#success-message-tpl').html().replace(new RegExp("&lt;", 'g'), '<').replace(new RegExp("&gt;", 'g'), '>'));
                            $('#messages').html( tpl(data) );
                            $(that).popover('hide');
							
							if ($('.ot-add-users-typeahead').attr('do')=="refresh") {
								location.reload();
							}                            
                        } else {
                            $('#add-referral-user-form').show();
                            $('#add-referral-user-loader').hide();
                            showError(data);                            
                        }
                    }, 'json');
                    
                    return false;
                });
                $('#add-referral-user-cancel').click(function(){
                    $(that).popover('hide');
                });
            });
    });

});
