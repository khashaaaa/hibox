<?php

if(!defined('BASE_SITE_PATH'))
    define('BASE_SITE_PATH', dirname(dirname(dirname(__DIR__))));

chdir(BASE_SITE_PATH);

$_SERVER['HTTP_HOST'] = 'demogit.loc';
$_SERVER['SERVER_NAME'] = 'demogit.loc';
require_once 'config.php';
require_once 'config/config.php';
General::init();
require_once 'packages/referral_add_bonus/ReferralAddBonus.class.php';

class ReferralUserManagerTest extends PHPUnit_Framework_TestCase {

    private $users = array(
        'referrer' => '16154',
        'referral_referrer' => '20718',
        'referral_1' => '6282',
        'referral_2' => '13148',
    );

    /**
     * @var ReferralUserManager
     */
    protected $object;

    /**
     * @var CMS
     */
    protected $cms;

    public function getConnection(){
        $this->cms = new CMS();
        $this->cms->Check();
    }

    protected function setUp()
    {
        $this->object = new ReferralUserManager(new CMS());
    }

    protected function tearDown(){
    }

    public function testRemove(){
        $this->removeTestUsers();
        $this->removeTestOrders();
    }

    private function removeTestUsers(){
        $this->object->Remove(16154);
        $this->object->Remove(20718);
        $this->object->Remove(6282);
        $this->object->Remove(13148);
    }

    private function removeTestOrders(){
        $this->getConnection();
        $this->cms->query('DELETE FROM referral_orders');
    }

    /**
     * @depends testRemove
     */
    public function testAdd(){

        $this->addTestUsers();

        $referrer = $this->object->GetById(16154);

        $expectUser = new ReferralUser(16154);
        $expectUser->SetBalance(0.0);
        $expectUser->SetCategory(1);
        $expectUser->SetLogin('referrer_test');
        $expectUser->SetParentId(0);

        $this->assertEquals($expectUser, $referrer);
    }

    private function addTestUsers(){
        $this->object->Add('referrer_test', 16154, 0, 1, 0.0);
        $this->object->Add('referral_test_1', 20718, 16154, 1, 0.0);
        $this->object->Add('referral_test_2', 6282, 20718, 1, 0.0);
        $this->object->Add('referral_test_3', 13148, 20718, 1, 0.0);
    }

    /**
     * @depends testAdd
     */
    public function testEdit(){
        $User = $this->object->GetById(16154);
        $User->SetCategory(2);
        $User->SetBalance(20.0);
        $this->object->Save($User);

        $modifiedUser = $this->object->GetById(16154);

        $expectUser = new ReferralUser(16154);
        $expectUser->SetBalance(20.0);
        $expectUser->SetCategory(2);
        $expectUser->SetLogin('referrer_test');
        $expectUser->SetParentId(0);

        $this->assertEquals($expectUser, $modifiedUser);

        $User->SetCategory(1);
        $User->SetBalance(0.0);
        $this->object->Save($User);
    }

    /**
     * @depends testEdit
     */
    public function testBLCallbacks(){
        $_SERVER['REQUEST_URI'] = '/';
        define('CFG_ADMIN_LOGIN', 'testOperator');
        define('CFG_ADMIN_PASSWORD', '123456');

        $Bonus = new ReferralAddBonus();
        $this->tryToPayToNotExistedReferral($Bonus);
        $this->tryToPayToNotExistedReferrer($Bonus);
        $this->tryToAddExistedOrder($Bonus);

        $this->tryToAddRealOrder($Bonus, 5, 100.0, $this->users['referral_referrer']);
        $this->tryToAddRealOrder($Bonus, 6, 100.0, $this->users['referral_referrer']);
        $this->tryToAddRealOrder($Bonus, 7, 400.0, $this->users['referral_1']);
        $this->tryToAddRealOrder($Bonus, 8, 300.0, $this->users['referral_2']);
        $this->tryToAddRealOrder($Bonus, 9, 370.0, $this->users['referral_1']);
    }

    private function tryToPayToNotExistedReferral(ReferralAddBonus $Bonus){
        $request = new RequestWrapper(array(
            'order_id' => '1',
            'user_id' => 'not_real',
            'amount' => 100.0,
        ));
        $Bonus->SetRequest($request);
        $this->assertEquals('referral_not_found', $Bonus->indexAction(), 'Пытаемся проверсти платеж несуществующему рефералу');
    }

    private function tryToPayToNotExistedReferrer(ReferralAddBonus $Bonus){
        $request = new RequestWrapper(array(
            'order_id' => '2',
            'user_id' => 16154,
            'amount' => 100.0,
        ));
        $Bonus->SetRequest($request);
        $this->assertEquals('referrer_not_found', $Bonus->indexAction(), 'Пытаемся проверсти платеж несуществующему рефералу');
    }

    private function tryToAddExistedOrder(ReferralAddBonus $Bonus){
        $request = new RequestWrapper(array(
            'order_id' => '2',
            'user_id' => 16154,
            'amount' => 100.0,
        ));
        $Bonus->SetRequest($request);
        $this->assertEquals('order exist', $Bonus->indexAction(), 'Пытаемся дублировать заказ');
    }

    private function tryToAddRealOrder(ReferralAddBonus $Bonus, $orderId, $amount, $userId){
        $request = new RequestWrapper(array(
            'order_id' => $orderId,
            'user_id' => $userId,
            'amount' => $amount,
        ));
        $Bonus->SetRequest($request);
        $Bonus->indexAction();
        //$this->assertEquals('Ok', $Bonus->indexAction(), 'Реальный заказ не добавился');

        $this->getConnection();
        $query = $this->cms->querySingleValue('SELECT COUNT(*) FROM referral_orders WHERE order_id='.$orderId);
        $this->assertEquals(1, $query, 'Заказ №'.$orderId.' не добавился');
    }
}
