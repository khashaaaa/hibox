<?php

interface WidgetInterface
{
    /**
     * Отобразить виджет
     *
     * @param array $options - настройки виджета
     * @return mixed
     */
    public static function getWidget(array $options = []);
}