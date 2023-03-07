<?php

abstract class Mapper
{
    protected $entityType;

    protected $tableName;

    protected $columnsMap = array();

    protected $defaultSort = 'id ASC';

    /**
     * @var CMS
     */
    protected $cms;

    /**
     * @param CMS $cms
     */
    public function __construct($cms)
    {
        $this->cms = $cms;
    }

    public function findById($id)
    {
        $id = intval($id);
        $result = $this->cms->queryMakeArray("SELECT * FROM {$this->tableName} WHERE id=$id", array($this->tableName));
        if (!$result)
            return null;

        return $this->createEntityFromData($result[0]);
    }

    public function findLimited($offset, $count, $filter = array())
    {
        $where = $this->createWhere($filter);
        $offset = intval($offset);
        $count = intval($count);
        $list = $this->cms->queryMakeArray("SELECT * FROM {$this->tableName} $where ORDER BY {$this->defaultSort} LIMIT {$offset},{$count}", array($this->tableName));
        return $this->createEntitiesFromData($list);
    }

    public function findAll()
    {
        $list = $this->cms->queryMakeArray("SELECT * FROM {$this->tableName}", array($this->tableName));
        return $this->createEntitiesFromData($list);
    }

    public function getCount($filter = array())
    {
        $where = $this->createWhere($filter);
        $count = $this->cms->querySingleValue("SELECT COUNT(*) FROM {$this->tableName} $where", array($this->tableName));
        return $count;
    }

    private function createWhere($filter)
    {
        $where = array();
        foreach ($filter as $name => $value) {
            if (empty($value)) {
                continue;
            }
            $value = $this->cms->escape($value);
            $where[] = "$name='$value'";
        }
        if ($where) {
            $where = 'WHERE ' . implode(' AND ', $where);
        } else {
            $where = '';
        }
        return $where;
    }

    public function createEntityFromData($data = array())
    {
        $className = $this->entityType;
        $entity = new $className();

        foreach ($data as $name => $value) {
            $fieldName = $this->columnsMap[$name];

            $fieldNameParts = explode('_', $fieldName);
            $setterNameParts = array_map('ucfirst', $fieldNameParts);
            $setterName = 'set' . implode($setterNameParts);

            if (method_exists($entity, $setterName)) {
                $entity->{$setterName}($value);
            } else {
                $entity->{$fieldName} = $value;
            }
        }

        return $entity;
    }

    public function createEntitiesFromData($data = array())
    {
        $result = array();
        foreach ($data as $entityData) {
            $result[] = $this->createEntityFromData($entityData);
        }
        return $result;
    }

    public function remove($id)
    {
        $id = intval($id);
        $this->cms->query("DELETE FROM {$this->tableName} WHERE id=$id", array($this->tableName));
    }

    /**
     * @param Entity $entity
     */
    public function save(&$entity)
    {
        if ($entity->getId() == null) {
            $this->createNew($entity);
            $entity = $this->findById($this->cms->insertedId());
        } else {
            $this->update($entity);
        }
    }

    abstract protected function createNew($entity);

    abstract protected function update($entity);
}