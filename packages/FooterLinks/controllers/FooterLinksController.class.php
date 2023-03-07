<?php

class FooterLinksController extends FooterController
{
    public function renderLinksAction()
    {
        $lang = Session::getActiveLang();
        $menu = $this->getBlocksName($lang);
        return General::viewFetch('view/renderFooterLinks', array(
            'path' => dirname(dirname(__FILE__)),
            'vars' => array(
                'footerMenu' => $menu
            )));
    }
    
    /**
     * @param $lang
     * @return array
     */
    public function getBlocksName($lang)
    {
        try{
            $data = [];
            $i = 0;
            foreach($this->cms->query("SELECT * FROM footer_menu WHERE lang = '$lang'") as $row) {
                $data[] = $row;
                $data[$i]['child'] = $this->getLinks($row['id']);
                $i++;
            }
            return $data;
        }
        catch (Exception $e){
            print_r($e);
        }
    }

    /**
     * @param $parent
     * @return array
     */
    public function getLinks($parent)
    {
        try{
            $data = [];
            foreach($this->cms->query("SELECT * FROM footer_menu_link WHERE parent = '$parent'") as $row) {
                $data[] = $row;
            }

            return $data;
        }
        catch (Exception $e){
            print_r($e);
        }
    }
}