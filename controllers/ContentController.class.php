<?php

class ContentController extends GeneralContoller
{
    public function defaultAction($alias)
    {
        $this->layout = 'twoColumn';
        $page = null;

        try {
            $cRep = new ContentRepository($this->cms);
            $page = $cRep->GetPageByAlias($alias);

            if (!$page) {
                General::setGroupId('404');
                header('HTTP/1.0 404 Not Found');
                return $this->renderNotFoundPage();
            }
        } catch (Exception $e) {
            $this->errorHandler->registerError($e);
        }

        return $this->render('controllers/content/content', [
            'page' => $page
        ]);
    }

    public function getMenuAction()
    {
        return $this->renderPartial('controllers/content/menu', [
            'menu' => $this->getMenu('left_menu', Session::getActiveLang()),
        ]);
    }
}
