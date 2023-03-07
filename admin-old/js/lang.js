/*
 * not used in site_config
 */
$(function(){
    $('#tabs').tabs();
    $('.choose-lang-btn').click(function(){
        $('#choose-lang [name="lang"]').val($(this).attr('lang'));
        $('#choose-lang').submit();
    });
});