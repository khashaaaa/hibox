<?php

class VendorHelper
{
    public static function generateLink($vendorId, $title, $attributes = array())
    {
        $result = '';
        if (!InstanceProvider::getObject()->isLimitItemsByCatalog()) {
            $href = UrlGenerator::generateSearchUrlByParams(['vid' => $vendorId]);
            $attr = self::getAttributes($attributes);
            $result .= '<a href="' . $href . '"' . $attr . '>' . $title . '</a>';
        } else {
            $result = $title;
        }
        return $result;
    }

    private static function getAttributes($attributes)
    {
        $result = '';
        foreach ($attributes as $key => $value) {
            $result .= ' ' . $key;
            if (isset($value)) {
                $result .= '="' . $value . '"';
            }
        }
        return $result;
    }
}