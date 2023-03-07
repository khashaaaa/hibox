<?php

/**
 * @deprecated use Image instead
 */
class ImageSize implements IRule
{
    protected $width;
    protected $height;
    protected $func;

    public function __construct($width, $height, $func = null)
    {
        $this->width = $width;
        $this->height = $height;

        $this->func = is_callable($func) ? $func : null;
    }

    public function test($value)
    {
        if(!$value || !is_file($value)) {
            return false;
        }

        list($x, $y, $this->type) = getimagesize($value);

        if (is_callable($this->func))
            return call_user_func($this->func, $x, $y, $this->width, $this->height);
        else
            return $this->defaultTest($x, $y, $this->width, $this->height);
    }

    public function defaultTest ($real_x, $real_y, $user_x, $user_y)
    {
        return $real_x > $user_x || $real_y > $user_y;
    }

    public function getMessage()
    {
        return Lang::get('Image_size_musqt_be_not_less') . ' ' . $this->width . 'x' . $this->height;
    }
}
