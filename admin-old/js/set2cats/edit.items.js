tinyMCE.init({
    mode : "textareas",
    theme : "advanced",
    height: 300,
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
    paste_use_dialog : false,
    theme_advanced_resizing : true,
    theme_advanced_resize_horizontal : true,
    apply_source_formatting : true,
    force_br_newlines : true,
    force_p_newlines : false,
    relative_urls : true,
    content_css : "css/style_editor.css"
});

$(function () {
    $("#item-edit-form").dialog({
        autoOpen:false,
        height:500,
        width:630,
        modal:true,
        position: 'top',
        buttons:[{
                text: trans.save,
                click: function () {
                    $(this).dialog({title: trans.saving});
                    $("#item-edit-form").find('[name="ItemDescription"]').val(tinyMCE.editors.ItemDescription.getContent());
                    $.post('index.php?cmd=Set2&do=saveItemInfo', $(this).serializeArray())
                        .success(function(){
                            $("#item-edit-form").dialog({title: trans.saved});
                        })
                        .error(handleAjaxError);
                }
            },{
                text: trans.cancel,
                click: function () {
                    $(this).dialog("close");
                }
            }
        ],
        close:function () {
            var idx = $("#tabs").tabs('option', 'selected');
            $("#tabs").tabs('load', idx);
        }
    });

    $('.edit-item-set').live('click', function(){
        $("#item-edit-form").find('[name="ItemTitle"]').val(trans.loading);
        tinyMCE.editors.ItemDescription.setContent(trans.loading);

        $("#item-edit-form").dialog('open');

        var jqXHR = $.get('index.php', {cmd: 'Set2', do: 'getItemInfo', id: $(this).attr('itemId')}, function(itemInfo){
            $("#item-edit-form").find('[name="ItemTitle"]').val(itemInfo.Title);
            tinyMCE.editors.ItemDescription.setContent(itemInfo.Description);
        }, 'json').error(handleAjaxError);
        jqXHR.error(function(){
            $("#item-edit-form").dialog('close');
        });

        $("#item-edit-form").find('[name="ItemId"]').val($(this).attr('itemId'));

        return false;
    });
});