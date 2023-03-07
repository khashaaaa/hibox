<? include (TPL_DIR."header.php"); ?>

<br/>
<a href="index.php?cmd=adminusers&sid=<?=$GLOBALS['ssid']?>"> <<<?=LangAdmin::get('to_the_list_of_users')?></a><br/>

<h2><?=LangAdmin::get('general_information')?></h2>
<form action="<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;do=saveuser&amp;cmd=adminusers" method="post">
    <table class="userinfo" >
        <tr><td><strong><?=LangAdmin::get('name')?>:</strong></td>
            <td><input type="text" name="Name" value=""/></td>
        </tr>
        <tr><td><strong><?=LangAdmin::get('login')?>:</strong></td>
            <td><input type="text" name="Login" value=""/></td>
        </tr>
        <tr><td><strong><?=LangAdmin::get('password')?>:</strong></td>
            <td><input type="text" name="Password" value=""/></td>
        </tr>
        <tr><td><strong>Email:</strong></td>
            <td><input type="text" name="Email" value=""/></td>
        </tr>
    </table>

    <div class="fbut clrfix">	
        <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="<?=LangAdmin::get('save')?>"/>
    </div>
</form>

<? include (TPL_DIR."footer.php"); ?>