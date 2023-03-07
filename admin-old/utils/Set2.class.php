<?php
/**
 * User: dima
 * Date: 08.12.12
 * Time: 9:42
 * Новый блок подборок товаров, продавцов, брендов и категорий
 */
class Set2 extends GeneralUtil{

    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'index'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = 'set2/'; //- путь к шаблону
    protected $tpl;

    public function __construct(){
        global $otapilib;
        $otapilib->setErrorsAsExceptionsOn();

        parent::__construct();
        @define('NO_DEBUG', 1);
    }

    /**
     * Вывод главной страницы подборок
     * Сейчас на ней вывод избранных товаров
     * @param RequestWrapper|bool $request
     */
    public function defaultAction($request = false){
		$this->checkAuth();
        print $this->fetchTemplate();
    }


}