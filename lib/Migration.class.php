<?php

abstract class Migration
{
    protected $name;

    /**
     * @var CMS
     */
    protected $cms;

    public function __construct($cms)
    {
        $this->name = get_class($this);
        $this->cms = $cms;
        $this->cms->Check();
        $this->cms->checkTable('migration');
    }

    abstract public function up();

    public function success()
    {
        $this->cms->query('INSERT INTO `migration` SET apply_time = "' . date('Y-m-d H:i:s') . '", name = "' . $this->name . '"'
         . ' ON DUPLICATE KEY UPDATE apply_time = "' . date('Y-m-d H:i:s') . '"');
    }

    public function isReady()
    {
        $sql = 'SELECT * FROM `migration` WHERE `name`="'.$this->name.'"';
        $r = $this->cms->query($sql);
        if ($r && mysqli_num_rows($r)) {
            return mysqli_fetch_assoc($r);
        }
        return false;
    }
}
