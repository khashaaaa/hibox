<div class="grid_16">
    <? if (! empty($user)) {?>
        <div id="general_information">
            <h2><?=LangAdmin::get('general_information')?></h2><br/>
            <? $recipient = $this->escape($user['recipientfirstname']).' '.$this->escape($user['recipientmiddlename']).
                ' '.$this->escape($user['recipientlastname']); ?>
            <? $address = array(); ?>
            <? if($recipient) $address[] = $recipient; ?>
            <? if($user['country']) $address[] = $this->escape($user['country']); ?>
            <? if($user['city']) $address[] = $this->escape($user['city']); ?>
            <? if($user['region']) $address[] = $this->escape($user['region']); ?>
            <? if((int)$user['postalcode']) $address[] = $this->escape($user['postalcode']); ?>
            <? if($user['address']) $address[] = $this->escape($user['address']); ?>
            <? if($user['phone']) $address[] = $this->escape($user['phone']); ?>
            <? if($user['email']) $address[] = $this->escape($user['email']); ?>
            <table>
                <tr><td><strong><?=LangAdmin::get('login')?>:</strong></td><td><?=$this->escape($user['login'])?></td></tr>
                <tr><td><strong><?=LangAdmin::get('email')?>:</strong></td><td><?=$this->escape($user['email'])?></td></tr>
                <tr><td><strong><?=LangAdmin::get('user_name')?>:</strong></td><td><? echo $this->escape($user['firstname']).' '.$this->escape($user['middlename']).' '.$this->escape($user['lastname']); ?></td></tr>
                <tr><td><strong><?=LangAdmin::get('name_and_address_of_the_recipient')?>:</strong></td><td><?=implode(', ', $address)?></td></tr>
                <tr><td><strong><?=LangAdmin::get('Skype')?>:</strong></td><td><?=$this->escape($user['skype'])?></td></tr>
            </table>
            <br clear="all"/>
        </div>

        <h2><?=LangAdmin::get('profiles')?></h2>
        <? if (! empty($profiles)) {?>
            <div style="margin-left:30px;">
                <label for="Profile"><?=LangAdmin::get('profile')?></label>
                <? if (count($profiles)) { ?>
                    <select name="profiles_select" id="profiles_select">
                        <? $profiles_count = 0; ?>
                        <? foreach ($profiles as $profile) { ?>
                            <? $profiles_count++; ?>
                            <option value="<?=$profile['id']?>"><?=LangAdmin::get('profile') . ' '  . $profiles_count ?></option>
                        <? } ?>
                    </select><br/><br/>
                <? } ?>
            </div>
            <? $profiles_count = 0; ?>
            <table>
                <? foreach ($profiles as $profile) { ?>
                    <? $profiles_count++; ?>
                    <tbody class="profile-data" id="profile-<?=$profile['id']?>" <? if ($profiles_count > 1) { ?> style="display:none;" <? } ?> >
                    <tr>
                        <td><strong><?=LangAdmin::get('name_of_recipient')?></strong></td>
                        <td><?=$this->escape($profile['firstname'])?></td>
                    </tr>
                    <tr>
                        <td><strong><?=LangAdmin::get('surname_of_recipient')?></strong></td>
                        <td><?=$this->escape($profile['lastname'])?></td>
                    </tr>
                    <tr>
                        <td><strong><?=LangAdmin::get('recipient_middle_name')?></strong></td>
                        <td><?=$this->escape($profile['middlename'])?></td>
                    </tr>
                    <tr>
                        <? $country = $profile['countrycode']; ?>
                        <? foreach($countries as $item) { ?>
                            <? if($profile['countrycode']==$item['id']) $country = $item['Name']; ?>
                        <? } ?>
                        <td><strong><?=LangAdmin::get('country')?></strong></td>
                        <td><?=$country?></td>
                    </tr>
                    <tr>
                        <td><strong><?=LangAdmin::get('city')?></strong></td>
                        <td><?=$this->escape($profile['city'])?></td>
                    </tr>
                    <tr>
                        <td><strong><?=LangAdmin::get('address')?></strong></td>
                        <td><?=$this->escape($profile['address'])?></td>
                    </tr>
                    <tr>
                        <td><strong><?=LangAdmin::get('zip_code')?></strong></td>
                        <td><?=$this->escape($profile['postalcode'])?></td>
                    </tr>
                    <tr>
                        <td><strong><?=LangAdmin::get('region')?></strong></td>
                        <td><?=$this->escape($profile['region'])?></td>
                    </tr>
                    <tr>
                        <td><strong><?=LangAdmin::get('phone')?></strong></td>
                        <td><?=$this->escape($profile['Phone'])?></td>
                    </tr>
                    <? if (in_array('PassportData', General::$enabledFeatures)) { ?>
                        <tr>
                            <td><strong><?=LangAdmin::get('passport')?></strong></td>
                            <td><?=$this->escape($profile['passportnumber'])?></td>
                        </tr>
                        <tr>
                            <td><strong><?=LangAdmin::get('registrationaddress')?></strong></td>
                            <td><?=$this->escape($profile['registrationaddress'])?></td>
                        </tr>
                    <? } ?>
                    </tbody>
                <? } ?>
            </table>
        <? } else { ?>
            <h3 class="lgray tagc mt10"> <?=LangAdmin::get('empty_list')?> </h3>
        <? } ?>

    <? } else { ?>
        <br/><strong><?=LangAdmin::get('User_not_found')?>!</strong><br/>
    <? } ?>
</div>
<br clear="all"/>
