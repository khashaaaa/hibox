<?php


class Application
{
    private $router;

    public function __construct()
    {
        // Инициализируем обработчик ошибок
        ErrorHandler::init();

        // Запускаем инициализацию основных настроек приложения
        $this->init();

        $this->router = Router::getInstance();
    }

    public function run()
    {
        ob_start();

        $request = new RequestWrapper();
        list($controller, $action, $parameters) = $this->router->resolve($request);

        // подгрузка старого фронконтроллера при необходимости
        if (! General::IsNewPlatform($controller, $action)) {
            require_once('index_old.php');
            die();
        }

        if (General::isTechnicalWorks() && !Session::get('sid')) {
            RequestWrapper::LocationRedirect('/?p=site_unavailable');
        }

        $result = General::runController($controller, $action, $parameters);

        Plugins::runSerialEvent('onGeneralRunApplication', array(
            'page' => $result
        ));

        print $result;

        // статистика и дебаг
        General::storeRequestGroup();
        if (! RequestWrapper::isAjax()) {
            if (OTBase::isTest()) {
                print Debugger::getRender();
            }
        }

        $result = ob_get_clean();
        print $result;
    }

    private function init()
    {
        Session::enableAutoClose();
        General::init();
    }

    public function somethingWrong($e) {
        ErrorHandler::somethingWrong($e);
    }
}