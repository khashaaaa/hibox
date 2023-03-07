<?php
class Shoptao{
    public static function onRenderFilterOrdersForm(){
        ob_start();
        require dirname(__FILE__).'/tpl/clients.shoptao.onRenderFilterOrdersForm.html';
        $c = ob_get_contents();
        ob_end_clean();
        return $c;
    }
}
