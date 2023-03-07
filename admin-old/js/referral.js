$(function(){
    $('.send_present').click(function(){
        if(confirm('Вы действительно хотите отправить подарок?')){
            window.location = '/admin/index.php?cmd=referrals&do=sendGift&from=' + $(this).attr('item');
        }
    });
});