<?php

class SliceMenu implements WidgetInterface
{

    public static function getWidget(array $options = [])
    {
        $defaultOptions = array(
            'id' => 'prf' . rand(10000, 99999) . '_slice-menu',     // id меню
            'class' => '',                                          // class меню
            'attributes' => []                                      // атрибуты
        );

        $items = $options['items'];
        $defaultItem = isset($options['defaultItem']) ? $options['defaultItem'] : null;

        // переопределить стандартные опции
        $options = array_merge($defaultOptions, $options['options']);

        AssetsMin::registerJsFile('/js/vendor/jquery.slice-menu.js');

        return General::viewFetch('/widget/slice-menu', array(
                'path' => CFG_VIEW_ROOT,
                'vars' => array(
                    'items' => $items,
                    'defaultItem' => $defaultItem,
                    'options' => $options
                )
            )
        );
    }

}