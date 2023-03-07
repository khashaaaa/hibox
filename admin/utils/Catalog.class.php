<?php
class Catalog extends GeneralUtil
{
    protected $_template = 'catalog';
    protected $_template_path = 'catalog/';

    public function __construct()
    {
        parent::__construct();
    }

    public function defaultAction($request)
    {
        $this->redirect('index.php?cmd=categories');
    }
}
