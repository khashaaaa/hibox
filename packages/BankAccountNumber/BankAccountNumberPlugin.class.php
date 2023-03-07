<?php

OTBase::import('system.lib.GeneralPlugin');

class BankAccountNumberPlugin extends GeneralPlugin
{

    /**
     * @param array $vars
     * @throws Exception
     */
    public static function onAfterGeneralInit($vars = array())
    {
        OTBase::import('system.packages.BankAccountNumber.controllers.*');
    }
}