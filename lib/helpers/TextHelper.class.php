<?php

class TextHelper
{
    public static function wrapWordsInText($text, $word_length = 40, $wrap_symbol = " ", $keep_html_tags = false)
    {
        $result = $text;
        if (! $keep_html_tags) {
            $words_array = explode(' ', strip_tags($text));
            if (! empty($words_array)) {
                foreach ($words_array as $key => $word) {
                    if ($word !== '') {
                        $words_array[$key] = ((mb_strlen($word) > $word_length) && (!preg_match('/(href=|src:)/', $word))) ? wordwrap($word, $word_length, $wrap_symbol, true) : $word;
                    }
                }
                $result = implode(' ', $words_array);
            }
        } else {
            $words_array = preg_split("'(<[\/\!]*?[^<>]*?>)'si", $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
            if (! empty($words_array)) {
                foreach ($words_array as $key => $word) {
                    if ($word[0] != '<') {
                        $words_array[$key] = ((mb_strlen($word) > $word_length) && (!preg_match('/(href=|src:)/', $word))) ? wordwrap($word, $word_length, $wrap_symbol, true) : $word;
                    }
                }
                $result = implode('', $words_array);
            }
        }
        return $result;
    }

    public static function formatNumber($number, $decimals = null, $dec_point = ',', $thousands_sep = '&nbsp;')
    {
        $decimals = !is_null($decimals) ? $decimals : (int)General::getConfigValue('price_rounding');
        $number = number_format($number, $decimals, $dec_point, " ");
        return str_replace(" ", $thousands_sep, $number);
    }

    /**
     * Форматирование цены вместе со знаком валюты
    **/
    public static function formatPrice(
        $price,
        $sign = '',
        $sign_right = true,
        $dec_point = '.',
        $decimals = null,
        $thousands_sep = '&nbsp;'
    ) {
        $decimals = is_null($decimals) ? (int)General::getConfigValue('price_rounding') : $decimals;
        $formatted_price = number_format((float)$price, $decimals, $dec_point, " ");
        $formatted_price = str_replace(" ", $thousands_sep, $formatted_price);

        if ($sign_right) {
            $result = $formatted_price . '&nbsp;' . $sign;
        } else {
            $result = $sign . '&nbsp;' . $formatted_price;
        }
        return $result;
    }

    public static function truncate($string, $length = 150)
    {
        $len = (mb_strlen($string) > $length) ? mb_strripos(mb_substr($string, 0, $length), ' ') : $length;
        $cutStr = mb_substr($string, 0, $len);
        return (mb_strlen($string) > $length) ? $cutStr . '...' : $cutStr;
    }
    
    public static function clearHTMLTags($text)
    {
        return strip_tags($text);
    }
    
    public static function truncateAndClearHTML($text, $length = 150)
    {
        $text = self::clearHTMLTags($text);
        $text = self::truncate($text, $length);     
        return $text;
    }

    public static function plural($n, $form1, $form2, $form5)
    {
        $n = abs($n) % 100;
        $n1 = $n % 10;
        if ($n > 10 && $n < 20) return $form5;
        if ($n1 > 1 && $n1 < 5) return $form2;
        if ($n1 == 1) return $form1;
        return $form5;
    }
    
    public static function translitСonverter($string)
    {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => "",  'ы' => 'y',   'ъ' => "",
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => "",  'Ы' => 'Y',   'Ъ' => "'",
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
            '/' => '', '.' => '', ',' => '', ';' => '', ' ' => '-', '\'' => '',
            ')' => '', '(' => '', '  ' => '-', '--' => '-',
            '<' => '', '>' => '', '!' => '', '@' => '', '#' => '', '$' => '',
            '%' => '', '^' => '', '&' => '', '*' => '', '[' => '', ']' => '',
            '"' => '', '_' => '', '\\' => '', '?' => ''
        );
        $tmpString = strtr($string, $converter);
        //$tmpString = preg_replace ("/[^a-zA-ZА-Яа-я_ -0-9\s]/iu","", $tmpString);
        $tmpString = preg_replace("/[^A-z0-9_-]/ius", "", $tmpString);
        return $tmpString;
    }
    
    public static function htmlFromUser($text) 
    {
    	return self::parseTextWithUrl(nl2br($text));
    }
    
    public static function parseTextWithUrl($text) 
    {
        $text = preg_replace_callback(
            '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,8}(\/\S*)?/',
            function ($m) {
                return stripslashes((strlen($m[0])>0 ? '<a href="' . $m[0] . '" target=\"_blank\">' . $m[0] . '</a>' : $m[0]));
            },
            $text
        );

        $text = preg_replace_callback(
            '/(^|\s|>)(www.[^<> \n\r]+)/ius',
            function ($m) {
                return stripslashes((strlen($m[2])>0 ? $m[1] .'<a href="http://' . $m[2] . '" target=\"_blank\">' . $m[2] . '</a>' : $m[0]));
            },
            $text
        );

        return $text;
    }
    
    public static function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES);
    }
    
    public static function htmlToJS($string)
    {
        return addslashes($string);
    }
    
    public static function urlDecode($string)
    {
        return urldecode($string);
    }

    /**
     * Добавляет закрывающиеся тэги
     * к html - строке, тем самым делая
     * html валидным
     *
     * @param $content string html - строка (НЕ валидная)
     * @return string html - строка (валидная)
     */
    public static function closeTags($content)
    {
        $position = 0;
        $open_tags = array();
        //теги для игнорирования
        $ignored_tags = array('br', 'hr', 'img');

        while (($position = strpos($content, '<', $position)) !== false) {
            //забираем все теги из контента
            if (preg_match("|^<(/?)([a-z\d]+)\b[^>]*>|i", substr($content, $position), $match)) {
                $tag = strtolower($match[2]);
                //игнорируем все одиночные теги
                if (in_array($tag, $ignored_tags) == false) {
                    //тег открыт
                    if (isset($match[1]) && $match[1] == '') {
                        if (isset($open_tags[$tag]))
                            $open_tags[$tag]++;
                        else
                            $open_tags[$tag] = 1;
                    }
                    //тег закрыт
                    if (isset($match[1]) && $match[1] == '/') {
                        if (isset($open_tags[$tag]))
                            $open_tags[$tag]--;
                    }
                }
                $position += strlen($match[0]);
            }
            else
                $position++;
        }
        //закрываем все теги
        foreach ($open_tags as $tag => $count_not_closed) {
            $content .= str_repeat("</{$tag}>", $count_not_closed);
        }

        return $content;
    }

    private static function getDate($format, $time)
    {
        $time = strtotime($time);
        return date($format, $time);
    }

    /**
     * @param $time string - string in a format accepted by strtotime.
     * @return string|false - a formatted date string.
     */
    public static function datetime($time)
    {
        return self::getDate(General::getConfigValue('format_datetime', 'd.m.Y H:i'), $time);
    }

    /**
     * @param $time string - string in a format accepted by strtotime.
     * @return string|false - a formatted date string.
     */
    public static function date($time)
    {
        return self::getDate(General::getConfigValue('format_date', 'd.m.Y'), $time);
    }

    /**
     * @param $time string - string in a format accepted by strtotime.
     * @return string|false - a formatted date string.
     */
    public static function time($time)
    {
        return self::getDate(General::getConfigValue('format_time', 'H:i'), $time);
    }
}
