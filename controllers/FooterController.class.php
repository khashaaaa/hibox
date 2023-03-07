<?php

class FooterController extends GeneralContoller
{
    public function defaultAction()
    {
        $bottomMenu = $this->getBottomMenu();
        $isPhoneRegistrationAllowed = false;

        try {
            $lang = Session::getActiveLang();
            $data = InstanceProvider::getObject()->GetCommonInstanceOptionsInfo($lang);
            $isPhoneRegistrationAllowed = $data->GetRegistration()->IsPhoneRegistrationAllowed();
        } catch (Exception $e) {
            Session::setError($e->getMessage());
        }

        return $this->renderPartial('main/footer', [
            'controller' => $this->request->get('p', 'index'),
            'bottomMenu' => $bottomMenu,
            'isPhoneRegistrationAllowed' => $isPhoneRegistrationAllowed,
        ]);
    }

    private function getBottomMenu()
    {
        $lang = Session::getActiveLang();
        $cacheKey = "Menu:bottom_menu_" . $lang;
        // проверяем наличие кеша
        if ($this->fileMysqlMemoryCache->Exists($cacheKey)) {
            $menu = json_decode($this->fileMysqlMemoryCache->GetCacheEl($cacheKey), true);
        } else {
            $menu = $this->getMenu('bottom_menu', $lang, array(
                'defaultItem' => array(),
            ));
            // сохраняем значение в кеше
            $this->fileMysqlMemoryCache->AddCacheEl($cacheKey, 86400, json_encode($menu));
        }

        return $menu;
    }
}
