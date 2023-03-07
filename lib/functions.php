<?php

// Функция предназначена для вывода численных результатов с учетом
// склонения слов, например: "1 ответ", "2 ответа", "13 ответов" и т.д.
//
// $digit — целое число
// можно вместе с форматированием, например "<b>6</b>"
//
// $expr — массив, например: array("ответ", "ответа", "ответов").
// можно указывать только первые 2 элемента, например для склонения английских слов
// (в таком случае первый элемент - единственное число, второй - множественное)
//
// $expr может быть задан также в виде строки: "ответ ответа ответов", причем слова разделены
// символом "пробел"
//
// $onlyword - если true, то выводит только слово, без числа;
// необязательный параметр

function declension($digit, $expr, $onlyword = false)
{
    if (!is_array($expr)) $expr = array_filter(explode(' ', $expr));
    if (empty($expr[2])) $expr[2] = $expr[1];
    $i = preg_replace('/[^0-9]+/s', '', $digit) % 100; //intval не всегда корректно работает
    if ($onlyword) $digit = '';
    if ($i >= 5 && $i <= 20) $res = $digit . ' ' . $expr[2];
    else {
        $i %= 10;
        if ($i == 1) $res = $digit . ' ' . $expr[0];
        elseif ($i >= 2 && $i <= 4) $res = $digit . ' ' . $expr[1];
        else $res = $digit . ' ' . $expr[2];
    }
    return trim($res);
}

/**
 * @param $elem
 * @param int $max_level
 * @param array $print_nice_stack
 */
function print_nice($elem, $max_level = 10, $print_nice_stack = array())
{
    if (is_array($elem) || is_object($elem)) {
        if (in_array($elem, $print_nice_stack, true)) {
            echo "<span class='color:red'>&nbsp;рекурсия</span>";
            return;
        }
        $print_nice_stack[] = & $elem;
        if ($max_level < 1) {
            echo "<font color=red>достигнут предел вывода</font>";
            return;
        }
        $max_level--;
        echo "<table border=0 cellspacing=0 cellpadding=0 style='font-size:8pt;background:#fff;font-family:\"Trebuchet MS\",serif;width:100%;'>";
        $cur = md5(microtime()).rand();
        if (is_array($elem)) {
            $thiscount = count($elem);
            if ($thiscount == 0) {
                echo '<tr><td colspan=2 style="background-color:#666;padding:2px;"><strong><font color=white>пустой массив</font></strong></td></tr><tr><td colspan=2><div id="' . $cur . '" style="height:0px;overflow:hidden"><table cellspacing=0 cellpadding=0>';
            } else {
                echo '<tr><td colspan=2 style="background-color:#666;padding:2px;"><strong><font color=white><span style="cursor:pointer" onclick="if(document.getElementById(\'' . $cur . '\').style.height==\'0px\'){document.getElementById(\'' . $cur . '\').style.height=\'auto\';this.innerHTML=\'&nbsp;-&nbsp;\'}else{document.getElementById(\'' . $cur . '\').style.height=\'0px\';this.innerHTML=\'&nbsp;+&nbsp;\'}">&nbsp;+&nbsp;</span>массив (' . declension($thiscount, array("элемент", "элемента", "элементов")) . ')</font></strong></td></tr><tr><td colspan=2><div id="' . $cur . '" style="height:0px;overflow:hidden"><table border=0 cellspacing=0 cellpadding=0 style="font-size:8pt;font-family:\'Trebuchet MS\';width:100%;">';
            }
        } else {
            echo '<tr><td colspan=2 style="background-color:#666;padding:2px;"><strong><font color=white><span style="cursor:pointer" onclick="if(document.getElementById(\'' . $cur . '\').style.height==\'0px\'){document.getElementById(\'' . $cur . '\').style.height=\'auto\';this.innerHTML=\'&nbsp;-&nbsp;\'}else{document.getElementById(\'' . $cur . '\').style.height=\'0px\';this.innerHTML=\'&nbsp;+&nbsp;\'}">&nbsp;+&nbsp;</span>объект типа: ' . get_class($elem) . '</font></strong></td></tr><tr><td colspan=2><div id="' . $cur . '" style="height:0px;overflow:hidden"><table border=0 cellspacing=0 cellpadding=0 style="font-size:8pt;font-family:\'Trebuchet MS\';width:100%;">';
        }
        $color = 0;
        foreach ($elem as $k => $v) {
            if ($max_level % 2) {
                $rgb = ($color++ % 2) ? "#999" : "#ccc";
            } else {
                $rgb = ($color++ % 2) ? "#b7db59" : "#92bc3c";
            }
            echo '<tr><td valign="top" style="width:20px;padding:2px;background-color:' . $rgb . ';">';
            echo $k . "</td><td style=''>";
            print_nice($v, $max_level, $print_nice_stack);
            echo "</td></tr>";
        }
        echo "</table></div></td></tr></table>";
        return;
    }
    if ($elem === null) {
        echo "<font color=#92bc3c>&nbsp;NULL</font>";
    } elseif ($elem === 0) {
        echo "0";
    } elseif ($elem === true) {
        echo '<font color=#92bc3c>&nbsp;true</font>';
    } elseif ($elem === false) {
        echo '<span style="color:#92bc3c">&nbsp;false</span>';
    } elseif ($elem === "") {
        echo '<span style=\'color:#92bc3c\'>&nbsp;пустая строка</span>';
    } else {
        echo "&nbsp;" . str_replace("\n", "<strong><font color=red>*</font></strong><br>\n", $elem);
    }
}


/**
 * Функция для выдачи данных из масиива или объекта
 *
 * @param  $params объект, массив или строка
 * @param bool $exit остновить работу скрипта
 * @param int $count
 * @count int сколько раз выполнится вывод
 */
function wtf($params = null, $exit = true, $count = 1)
{
    global $debug_count;

    $debug = debug_backtrace();


    echo "<div style='font-family:\"Trebuchet MS\";font-size:9pt;background:#fff;z-index:9999;width:100%;'>";
    echo "<p>Отладка в: " . $debug[0]["file"] . ", на строке: <b>" . $debug[0]["line"] . "</b></p>";
    if (is_array($params) || is_object($params)) {
        print_nice($params);
    } elseif (is_string($params) || is_numeric($params)) {
        echo $params;
    } else {
        echo "<pre>";
        var_dump($params);
        echo "</pre>";
    }
    echo "</div>";


    if (($debug_count <= $count || $count == 1) && $exit) {
        exit;
    }
    $debug_count++;
}

/*
 * Функция проверки существования и доступности другой функции PHP
 * 
 * @param $func string - название функции
 * @return bool
 */
function is_function_enabled($func)
{
    $name = strtolower(trim($func));
    if ($name == '') return false;

    // Получить список функций, отключенных в php.ini
    $disabled = explode(",", @ini_get("disable_functions"));
    if (empty($disabled)) {
        $disabled=array();
    } else {
        // Убрать пробелы и привести названия к нижнему регистру
        $disabled = array_map('trim', array_map('strtolower', $disabled));
    }

    // проверить доступность функции разными способами
    return (function_exists($func) && is_callable($func) && !in_array($func, $disabled));
} 
