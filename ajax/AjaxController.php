<?php

require_once dirname(dirname(__FILE__)) . '/config.php';
require_once dirname(dirname(__FILE__)) . '/config/config.php';
General::init();

class AjaxController
{
    protected $otapilib;

    public function __construct()
    {
        $action = RequestWrapper::post('action') . 'Action';
        if (method_exists($this, $action)) {
            try {
                $this->otapilib = new OTAPIlib();
                $this->otapilib->setErrorsAsExceptionsOn();
                $this->$action(new RequestWrapper());
            } catch (Exception $e) {
                $this->respondError($e->getMessage());
            }
        } else {
            $this->respondError('Unknown action "' . $action . '" requested.');
        }
    }

    public function respondError($errorMessage)
    {
        $response = array(
            'error' => 1,
        );
        if (is_array($errorMessage)) {
            $response['errors'] = $errorMessage;
        } else {
            $response['message'] = $errorMessage;
        }
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($response, JSON_FORCE_OBJECT);
        die();
    }

    public function sendResponse($response = array(), $checkForOpera = false)
    {
        // http://jira.rkdev.ru/browse/OTDEMO-752
        // При загрузке файла аяксом Опера не отправляет заголовок application/json
        if ($checkForOpera && (BrowserHelper::isOpera() && !BrowserHelper::isJsonAcceptable())) {
            header('Content-Type: text/plain; charset=utf-8');
        } else {
            header('Content-Type: application/json; charset=utf-8');
        }

        echo json_encode($response);
        die();
    }

}