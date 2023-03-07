function Recover(data){
	$('#listfiles').hide();
	$('#loader').show();
	$.ajax({
        url: 'index.php?cmd=Update&do=Recover&file='+data,		
        success: function(dt) {
            $('#recoverdone').show();
			$('#recoverdone').html(dt);
			$('#listfiles').show();
			$('#loader').hide();
        }
    });    
}

function showOverlay(){
    var h = $('#overlay').parent().height();
    $('#overlay').css({
        height: h+'px',
        display: 'block'
    });
}
function hideOverlay(){
    $('#overlay').hide();
}
$(function(){
    $( "#dialog-form:ui-dialog" ).dialog( "destroy" );

    $("#dialog-form").dialog({
        autoOpen: false,
        modal: true,
        buttons : {
            "Ок" : function() {
                $("#dialog-form").dialog("close");
            }
        }
    });
    
    $('#update').click(function(){
        showOverlay();
        $.get('index.php', {
            'cmd': 'Update',
            'do': 'download'
        }, function(data){
            if(!data.success){
                $('#basketinfo').text(data.error);
                $("#dialog-form").dialog("open");
            }
            else{
                $.get('index.php?cmd=Update&do=extract', function(data){
                    if(data == 'Ok' || data == "\nOk"){
                        window.location = window.location.href.replace(/admin.*$/, '')+'install/update.php';
                    }
                    else{
                        $('#basketinfo').text(data);
                        $("#dialog-form").dialog("open");
                    }
                });
            }
            hideOverlay();
        }, 'json');
    });
});
