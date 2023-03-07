<?php

class MetaUI
{
    const NODES_SEPARATOR = '_';
    const ATTR_SEPARATOR = '--';
    const ATTR_VALUE_SEPARATOR = '-';

    static $readOnly = false;

    /**
     * @var array
     */
    static $registry = array();

    /**
     * @var array
     */
    static $additionalParameters = array();

    public static function render($settingsXmlData, $updateSettingsUrl, $additionalParameters = array())
    {

        $metaInfo = $settingsXmlData->MetaInfo;
        self::$readOnly = ((string)$metaInfo['IsReadOnly']) == 'true';
        self::$additionalParameters = $additionalParameters;

        $output = '<div class="meta-ui-container" update-settings-url="' . $updateSettingsUrl . '">';
        $output .= self::renderFields($metaInfo->Field, $settingsXmlData);
        $output .= '</div>';

        if (!RequestWrapper::isAjax()) {
            AssetsMin::registerJsFile('/js/tiny_mce/tinymce.min.js', array('minify' => false));
            AssetsMin::registerJsFile('/js/tiny_mce/jquery.tinymce.min.js', array('minify' => false));
        }

        self::$readOnly = false;
        return $output;        
    }

    public static function renderFields($fields, $data, $prefix = '')
    {
        $tabFields = array();
        $output = '';

        foreach ($fields as $key => $field) {
            if ($field->IsTab) {
                $tabFields[] = $field;
            } elseif ($key === "Field") {
                $output .= self::renderField($field, $data, $prefix);
            }
        }
        if (!empty($tabFields)) {
            $output .= self::renderTabs($tabFields, $data, $prefix);
        }
        return $output;
    }

    public static function prepareSelectTreeData($items, $selected, &$parent) {
        foreach ($items as $value) {
            $row = array();
            if (isset($value['DisplayName']) && isset($value['Name'])) {
                $row['attr']['id'] = (string)$value['Name'];
                $row['data']['id'] = (string)$value['Name'];
                $row['data']['title'] = (string)$value['DisplayName'];
                if (isset($value['Description'])) {
                    $row['data']['title'] .= ' (' . (string)$value['Description'] . ')';
                }
                if ($value['Name'] == $selected) {
                    $row['state'] = array('selected' => true);
                }
            }
            $disabled = (isset($value['Disabled']) && (string)$value['Disabled'] === 'true') ? true : false;
            if ($disabled) {
                $row['attr']['class'] = 'disabled';
            }

            $childrens = array();
            self::prepareSelectTreeData($value->Children(), $selected, $childrens);
            if (! empty($childrens)) {
                if (! empty($row)) {
                    $row['children'] = $childrens;
                } else {
                    $parent = $childrens;
                }
            }
            if (! empty($row)) {
                $parent[] = $row;
            }
        }
    }

    public static function renderTabs($fields, $data, $prefix) {
        return General::viewFetch('meta-ui/type/tabs', array(
            'path' => CFG_VIEW_ROOT,
            'vars' => array(
                'fields' => $fields,
                'data' => $data,
                'prefix' => $prefix,
            )
        ));
    }
    
