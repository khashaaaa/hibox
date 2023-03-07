<?php

class ContentSectionController extends GeneralContoller
{
    public function defaultAction()
    {
        try {
            $result = ContentSection::run($this->request, [
                'cache' => $this->request->getValue('cache'),
                'route' => $this->request->getValue('route'),
                'cacheKey' => $this->request->getValue('cacheKey'),
            ]);
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse($result);
    }
}