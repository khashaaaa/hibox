<?php

class ServiceException extends Exception
{
    private $errorCode;
    private $subErrorCode = null;
    private $errorMessage;

    const ERROR_CONTRACT_VIOLATION  = 'ContractViolation';

    const SUB_ERROR_INVALID_INPUT   = 'InvalidInputValue';

    public function __construct($method, $params, $message, $code, $serviceUrl = null, $subCode = null)
    {
        $this->errorCode = $code;
        $this->subErrorCode = $subCode;
        if (! Session::get('sid') && isset($params['instanceKey'])) {
            unset($params['instanceKey']);
        }
        $this->errorMessage = $exception = (string)$message;
        if (OTBase::isTest() && Session::get('sid') && $serviceUrl) {
            $params = array_map(function ($a) {
                return preg_replace("#<\?xml[^>]*>|\n#si", "", $a);
            }, $params);
            $url = $serviceUrl . $method . '?' . http_build_query($params);
            $exception = 'ServiceException: <a target="_blank" href="' . $url . '">' . $method . '</a> - ' . (string)$message;
        }
        parent::__construct($exception);
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function getSubErrorCode()
    {
        return $this->subErrorCode;
    }

    public function getErrorMessage()
    {
        $errorMessage = $this->errorMessage;

        if ($this->getErrorCode() == self::ERROR_CONTRACT_VIOLATION  &&
            $this->getSubErrorCode() == self::SUB_ERROR_INVALID_INPUT) {

            switch (SCRIPT_NAME) {
                case 'search':
                    $errorMessage = Lang::get('Search:NotFound');
                    break;
                case 'category':
                    $errorMessage = Lang::get('No_search_reason_category');
                    break;
                case 'subcategory':
                    $errorMessage = Lang::get('No_search_reason_subcategory');
                    break;
                case 'vendor':
                    $errorMessage = Lang::get('No_search_reason_vendor');
                    break;
                case 'item':
                    $errorMessage = Lang::get('good_not_found');
                    break;
                default:
                    break;
            }

        }

        $errorMessage = TextHelper::parseTextWithUrl($errorMessage);
        return $errorMessage;
    }

    public static function generateTestServiceException()
    {
        return new self('TestMethod', array(), 'ServiceException test message', 'NotAvailable', '', 'External_ProviderTemporaryUnavailable');
    }
}
