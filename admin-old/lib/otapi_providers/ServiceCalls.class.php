<?php

class ServiceCalls
{
    /**
     * @var OTAPILib
     */
    protected $otapilib;

    /**
     * @param OTAPILib $otapilib
     */
    public function __construct($otapilib){
        $this->otapilib = $otapilib;
    }

    /**
     * @param $callsStat
     * @return stdClass
     */
    public function PrepareCallStatistic($callsStat)
    {
        $result = new stdClass();

        $result->CallCount = $callsStat['CallCount'];
        $result->DailyCallCount = $callsStat['DailyCallCount'];
        $result->MonthlyCallCount = $callsStat['MonthlyCallCount'];

        $translatorInfoByTime = $callsStat['TotalLengthTranslatedTexts']['StatisticsByTimePeriod'];
        $result->TotalLengthTranslatedTextsTotalCallCount = $callsStat['TotalLengthTranslatedTexts']['TotalCount'];
        $result->TotalLengthTranslatedTextsDailyCallCount = $translatorInfoByTime['DailyCallCount'];
        $result->TotalLengthTranslatedTextsMonthlyCallCount = $translatorInfoByTime['MonthlyCallCount'];

        $externalTranslatorInfoByTime = $callsStat['LengthExternalTranslatedTexts']['StatisticsByTimePeriod'];
        $result->LengthExternalTranslatedTextsTotalCallCount = $callsStat['LengthExternalTranslatedTexts']['TotalCount'];
        $result->LengthExternalTranslatedTextsDailyCallCount = $externalTranslatorInfoByTime['DailyCallCount'];
        $result->LengthExternalTranslatedTextsMonthlyCallCount = $externalTranslatorInfoByTime['MonthlyCallCount'];

        $cachedAdapter = $callsStat['CachedAdapterCalltatistics'];
        $adapter = $callsStat['AdapterCalltatistics'];
        $result->CachedDailyCallCount = round(100-($adapter['StatisticsByTimePeriod']['DailyCallCount']/$cachedAdapter['StatisticsByTimePeriod']['DailyCallCount']), 2);
        $result->CachedMonthlyCallCount = round(100-($adapter['StatisticsByTimePeriod']['MonthlyCallCount']/$cachedAdapter['StatisticsByTimePeriod']['MonthlyCallCount']), 2);
        $result->CachedTotalCount = round(100-($adapter['TotalCount']/$cachedAdapter['TotalCount']), 2);

        return $result;
    }

    public function GetCallStatisticFromOTAPI()
    {
        return $this->PrepareCallStatistic($this->otapilib->GetCallStatistics());
    }
}
