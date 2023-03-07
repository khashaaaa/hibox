<?php
OTBase::import('system.lib.CMS');
OTBase::import('system.lib.General');
OTBase::import('system.lib.referral_system.lib.ReferralCategoryManager');
OTBase::import('system.lib.referral_system.lib.ReferralUserManager');
OTBase::import('system.lib.referral_system.lib.ReferralUser');

class ReferallUpdateUserParent {

    public static function updateUsers($dueTime) {
        $usersManager = new ReferralUserManager(new CMS());
        //$dueTime - дни
        //Что сравнивать с time надо перевести в секунды = кол-во дней * 24 часа * 60 минут * 60 секунд
        $usersManager->updateUserByDueTime($dueTime * 24 * 60 * 60);
    }
}