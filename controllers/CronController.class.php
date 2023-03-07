<?php

class CronController extends GeneralContoller
{
    private $lockDirectory = '';
    protected static $config = array(
        array('action' => 'sendNewsletters', 'period' => 1),
        array('action' => 'clearOldCache', 'period' => 1),
        array('action' => 'setsUpdate', 'period' => 1),
        array('action' => 'setReviewedItemsUpdate', 'period' => 1),
        array('action' => 'updateCommonInstanceOptionsInfo', 'period' => 60),
        array('action' => 'getPluginsInfo', 'period' => 1440), // 60 * 24
        array('action' => 'generateMenuCategories', 'period' => 1440), // 60 * 24
        array('action' => 'updateReferrals', 'period' => 43200), // 12 часов = 43200 секунд
    );

    public function __construct()
    {
        parent::__construct();

        Session::close();
        @ignore_user_abort(true);
        @ini_set('memory_limit', '-1');

        $this->lockDirectory = CFG_APP_ROOT . '/lock/';
        if (!file_exists($this->lockDirectory)) {
            mkdir($this->lockDirectory);
        }
    }

    public function defaultAction()
    {
        shuffle(self::$config);

        $executionJobs = 0;
        foreach (self::$config as $job) {
            @set_time_limit(300); // выделить 5 минут на выполнение каждой работы

            try {
                if (!$this->needRun($job)) {
                    continue;
                }

                $methodName = $job['action'] . 'Action';
                if (method_exists($this, $methodName)) {
                    // выполнить работу
                    $this->{$methodName}();
                    // обновить время последнего выполнения
                    $lastExecutionTime = time();
                    $this->setLastExecutionTime($job['action'], $lastExecutionTime);
                    $executionJobs++;
                } else {
                    throw new Exception(__CLASS__ . '::' . $methodName . ' not exists');
                }
            } catch (Exception $e) {
                if (OTBase::isTest()) {
                    $this->throwAjaxError($e);
                }
            }
        }
        if (OTBase::isTest() && $executionJobs > 0) {
            $this->sendAjaxResponse();
        }
    }

    private function getLastExecutionTime($action)
    {
        $lockFilePath = $this->getLockPath($action);
        $time = file_get_contents($lockFilePath);
        return !empty($time) ? $time : 0;
    }

    private function setLastExecutionTime($action, $time)
    {
        $lockFilePath = $this->getLockPath($action);
        file_put_contents($lockFilePath, $time);
    }

    private function needRun($job)
    {
        $action = $job['action'];

        // пропустить уже запущенные работы
        $lockFilePath = $this->getLockPath($action);
        if ($this->isRun($lockFilePath)) {
            return false;
        }

        // пропустить уже выполненные работы в выбранном периоде
        $lastExecutionTime = $this->getLastExecutionTime($action);
        $now = time();
        $period = $job['period'] * 60; // период в секундах
        if (($now - $lastExecutionTime) <= $period) {
            return false;
        }

        return true;
    }

    private function isRun($fileName)
    {
        $fp = fopen($fileName, 'c');

        // установить блокировку
        if (!flock($fp, LOCK_EX | LOCK_NB)) {
            fclose($fp);
            return true;
        }
        // регистрируем функцию shutdown, которая будет выполнена при завершении скрипта
        register_shutdown_function('CronController::removeLock', $fp);
        return false;
    }

    private function getLockPath($action)
    {
        $lockFilePath = $this->lockDirectory . $action;

        if (defined('CFG_SPLIT_CACHE_BY_INSTANCEKEY')) {
            $lockFilePath .= '_' . substr(md5(CFG_SERVICE_INSTANCEKEY), 0, 8);
        } elseif (defined('CFG_SPLIT_CACHE_BY_DOMAIN')) {
            $lockFilePath .= '_' . substr(md5($_SERVER['HTTP_HOST']), 0, 8);
        }

        return $lockFilePath . '.lock';
    }

    public static function removeLock($fp)
    {
        fclose($fp);
    }


    /***************************************************************************************
     ******************************** Cron methods(jobs) ***********************************
     ***************************************************************************************/

    /**
     * Новостная рассылка
     */
    protected function sendNewslettersAction()
    {
        if(CMS::IsFeatureEnabled('Newsletter')) {
            $N = new NewslettersSender();
            $N->sendQueue(
                General::getConfigValue('newsletter_per_send_limit', 5),
                General::getConfigValue('notification_send_from'),
                General::getConfigValue('notification_send_from_name', 'Newsletter')
            );
        }
    }

    /**
     * Пакетная очистка устаревшего кэша
     * @throws DBException
     */
    protected function clearOldCacheAction()
    {
        $cacher = new FileAndMysqlMemoryCache(General::getCms());
        $cacher->clearCache();
    }

    /**
     * Обновление кэша подборок
     * @throws Exception
     */
    protected function setsUpdateAction()
    {
        $language = Session::getActiveLang();
        $setsUpdater = SetsUpdater::getInstance();
        $setsUpdater->getData($language, array(), true);
    }

    /**
     * Обновление кэша подборки
     * товаров с отзывами
     * @throws Exception
     */
    protected function setReviewedItemsUpdateAction()
    {
        $language = Session::getActiveLang();
        $setsUpdater = SetsUpdater::getInstance();
        $setsUpdater->getReviewedItems($language, true);
    }

    public function updateCommonInstanceOptionsInfoAction()
    {
        $language = Session::getActiveLang();
        $instanceOptions = InstanceProvider::getObject()->updateCommonInstanceOptionsInfo($language);
    }

    public function getPluginsInfoAction()
    {
        $language = Session::getActiveLang();
        $plugins = Plugins::getPluginsInfo($language);
    }

    public function generateMenuCategoriesAction()
    {
        $categoriesMenuUpdater = CategoriesMenuUpdater::getInstance();
        $languages = InstanceProvider::getObject()->GetLanguageInfoList();

        foreach ($languages->GetContent()->GetItem() as $langObj) {
            $categoriesMenuUpdater->generateData($langObj->getName());
        }
    }

    public function updateReferralsAction()
    {
        try {
            SilentActions::updateRefferals();
        } catch (Exception $e) {
            if (OTBase::isTest()) {
                $this->throwAjaxError($e);
            }
        }
    }
}