<?php

class RandomSetNew extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'randomsetnew'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/main/';

    public function __construct()
    {
        parent::__construct(true);
    }

    protected function setVars()
    {
        global $otapilib;
        // получаем количество выводимых пунктов из установок сайта или по умолчанию
        $numberItem = 16;
        
        if (isset($_GET['set1'])) {
            $cat_id = 'set1';
        } elseif (isset($_GET['set2'])) {
            $cat_id = 'set2';
        } elseif (isset($_GET['set3'])) {
            $cat_id = 'set3';
        } elseif (isset($_GET['set4'])) {
            $cat_id = 'set4';
        } else {
            $cat_id = 'set1';
        }
        
        // инициализируем массивы
        $recommend = Array();
        
        // С мультипотоками
        if (0) {
            // Инициализируем
            $otapilib->InitMulti();
            
            // Суём задачи в потоки
            if ($numberItem) {
                $recommend = $otapilib->GetItemRatingList('Recommend', $numberItemt, $cat_id);
            }
            // Делаем запросы
            $otapilib->MultiDo();

            // В том же порядке забираем результаты
            if ($numberItem) {
                $recommend = $otapilib->GetItemRatingList('Recommend', $numberItem, $cat_id);
            }

            // Сбрасываем
            $otapilib->StopMulti();
        } else {
            // По старому
            if ($numberItem) {
                $recommend = $otapilib->GetItemRatingList('Recommend', $numberItem, $cat_id);

            }
        }

        $this->tpl->assign('itemlist', $recommend);
    }
}

?>