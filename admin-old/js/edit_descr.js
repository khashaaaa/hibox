$(function(){
    $( "#dialog-form" ).dialog({
            autoOpen: false,
            height: 560,
            width: 610,
            modal: true,
            buttons: {
                "Сохранить": function() {
                    $('.validateTips').html('Идет сохранение');
                    $.post('index.php',{
                        'cmd'   : 'Set',
                        'do'    : 'saveTitle',
                        'id'    : $('[name="ItemId"]').val(),
                        'descr' : $('#Title').val()
                    }, function(data){
                        $('.validateTips').html(data);
                        setTimeout('location.reload(true);', 1000);
                    });
                },
                'Закрыть': function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
            }
    });
    $( "#dialog-form-descr" ).dialog({
            autoOpen: false,
            height: 560,
            width: 610,
            modal: true,
            buttons: {
                "Сохранить": function() {
                    $('.validateTips').html('Идет сохранение');
                    $.post('index.php',{
                        'cmd'   : 'Set',
                        'do'    : 'saveDescr',
                        'id'    : $('[name="ItemId"]').val(),
                        'descr' : tinyMCE.activeEditor.getContent()
                    }, function(data){
                        $('.validateTips').html(data);
                        setTimeout('location.reload(true);', 1000);
                    });
                },
                'Закрыть': function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
            }
    });
    tinyMCE.init({
        mode : "exact",
        elements : "Description",
        theme : "advanced",
        plugins : "table,advhr,advimage,advlink,paste,fullscreen,noneditable,contextmenu,inlinepopups,emotions,media,insertdatetime,nonbreaking",
        theme_advanced_buttons1_add_before : "newdocument,separator",
        theme_advanced_buttons1_add : "fontselect,fontsizeselect",
        theme_advanced_buttons2_add : "separator,forecolor,backcolor,liststyle,separator,insertdate,inserttime",
        theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,separator",
        theme_advanced_buttons3_add_before : "tablecontrols,separator",
        theme_advanced_buttons3_add : "flash,advhr,emotions,media,nonbreaking,separator,fullscreen",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        extended_valid_elements : "hr[class|width|size|noshade],ul[class=listUL],ol[class=listOL],script[language|type|src], object[width|height|classid|codebase|embed|param],param[name|value],embed[param|src|type|width|height|flashvars|wmode], iframe[src|style|width|height|scrolling|marginwidth|marginheight|frameborder]",
        media_strict: false,
        file_browser_callback : "ajaxfilemanager",
        paste_use_dialog : false,
        theme_advanced_resizing : true,
        theme_advanced_resize_horizontal : true,
        apply_source_formatting : true,
        force_br_newlines : true,
        force_p_newlines : false,
        relative_urls : true,
        content_css : "css/style_editor.css"
    });
    
    $('.edit-name').click(function(){
        var id = $(this).attr('rel');
        $('[name="ItemId"]').val(id);
        
        $.get('index.php',{
            cmd: 'Set',
            'do': 'getItemInfo',
            id: id
        }, function(data){
            $('#Title').val(data.Title);
        }, 'json');
        
        $( "#dialog-form" ).dialog('open');
        
        return false;
    });
    
    $('.edit-descr').click(function(){
        var id = $(this).attr('rel');
        $('[name="ItemId"]').val(id);
        
        $.get('index.php',{
            cmd: 'Set',
            'do': 'getItemDescr',
            id: id
        }, function(data){
            tinyMCE.activeEditor.setContent(data[0]);
        }, 'json');
        
        $( "#dialog-form-descr" ).dialog('open');
        
        return false;
    });
});