    private static function renderField($field, $data, $prefix = '')
    {
        $name = (string)$field['Name'];
        $encodeName = self::encodeParametersString($name);
        $currentValue = isset($data->$name) ? $data->$name : null;
        $type = $field['Type'];
        $description = (isset($field['Description'])) ? (string)$field['Description'] : false;
        if (isset($field->ConstraintDescriptions->Description)) {
            foreach ($field->ConstraintDescriptions->Description as $constraintDescription) {
                $description .= '<br> ' . (string)$constraintDescription;
            }
        }
        $unit = (string)$field['Unit'];
        $placeholders = array();
        if (isset($field->Placeholders)) {
            foreach ($field->Placeholders->Placeholder as $p => $placeholder) {
                $placeholders[] = array('Value' => (string)($placeholder['Value']), 'Description' => (string)($placeholder['Description']));
            }
        }

        $containerReadOnly = self::$readOnly;
        $isReadOnly = $containerReadOnly;
        if (!$containerReadOnly) {
            $isReadOnly = ((string)$field['IsReadOnly']) == 'true';
        }
        if (in_array($type, array('Group'))) {
            self::$readOnly = $isReadOnly;
        }
        $canBeReset = ((string)$field['CanBeReset']) == 'true';
        $requireReload = ((string)$field['RequireReload']) == 'true';
        $nameCanByReset = $canBeReset ? $prefix . self::encodeParametersString('Reset' . $name) : false;

        $tinyMCEAbsoluteUrls = false;
        if (isset(self::$additionalParameters['tinyMCEAbsoluteUrls'])) {
            $tinyMCEAbsoluteUrls = self::$additionalParameters['tinyMCEAbsoluteUrls'];
        }

        $output = '';
        // рендерим шаблон в зависимости от типа виджета
        switch ($type) {
            case 'Group':
                $output = General::viewFetch('meta-ui/type/group', array(
                    'path' => CFG_VIEW_ROOT,
                    'vars' => array(
                        'type' => $type,
                        'displayName' => $field['DisplayName'],
                        'description' => $description,
                        'content' => self::renderFields($field->Field, $currentValue, $prefix . $encodeName . MetaUI::NODES_SEPARATOR),
                    )
                ));
                break;

            case 'List':
                $output = General::viewFetch('meta-ui/type/list', array(
                    'path' => CFG_VIEW_ROOT,
                    'vars' => array(
                        'type' => $type,
                        'name' => $encodeName,
                        'displayName' => $field['DisplayName'],
                        'prefix' => $prefix . $encodeName,
                        'field' => $field,
                        'currentValue' => $currentValue,
                        'description' => $description,
                        'isReadOnly' => $isReadOnly ? 1 : 0,
                        'nameCanByReset' => $nameCanByReset,
                        'requireReload' => $requireReload,
                        'placeholders' => $placeholders
                    )
                ));
                break;

            case 'SelectOne':
                $output = General::viewFetch('meta-ui/type/select-one', array(
                    'path' => CFG_VIEW_ROOT,
                    'vars' => array(
                        'type' => $type,
                        'name' => $prefix . $encodeName,
                        'displayName' => $field['DisplayName'],
                        'values' => $field->Values->Value,
                        'currentValue' => $currentValue,
                        'description' => $description,
                        'unit' => $unit,
                        'isReadOnly' => $isReadOnly ? 1 : 0,
                        'nameCanByReset' => $nameCanByReset,
                        'requireReload' => $requireReload,
                        'placeholders' => $placeholders
                    )
                ));
                break;

            case 'SelectTree':
                $items = array();
                self::prepareSelectTreeData($field->Values->Value, (string)$currentValue, $items);
                $items = json_encode($items);
                $output = General::viewFetch('meta-ui/type/select-tree', array(
                    'path' => CFG_VIEW_ROOT,
                    'vars' => array(
                        'type' => $type,
                        'name' => $prefix . $encodeName,
                        'displayName' => $field['DisplayName'],
                        'values' => $items,
                        'currentValue' => $currentValue,
                        'description' => $description,
                        'unit' => $unit,
                        'isReadOnly' => $isReadOnly ? 1 : 0,
                        'nameCanByReset' => $nameCanByReset,
                        'requireReload' => $requireReload,
                        'placeholders' => $placeholders
                    )
                ));
                break;
                
            case 'ManyTexts':
                $output = General::viewFetch('meta-ui/type/many-texts', array(
                    'path' => CFG_VIEW_ROOT,
                    'vars' => array(
                        'type' => $type,
                        'name' => $prefix . $encodeName,
                        'displayName' => $field['DisplayName'],
                        'currentValue' => $currentValue,
                        'description' => $description,
                        'isReadOnly' => $isReadOnly ? 1 : 0,
                        'nameCanByReset' => $nameCanByReset,
                        'requireReload' => $requireReload,
                        'placeholders' => $placeholders
                    )
                ));
                break;

            case 'Textarea':
                $output = General::viewFetch('meta-ui/type/textarea', array(
                    'path' => CFG_VIEW_ROOT,
                    'vars' => array(
                        'type' => $type,
                        'name' => $prefix . $encodeName,
                        'displayName' => $field['DisplayName'],
                        'currentValue' => $currentValue,
                        'description' => $description,
                        'unit' => $unit,
                        'isReadOnly' => $isReadOnly ? 1 : 0,
                        'nameCanByReset' => $nameCanByReset,
                        'requireReload' => $requireReload,
                        'placeholders' => $placeholders
                    )
                ));
                break;

            case 'Html':
                $output = General::viewFetch('meta-ui/type/html', array(
                    'path' => CFG_VIEW_ROOT,
                    'vars' => array(
                        'type' => $type,
                        'name' => $prefix . $encodeName,
                        'displayName' => $field['DisplayName'],
                        'currentValue' => $currentValue,
                        'description' => $description,
                        'isReadOnly' => $isReadOnly ? 1 : 0,
                        'nameCanByReset' => $nameCanByReset,
                        'requireReload' => $requireReload,
                        'placeholders' => $placeholders,
                        'tinyMCEAbsoluteUrls' => $tinyMCEAbsoluteUrls
                    )
                ));
                break;

            case 'SelectMany':
                $output = General::viewFetch('meta-ui/type/select-many', array(
                    'path' => CFG_VIEW_ROOT,
                    'vars' => array(
                        'type' => $type,
                        'name' => $prefix . $encodeName,
                        'displayName' => $field['DisplayName'],
                        'values' => $field->Values->Value,
                        'currentValue' => $currentValue,
                        'description' => $description,
                        'isReadOnly' => $isReadOnly ? 1 : 0,
                        'nameCanByReset' => $nameCanByReset,
                        'requireReload' => $requireReload,
                        'placeholders' => $placeholders
                    )
                ));
                break;

            case 'PrioritySelectMany':
                $output = General::viewFetch('meta-ui/type/priority-select-many', array(
                    'path' => CFG_VIEW_ROOT,
                    'vars' => array(
                        'type' => $type,
                        'name' => $prefix . $encodeName,
                        'displayName' => $field['DisplayName'],
                        'values' => $field->Values->Value,
                        'currentValue' => $currentValue,
                        'description' => $description,
                        'isReadOnly' => $isReadOnly ? 1 : 0,
                        'nameCanByReset' => $nameCanByReset,
                        'requireReload' => $requireReload,
                        'placeholders' => $placeholders
                    )
                ));
                break;

            case 'NumericUpDown':
                $output = General::viewFetch('meta-ui/type/numeric-up-down', array(
                    'path' => CFG_VIEW_ROOT,
                    'vars' => array(
                        'type' => $type,
                        'name' => $prefix . $encodeName,
                        'displayName' => $field['DisplayName'],
                        'values' => $field->Values->Value,
                        'currentValue' => $currentValue,
                        'description' => $description,
                        'unit' => $unit,
                        'isReadOnly' => $isReadOnly ? 1 : 0,
                        'nameCanByReset' => $nameCanByReset,
                        'minValue' => isset($field['MinValue']) ? (int)$field['MinValue'] : null,
                        'maxValue' => isset($field['MaxValue']) ? (int)$field['MaxValue'] : null,
                        'requireReload' => $requireReload,
                        'placeholders' => $placeholders
                    )
                ));
                break;

            case 'Date':
                $currentValue = self::getDateByDatePrecision($currentValue, (string)$field['DatePrecision']);
                $output = General::viewFetch('meta-ui/type/date', array(
                    'path' => CFG_VIEW_ROOT,
                    'vars' => array(
                        'type' => $type,
                        'name' => $prefix . $encodeName,
                        'displayName' => $field['DisplayName'],
                        'currentValue' => $currentValue,
                        'description' => $description,
                        'isReadOnly' => $isReadOnly ? 1 : 0,
                        'nameCanByReset' => $nameCanByReset,
                        'format' => self::getDateByDatePrecision(null, (string)$field['DatePrecision'], true),
                        'requireReload' => $requireReload,
                        'placeholders' => $placeholders
                    )
                ));
                break;

            case 'Money':
                $currentValue = General::priceFormat((string)$currentValue, General::getNumConfigValue('price_rounding'));
                $output = General::viewFetch('meta-ui/type/money', array(
                    'path' => CFG_VIEW_ROOT,
                    'vars' => array(
                        'type' => $type,
                        'name' => $prefix . $encodeName,
                        'displayName' => $field['DisplayName'],
                        'currentValue' => $currentValue,
                        'description' => $description,
                        'unit' => $unit,
                        'isReadOnly' => $isReadOnly ? 1 : 0,
                        'nameCanByReset' => $nameCanByReset,
                        'requireReload' => $requireReload,
                        'placeholders' => $placeholders
                    )
                ));
                break;

            case 'Link':
                $output = General::viewFetch('meta-ui/type/link', array(
                    'path' => CFG_VIEW_ROOT,
                    'vars' => array(
                        'type' => $type,
                        'name' => $prefix . $encodeName,
                        'displayName' => $field['DisplayName'],
                        'currentValue' => $currentValue,
                        'description' => $description,
                        'isReadOnly' => $isReadOnly ? 1 : 0,
                        'nameCanByReset' => $nameCanByReset,
                        'placeholders' => $placeholders
                    )
                ));
                break;

            case 'Password':
                $output = General::viewFetch('meta-ui/type/password', array(
                    'path' => CFG_VIEW_ROOT,
                    'vars' => array(
                        'type' => $type,
                        'name' => $prefix . $encodeName,
                        'displayName' => $field['DisplayName'],
                        'currentValue' => $currentValue,
                        'description' => $description,
                        'isReadOnly' => $isReadOnly ? 1 : 0,
                        'nameCanByReset' => $nameCanByReset,
                        'requireReload' => $requireReload,
                        'placeholders' => $placeholders
                    )
                ));
                break;

            case 'File':
                $output = General::viewFetch('meta-ui/type/file', array(
                    'path' => CFG_VIEW_ROOT,
                    'vars' => array(
                        'type' => $type,
                        'name' => $prefix . $encodeName,
                        'displayName' => (string)$field['DisplayName'],
                        'maxFileCount' => (string)$field['MaxFileCount'],
                        'fileType' => (string)$field['FileType'],
                        'currentValue' => $currentValue,
                        'isReadOnly' => $isReadOnly ? 1 : 0,
                        'nameCanByReset' => $nameCanByReset
                    )
                ));
                break;

            case 'Color':
                $output = General::viewFetch('meta-ui/type/color', array(
                    'path' => CFG_VIEW_ROOT,
                    'vars' => array(
                        'type' => $type,
                        'name' => $prefix . $encodeName,
                        'displayName' => $field['DisplayName'],
                        'currentValue' => $currentValue,
                        'description' => $description,
                        'unit' => $unit,
                        'isReadOnly' => $isReadOnly ? 1 : 0,
                        'nameCanByReset' => $nameCanByReset,
                        'requireReload' => $requireReload,
                        'placeholders' => $placeholders
                    )
                ));
                break;

            case 'Button':
                $output = General::viewFetch('meta-ui/type/button', array(
                    'path' => CFG_VIEW_ROOT,
                    'vars' => array(
                        'type' => $type,
                        'name' => $prefix . $encodeName,
                        'displayName' => $field['DisplayName'],
                        'buttons' => $field->Buttons->Button,
                        'description' => $description,
                        'unit' => $unit,
                        'isReadOnly' => $isReadOnly ? 1 : 0,
                        'requireReload' => $requireReload,
                    )
                ));
                break;

            default:
                // Type="Text" - обычное поле ввода
                $output = General::viewFetch('meta-ui/type/text', array(
                    'path' => CFG_VIEW_ROOT,
                    'vars' => array(
                        'type' => $type,
                        'name' => $prefix . $encodeName,
                        'displayName' => $field['DisplayName'],
                        'currentValue' => $currentValue,
                        'description' => $description,
                        'unit' => $unit,
                        'isReadOnly' => $isReadOnly ? 1 : 0,
                        'nameCanByReset' => $nameCanByReset,
                        'requireReload' => $requireReload,
                        'placeholders' => $placeholders
                    )
                ));
                break;
        }

        if (!$containerReadOnly) {
            self::$readOnly = false;
        }
        return $output;
    }

