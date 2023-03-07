<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dima
 * Date: 06.05.13
 * Time: 7:30
 * To change this template use File | Settings | File Templates.
 */

require_once __DIR__ . '/../Command.class.php';

class XEditableField {
    static $field = array();
    static $fieldLabel = '';
    static $fieldHint = '';
    static $optionsLabels = array();
    static $part = '';
    static $partJSON = '';

    public static function RunAdd(){
        self::GetField();
        self::AddFieldToJSONConfig();
        self::AddTranslations();
    }

    private function GetField(){
        self::GetPart();
        self::GetName();
        self::GetNameTranslate();
        self::GetHint();
        self::GetType();
        self::GetPossibleValues();
    }

    private static function GetPart(){
        print "Разделы сайта: \n build - Конструкция сайта \n order - настройки заказов \n";
        self::$part = Command::Read("Razdel kuda budet dobavlen (build, order, promo): ");
        self::$partJSON = json_decode(file_get_contents(__DIR__ . '/../../cfg/site_configuration/'.self::$part.'.json'));
    }

    private static function GetName(){
        self::$field['name'] = Command::Read("Vvedite nazvanie polya: ");
    }

    private static function GetNameTranslate(){
        self::$fieldLabel = Command::Read("Vvedite podpis k polu na russkom yazake: ");
    }

    private static function GetHint(){
        self::$fieldHint = Command::Read("Vvedite podskazku na russkom yazike: ");
    }

    private static function GetType(){
        self::$field['type'] = Command::Read("Введите тип поля (select, text, textarea): ");
    }

    private static function GetPossibleValues(){
        if(self::$field['type'] == 'select'){
            self::$field['valuesList'] = array();
            do{
                $value = Command::Read("Vvedite znachenie dlya pola (dlya okonchania najmite enter): ");
                if($value !== ''){
                    self::$field['valuesList'][] = $value;
                    self::$optionsLabels[] = Command::Read("Vvedite podpis dlya znachenia na russkom: ");
                }
            }while($value !== '');
        }
    }

    private static function AddFieldToJSONConfig(){
        self::$partJSON->fields[] = self::$field;
        file_put_contents(__DIR__ . '/../../cfg/site_configuration/'.self::$part.'.json',
            json_encode(self::$partJSON, JSON_PRETTY_PRINT));
    }

    private static function AddTranslations(){
        setlocale(LC_ALL, 'ru_RU.UTF-8');

        $xml = new DOMDocument('1.0', 'utf-8');
        $xml->load(__DIR__ . '/../../langs/ru.xml');

        $xml->formatOutput = true;
        $xml->preserveWhiteSpace = false;

        $translations = $xml->firstChild;

        $label = $xml->createElement('key', self::$fieldLabel);
        $label->setAttribute('name', self::$field['name'].'_label');
        $translations->appendChild($label);

        $label = $xml->createElement('key', self::$fieldHint);
        $label->setAttribute('name', self::$field['name'].'_hint');
        $translations->appendChild($label);

        if(isset(self::$field['valuesList']) && self::$field['valuesList'])
        foreach(self::$field['valuesList'] as $key => $value){
            $label = $xml->createElement('key', self::$optionsLabels[$key]);
            $label->setAttribute('name', self::$field['name'].'_value:'.$value);
            $translations->appendChild($label);

            if(!$value){
                $label = $xml->createElement('key', self::$optionsLabels[$key]);
                $label->setAttribute('name', self::$field['name'].'_value:*');
                $translations->appendChild($label);
            }
        }

        $xml->save(__DIR__ . '/../../langs/ru.xml');
    }
}