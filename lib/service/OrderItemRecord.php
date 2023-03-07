<?php

OTBase::import('system.lib.service.OrderItem.Picture');
OTBase::import('system.lib.service.ServiceRecord');

class OrderItemRecord extends ServiceRecord
{
    private $pictures;

    public function __construct($data)
    {
        $this->addConverters(array(
            'operatorcomment' => function ($a) {
                return str_replace('\n', "\n", $a);
            },
        ));
        parent::__construct($data);
    }

    public function canBeDeleted()
    {
        // TODO: все статусы должны быть расшифрованы
        return in_array($this->statusCode, array(1, 11, 12));
    }

    public function getPictures()
    {
        if (is_null($this->pictures)) {
            $oldWebcamFiles = glob(CFG_APP_ROOT . '/files/ItemCam/' . $this->orderId . '-' . $this->id . '*.jpg');
            $oldWebcamFiles = is_array($oldWebcamFiles) ? $oldWebcamFiles : array();

            $uploadedFiles = glob(CFG_APP_ROOT . '/uploaded/items/' . $this->id . '/' . $this->orderId . '/*.*');
            $uploadedFiles = is_array($uploadedFiles) ? $uploadedFiles : array();

            $operatorFiles = array();
            preg_match_all('#https?:\/\/\S+\/(.+)(jpg | jpeg | png | ico | gif | bmp)#si', $this->operatorcomment, $m);
            if (! empty($m[0])) {
                $operatorFiles = $m[0];
            }

            $pictures = array_merge($oldWebcamFiles, $uploadedFiles, $operatorFiles);
            $this->pictures = array();
            foreach ($pictures as $file) {
                $this->pictures[] = new Picture($file);
            }
        }
        return $this->pictures;
    }

    public function asArray()
    {
        return array_merge(parent::asArray(), array(
            'pictures' => $this->getPictures(),
        ));
    }
    
    public function getCssWrapperForVendPurch() {
        if ($this->vendorpurchaseitempricewarninglevel == 'Ok') {
            return 'alert alert-success';
        } elseif ($this->vendorpurchaseitempricewarninglevel == 'SmallOverpayment') {
            return 'alert alert-warning';
        } elseif ($this->vendorpurchaseitempricewarninglevel == 'BigOverpayment') {
            return 'alert alert-danger';
        }
        return '';
    }
}
