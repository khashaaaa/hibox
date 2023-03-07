$(function(){
    $('.ajax-form').live('submit', function(){
		if (CheckSetsAdd(this)!= true) 
			return false;
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

function CheckSetsAdd(form){
	var can = true;
	var str;
	$(form).find('input[required]').each(function() {      
	  str = $(this).val();
      str = str.replace(/\s/g, '');
      if (str.length==0) {
        $(this).css('border-color','red');
        can = false;
      } 
       
    });   	
	return can;
}
