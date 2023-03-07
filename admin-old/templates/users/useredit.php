<? include (TPL_DIR . "header.php"); ?>

<?=Lang::loadJSTranslation(array('proceed', 'notify_success', 'there_was_an_error', 'save', 'this_profile_be_removed', 'OK', 'remove', 'cancel'))?>

<script type="text/javascript" src="js/edit_user.js"></script>

<div id="add-profile-dialog-form" title="<?=LangAdmin::get('add')?>">
    <? if(count($profiles) >= 3) { ?>
        <div id="message" ><?=LangAdmin::get('limiting_the_number_of_profiles')?></div>
    <? } else  { ?>
        <form id="add-profile-delivery">
            <input  type="hidden" name="Profile[UserId]" value="<?=$user['id']?>" />
            <table>
                <tr>
                        <td><?=LangAdmin::get('name_of_recipient')?></td>
                        <td><input id="RecipientFirstName" type="text" name="Profile[FirstName]" value="" /></td>
                </tr>

                <tr>
                        <td><?=LangAdmin::get('surname_of_recipient')?></td>
                        <td><input id="RecipientLastName" type="text" name="Profile[LastName]" value="" /></td>
                </tr>

                <tr>
                        <td><?=LangAdmin::get('recipient_middle_name')?></td>
                        <td><input id="RecipientMiddleName" type="text" name="Profile[MiddleName]" value="" /></td>
                </tr>

                <tr>
                        <td><?=LangAdmin::get('country')?></td>
                        <td>
                            <select name="Profile[CountryCode]" id="Profile[Country]">
                                    <? foreach($countries as $item) { ?>
                                    <option value="<?=$item['Id']?>"> <?=$item['Name']?></option>
                                    <? } ?>
                            </select>
                        </td>
                </tr>

                <tr>
                        <td><?=LangAdmin::get('city')?></td>
                        <td><input id="City" type="text" name="Profile[City]" value="" /></td>
                </tr>

                <tr>
                        <td><?=LangAdmin::get('address')?></td>
                        <td><input id="Address" type="text" name="Profile[Address]" value="" /></td>
                </tr>

                <tr>
                        <td><?=LangAdmin::get('zip_code')?></td>
                        <td><input id="PostalCode" type="text" name="Profile[PostalCode]" value="" /></td>
                </tr>

                <tr>
                        <td><?=LangAdmin::get('region')?></td>
                        <td><input id="Region" type="text" name="Profile[Region]" value="" /></td>
                </tr>

                <tr>
                        <td><?=LangAdmin::get('phone')?></td>
                        <td><input id="Phone" type="text" name="Profile[Phone]" value="" /></td>
                </tr>
                <? if (in_array('PassportData', General::$enabledFeatures)) { ?>
                    <tr>
                            <td><?=LangAdmin::get('passport')?></td>
                            <td><input id="PassportNumber" type="text" name="Profile[PassportNumber]" value="" /></td>
                    </tr>

                    <tr>
                            <td><?=LangAdmin::get('registrationaddress')?></td>
                            <td><input id="RegistrationAddress" type="text" name="Profile[RegistrationAddress]" value="" /></td>
                    </tr>
                <? } ?>
            </table>
        </form>
    <? } ?>
</div>


<div id="confirm-dialog-form" title="<?=LangAdmin::get('message')?>">
    <div id="message_info" ></div>
</div>