    public static function encodeParametersString($parametersStr)
    {
        // кодированная строка используется в js селекторах, поэтому она должна начинаться с букв
        return !empty($parametersStr) ? 'id' . bin2hex($parametersStr) : '';
    }

    public static function decodeParametersString($parametersStr)
    {
        if ($parametersStr) {
            // вырезаем приставку `id` из начала строки
            $parametersStr = substr($parametersStr, 2);
        }
        return !empty($parametersStr) ? hex2bin($parametersStr) : '';
    }

    public static function generateSingleParamXml($root, $path, $value, $type)
    {
        $xmlParams = new SimpleXMLElement('<'. $root .'></' . $root . '>');
        $param = $xmlParams;
        foreach ($path as $par) {
            $par = self::decodeParametersString($par);
            if ($par == self::decodeParametersString($path[count($path)-1])) {
                switch ($type) {
                    case 'List':
                        $param = self::addXMLNode($param, $par);
                        break;
                    case 'ManyTexts':
                        if (! is_array($value)) {
                            $values = explode(',', $value);
                        } else {
                            $values = $value;
                        }
                        $param = self::addXMLNode($param, $par);
                        if (!empty($value)) {
                            foreach ($values as $val) {
                                $val = trim($val);
                                if ($val != '') {
                                    self::addXMLNode($param, 'Value', $val);

                                }
                            }
                        }
                        break;
                    case 'SelectOne':
                    case 'SelectMany':
                    case 'PrioritySelectMany':
                        if (! is_array($value)) {
                            $values = explode(',', $value);
                        } else {
                            $values = $value;
                        }
                        $param = self::addXMLNode($param, $par);
                        foreach ($values as $val) {
                            $val = trim($val);
                            if ($val != '') {
                                self::addXMLNode($param, 'Value', $val);
                            }
                        }
                        break;
                    case 'Float':
                        $pValue = trim(preg_replace('/\s+/', '', $value));
                        if (! is_numeric($pValue)) {
                            throw new ValidationException(LangAdmin::get("Value_must_be_numeric"));
                        }
                        $param = self::addXMLNode($param, $par, (float)$pValue);
                        break;
                    case 'File':
                        $param = self::addXMLNode($param, $par);
                        if (!empty($value)) {
                            foreach ($value as $val) {
                                if ($val != '') {
                                    $par = 'Value' . MetaUI::ATTR_SEPARATOR . 'Id' . MetaUI::ATTR_VALUE_SEPARATOR . $val;
                                    self::addXMLNode($param, $par);
                                }
                            }
                        }
                        if ($value === null) {
                            self::addXMLNode($xmlParams, 'ResetImage', 'true');
                        }
                        break;
                    case 'Date':
                        $format = self::getDateFormatByDateValue($value);
                        $unixDate = DateTime::createFromFormat($format, $value);
                        $date = $unixDate->format(DATE_ATOM);
                        $param = self::addXMLNode($param, $par, $date);
                        break;
                    case 'Html':
                    case 'Text':
                    default:
                        $param = self::addXMLNode($param, $par, $value);
                        break;
                }
            } else {
                $param = self::addXMLNode($param, $par);
            }
             
        }
        return str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
    }

