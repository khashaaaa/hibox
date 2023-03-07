<?php

class SubCategory2New extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'subcategorylist2new'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/main/';

    public function __construct()
    {
		
        parent::__construct(true);
    }

    protected function setVars()
    {
		
        // Получаем субкатегории
        $cid = RequestWrapper::getValueSafe('cid');
        global $otapilib;
        $subcats = $otapilib->GetCategorySubcategoryInfoList($cid);
        //if ($subcats === false) show_error(__FILE__.'  (line='.__LINE__.')');
        if ($subcats === false) show_error($otapilib->error_message);
        $GLOBALS['cats'] = $subcats;
        $this->tpl->assign('subcats', $subcats);
        if (count($subcats)>0) $GLOBALS['no_search_props'] = true;
    }
}

?>