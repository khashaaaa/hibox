<?php

// Запоминаем время начала генерации страницы
$GLOBALS['script_start_time'] = microtime(true);
$GLOBALS['trace'] = array();

// Подключаем файл с паролями от сервиса
require_once('config.php');
// Подключаем конфигурационный файл
require_once('config/config.php');

session_cache_expire(60*24);

// Инициализируем обработчик ошибок
ErrorHandler::init();

General::init();

if(!defined('CFG_CACHED')){
    define('CFG_CACHED', false);
}

if(file_exists(dirname(__FILE__).'/custom/custom.php')){
    require dirname(__FILE__).'/custom/custom.php';
}

switch(SCRIPT_NAME)
{
    case Plugins::onAddScriptProcessorCheck(SCRIPT_NAME, ''):
        $CFG_CREATE_BLOCKS = Plugins::onAddScriptProcessor(SCRIPT_NAME, '');
        break;
    case Plugins::onAddScriptProcessorCheck(SCRIPT_NAME, '_custom'):
        $CFG_CREATE_BLOCKS = Plugins::onAddScriptProcessor(SCRIPT_NAME, '_custom');
        break;

}

//===================================================
global $HSTemplate;
$HSTemplate->assignGlobal('script_name', SCRIPT_NAME);

class ShowPage extends GenerateBlock
{
    protected $_cache           = false; //- кэшируем или нет.
    protected $_life_time       = 30; //- время на которое будем кешировать.
    public    $_template        = 'index'; //- шаблон, на основе которого будем собирать блок.
    public    $_template_path   = '/';

    public function __construct()
    {
        parent::__construct();
    }

    protected function setVars()
    {
        // Шаблон страницы
        if (defined('CFG_PAGE_TEMPLATE'))
        {
            $this->_template = CFG_PAGE_TEMPLATE;
        }
        // Генерируем блоки
        if ((isset($GLOBALS['CFG_CREATE_BLOCKS'])) && (is_array($GLOBALS['CFG_CREATE_BLOCKS'])))
        {
            foreach ($GLOBALS['CFG_CREATE_BLOCKS'] as $class)
            {
                $block = new $class();
                $data = '';
                try{
                    $data = $block->Generate();
                }
                catch(Exception $e){
                    show_error($e->getMessage());
                }
                $this->tpl->assign($class, $data);
            }
        }
    }

    public function Show()
    {
        print parent::Generate();
    }
}


// Отображаем страницу
$show = new ShowPage();
$show->Show();

?>
