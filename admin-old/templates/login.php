<?
include ("header.php");
//unset($_SESSION['sid']);
?>
<div class="main"><div class="canvas clrfix">
    <h1> <?=LangAdmin::get('authorization_is_required')?>!  </h1> <br/>
    <p></p>
    <p style="color:red;font-weight:bold;"><?=LangAdmin::get('Old_admin_is_for_root_only')?></p>
    <p></p>
    <?
    if (isset($_POST['login'],$_POST['password'])) {
            echo '<p style="color:red"><strong>'.LangAdmin::get('authorization_error').'!<br />
            '.LangAdmin::get('check_your_username_or_password').'!<br />'.Lang::get('incorrect_code').'!<br /></strong></p>';
    }
    ?>
    <?
    if (isset($_GET['expired'])) {
        echo '<p style="color:red"><strong>'.LangAdmin::get('session_has_expired_or_is_wrong').'!<br />
            '.LangAdmin::get('need_to_log_in_again').'.<br /></strong></p>';
    }
    ?>
    <?
    if (isset($_SESSION['fatal_error'])) {
        echo '<p style="color:red"><strong>'.$_SESSION['fatal_error'].'!</strong></p>';
    }
    else{
        ?>
        <table class="tauth">
            <form action="index.php" method="post">
                <tr><td><?=LangAdmin::get('your_login')?>:</td>
                    <td><input type="text" name="login" size="32" value="<?=isset($_POST['login'])?htmlspecialchars($_POST['login']):'';?>"/></td>
                </tr>
                <tr><td><?=LangAdmin::get('your_password')?>:</td>
                    <td><input type="password" name="password" size="32" value="<?=isset($_POST['password'])?htmlspecialchars($_POST['password']):'';?>"/></td>
                </tr>
                <? if (General::getConfigValue('auth_capcha_admin')){ ?>
                <tr>
                    <td><label>&nbsp;</label></td>
                    <td>
                        <img id="siimage" style="border: 1px solid #000; margin-right: 15px" src="../lib/securimage/securimage_show.php?sid=<?php echo md5(uniqid()) ?>" alt="CAPTCHA Image" align="left">
                        <object type="application/x-shockwave-flash" data="../lib/securimage/securimage_play.swf?audio_file=../lib/securimage/securimage_play.php&amp;bgColor1=#fff&amp;bgColor2=#fff&amp;iconColor=#777&amp;borderWidth=1&amp;borderColor=#000" height="32" width="32">
                        <param name="movie" value="../lib/securimage/securimage_play.swf?audio_file=../lib/securimage/securimage_play.php&amp;bgColor1=#fff&amp;bgColor2=#fff&amp;iconColor=#777&amp;borderWidth=1&amp;borderColor=#000">
                        </object>
                        &nbsp;
                        <a tabindex="-1" style="border-style: none;" href="#" title="Refresh Image" onclick="document.getElementById('siimage').src = '../lib/securimage/securimage_show.php?sid=' + Math.random(); this.blur(); return false"><img src="../lib/securimage/images/refresh.png" alt="Reload Image" onclick="this.blur()" align="bottom" border="0"></a><br />
                        <?=Lang::get('input_captcha')?>:<br />
                        <?php echo @$_SESSION['ctform']['captcha_error'] ?>
                        <input type="text" name="ct_captcha" class="captchainp" size="12" maxlength="8" />
                    </td>
                 </tr>
            <? } ?>
                <tr><td></td><td><input type="submit" value="<?=LangAdmin::get('input')?>" /></td></tr>
            </form>
        </table>
</div></div>
        <?
    }
    ?>

<?
include ("footer.php");
?>

