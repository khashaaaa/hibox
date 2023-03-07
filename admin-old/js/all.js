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

function show_error(data) {
    $('#error').html(data);
    $('#dialog-error').dialog('open');
}

function handleAjaxError (xhr, ajaxOptions, thrownError){
    if(thrownError == 'SessionExpired'){
        window.location = 'index.php?cmd=login';
    }
    else{
        show_error(thrownError + '<br />' + xhr.responseText);
    }
}
