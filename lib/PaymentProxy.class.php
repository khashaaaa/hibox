<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dima
 * Date: 04.02.13
 * Time: 12:06
 * To change this template use File | Settings | File Templates.
 */

if (! class_exists('OTBase')) {
    require dirname(__FILE__).'/OTBase.class.php';
}

class PaymentProxy
{
    /**
     * @var string
     */
    private $_server;
    /**
     * @var string
     */
    private $_ps_name;
    /**
     * @var CMS
     */
    protected $cms;

    /**
     * @param string $ps_name
     * @param CMS $cms
     */
    public function __construct($ps_name, $cms) {
        $this->_ps_name = $ps_name;
        $this->cms = $cms;
        $this->cms->Check();

        $server = defined('CFG_PAYMENT_SERVER') ? CFG_PAYMENT_SERVER :
            (OTBase::isTest() ?
                'http://paymentsystemconnector.payments.dev.joker.s.otdev.net/' :
                'http://paygate.otapi.net/');

        if(defined('CFG_SERVICE_INSTANCEKEY'))
            $internal = CFG_SERVICE_INSTANCEKEY;
        else
            die('Error: no Internal');

        $this->_server = $server.$ps_name.'.callback?instanceKey='.$internal;
    }

    private function __getData($method, $params){
        $curl = new Curl($this->_server, 60, false, 10, false, false, false);
        $curl->setPost(http_build_query($params));
        $res = $curl->connect();
        $str = '';
        if($res){
            $str = $curl->getWebPage();
        }
        return $str;
    }

    public function result($params){
        $res = $this->__getData('result', $params);
        return $res;
    }

    public function success(){
		$cRep = new ContentRepository($this->cms);
        $page = $cRep->GetPageByAlias($this->_ps_name.'_success');
        if($page){
            header('Location: '.UrlGenerator::getHomeUrl().'/?p='.
                $this->_ps_name.'_success');
        }
        else{
            header('Location: '.UrlGenerator::getHomeUrl().'/?p=payment_success');
        }
    }

    public  function fail(){
		$cRep = new ContentRepository($this->cms);
        $page = $cRep->GetPageByAlias($this->_ps_name.'_fail');
        if($page){
            header('Location: '.UrlGenerator::getHomeUrl().'/?p='.
                $this->_ps_name.'_fail');
        }
        else{
            header('Location: '.UrlGenerator::getHomeUrl().'/?p=payment_fail');
        }
    }
}
