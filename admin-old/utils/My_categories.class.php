<?php

class My_categories
{
	private $categories = array();

    function defaultAction(){
        global $otapilib;

        if (!Login::auth()){
            include(TPL_DIR.'login.php');
            return false;
        }

        $cms = new CMS();
        if (!$cms->Check()){
            include(TPL_DIR . 'db_connection_fail.php');
            die;
        }

        $cms->checkTable('my_categories');

        $category = $cms->GetCategoryById();
		$this->_getCategories($category);
		$categories = $this->categories;

        $current_lang = $this->setActiveLang();
        $translations = $cms->getTranslations('', $current_lang);
        include(TPL_DIR . 'category/index.php');

    }

	private function _getCategories($data)
	{
        foreach ($data as $row) {
            $this->categories[$row['parent_id']][] = $row;
        }
	}

	private function _htmlTree($parent_id=0, $level=0)
	{
        if (isset($this->categories[$parent_id])) {
            foreach ($this->categories[$parent_id] as $values) {
                echo	'<tr>',
						'<td width="20">'.$values['id'].'</td>',
						'<td width="20">'.$values['parent_id'].'</td>',
						'<td width="120" style="padding-left:'.($level*25).'px;">'.$values['name'].'</td>',
						'<td>'.$values['description'].'</td>',
						'<td width="190"><a href="?cmd=my_categories&do=add_category&pid='.$values['id'].'">'.LangAdmin::get('add_a_subcategory').'</a> <a href="?cmd=my_categories&do=edit_category&id='.$values['id'].'">'.LangAdmin::get('edit').'</a><a class="del" href="javascript:;" item="'.$values['id'].'" >'.LangAdmin::get('remove').'</a></td>',
						'</tr>';

                $level++;
                $this->_htmlTree($values['id'], $level);
                $level--;
            }
        }
	}

    private function setActiveLang(){
        if(@$_GET['lang'])
            $_SESSION['translate_lang'] = @$_GET['lang'];
        if(!@$_SESSION['translate_lang']){
            $_SESSION['translate_lang'] = 'en';
        }
        return $_SESSION['translate_lang'];
    }

    public function add_categoryAction()
    {
        if (Login::auth()) {
            global $otapilib;
            $sid = $_SESSION['sid'];
            $userid = @$_GET['id'];

            $result = $otapilib->GetWebUISettings($sid);
            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }

            include(TPL_DIR . 'category/edit.php');
        } else {
            include(TPL_DIR . 'login.php');
        }
    }

    public function saveAction()
    {
        if (Login::auth()) {
            global $otapilib;
            $sid = $_SESSION['sid'];

            if ($otapilib->error_message == 'SessionExpired') {
                header('Location: index.php?expired');
                die;
            }

            $cms = new CMS();
            $status = $cms->Check();

            if ($status)
            {
                $cms->AddOrUpdateMyCategory();
            }
            header('Location:index.php?cmd=my_categories');

        } else {
            include(TPL_DIR . 'login.php');
        }
    }

    public function edit_categoryAction()
    {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired');
            die;
        }
        $cms = new CMS();
        $status = $cms->Check();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($status && $id)
        {
            $categories = $cms->GetCategoryById($id);

        } else {
            include(TPL_DIR . 'index.php');
            die;
        }

        include(TPL_DIR . 'category/edit.php');
    }

    public function del_categoryAction()
    {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired');
            die;
        }
        $cms = new CMS();
        $status = $cms->Check();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : NULL;
		try {
			$result = $cms->DeleteRow('my_categories', $id);
		} catch (Exception $e) {
			echo $e->getMessage();
		}
        if ($result) echo 'Ok';
        die;
	}
}