<br/><br/>
<a href="index.php?cmd=users&sid=<?=$GLOBALS['ssid']?>"> &lt; &lt; <?=LangAdmin::get('to_the_list_of_users')?>
</a><br/>
<? if ($user) { ?>
<h2><?=LangAdmin::get('user_information')?></h2>

<table class="notepad">
    <thead>
    <tr>
        <td><?=LangAdmin::get('name_short')?></td>
        <td><?=LangAdmin::get('account_number')?></td>
        <td><?=LangAdmin::get('additional_information')?></td>
        <td></td>
    </tr>
    </thead>
    <tbody>
    <tr id="user<?=$user['id']?>">
        <td><a
            href="index.php?sid=<?=$GLOBALS['ssid'];?>&cmd=users&do=userinfo&id=<?=$user['id']?>"><?=$user['firstname']?></a>
        </td>
        <td></td>
        <td>email: <?=$user['email']?></td>
        <td>
        </td>
    </tr>
    </tbody>
</table>


<div id="tabs">
    <ul>
        <li id="itab1"><a href="#tabs-1"><?=LangAdmin::get('general_information')?></a></li>
        <li id="itab2"><a href="#tabs-2"><?=LangAdmin::get('profiles')?></a></li>
    </ul>
    
    <div id="tabs-1">
        <h2><?=LangAdmin::get('general_information')?></h2>
        <?php if (isset($user['IsEmailVerified']) && 'false' === $user['IsEmailVerified']) { ?>
            <h3 style="color: red"><?=LangAdmin::get('user_is_not_activated')?></h3>
        <?php } ?>
        <form id="user_form"  action="<?=BASE_DIR;?>index.php?sid=<?=$GLOBALS['ssid'];?>&amp;do=saveuser&amp;cmd=users" method="post">
            <table class="userinfo">
                <tr>
                    <td><strong><?=LangAdmin::get('login')?>:</strong></td>
                    <td><input type="text" name="Login" value="<?=$user['login']?>" required/></td>
                </tr>
                <tr>
                    <td><strong>Email:</strong></td>
                    <td><input type="email" name="Email" value="<?=$user['email']?>" required/></td>
                </tr>
                <tr>
                    <td><strong><?=LangAdmin::get('active')?> (<?=LangAdmin::get('not_banned')?>)</strong></td>
                    <td>
                        <? if ((string)$user['isactive'] == 'false') {
                        print LangAdmin::get('banned');
                    }
                    else {
                        print LangAdmin::get('not_banned');
                    } ?>
                    </td>
                </tr>
                <tr>
                    <td><strong><?=LangAdmin::get('paul')?>:</strong></td>
                    <td><select name="Sex">
                        <option value="Male" <? if ((string)$user['sex'] == 'Male') print 'selected'; ?>><?=LangAdmin::get('male')?></option>
                        <option value="Female" <? if ((string)$user['sex'] == 'Female') print 'selected'; ?>><?=LangAdmin::get('female')?></option>
                    </select>
                    </td>
                </tr>

                <tr>
                    <td><strong><?=LangAdmin::get('last_name')?>:</strong></td>
                    <td><input type="text" name="LastName" value="<?=$user['lastname'];?>" required/></td>
                </tr>
                <tr>
                    <td><strong><?=LangAdmin::get('name_short')?>:</strong></td>
                    <td><input type="text" name="FirstName" value="<?=$user['firstname'];?>" required/></td>
                </tr>
                <tr>
                    <td><strong><?=LangAdmin::get('middle_name')?>:</strong></td>
                    <td><input type="text" name="MiddleName" value="<?=$user['middlename'];?>"/></td>
                </tr>

                <tr>
                    <td><strong><?=LangAdmin::get('phone')?>:</strong></td>
                    <td><input type="text" name="Phone" value="<?=$user['phone']?>"/></td>
                </tr>
                <tr>
                    <td><strong><?=LangAdmin::get('Skype')?>:</strong></td>
                    <td><input type="text" name="Skype" value="<?=$user['skype']?>"/></td>
                </tr>
                
                <?=Plugins::invokeEvent('onRenderUserEditForm', array('user' => $user))?>
            </table>

            <input type="hidden" name="Id" value="<?=$user['id']?>"/>

            <div class="fbut clrfix">
                <input type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="<?=LangAdmin::get('save')?>"/>
                <?php if (isset($user['IsEmailVerified']) && 'false' === $user['IsEmailVerified']) { ?>
                    <input type="hidden" value="<?=$user['EmailConfirmationCode']?>" name="EmailConfirmationCode" />
                    <input id="activate_user" style="color:red !important; float:right" type="submit" class="ui-button ui-widget ui-state-default ui-corner-all" value="<?=LangAdmin::get('activate_user')?>"/>
                <?php } ?>
            </div>
        </form>
        <br clear="all"/>
    </div>
    
    
    <div id="tabs-2">
        <h2><?=LangAdmin::get('profiles')?></h2>
        <div id="overlay"></div>
        <div id="row">
        <div class="form-row">
                <label for="Profile"><?=LangAdmin::get('profile')?></label>
                <? if (count($profiles)) { ?>
                    <select name="profiles_select" id="profiles_select">
                        <? $profiles_count = 0; ?>
                        <? foreach ($profiles as $profile) { ?>
                            <? $profiles_count++; ?>
                            <option value="<?=$profile['id']?>"><?=LangAdmin::get('profile') . ' '  . $profiles_count ?></option>
                        <? } ?>
                    </select>
                <? } ?>
                <a href='#' id="add-delivery-profile-button"><?=Lang::get('add')?></a>
        </div>
        
        <br/>
        
        <? $profiles_count = 0; ?>
        <? foreach ($profiles as $profile) { ?>
            <? $profiles_count++; ?>
            <div class="profile-data" id="profile-<?=$profile['id']?>" <? if ($profiles_count > 1) { ?> style="display:none;" <? } ?> >
            <form id="form-profile-<?=$profile['id']?>" action="index.php?p=profile" method="post">
                <input  type="hidden" name="Profile[Id]" value="<?=$profile['id']?>" />
                <input  type="hidden" name="Profile[UserId]" value="<?=$user['id']?>" />
                
                <table>
                    <tr>
                            <td><?=LangAdmin::get('name_of_recipient')?></td>
                            <td><input id="RecipientFirstName" type="text" name="Profile[FirstName]" value="<?=$profile['firstname']?>" /></td>
                    </tr>

                    <tr>
                            <td><?=LangAdmin::get('surname_of_recipient')?></td>
                            <td><input id="RecipientLastName" type="text" name="Profile[LastName]" value="<?=$profile['lastname']?>" /></td>
                    </tr>

                    <tr>
                            <td><?=LangAdmin::get('recipient_middle_name')?></td>
                            <td><input id="RecipientMiddleName" type="text" name="Profile[MiddleName]" value="<?=$profile['middlename']?>" /></td>
                    </tr>

                    <tr>
                            <td><?=LangAdmin::get('country')?></td>
                            <td>
                                <select name="Profile[CountryCode]" id="Profile[Country]">
                                        <? foreach($countries as $item) { ?>
                                        <option value="<?=$item['Id']?>" <? if($profile['countrycode']==$item['id']) print 'selected'; ?>> <?=$item['Name']?></option>
                                        <? } ?>
                                </select>
                            </td>
                    </tr>

                    <tr>
                            <td><?=LangAdmin::get('city')?></td>
                            <td><input id="City" type="text" name="Profile[City]" value="<?=$profile['city']?>" /></td>
                    </tr>

                    <tr>
                            <td><?=LangAdmin::get('address')?></td>
                            <td><input id="Address" type="text" name="Profile[Address]" value="<?=$profile['address']?>" /></td>
                    </tr>

                    <tr>
                            <td><?=LangAdmin::get('zip_code')?></td>
                            <td><input id="PostalCode" type="text" name="Profile[PostalCode]" value="<?=$profile['postalcode']?>" /></td>
                    </tr>

                    <tr>
                            <td><?=LangAdmin::get('region')?></td>
                            <td><input id="Region" type="text" name="Profile[Region]" value="<?=$profile['region']?>" /></td>
                    </tr>

                    <tr>
                            <td><?=LangAdmin::get('phone')?></td>
                            <td><input id="Phone" type="text" name="Profile[Phone]" value="<?=$profile['Phone']?>" /></td>
                    </tr>
                    <? if (in_array('PassportData', General::$enabledFeatures)) { ?>
                        <tr>
                                <td><?=LangAdmin::get('passport')?></td>
                                <td><input id="PassportNumber" type="text" name="Profile[PassportNumber]" value="<?=$profile['passportnumber']?>" /></td>
                        </tr>

                        <tr>
                                <td><?=LangAdmin::get('registrationaddress')?></td>
                                <td><input id="RegistrationAddress" type="text" name="Profile[RegistrationAddress]" value="<?=$profile['registrationaddress']?>" /></td>
                        </tr>
                    <? } ?>
                    <tr>
                            <td colspan="2">
                                <input type="submit" id="save-delivery-profile" data-profile-id="<?=$profile['id']?>" data-user-id="<?=$user['id']?>" value="<?=LangAdmin::get('save')?>" class="btn_office" />
                                <input type="submit" id="delete-delivery-profile"  data-profile-id="<?=$profile['id']?>" data-user-id="<?=$user['id']?>" value="<?=LangAdmin::get('remove')?>" class="btn_office" />
                            </td>
                    </tr>
                </table>
            </form>
            </div>
        <? } ?>
        </div>
        <br clear="all"/>
    </div>
    
</div>

<script>
    var delivery_profiles_count = <?=count($profiles)?>;
</script>

<? } ?>

<? include (TPL_DIR . "footer.php"); ?>