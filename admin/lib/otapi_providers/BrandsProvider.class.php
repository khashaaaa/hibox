<?php

class BrandsProvider extends Repository
{
    /**
     * @var OTAPIlib
     */
    private $otapilib;

    public function __construct($cms, $otapilib)
    {
        parent::__construct($cms);
        $this->otapilib = $otapilib;
        $this->otapilib->setErrorsAsExceptionsOn();
    }

    public function GetBrandInfoList($predefinedData = "")
    {
        return $this->otapilib->GetBrandInfoList($predefinedData);
    }

    public function GetBrandInfoFullList($sessionId, $predefinedData = "")
    {
        return $this->otapilib->GetBrandInfoFullList($sessionId, $predefinedData);
    }

    public function EditBrandInfo($sessionId, $brandId, $xmlBrandInfo, $predefinedData = "")
    {
        return $this->otapilib->EditBrandInfo($sessionId, $brandId, $xmlBrandInfo, $predefinedData = "");
    }
    
    public function RemoveBrandInfo($sessionId, $brandId, $predefinedData = "")
    {
        return $this->otapilib->RemoveBrandInfo($sessionId, $brandId, $predefinedData = "");
    }

    public function GetGlobalBrandInfoList($sessionId)
    {
        return $this->otapilib->GetGlobalBrandInfoList($sessionId);
    }
    
    public function GetBrandInfo($brandId, $predefinedData = "")
    {
        return $this->otapilib->GetBrandInfo($brandId); 
    }
    
    public function GetBrandInfoListFrame($framePosition = 0, $frameSize = 18, $predefinedData = "")
    {
        return $this->otapilib->GetBrandInfoListFrame($framePosition, $frameSize, $predefinedData);
    }
    
    public function UpdateBrandInfo($sid, $id, $name, $imageUrl, $description, $externalId, $isNameSearch)
    {
        $xmlParams = new SimpleXMLElement('<EditableBrandInfo></EditableBrandInfo>');
        $xmlParams->addChild('Name', htmlspecialchars($name));
        $xmlParams->addChild('PictureUrl', htmlspecialchars($imageUrl));
        $xmlParams->addChild('Description', htmlspecialchars($description));
        if (! $externalId) {
                $externalId = '1';
        }

        if ($isNameSearch) {
            $xmlParams->addChild('IsNameSearch', 'true');
        } else {
            $xmlParams->addChild('IsNameSearch', 'false');
        }

        $xmlParams->addChild('ExternalId', htmlspecialchars($externalId));
        $xml = str_replace('<?xml version="1.0" encoding="utf-8"?>','',$xmlParams->asXML());
        return $this->otapilib->EditBrandInfo($sid, $id, $xml);    
    }

    public function AddBrandInfo($sid, $name, $imageUrl, $description, $externalId, $isNameSearch = false)
    {
        $xmlParams = new SimpleXMLElement('<EditableBrandInfo></EditableBrandInfo>');
        $xmlParams->addChild('Name', htmlspecialchars($name));
        $xmlParams->addChild('PictureUrl', htmlspecialchars($imageUrl));
        $xmlParams->addChild('Description', htmlspecialchars($description));
        if (! $externalId) {
            $externalId = '1';
        }

        if ($isNameSearch) {
            $xmlParams->addChild('IsNameSearch', 'true');
        } else {
            $xmlParams->addChild('IsNameSearch', 'false');
        }

        $xmlParams->addChild('ExternalId', htmlspecialchars($externalId));
        $xml = str_replace('<?xml version="1.0" encoding="utf-8"?>','',$xmlParams->asXML());
        return $this->otapilib->AddBrandInfo($sid, $xml);
    }

    public function SearchOriginalBrandsFrame($sessionId, $name, $framePosition = 0, $frameSize = 18, $predefinedData = "")
    {
        return $this->otapilib->SearchOriginalBrandsFrame($sessionId, $name, $framePosition, $frameSize, $predefinedData);
    }
}

