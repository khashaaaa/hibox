<?php

class UploadController extends GeneralContoller
{
    public function defaultAction()
    {
        $language = $this->request->getValue('language');
        $fileType = $this->request->getValue('fileType');

        $uploadedFiles = array();
        try {
            if (!$this->request->isPost()) {
                throw new Exception('Method Not Allowed');
            }

            $uploader = new OTFileStorage($language, $fileType);
            $uploadedFiles = $uploader->post();
        } catch (Exception $e) {
            $this->throwAjaxError($e);
        }
        $this->sendAjaxResponse($uploadedFiles);
    }
}
