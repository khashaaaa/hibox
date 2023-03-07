<?php

abstract class Repository
{
    /**
     * @var CMS
     */
    protected $cms;

    /**
     * @var Mapper
     */
    protected $mapper = null;

    protected $tableName = null;

    public function __construct($cms, $mapper = null, $tableName = null)
    {
        $this->cms = $cms;
        $this->mapper = $mapper;
        $this->tableName = $tableName;
    }
}
