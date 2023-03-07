<?php

class FileAndMysqlMemoryCache implements ICache {
    /**
     * @var CMS
     */
    protected $cms;

    /**
     * @var MemoryCacheRepository
     */
    protected $memoryCache;

    /**
     * @param CMS $cms
     */
    public function __construct($cms){
        $this->cms = $cms;
        $this->cms->checkTable('memory_cache');

        $this->memoryCache = new MemoryCacheRepository($this->cms);
    }
    
    public function clearCache($count = 100)
    {
        $oldCacheFiles = $this->memoryCache->getOldestRecords($count);
        $toClear = array();
        foreach($oldCacheFiles as $f){
            $result = $this->DelCacheEl($f['cache_entity'].':' . $f['session_id']);
            if ($result) {
                $toClear[] = $f['id'];
            }
        }
        if (! empty($toClear)) {
            $this->memoryCache->deleteRecords($toClear);
        }
    }

    public function AddCacheEl($key, $life_time = 21600, $value){
        list($cacheDir, $cacheFile, $realKey, $cacheEntity) = $this->GetCacheDirAndFileNameFromKey($key);

        $createDirResult = !file_exists($cacheDir) ? mkdir($cacheDir, 0777, true) : true;
        if(!$createDirResult) {
            throw new Exception('Cache dir was not created');
        }

        file_put_contents($cacheDir . '/' . $cacheFile, $value);

        $this->memoryCache->Add($realKey, $cacheEntity, $life_time);
    }

    public function DelCacheEl($key)
    {
        list($cacheDir, $cacheFile) = $this->GetCacheDirAndFileNameFromKey($key);
        if (file_exists($cacheDir . '/' . $cacheFile)) {
            // TODO: убрать собачку, после того как унесем очистку кеша в фоновый режим
            @unlink($cacheDir . '/' . $cacheFile);
        }
        if (file_exists($cacheDir) && count(scandir($cacheDir)) == 2) {
            rmdir($cacheDir);
        }
        clearstatcache(); //Clear PHP file states cache to get actual file_exists result
        return ! (file_exists($cacheDir . '/' . $cacheFile));
    }

    public function GetCacheEl($key)
    {
        list($cacheDir, $cacheFile) = $this->GetCacheDirAndFileNameFromKey($key);
        return file_get_contents($cacheDir . '/' . $cacheFile);
    }

    public function Exists($key){
        list($cacheDir, $cacheFile, $realKey, $cacheEntity) = $this->GetCacheDirAndFileNameFromKey($key);
        return file_exists($cacheDir . '/' . $cacheFile) && $this->memoryCache->IsValid($realKey, $cacheEntity);
    }

    public function getExpireDate($key)
    {
        list($cacheDir, $cacheFile, $realKey, $cacheEntity) = $this->GetCacheDirAndFileNameFromKey($key);
        return $this->memoryCache->getExpireDate($realKey, $cacheEntity);
    }

    private function GetCacheDirAndFileNameFromKey($key)
    {
        $parts = explode(':', $key);
        $directory = $parts[0];
        if (defined('CFG_SPLIT_CACHE_BY_INSTANCEKEY')) { // если необходимо разделить кеш по INSTANCEKEY
            $cacheKey = implode(':', array_slice($parts, 1)) . substr(md5(CFG_SERVICE_INSTANCEKEY), 0, 8);
        } elseif (defined('CFG_SPLIT_CACHE_BY_DOMAIN')) { // если необходимо разделить кеш по HTTP_HOST
            $cacheKey = implode(':', array_slice($parts, 1)) . substr(md5($_SERVER['HTTP_HOST']), 0, 8);
        } else {
            $cacheKey = implode(':', array_slice($parts, 1));
        }

        $cacheKey = mb_strlen($cacheKey) > 50 ? md5($cacheKey) : $cacheKey;
        $cacheKeyHash = md5($cacheKey);

        $cacheDir = CFG_APP_ROOT . '/cache/' . $directory . '/' . substr($cacheKeyHash, 0, 2) . '/' . substr($cacheKeyHash, 2, 2);

        return array(
            $cacheDir,
            $cacheKeyHash . '.dat',
            $cacheKey,
            $directory
        );
    }

    /**
     * Продлеваем время жизни кэша
     */
    public function updateLifeTime($key, $ttl = 600)
    {
        list($cacheDir, $cacheFile, $realKey, $cacheEntity) = $this->GetCacheDirAndFileNameFromKey($key);
        return file_exists($cacheDir . '/' . $cacheFile) && $this->memoryCache->updateLifeTime($realKey, $cacheEntity, $ttl);
    }
}