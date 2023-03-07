<?php

class SupportProvider
{
    /**
     * @var OTAPIlib
     */
    private $otapilib;

    public function __construct($otapilib)
    {
        $this->otapilib = $otapilib;
    }

    public function GetTicketCatogories()
    {
        return $this->otapilib->GetTicketCatogories();
    }    
}