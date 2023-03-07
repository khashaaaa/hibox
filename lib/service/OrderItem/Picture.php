<?php

class Picture
{
    const TYPE_WEBCAM   = 'webcam';
    const TYPE_UPLOADED = 'uploaded';
    const TYPE_LINK     = 'link';

    private $file;
    private $path;
    private $name;
    private $type;

    public function __construct($file)
    {
        $this->file = $file;
        $this->path = dirname($this->getFilePath($this->file));
        $this->name = basename($this->file);
    }

    public static function getAvailableTypes()
    {
        return array(
            self::TYPE_WEBCAM,
            self::TYPE_UPLOADED,
            self::TYPE_LINK,
        );
    }

    public function getFilePath()
    {
        if (strpos($this->file, UrlGenerator::getHomeUrl()) !== false) {
            return preg_replace('#' . preg_quote(UrlGenerator::getHomeUrl()) . '#si', CFG_APP_ROOT, $this->file);
        }
        return $this->file;
    }

    public function getSize($size, $absolutePath = false)
    {   
        if ($this->getType() != self::TYPE_LINK) { 
            $suffix = sprintf('_%d_%d', $size, $size);
            $sizePath = $this->path . '/thumbnail' . $suffix;
            if (! is_dir($sizePath) && $this->getType() === self::TYPE_WEBCAM) {
                $sizePath = $this->path . '/thumbs';
            }
            $sizePath = is_dir($sizePath) ? $sizePath : $this->path;
            if (! $absolutePath) {
                return $this->getUrl($sizePath . '/' . $this->name);
            } else {
                return $sizePath . '/' . $this->name;
            }
        } else {
            return $this->getUrl();
        }
    }

    public function getType()
    {
        if (is_null($this->type)) {
            if (preg_match('#\/uploaded\/items\/.*?\d+\-\d{4}\.jpeg#si', $this->file) ||
                preg_match('#\/files\/ItemCam\/.*?\.jpg#si', $this->file)
            ) {
                $this->type = self::TYPE_WEBCAM;
            } elseif (preg_match('#\/uploaded\/items\/#si', $this->file)) {
                $this->type = self::TYPE_UPLOADED;
            } elseif (preg_match('#^(https?|ftp):\/\/\S+#si', $this->file)) {
                $this->type = self::TYPE_LINK;
            }
        }
        return $this->type;
    }

    public function getUrl($file = null)
    {
        return str_replace(CFG_APP_ROOT, UrlGenerator::getHomeUrl(), is_null($file) ? $this->file : $file);
    }

    public function remove($imagesSizeArray = array(100, 300))
    {
        switch ($this->getType()) {
            case self::TYPE_WEBCAM:
            case self::TYPE_UPLOADED:
                $file = $this->getFilePath();
                @unlink($file);
                foreach ($imagesSizeArray as $size) {
                    $file = $this->getSize($size, true);
                    @unlink($file);
                }
            break;
        }
    }
}
