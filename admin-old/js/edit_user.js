$(function(){
    $('#tabs').tabs();
    
    $("#confirm-dialog-form").dialog({
            autoOpen:false,
            modal:true,
            width:350,
            buttons:[{
                text: trans.remove,
                click: function () {
                    var profile_id = $(this).find('input[name=profile_id]').val();
                    var user_id = $(this).find('input[name=user_id]').val();;
                    $.ajax({
                        url: 'index.php',
                        data: {cmd: 'users', do: 'deleteDeliveryProfile', profileId: profile_id, userId: user_id}
                    })
                    .success(function(data){
                        if (data == 'Ok') {
                            window.location.href = 'index.php?cmd=users&do=useredit&id=' + user_id; 
                        } else {
                            show_error(data);
                        }
                    })
                    .error(function(jqXHR, textStatus, errorThrown){				
                        show_error(errorThrown);
                    });
                    }
            } , {
                text: trans.cancel,
                click: function () {
                    $(this).dialog("close");
                }
            }
        ]
    });
    
    
    $("#add-profile-dialog-form").dialog({
		autoOpen:false,
		modal:true,
                width:350
	});

    if (delivery_profiles_count < 3) {
        $("#add-profile-dialog-form").dialog({
            buttons:[{
                    text: trans.save,
                    click: function () {
                        var params = $('#add-profile-delivery').serializeArray();
                        save_delivery_profile(params, true); 
                    }
                }, {
                    text: trans.cancel,
                    click: function () {
                        $(this).dialog("close");
                    }
                }
            ]
        });
    } else {
        $("#add-profile-dialog-form").dialog({
            buttons:[{
                text: trans.OK,
                click: function () {
                    $(this).dialog("close");
                }
            }]
        });
    }
    
    
    $('#add-delivery-profile-button').live('click', function (event) {
        event.preventDefault();
        $("#add-profile-dialog-form").dialog('open');
    });
    
    
    $('#activate_user').live('click', function (event) {
        event.preventDefault();

        var userId = parseInt($('input[name=Id]').val());
        var server_url = 'index.php?cmd=users&do=activate&userId=' + userId;

        $.post(
            server_url,
            {'EmailConfirmationCode' : $('input[name=EmailConfirmationCode]').val()},
            function (data) {
                if (data == 'RELOGIN') location.href = 'index.php?expired';
                if (data == 'Ok') {
                    alert(trans.notify_success)
                    window.location.reload();
                } else {
                    alert(trans.there_was_an_error + '\n\n' + data);
                }
            }
        );

        return false;
    });
    
    $('input#save-delivery-profile').live('click', function(e) {
        e.preventDefault();
        var profile_id = $(this).attr('data-profile-id');
        
        var params = $('#form-profile-' + profile_id).serializeArray();
        
        save_delivery_profile(params, false); 
        
    });
    
    $('input#delete-delivery-profile').live('click', function(e) {
        e.preventDefault();
        
        var profile_id = $(this).attr('data-profile-id');
        var user_id = $(this).attr('data-user-id');
        confirm_delete(profile_id, user_id);
        
    });
    
    $('select#profiles_select').live('change', function() {
        $('.profile-data').hide();
        $('#profile-' + $(this).val()).show();
    });
    
});

function confirm_delete(profile_id, user_id) {
    $("#confirm-dialog-form").dialog("open");
    var html = '<input type="hidden" name="profile_id" value="' + profile_id + '"/>';
    html += '<input type="hidden" name="user_id" value="' + user_id + '"/>';
    html += trans.this_profile_be_removed + ', ' + trans.proceed + '?';
    $('#message_info').empty().html(html);
}

function save_delivery_profile(params, redirect) {
    showOverlay();
    $.post('index.php?cmd=users&do=editDeliveryProfile',params, function(data){
        var userId = '';
        $.each(params, function(key, value){
            if (value.name == 'Profile[UserId]') {
                userId = value.value;
            }
        });
        hideOverlay();
        if (data.success) {
            if (redirect)
                window.location.href = 'index.php?cmd=users&do=useredit&id=' + userId; 
        } else if (data.message === 'RELOGIN') {
            window.location.href = 'index.php?cmd=login'; 
        } else {
            show_error(data.message); 
        }
    }, 'json');
}