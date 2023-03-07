<?php
class UserDataBlock extends GenerateBlock {
    /**
     * @var UserData
     */
    protected $userData;

    public function __construct(){
        parent::__construct();
        $this->otapilib->setErrorsAsExceptionsOn();
        $this->userData = new UserData();
    }

    /**
     * @param RequestWrapper $request
     * @return array
     */
    public function getUserDataAction($request){
        try {
            return $this->userData->getUserData();
        } catch(ServiceException $e) {
            header('HTTP/1.1 500 ' . $e->getErrorCode());
            die($e->getMessage());
        }
    }

    /**
     * @param RequestWrapper $request
     */
    public function getUserDataSummaryAction($request)
    {
        try {
            $userDataFromOtapi = $this->userData->getUserData();
        } catch(ServiceException $e) {
            header('HTTP/1.1 500 ' . $e->getErrorCode());
            die($e->getMessage());
        }
        $userInfo = isset($userDataFromOtapi['AccountData']['userStatus']) ? $userDataFromOtapi['AccountData']['userStatus'] : '';
        $deposit = isset($userDataFromOtapi['AccountData']['accountInfo']) ? $userDataFromOtapi['AccountData']['accountInfo']['AvailableAmount'].' '.(string)$userDataFromOtapi['AccountData']['accountInfo']['CurrencySign'] : '';        
        print json_encode(array(
            'IsAuthenticated' => ! empty($userDataFromOtapi['AccountData']),
            'username' => isset($userInfo['Info']) ? $userInfo['Info'] : '',
            'favourites' => $userDataFromOtapi['UserData']['NoteSummary']['TotalCount'] ? $userDataFromOtapi['UserData']['NoteSummary']['TotalCount'] : 0,
            'basket' => $userDataFromOtapi['UserData']['BasketSummary']['TotalCount'] ? $userDataFromOtapi['UserData']['BasketSummary']['TotalCount'] : 0,
            'deposit' => $deposit
        ));
    }
}