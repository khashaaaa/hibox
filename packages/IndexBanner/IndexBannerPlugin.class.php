<?php

OTBase::import('system.lib.GeneralPlugin');

class IndexBannerPlugin extends GeneralPlugin
{

    private $cms;

    /**
     * SmallBannersWidgetPlugin constructor.
     */
    public function __construct()
    {
        parent::__construct();
        LangAdmin::getTranslations(dirname(__FILE__) . '/langs/');

        $this->cms = new CMS();
    }

    /**
     * @param $request
     * @return bool|string
     */
    public function renderPluginPage($request)
    {
        $route = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
        switch ($route){
            case 'create':
                $this->create($request);
                break;
            case 'delete':
                $this->delete($request);
                break;
            default:
                $banners = $this->get_banners();
                return General::viewFetch('/view/renderPluginPage', array(
                    'path' => dirname(__FILE__),
                    'vars' => array(
                        'banners' => $banners
                    )
                ));
        }
    }

    /**
     * @return array
     */
    public function get_banners()
    {
        try {
            $sql = 'SELECT * from index_banner';

            $result = $this->cms->queryMakeArray($sql);

            return $result;
        } catch (ServiceException $e) {
            print_r($e);
        }
    }

    /**
     * @param $file
     * @return bool|mixed
     */
    public function can_upload($file)
    {
        if($file['name'] == '')
            return LangAdmin::get('you_have_not_selected_a_file');

        if($file['size'] == 0)
            return LangAdmin::get('file_size_too_large');

        $getMime = explode('.', $file['name']);
        $mime = strtolower(end($getMime));
        $types = array('jpg', 'png', 'gif', 'bmp', 'jpeg', 'svg');
        if(!in_array($mime, $types))
            return LangAdmin::get('invalid_file_type');
        return true;
    }

    /**
     * @param $file
     * @return string
     */
    public function make_upload($file)
    {
        $getMime = explode('.', $file['name']);
        $mime = strtolower(end($getMime));
        $name = time() . '.' . $mime;
        $destination_dir = dirname(__FILE__) .'/assets/images/' . $name;
        move_uploaded_file($file['tmp_name'], $destination_dir);
        return $name;
    }

    /**
     * @param $request
     * @return resource
     */

    public function delete($request)
    {
        try {
            $sql = 'DELETE FROM index_banner WHERE id = '.$request->getValue('id');
            $result = $this->cms->query($sql);
        }
        catch (Exception $e){
            print_r($e);
        }
        header("Location: /admin/?cmd=pluginsutil&do=view&plugin=IndexBanner");
        exit;
    }

    /**
     * @param $request
     */

    public function create($request)
    {

        $file = $_FILES['image'];

        try{
            $check = $this->can_upload($file);
            if($check === true){
                $images = $this->make_upload($file);

                $sql = 'insert into `index_banner` (`name`, `link`, `filename`, `type`) values ("'.$request->getValue('name').'", "'.$request->getValue('link').'", "'.$images.'", "'.$request->getValue('type').'")';

                $result = $this->cms->query($sql);

                $_SESSION['success'] = LangAdmin::get('banner_created');
            } else {
                $_SESSION['error'] = LangAdmin::get('invalid_file_type');
            }
        }
        catch (Exception $e){
            print_r($e);
        }
        header("Location: /admin/?cmd=pluginsutil&do=view&plugin=IndexBanner");
        exit;
    }
    
    /**
     * @param array $vars
     * @throws Exception
     */
    public static function onAfterGeneralInit($vars = array())
    {
        OTBase::import('system.packages.IndexBanner.controllers.*');
    }
}
