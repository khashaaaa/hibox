<?php
class MemoryCacheRepository extends Repository 
{
    public function Add($key, $cacheEntity, $lifeTime)
    {
        $key = $this->cms->escape($key);
        $this->cms->query("REPLACE INTO `memory_cache` SET `session_id` = '$key', `cache_entity` = '$cacheEntity',
            `expires` = " . (time()+$lifeTime));
    }

    public function getOldestRecords($count)
    {
        $oldFiles = $this->cms->queryMakeArray("SELECT * FROM `memory_cache` WHERE `expires` <= " . time() . ' ORDER BY `expires` LIMIT ' . $count);
        return $oldFiles;
    }

    public function deleteRecords($ids)
    {
        if (! empty($ids)) {
            $escapeIds = array();
            foreach ($ids as $id) {
                $escapeIds[] = '"' . $this->cms->escape($id) . '"';
            }
            $ids = implode(',', $escapeIds);
            $this->cms->query('DELETE FROM `memory_cache` WHERE `id` in (' . $ids .')');
        }
    }

    public function IsValid($key, $cacheEntity)
    {
        $key = $this->cms->escape($key);
        $cacheEntity = $this->cms->escape($cacheEntity);
        $isValid = $this->cms->querySingleValue('SELECT COUNT(*) FROM `memory_cache` '
            . ' WHERE `session_id` = "' . $key . '" AND `cache_entity` = "' . $cacheEntity .'"'
            . ' AND `expires` > '  . time());
        return $isValid;
    }
    
    /**
     * Продлевает время жизни кэша
     */
    public function updateLifeTime($key, $cacheEntity, $ttl = 600)
    {
        $key = $this->cms->escape($key);
        $cacheEntity = $this->cms->escape($cacheEntity);
        $this->cms->query('UPDATE `memory_cache` SET `expires` = "' . (time() + $ttl) . '"'
            . ' WHERE `session_id` = "' . $key . '" AND `cache_entity` = "' . $cacheEntity .'"');
    }

    public function getExpireDate($key, $cacheEntity)
    {
        $key = $this->cms->escape($key);
        $cacheEntity = $this->cms->escape($cacheEntity);
        $dateTime = $this->cms->querySingleValue('SELECT `expires` FROM `memory_cache` '
            . ' WHERE `session_id` = "' . $key . '" AND `cache_entity` = "' . $cacheEntity .'"');
        return $dateTime;
    }
}
