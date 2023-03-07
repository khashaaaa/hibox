<?php

OTBase::import('system.admin.lib.RightsManager');
OTBase::import('system.lib.Cache');
OTBase::import('system.lib.cache.Key');

class AuthenticationListener
{
    protected $otapilib;

    public function __construct()
    {
        global $otapilib;
        $this->otapilib = $otapilib;
        $this->otapilib->setErrorsAsExceptionsOn();
    }

    /**
     * @param RequestWrapper $request
     */
    public function CheckAuthentication($request)
    {
        if(! $this->IsAuthenticated($request)){
            $pageUrl = new AdminUrlWrapper();
            $pageUrl->Set(UrlGenerator::getProtocol() . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
            $pageUrl->Add('cmd', 'Login')->DeleteKey('do')->DeleteKey('expired')->DeleteKey('referer');
            if ($request->get('do') != 'logout') {
                $pageUrl->Add('referer', $_SERVER['REQUEST_URI']);
            }
            $request->LocationRedirect($pageUrl->Get());
        }
    }

    /**
     * @param RequestWrapper $request
     * @throws Exception
     */
    public function CheckAuthenticationWithException($request)
    {
        if(!$this->IsAuthenticated($request)){
            throw new Exception('session expired');
        }
    }

    /**
     * @param RequestWrapper $request
     */
    public function Authenticate($request, $predefinedData = "")
    {
        if ($predefinedData  && isset($predefinedData['sid'])) {
            $authResult['SessionId'] = $predefinedData['sid'];
        } else {
            $authResult = $this->otapilib->AuthenticateInstanceOperator($request->post('login'), $request->post('password'));
        }
        
        if ($authResult && isset($authResult['SessionId'])) {
            Session::set('sid', (string)$authResult['SessionId']);
            Session::set('adminLogin', (string)$request->post('login'));
        }
    }

    /**
     * @param RequestWrapper $request
     */
    public function Logout($request)
    {
        $this->ClearSession();
        $this->CheckAuthentication($request);
    }

    /**
     * @param RequestWrapper $request
     * @return bool
     */
    public function IsAuthenticated($request)
    {
        if($request->valueExists('expired')) {
            $this->ClearSession();
        }
        return Session::get('sid') && !$request->valueExists('expired');
    }

    public function ClearSession()
    {
        $cacher = new Cache('Rights' . RightsManager::getCurrentRole());
        $cacher->drop();
        Session::set('sid', null);
        Session::set('role', null);
        Session::clearError();
    }
}