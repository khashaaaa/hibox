<?php

OTBase::import('system.lib.GeneralPlugin');

class FooterLinksPlugin extends GeneralPlugin
{
    private $cms;
    protected $contentsProvider;

    /**
     * FooterLinksPlugin constructor.
     */
    public function __construct() {
        parent::__construct(true);
        LangAdmin::getTranslations(dirname(__FILE__) . '/langs/');
        $this->cms = new CMS();
        //$this->contentsProvider = new ContentsProvider($this->cms, $this->getOtapilib());
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
            case 'addlink':
                $this->addLink($request);
                break;
            case 'edit':
                $this->edit($request);
                break;
            case 'delete':
                $this->delete($request);
                break;
            case 'delete_group':
                $this->deleteGroup($request);
                break;
            default:
                //$languages = $this->contentsProvider->GetLanguageInfoList();
                $block = $this->getBlocksName();
                return General::viewFetch('/view/renderPluginPage', array(
                    'path' => dirname(__FILE__),
                    'vars' => array(
                        'data' => $block,
                        'languages' => @$GLOBALS['langs']
                    )
                ));
        }
    }

    /**
     * @param $request
     */
    public function create($request) {
        $data = $_POST;

        try{
            $stmt = $this->db()->prepare("insert into footer_menu (title, lang) values (:title, :lang)");
            $stmt->execute(array(
                    'title' => $data['name'],
                    'lang' => $data['lang']
                )
            );

            $_SESSION['success'] = "Раздел создан";
        }
        catch (Exception $e){
            print_r($e);
        }
        header("Location: /admin/?cmd=pluginsutil&do=view&plugin=FooterLinks");
        exit;
    }

    /**
     * @param $request
     */
    public function addLink($request) {
        $data = $_POST;

        try{
            $stmt = $this->db()->prepare("insert into footer_menu_link (title, link, parent, lang) values (:title, :link, :parent, :lang)");
            $stmt->execute(array(
                    'title' => $data['name'],
                    'link' => $data['link'],
                    'parent' => $data['parent'],
                    'lang' => $data['lang']
                )
            );

            $_SESSION['success'] = "Link added";
        }
        catch (Exception $e){
            print_r($e);
        }
        header("Location: /admin/?cmd=pluginsutil&do=view&plugin=FooterLinks");
        exit;
    }

    /**
     * @param $request
     */
    public function edit($request) {
        $data = $_POST;

        try{
            $stmt = $this->db()->prepare("UPDATE footer_menu_link SET title = :title, link = :link WHERE id = :id");
            $stmt->execute(array(
                    'id' => $data['id'],
                    'title' => $data['name'],
                    'link' => $data['link']
                )
            );

            $_SESSION['success'] = "Saved";
        }
        catch (Exception $e){
            print_r($e);
        }
        header("Location: /admin/?cmd=pluginsutil&do=view&plugin=FooterLinks");
        exit;
    }

    /**
     * @param $request
     */
    public function delete($request) {
        $data = $_POST;

        try{
            $stmt = $this->db()->prepare("DELETE FROM footer_menu_link WHERE id = :id");
            $stmt->execute(array(
                    'id' => $data['id']
                )
            );

            $_SESSION['success'] = "Deleted";
        }
        catch (Exception $e){
            print_r($e);
        }
        header("Location: /admin/?cmd=pluginsutil&do=view&plugin=FooterLinks");
        exit;
    }

    /**
     * @param $request
     */
    public function deleteGroup($request) {
        $data = $_POST;

        try{
            $stmt = $this->db()->prepare("DELETE FROM footer_menu WHERE id = :id");
            $stmt->execute(array(
                    'id' => $data['id']
                )
            );

            $_SESSION['success'] = "Deleted";
        }
        catch (Exception $e){
            print_r($e);
        }
        header("Location: /admin/?cmd=pluginsutil&do=view&plugin=FooterLinks");
        exit;
    }

    /**
     * @return array
     */
    public function getBlocksName() {
        try{
            $stmt = $this->db()->query('SELECT n.id AS group_id, n.title AS group_title, n.lang AS group_lang, l.parent, l.id, l.link, l.title FROM footer_menu n LEFT OUTER JOIN footer_menu_link l ON l.parent = n.id');

            $arr = [];

            foreach($stmt->fetchAll() as $key => $item)
            {
                $arr[$item['group_id']][$key] = $item;
                $arr[$item['group_id']]['group_name'] = $item['group_title'];
                $arr[$item['group_id']]['group_parent'] = $item['group_id'];
                $arr[$item['group_id']]['group_lang'] = $item['group_lang'];
            }

            return $arr;
        }
        catch (Exception $e){
            print_r($e);
        }
    }

    /**
     * @return null|PDO
     */
    public function db(){
        static $db = null;
        if(is_null($db)){
            $db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_BASE, DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $db->query('set names utf8');
        }
        return $db;
    }

    /**
     * @return \OTAPILib
     */
    public function getOtapilib()
    {
        global $otapilib;
        return $otapilib;
    }
    
    /**
     * @param array $vars
     * @throws Exception
     */
    public static function onAfterGeneralInit($vars = array())
    {
        OTBase::import('system.packages.FooterLinks.controllers.*');
    }
}