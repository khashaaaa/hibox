<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Программист
 * Date: 13.09.13
 * Time: 7:56
 * To change this template use File | Settings | File Templates.
 */

class BaseElement {
    /**
     * @var SimpleXMLElement
     */
    protected $xmlData;
    /**
     * @param SimpleXMLElement $xmlData
     */
    public function __construct($xmlData){
        $this->xmlData = $xmlData;
    }
}