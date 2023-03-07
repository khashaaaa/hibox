<?php

class PromoProvider {
    /**
     * @var OTAPIlib
     */
    private $otapilib;

    function __construct($otapilib){
        $this->otapilib = $otapilib;
    }
}
