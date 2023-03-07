$(function(){
    //$('input').css({'border-style':'solid','border-color': '#bd4247'});
    $('#save').click(function(ev){
        ev.preventDefault();
        var $button = $(this).button('loading');
        var action = $(this).closest('form').attr('action');

        $.post(action, $(this).closest('form').serializeArray(), function(data){
        })
            .success(function(){
                window.location.href = $(ev.target).data('link');
                return;

                $('.input-xlarge').css({'border-color': '#ccc'});
                $button.button('reset');
                $('#save').parent().parent().removeClass('error').addClass('success');
                $('#save').next().remove();
                $('#save').parent().append(
                    $('<p></p>').addClass('help-inline').text(''+trans.get('Data_save_success')+'')
                );
            })
            .error(function(xhr, ajaxOptions, thrownErro){
                $('.input-xlarge').css({'border-color': '#ccc'});
                $button.button('reset');
                if(thrownErro == 1){
                    var fields = eval(xhr.responseText);
                    $.each(fields, function(key, value){
                        $('#ot_'+value).css({'border-style':'solid','border-color': '#bd4247'});
                    });
                    $('#save').parent().parent().removeClass('success').addClass('error');
                    $('#save').next().remove();
                    $('#save').parent().append(
                        $('<p></p>').addClass('help-inline').text(''+trans.get('Fields_incorrect')+'')
                    );
                }
            });
    });

    $('#cancel').click(function(){

    });
});