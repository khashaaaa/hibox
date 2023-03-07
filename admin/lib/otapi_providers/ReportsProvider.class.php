<?php
OTBase::import('system.lib.Cache');
OTBase::import('system.lib.cache.Key');

class ReportsProvider extends Repository
{
    /**
     * @var OTAPIlib
     */
    private $otapilib;

    /**
     * @var FileAndMysqlMemoryCache
     */
    private $fileMysqlMemoryCache;

    private $types = array();

    //Функция возвращает список типов операций
    public function __construct($cms, $otapilib)
    {
        parent::__construct($cms);
        $this->otapilib = $otapilib;
        $this->fileMysqlMemoryCache = new FileAndMysqlMemoryCache(General::getCms());
    }

    // Функция возвращает типы операций оператора
    public function GetOperationTypes($sid, $lang)
    {
        if (!empty($this->types)) {
            return $this->types;
        }

        $cacheKey = 'admin:GetOperationTypes' . $lang;

        if ($this->fileMysqlMemoryCache->Exists($cacheKey)) {
            $resultXML = $this->fileMysqlMemoryCache->GetCacheEl($cacheKey);
        } else {
            $this->otapilib->setResultInXMLOn();
            $result = $this->otapilib->GetOperationTypes($sid, '', $lang);
            $this->otapilib->setResultInXMLOff();
            $resultXML = $result->asXML();
            $this->fileMysqlMemoryCache->AddCacheEl($cacheKey, 86400, $resultXML);
        }

        $result = $this->otapilib->GetOperationTypes($sid, $resultXML);
        $this->types = $result;

        return $result;
    }

    // Функция возвращает типы операций оператора в формате key=>value
    public function GetOperationTypesAsKeyValue($sid, $lang)
    {
        if (! count($this->types)) {
            $this->types = $this->GetOperationTypes($sid, $lang);
        }

        $opTypes = array();
        foreach ($this->types as $key => $type) {
            $opTypes[$type['name']] = $type['description'];
        }

        return $opTypes;
    }

    // Функция возвращает описание типа операции в зависимости от имени типа
    public function GetOperationTypeDescriptionByName($sid, $name, $lang)
    {
        if (! count($this->types)) {
            $this->types = $this->GetOperationTypes($sid, $lang);
        }

        foreach ($this->types as $key => $type) {
            if ($type['name'] == $name) {
                return $type['description'];
            }
        }
        /** Если среди прлученных типов не найдено его описание, 
        * то возвращаем обратно название типа операции 
        */
        return $name;
    }
}
