<?php


class CronHelper
{
    /**
     * @var int TTL: время жизни кэша по умолчанию 1 час (60*60)
     */
    CONST TTL = 3600;
    /**
     * @var int INCREASE_TTL: завышенное время жизни кэша 7 дней (60*60*24*7);
     */
    CONST INCREASE_TTL = 604800;

    private $defaultOptions = array(
        'defaultTtl' => self::TTL,
        'increaseTtl' => self::INCREASE_TTL
    );

    private $cms;
    private $fileMysqlMemoryCache;
    private static $_object = null;

    public static function getObject()
    {
        // проверяем актуальность экземпляра
        if (null === self::$_object) {
            // создаем новый экземпляр
            self::$_object = new self();
        }
        // возвращаем созданный или существующий экземпляр
        return self::$_object;
    }

    private function __construct()
    {
        $this->cms = General::getCms();
        $this->cms->Check();

        $this->fileMysqlMemoryCache = new FileAndMysqlMemoryCache($this->cms);
    }
    private function __wakeup(){}
    private function __clone(){}

    /**
     * Основывая на дате кэша указывает нужно ли обновить кэш
     * элемента $cacheKey в зависимости от $realTime.
     *
     * @param string $cacheKey - ключ кэша
     * @param bool $realTime - признак проверки в реальном времени или в завышенном
     * @param array $options - позволяет изменять параметры ttl по умолчанию
     * @return bool
     */
    public function needUpdate($cacheKey, $realTime, $options = array())
    {
        $options = array_merge($this->defaultOptions, $options);

        if ($this->fileMysqlMemoryCache->Exists($cacheKey)) {
            if ($realTime) {
                $expireDate = $this->fileMysqlMemoryCache->getExpireDate($cacheKey);
                $realExpireDate = $expireDate - $options['increaseTtl'];
                $needUpdate = ($realExpireDate - time()) < 0 ? true : false;
            } else {
                $needUpdate = false;
            }
        } else {
            $needUpdate = true;
        }
        return $needUpdate;
    }
}