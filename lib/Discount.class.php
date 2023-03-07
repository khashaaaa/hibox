<?php

class Discount {
    /**
     * @var OTAPIlib
     */
    protected $otapilib;

    public function __construct()
    {
        $this->otapilib = new OTAPIlib();
        $this->otapilib->setErrorsAsExceptionsOn();
    }

    public function getDiscountsData()
    {
		try {
        	$sid = Session::getUserOrGuestSession();
        	$lang = User::getObject()->getActiveLang();
			$userDiscounts = false;

			if (CMS::IsFeatureEnabled('Discount')) {
			    OTAPILib2::GetDiscountGroups($lang, $sid, $answer);
                OTAPILib2::makeRequests();
                $userDiscounts = $answer->GetResult()->GetContent();
            }

        	return $userDiscounts;
		} catch(ServiceException $e) {
            Session::setError($e->getMessage(), $e->getErrorCode());
        } catch(Exception $e) {
            Session::setError($e->getMessage(), $e->getCode());
        }
    }
}