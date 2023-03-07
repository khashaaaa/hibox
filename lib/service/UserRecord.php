<?php

OTBase::import('system.lib.service.ServiceRecord');

class UserRecord extends ServiceRecord
{
    private $displayName;

    public function getDisplayName()
    {
        if (is_null($this->displayName)) {
            // Если у нас есть имя, отчество и фамилия - выводим их.
            if (mb_strlen($this->firstname) && mb_strlen($this->MiddleName) && mb_strlen($this->lastname)) {
                if (InstanceProvider::getObject()->GetProfileFieldState('MiddleName') !== "Disabled") {
                    $userFIO = array($this->lastname, $this->firstname, $this->MiddleName);
                } else {
                    $userFIO = array($this->lastname, $this->firstname);
                }
                $this->displayName = implode(' ', $userFIO);
            }
            // Если есть имя и фамилия, выводим их
            elseif (mb_strlen($this->firstname) && mb_strlen($this->lastname)) {
                $userFIO = array($this->lastname, $this->firstname);
                $this->displayName = implode(' ', $userFIO);
            }
            // Если есть имя и отчество, выводим их
            elseif (mb_strlen($this->firstname) && mb_strlen($this->MiddleName)) {
                 if (InstanceProvider::getObject()->GetProfileFieldState('MiddleName') !== "Disabled") {
                    $userFIO = array($this->firstname, $this->MiddleName);
                } else {
                    $userFIO = array($this->firstname);
                }
                $this->displayName = implode(' ', $userFIO);
            }
            // Если есть имя, выводим его
            elseif (mb_strlen($this->firstname)) {
                $this->displayName = $this->firstname;
            }
            // Если есть фамилия, выводим ее
            elseif (mb_strlen($this->lastname)) {
                $this->displayName = $this->lastname;
            }
            // Если ничего другого не остается, выводим login
            else {
                $this->displayName = !mb_strpos($this->login, '@') ? $this->login
                    : mb_substr($this->login, 0, mb_strpos($this->login, '@'));
            }
        }
        return $this->displayName;
    }

    public function asArray()
    {
        return array_merge(parent::asArray(), array(
            'displayName'   => $this->getDisplayName(),
        ));
    }
}
