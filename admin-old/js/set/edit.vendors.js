$(function () {
    $("#vendor-edit-form").dialog({
        autoOpen:false,
        height:450,
        width:450,
        modal:true,
        position: 'top',
        buttons:[{
                text: trans.save,
                click: function () {
                    $(this).dialog({title: trans.saving});
                    $("#vendor-edit-form").find('[name="ItemId"]').val();
                    $("#vendor-edit-form").find('[name="VendorName"]').val();
                    $("#vendor-edit-form").find('[name="PictureUrl"]').val();

                    $.post('index.php?cmd=Set2&do=saveVendorInfo', $(this).serializeArray())
                        .success(function(){
                            $("#vendor-edit-form").dialog({title: trans.saved});
                        })
                        .error(handleAjaxError);
                }
            },{
                text: trans.delpic,
                click: function () {
					
                    $(this).dialog({title: trans.saving});
					$("#vendor-edit-form").find('[name="ItemId"]').val();
                    $("#vendor-edit-form").find('[name="VendorName"]').val();
					$("#vendor-edit-form").find('[name="PictureUrl"]').val('');

                    $.post('index.php?cmd=Set2&do=saveVendorInfo', $(this).serializeArray())
                        .success(function(){
                            $("#vendor-edit-form").dialog({title: trans.saved});
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
        $("#vendor-edit-form").find('[name="VendorName"]').val(trans.loading);
        $("#vendor-edit-form").find('[name="PictureUrl"]').val(trans.loading);
        $(".qq-upload-list").html('');
		$("#vendor_img").find('[name="PictureUrl"]'). removeAttr('src');
		
        $("#vendor-edit-form").dialog('open');

        var jqXHR = $.get('index.php', {cmd: 'Set2', do: 'getVendorInfo', id: $(this).attr('itemId')}, function(vendorInfo){
            $("#vendor-edit-form").find('[name="Id"]').val(vendorInfo.Id);
            $("#vendor-edit-form").find('[name="VendorName"]').val(vendorInfo.VendorName);
			$("#vendor-edit-form").find('[name="PictureUrl"]').val(vendorInfo.PictureUrl);
			$("#vendor_img").find('[name="PictureUrl"]').attr('src',vendorInfo.PictureUrl);
			}, 'json').error(handleAjaxError);
			
        jqXHR.error(function(){
            $("#vendor-edit-form").dialog('close');
        });

        $("#vendor-edit-form").find('[name="ItemId"]').val($(this).attr('itemId'));

        return false;
    });
});