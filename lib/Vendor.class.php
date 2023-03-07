<?php

class Vendor
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var bool|string
     */
    private $errorCode;

    /**
     * @var string
     */
    private $displayName;

    /**
     * @var string
     */
    private $displayPictureUrl;

    /**
     * @var array
     */
    private $features;

    /**
     * @param $id
     * @param null $data
     * @param array $options
     * @return Vendor
     */
    public static function getObject($id, $data = null)
    {
        return new self($id, $data);
    }

    private function __construct($id, $data)
    {
        $id = strval($id);
        $this->id = $id;

        if ($data instanceof OtapiSimplifiedVendorInfo) {
            $this->id = $data->GetId();
            $this->errorCode = $data->GetErrorCode();
            $this->displayName = $data->GetDisplayName();
            $this->displayPictureUrl = $data->GetDisplayPictureUrl();

            foreach ($data->GetFeatures()->GetFeature() as $feature) {
                $this->features[] = $feature;
            }
        }
    }

    /**
     * @param array $attributes
     * @return string
     */
    public function renderName($attributes = array())
    {
        $name = $this->displayName ? $this->displayName : $this->id;

        return $this->renderLink($name, $attributes);
    }

    /**
     * @param array $attributes
     * @return string
     */
    public function renderImage($attributes = array())
    {
        $image = $this->displayPictureUrl ? $this->displayPictureUrl : '/i/noimg.png';
        $image = '<img src="' . $image . '" title="' . Lang::get('Go_to_merchants_goods') . '">';

        return $this->renderLink($image, $attributes);
    }

    /**
     * @param array $attributes
     * @return string
     */
    public function renderLink($inner, $attributes = array())
    {
        $result = '';

        if (!InstanceProvider::getObject()->isLimitItemsByCatalog()) {
            $url = UrlGenerator::generateSearchUrlByParams(['vid' => $this->id]);;
            $attr = $this->getAttributes($attributes);
            $result .= '<a href="' . $url . '"' . $attr . '>' . $inner . '</a>';
        } else {
            $result = '<span>' . $inner . '</span>';
        }

        return $result;
    }

    /**
     * @param array $attributes
     * @return string
     */
    private function getAttributes($attributes)
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

    /**
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function __get($name)
    {
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter) ) {
            return $this->$getter();
        } elseif(property_exists(__CLASS__, $name)) {
            return $this->$name;
        }

        throw new \Exception('Not found getter for property - "' . $name . '"');
    }
}