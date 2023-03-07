$(function(){
    $('#bank-save').off().on('click', function(){
        var $button = $(this).button('loading');
        var action = $(this).closest('form').attr('action');
        $.post(action, $(this).closest('form').serializeArray(), function (data) {
            $button.button('reset');
            if (! data.error) {
                showMessage(trans.get('Notify_success'));
            } else {
                showError(data);
            }
        }, 'json');
    });
});
