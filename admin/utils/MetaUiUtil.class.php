<?php

class MetaUiUtil extends GeneralUtil
{
    /**
     * @param $request \RequestWrapper
     */
    public function updateSettingsAction($request)
    {
        Session::close();

        $name = $request->post('name');
        $value = $request->post('value');
        $type = $request->get('type');
        $metaEntity = $request->get('metaEntity');
        $lang = $request->get('inputLanguage');

        try {
            $entities = MetaUI::GetMetaEntities($lang);
            /** @var OtapiMetaEntityInfo $entity */
            $entity = isset($entities[$metaEntity]) ? $entities[$metaEntity] : new OtapiMetaEntityInfo(null);
            $additionalParameters = array();
            if ($entity->GetAdditionalParameters()) {
                foreach ($entity->GetAdditionalParameters()->GetParameter() as $parameter) {
                    $additionalParameters[$parameter] = $request->get($parameter);
                }
            }

            $params = explode(MetaUI::NODES_SEPARATOR, $name);
            if (is_array($params) && count($params) > 0) {
                $xmlParameters = MetaUI::generateSingleParamXml($entity->GetUpdateDataRootName(), $params, $value, $type);
                $answer = false;
                OTAPILib2::simpleRequest($entity->GetUpdateMethod(), array_merge(
                    array(
                        'language' => $lang,
                        'sessionId' => Session::get('sid'),
                        'xmlUpdateData' => $xmlParameters,
                    ),
                    $additionalParameters
                ), $answer);
                OTAPILib2::makeRequests();
            }
        } catch (Exception $e) {
            $this->respondAjaxError($e);
        }

        $this->sendAjaxResponse(array(), true);
    }

}
