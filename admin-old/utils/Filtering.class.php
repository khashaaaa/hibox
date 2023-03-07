<?php
/*
 * Класс для фильтрации контента
 * 
 */
class Filtering {

    const CONTENT_TYPE_ITEM       = 'Item';
    const CONTENT_TYPE_CATEGORY   = 'Category';
    const CONTENT_TYPE_VENDOR     = 'Vendor';
    const CONTENT_TYPE_STRING     = 'SearchString';
    const CONTENT_TYPE_BRAND      = 'Brand';

    public function defaultAction() {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired');
            die;
        }
        $types = $otapilib->GetContentTypes($sid);
        $cats = $otapilib->GetRootCategoryInfoList();
        $hiddenstat = 0;
        $list = $otapilib->GetBlackListContents($sid);
        //var_dump($list);
        $blacklist = array();
        foreach ($list as $item) {
            $new_item = array();
            $type = $item['ContentType'];
            if ($type == self::CONTENT_TYPE_ITEM) {
                foreach ($item['content'] as $content) {
					if ($content) {
                    	$item1 = array();
                    	$item1['id'] = $content;
                    	$item1['info'] = $otapilib->GetItemFullInfo($item1['id']);
                    	$new_item[] = $item1;
					}
                }
            } elseif ($type == self::CONTENT_TYPE_CATEGORY) {
                foreach ($item['content'] as $content) {
					if ($content) {
                    	$item1 = array();
                    	$item1['id'] = $content;
                    	$item1['info'] = $otapilib->getCategoryInfo($item1['id']);
                    	$new_item[] = $item1;
					}
                }
            } elseif ($type == self::CONTENT_TYPE_VENDOR) {
                foreach ($item['content'] as $content) {
					if ($content) {
                    	$item1 = array();
                    	$item1['id'] = $content;
                    	$item1['info'] = $otapilib->getVendorInfo($item1['id']);
                    	$new_item[] = $item1;
					}
                }
            } elseif ($type == self::CONTENT_TYPE_STRING) {
                foreach ($item['content'] as $content) {
					if ($content) {
                    	$item1 = array();
                    	$item1['id'] = $content;
                    	$item1['info'] = $content;
                    	$new_item[] = $item1;
					}
                }
            } elseif ($type == self::CONTENT_TYPE_BRAND) {
                foreach ($item['content'] as $content) {
					if ($content) {
                    	$item1 = array();
                    	$item1['id'] = $content;
                    	$item1['info'] = $otapilib->getBrandInfo($item1['id']);
                    	$new_item[] = $item1;
					}
                }
            }
            $blacklist[(string)$item['ContentType']] = $new_item;
        }
        include(TPL_DIR . 'content_filtering/main.php');
    }
    
    public function editfiltersAction() {
        global $otapilib;
        $sid = @$_SESSION['sid'];
        $webui = $otapilib->GetWebUISettings($sid);
        if ($otapilib->error_message == 'SessionExpired' || $sid == '')
        {
            header('Location: index.php?expired');
            die;
        }
        
        $types = $otapilib->GetContentTypes($sid);
        $str = "<ArrayOfContentList>";
        foreach ($types as $type) {
            $type = (string)$type;
            $str .= "<ContentList ContentType='$type'>";
			if (isset($_POST[$type])) {
                foreach ($_POST[$type] as $item) {
                    $str .= "<Content>$item</Content>";
                }
			}
            $str .= "</ContentList>";
           
        }
        $str .= "</ArrayOfContentList>";
        
        $r = $otapilib->EditBlackListContents($sid, $str);
        header('Location: index.php?cmd=filtering');
    }
}

?>