    public static function getDateByDatePrecision($value, $precision, $onlyFormat = false)
    {
        // Year, Month, Day, Hour, Minute, Second
        switch ($precision) {
            case 'Year':
                $format = 'Y';
                break;
            case 'Month':
                $format = 'm.Y';
                break;
            case 'Day':
                $format = 'd.m.Y';
                break;
            case 'Hour':
                $format = 'd.m.Y H:00';
                break;
            case 'Minute':
                $format = 'd.m.Y H:i';
                break;

            default:
                $format = 'd.m.Y H:i:s';
                break;
        }
        if ($onlyFormat) {
            return $format;
        }
        $date = strtotime($value);
        return date($format, $date);
    }

    public static function getDateFormatByDateValue($value)
    {
        $value = trim($value);
        if (preg_match('/^[0-9]{4}$/', $value)) {
            return 'Y';
        } else if (preg_match('/^[0-9]{2}\.[0-9]{4}$/', $value)) {
            return 'm.Y';
        } else if (preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/', $value)) {
            return 'd.m.Y';
        } else if (preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}\s[0-9]{2}:00$/', $value)) {
            return 'd.m.Y H:00';
        } else if (preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}\s[0-9]{2}:[0-9]{2}$/', $value)) {
            return 'd.m.Y H:i';
        } else if (preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}\s[0-9]{2}:[0-9]{2}:[0-9]{2}$/', $value)) {
            return 'd.m.Y H:i:s';
        } else {
            throw new ValidationException(Lang::get('wrong_string_format_error'));
        }
    }

    private static function addXMLNode($xmlParams, $parameter, $value = NULL)
    {
        $nodeAttributes = explode(MetaUI::ATTR_SEPARATOR, $parameter);
        $nodeName = array_shift($nodeAttributes);

        if (is_null($value)) {
            $xmlParams = $xmlParams->addChild($nodeName);
        } else {
            $value = str_replace('&', '&amp;', $value);
            $xmlParams = $xmlParams->addChild($nodeName, $value);
        }

        foreach ($nodeAttributes as $attribute) {
            $attributeArray = explode(MetaUI::ATTR_VALUE_SEPARATOR, $attribute);
            $name = array_shift($attributeArray);
            $value = implode(MetaUI::ATTR_VALUE_SEPARATOR, $attributeArray);
            $xmlParams->addAttribute($name, $value);
        }

        return $xmlParams;
    }

    public static function GetMetaEntities($lang)
    {
        $cacheKey = "MetaUI:GetMetaEntities" . $lang;
        if (isset(self::$registry[$cacheKey])) {
            return self::$registry[$cacheKey];
        }

        $fileMysqlMemoryCache = new FileAndMysqlMemoryCache(General::getCms());

        $data = new OtapiDataListOfMetaEntityInfo(null);
        if ($fileMysqlMemoryCache->Exists($cacheKey)) {
            $cache = simplexml_load_string(json_decode($fileMysqlMemoryCache->GetCacheEl($cacheKey), true));
            $data = new OtapiDataListOfMetaEntityInfo($cache);
            self::$registry[$cacheKey] = $data;
        } else {
            /** @var OtapiMetaEntityInfoListAnswer $data */
            OTAPILib2::GetMetaEntities($lang, $data);
            OTAPILib2::makeRequests();
            $data = $data->GetResult();
            $fileMysqlMemoryCache->AddCacheEl($cacheKey, 3600, json_encode($data->asXML()));
        }

        $entities = array();
        if ($data->GetContent()) {
            /** @var OtapiMetaEntityInfo $item */
            foreach ($data->GetContent()->GetItem() as $item) {
                $entities[$item->GetName()] = $item;
            }
        }

        self::$registry[$cacheKey] = $entities;

        return $entities;
    }
}