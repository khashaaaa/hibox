<div class="important_message" id="important_message"></div>
<script type="text/javascript">
function getImportantMessage() {
    $.ajax({
        type: "POST",
        dataType: 'json',
        url: '<?=$urlRequest?>',
        success: function(data) {
            if (data.message !== undefined) {
                $('#important_message').html(data.message);
            }
        }
    });
}

$(function () {
    getImportantMessage();
});
</script>