<?php

class ContentMenu extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'contentvenunew'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/main/';

    /**
     * @var cms
     */
    protected $cms;
    protected $digest;

    public function __construct()
    {
        parent::__construct(true);
        $this->cms = new CMS();
        $this->digest = new DigestRepository($this->cms);
    }

    protected function setVars()
    {
        if ($this->cms->Check()) {
            try {
				$cRep = new ContentRepository($this->cms);
                $thisPage = $cRep->GetPageByAlias($this->request->getValue('p'));
                // получить все левое меню
                $left_menu_json = $this->cms->getBlock('left_menu_'.Session::get('active_lang'));
                $left_menu_ids = $left_menu_json ? json_decode($left_menu_json) : array();
                $left_menu_ids = CMS::removeNotAvailableMenuItems($left_menu_ids);
                $left_menu = array();
                foreach ($left_menu_ids as $pid) {
                    $isContentPage = is_numeric($pid);
                    $pageCurrent = $isContentPage ? $cRep->GetPageByID($pid) : array('alias'=>$pid,'title'=>Lang::get($pid));

                    // если по умолчанию OR текущая страница == текущий раздел  OR родитель страницы == текущий раздел
                    if ((General::getConfigValue('left_menu_mode_for_content', 2) == 1) or
                            ($pid == $thisPage['id']) or
                            ($pid == $this->cms->get_parent_id_site_pages_parents_page_id($thisPage['id']))) {
                        $pageCurrent['children'] = $this->getChildrenPages($pid);
                    } else {
                        $pageCurrent['children'] = Array();
                    }

                    if ($pid == 'digest') {
                        $allCats = $this->digest->GetAllDigestCategories(Session::get('active_lang'));
                        foreach ($allCats as $cat) {
                        $tmp['alias'] = $cat['cid'];
                            $tmp['title'] = $cat['title'] . ' ('. $cat['count'] .')' ;
                            $tmp['description'] = $cat['description'];
                            $pageCurrent['children'][] = $tmp;
                        }
                    }
                    $left_menu[] = $pageCurrent;
                }
                $this->tpl->assign('left_menu', $left_menu);
                $this->tpl->assign('cur_page', $this->request->getValue('p'));
                $this->tpl->assign('cur_page_digest', $this->request->getValue('cat'));
            } catch (DBException $e) {
                Session::setError($e->getMessage(), 'DBError');
            }

        }
        $this->tpl->assign('status', $this->cms->Check());
    }

    public function getChildrenPages($id)
    {
        if ($this->cms->Check()) {
            try {
				$cRep = new ContentRepository($this->cms);
                $this->cms->checkTable('site_pages_parents');
                $q = $this->cms->query('SELECT * FROM `site_pages_parents` WHERE `parent_id`="' . $id . '" ORDER BY `page_id`');
                $children = array();
                if(!$q){
                    show_error(mysqli_error($this->cms->getLink()));
                    return;
                }
                if(!mysqli_num_rows($q))
                    return array();
                while($row = mysqli_fetch_assoc($q)){
                    $children[] = $cRep->GetPageByID($row['page_id']);
                }
                return array_filter($children);
            } catch (DBException $e) {
                Session::setError($e->getMessage(), 'DBError');
            }
        } else {
            return;
        }

    }

}
