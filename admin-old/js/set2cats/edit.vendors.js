$(function () {
    $("#vendor-edit-form").dialog({
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

    $('.edit-vendor-set').live('click', function(){
        $("#vendor-edit-form").find('[name="Title"]').val(trans.loading);
        $("#vendor-edit-form").dialog('open');

        var jqXHR = $.get('index.php', {cmd: 'Set2', do: 'getVendorInfo', id: $(this).attr('itemId')}, function(itemInfo){
            $("#vendor-edit-form").find('[name="Title"]').val(itemInfo.Title);
        }, 'json').error(handleAjaxError);
        jqXHR.error(function(){
            $("#vendor-edit-form").dialog('close');
        });

        $("#vendor-edit-form").find('[name="Id"]').val($(this).attr('itemId'));

        return false;
    });
});