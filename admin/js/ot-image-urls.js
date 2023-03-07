$(function() {
    $('.modal-add-image').click(function() {
            $('.ot_crud_custom_picture_window .modal-body #dataId').attr('value', '');
            var content = $('.ot_crud_custom_picture_window .modal-body').html();
            var modal = modalDialog(trans.get('Logo'), content, function(body) {
                var newImageUrl = $('#dataId', body).val();
                $('#newUrlPicture').val(newImageUrl);
                $('.thumbnail-placeholder').find('img').attr('src', newImageUrl);
                modal.toggle();
                $(".modal-backdrop").remove();
                return false;
              }, {confirm: trans.get('Add'), cancel: trans.get('Cancel') }, initPopoverInsideDialog);
        });
});
