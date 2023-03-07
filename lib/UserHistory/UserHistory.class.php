<?php
class UserHistoryGen extends GenerateBlock {

    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'usrhistorynew'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/main/';

    public function __construct() {
        parent::__construct(true);
    }

    protected function setVars() {
        try{
            $cms = new CMS();
            $status = $cms->Check();
            if ($status)
            {
                $repository = new UserHistoryRepository(new CMS());
                $this->tpl->assign('userhistory', $repository->getHistoryInfo());
            }
        }
        catch(ServiceException $e){
            Session::setError($e->getMessage(), $e->getErrorCode());
        }
        catch(Exception $e){
            Session::setError($e->getMessage(), $e->getCode());
        }
    }

}

class UserHistory  {
    public static function ShowHistory(){
         $p = new UserHistoryGen();
         print $p->Generate();
    }

    public static function AddItemHistory($nme,$id,$price,$pic,$promo_price=null){
        try{
            $repository = new UserHistoryRepository(new CMS());
            $repository->addUserHistoryItem($id, $nme, $price, $promo_price, $pic);
        }
        catch(ServiceException $e){
            Session::setError($e->getMessage(), $e->getErrorCode());
        }
        catch(Exception $e){
            Session::setError($e->getMessage(), $e->getCode());
        }

    }

}


?>