<? include (TPL_DIR."header.php"); ?>

<br/>
<a href="index.php?cmd=users&sid=<?=$GLOBALS['ssid']?>"> &lt; &lt; <?=LangAdmin::get('to_the_list_of_users')?></a><br/>

<h2><?=LangAdmin::get('general_information')?></h2>
<form action="<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;do=saveuser&amp;cmd=users" method="post">
    <table class="userinfo" >
        <tr><td><strong><?=LangAdmin::get('login')?>:</strong></td>
            <td><input type="text" name="Login" value=""/></td>
        </tr>
        <tr><td><strong><?=LangAdmin::get('password')?>:</strong></td>
            <td><input type="text" name="Password" value=""/></td>
        </tr>
        <tr><td><strong>Email:</strong></td>
            <td><input type="text" name="Email" value=""/></td>
        </tr>
        <tr><td><strong><?=LangAdmin::get('paul')?>:</strong></td>
            <td><select name="Sex">
                    <option value="Male" selected><?=LangAdmin::get('male')?></option>
                    <option value="Female" ><?=LangAdmin::get('female')?></option>
                </select>
            </td>
        </tr>

        <tr><td><strong><?=LangAdmin::get('last_name')?>:</strong></td>
            <td><input type="text" name="LastName" value=""/></td>
        </tr>
        <tr><td><strong><?=LangAdmin::get('user_name')?>:</strong></td>
            <td><input type="text" name="FirstName" value=""/></td>
        </tr>
        <tr><td><strong><?=LangAdmin::get('middle_name_by')?>:</strong></td>
            <td><input type="text" name="MiddleName" value=""/></td>
        </tr>

        <tr><td><strong><?=LangAdmin::get('country')?>:</strong></td>
            <td><input type="text" name="Country" value=""/></td>
        </tr>
        <tr><td><strong><?=LangAdmin::get('city')?>:</strong></td>
            <td><input type="text" name="City" value=""/></td>
        </tr>
        <tr><td><strong><?=LangAdmin::get('address')?>:</strong></td>
            <td><input type="text" name="Address" value=""/></td>
        </tr>
        <tr><td><strong><?=LangAdmin::get('phone')?>:</strong></td>
            <td><input type="text" name="Phone" value=""/></td>
        </tr>
        <tr><td><strong><?=LangAdmin::get('region')?>:</strong></td>
            <td><input type="text" name="Region" value=""/></td>
        </tr>
        <tr><td><strong><?=LangAdmin::get('zip_code')?>:</strong></td>
            <td><input type="text" name="PostalCode" value=""/></td>
        </tr>


        <tr><td><strong><?=LangAdmin::get('surname_of_recipient')?>:</strong></td>
            <td><input type="text" name="RecipientLastName" value=""/></td>
        </tr>
        <tr><td><strong><?=LangAdmin::get('name_of_recipient')?>:</strong></td>
            <td><input type="text" name="RecipientFirstName" value=""/></td>
        </tr>
        <tr><td><strong><?=LangAdmin::get('recipient_middle_name')?>:</strong></td>
            <td><input type="text" name="RecipientMiddleName" value=""/></td>
        </tr>
    </table>

    <div class="fbut clrfix">	
        <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="<?=LangAdmin::get('save')?>"/>
    </div>
</form>

<? include (TPL_DIR."footer.php"); ?>