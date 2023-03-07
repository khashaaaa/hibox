var originallink;
$(function(){
    $('.ot_show_deletion_dialog').click(function(e){ 
        $('#delete-link-confirm').attr('href', $(this).attr('action'));		
        $('.ot_show_deletion_dialog_modal').show();
        return false;
    });
	
	$('.ot_show_3answer_dialog_modal').click(function(e){
        e.preventDefault();		        
        $('#delete-link-confirm-ref').attr('href', $(this).attr('href'));	
		$('#replace-link-confirm-ref').attr('href', $(this).attr('2_url'));
		$('#users_in_group').html('--');
        $('.ot_3answer_dialog_modal').show();
		$.get('index.php?cmd=referralold&do=getCount&id='+$(this).attr('cat_id'), {}, function (data) {
		   $('#users_in_group').html(data);
        });
		
        return false;
    });
	
	
	$('.ot-add-users-typeahead_link').click(function(e){        	        
        originallink = $(this).attr('data-url');		
        $('.ot-add-users-typeahead').show();			
        return false;
    });
	
	$('#logininput').keyup(function(e) { 
    if ((e.keyCode != 40) && (e.keyCode != 38) && (e.keyCode != 13)) {
      counter = $('#logininput').val().length; 
	  if(counter >= 2){  			  
  		$.get('index.php?cmd=referralold&do=searchUsers&login=' + $('#logininput').val(), {}, function(data){ 
 		   if(data != 0){   
  		     $("#showera").html(data); 
  	         $("#showera").fadeIn(400);   
           }else { 
	         $("#showera").css('display','none'); 
           } 
  
       	});   
      } 
	  
	}
	});     

});

function SetUser(usr) {
	$('#logininput').val(usr);	
	$('#add-link-confirm-ref').attr('href', originallink+'&login='+usr);
	$('#showera').hide();
}
