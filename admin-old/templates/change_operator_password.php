<?
include ("header.php");
?>
     

<div class="col700">
    <div class="tuning">
        <h2 class="mt30"><?=LangAdmin::get('change_password')?></h2>
        <div id="wrapper_change_password">
            <form id="form_change_password" action="" method="post">
            <div class="fbut clrfix">
                <p><?=LangAdmin::get('enter_new_password')?>:</p>
                <p>
                    <input type="input" name="password_new" value="" />
                    <input type="button" value="<?=LangAdmin::get('generate')?>" onclick="generatePassword();" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" />
                </p>
                <p><?=LangAdmin::get('enter_old_password')?>:</p>
                <p><input id="password_old" type="input" name="password_old" value="" /></p>
                <input type="button" value="<?=LangAdmin::get('change')?>" onclick="changePassword();" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" />
                </div>
            </form>
        </div>
        <div id="change_password_response" style="padding: 20px; color: red;"></div>
    </div>    
</div>

<script type="text/javascript">
    function alert(text) {
        $('#change_password_response').html(text);
    }

    function confirm_my(text) {
            return true;
        if (confirm(text)) {
            return true;
        }
        return false;
    }

    function changePassword() {
        if (confirm_my("<?=LangAdmin::get('change_password_confirm')?>?")) {
            $.post("<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&cmd=Control&do=ajaxChangeOperatorPassword",
            $('#form_change_password').serialize(), function(data) {
                var response = JSON.parse(data);
                if (response.error == 1) {
                    $('#wrapper_change_password').hide();
                    alert('<p><?=LangAdmin::get('change_password_true')?>, <?=LangAdmin::get('your_password')?>: <b>' + response.data + '</b></p>');
                } else {
                    alert('<?=LangAdmin::get('error')?>: ' + response.data);
                }
            });
            $('#password_old').val('');
        }
    }

    function getRandomInt(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    function generatePassword() {
        var arr_symbol = ['a', 'b', 'c', 'd', 'e', 'f',
        'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o',
        'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
        'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        var word = '';
        for (i = 1; i <= 8; i++) {
            word = word + arr_symbol[getRandomInt(0, (arr_symbol.length - 1))];
        }
        $('input[name="password_new"]').val(word);
    }
</script>

<?
include ("footer.php");
?>
	
	