$(function(){
    $('.ajax-form').live('submit', function(){
        showLoader();
        var form = this;
        var jqXHR = $.ajax({
            url: $(form).attr('action'),
            type: $(form).attr('method'),
            data: $(form).serializeArray()
        })
            .success(function(){
                window[$(form).attr('onSuccess')]();
            })
            .error(function(xhr, ajaxOptions, thrownErro){
                handleAjaxError(xhr, ajaxOptions, thrownErro);
                window[$(form).attr('onError')]();
            });

        return false;
    });
})
