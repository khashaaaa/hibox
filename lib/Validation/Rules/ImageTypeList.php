<?php

/**
 * @deprecated use Image instead
 */
class ImageTypeList implements IRule
{
    protected $types;
    protected $type;

    public function __construct($types)
    {
        $this->types = $types;
    }

    public function test($value)
    {
        if(!$value || !is_file($value)) {
            return false;
        }

        list(,, $this->type) = getimagesize($value);
        return in_array($this->type, $this->types);
    }

    public function getMessage()
    {
        return Lang::get('Incorrect_image_format') . " $this->type";
    }
}
