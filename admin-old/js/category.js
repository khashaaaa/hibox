$(function(){
    var categoryId = 0;

    $('body').append(
        $('<div></div>')
            .attr({
                'id': 'seo-text-wrapper'
            })
            .append(
                $('<textarea></textarea>')
                    .attr({
                        'rows': 10,
                        'columns': 20,
                        'id': 'seo-text'
                    })
            )
    );
    tinyMCE.init({
        mode : "exact",
        elements : "seo-text",
        theme : "advanced",
        height: 230,
        plugins : "table,heading,advhr,advimage,advlink,paste,fullscreen,noneditable,contextmenu,inlinepopups,emotions,media,insertdatetime,nonbreaking",
        theme_advanced_buttons1_add_before : "newdocument,separator",
        theme_advanced_buttons1_add_before : "fontselect,fontsizeselect,h1,h2,h3,h4,h5,h6,separator",
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
        forced_root_block : "div",
        force_p_newlines : false,
        relative_urls : true,
        heading_clear_tag : "p",
        content_css : "../css/style.css"
    });
    $('#seo-text-wrapper').dialog({
        autoOpen: false,
        resizable: false,
        height:340,
        width: 680,
        modal: true,
        buttons: [
            {
                text: trans.cancel,
                click: function() {
                    $( this ).dialog( "close" );
                }
            },
            {
                text: trans.save,
                click: function(){
                    $.post('index.php?cmd=category&do=saveSeoText',{
                        'text': tinyMCE.get('seo-text').getContent(),
                        'category_id': categoryId
                    }, function(data){
                        $('#seo-text-wrapper').dialog({'title': 'Saved!'});
                    });
                }
            }
        ]
    });

    $('.edit-seo-text').live('click', function(){
        categoryId = $(this).attr('cid');
        $.post('index.php?cmd=category&do=getSeoText',{
            'category_id': categoryId
        }, function(data){
            tinyMCE.get('seo-text').setContent(data);
        });
        $('#seo-text-wrapper').dialog('open');
        return false;
    });
});
