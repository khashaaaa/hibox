<?php

class Logger
{
    /**
     * @var CMS
     */
    protected $cms;

    /**
     * @param CMS $cms
     */
    public function __construct($cms){
        $this->cms = $cms;
    }

    public function log($message, $parameters = array(), $type="DEBUG"){
        $message .= "\nParameters: " . print_r($parameters, 1);
        $messageSafe = $this->cms->escape($message);
        $typeSafe = $this->cms->escape($type);
        $added = time();
        $this->cms->query("
            INSERT INTO `site_logs`
            SET `log_type` = '$typeSafe', `added` = $added, `text` = '$messageSafe'
        ", array('site_logs'));
    }
}
