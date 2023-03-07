<?php
/*
 * <?=LangAdmin::get('the_class_forms_a_collection_of_goods')?>
 * 1 -  Best
 * 2 - Popular
 * 3 - Recommend
 */

include dirname(__FILE__).'/GeneralUtil.class.php';

class Set extends GeneralUtil {
    
    function defaultAction()
    {
        if (Login::auth())
        {            
            include(TPL_DIR.'sets.php');
            if(isset($_SESSION['error'])) unset($_SESSION['error']);
        } else {
            include(TPL_DIR.'login.php');
        } 
    }
}