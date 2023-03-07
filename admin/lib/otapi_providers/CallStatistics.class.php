<?php

class CallStatistics
{
    /**
     * @var OTAPILib
     */
    protected $otapilib;

    /**
     * @param OTAPILib $otapilib
     */
    public function __construct($otapilib)
    {
        $this->otapilib = $otapilib;
    }

    /**
     * @param $data
     * @return array
     */
    private function prepareCallStatistic($data)
    {
        $result = array();

        $result['OtapiAllCallStatistics'] = $this->prepareNodeStatistic($data, 'OtapiAllCallStatistics');
        $result['OtapiCallStatistics'] = $this->prepareNodeStatistic($data, 'OtapiCallStatistics');
        $result['TotalLengthTranslatedTexts'] = $this->prepareNodeStatistic($data, 'TotalLengthTranslatedTexts');
        $result['LengthExternalTranslatedTexts'] = $this->prepareNodeStatistic($data, 'LengthExternalTranslatedTexts');

        $cachedAdapter = $data['CachedAdapterCalltatistics'];
        $adapter = $data['AdapterCalltatistics'];
        if ($cachedAdapter['StatisticsByTimePeriod']['DailyCallCount'] != 0) {
            $result['CachedDailyCallCount'] = round(100-($adapter['StatisticsByTimePeriod']['DailyCallCount']/$cachedAdapter['StatisticsByTimePeriod']['DailyCallCount']), 2);
        } else {
            $result['CachedDailyCallCount'] = 0;
        }
        if ($cachedAdapter['StatisticsByTimePeriod']['MonthlyCallCount'] != 0) {
            $result['CachedMonthlyCallCount'] = round(100-($adapter['StatisticsByTimePeriod']['MonthlyCallCount']/$cachedAdapter['StatisticsByTimePeriod']['MonthlyCallCount']), 2);
        } else {
            $result['CachedMonthlyCallCount'] = 0;
        }        
        if ($cachedAdapter['TotalCount'] != 0) {
            $result['CachedTotalCount'] = round(100-($adapter['TotalCount']/$cachedAdapter['TotalCount']), 2);
        } else {
            $result['CachedTotalCount'] = 0;
        }
        return $result;
    }

    /**
     * @return array
     */
    private function prepareNodeStatistic($data, $node)
    {
        $statistic = array();

        $statistic['DailyCallCount'] = $data[$node]['StatisticsByTimePeriod']['DailyCallCount'];
        $statistic['MonthlyCallCount'] = $data[$node]['StatisticsByTimePeriod']['MonthlyCallCount'];
        $statistic['TotalCount'] = $data[$node]['TotalCount'];

        return $statistic;
    }

    public function getCallStatistics()
    {
        return $this->prepareCallStatistic($this->otapilib->GetCallStatistics());
    }
    
    public function getCallsCount($dateFrom, $dateTo, $timePeriod, $byMethods = false) 
    {
        $xmlParams = new SimpleXMLElement('<CallArchivesSearchParameters></CallArchivesSearchParameters>');
        $xmlParams->addChild('TimePeriod', $timePeriod);
        $xmlParams->addChild('DateFrom', $dateFrom);
        $xmlParams->addChild('DateTo', $dateTo);
        if ($byMethods) {
            $xmlParams->addChild('IncludeArchiveByMethods', 'true');
        } else {
            $xmlParams->addChild('IncludeArchiveByMethods', 'false');
        }
        
        $xmlParams = str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
        $data = $this->otapilib->GetCallArchives($xmlParams);
        
        if ($byMethods) {
            $result = $this->prepareMethodsStatistic($data);
        } else {
            $result = $this->preparePeriodsStatistic($data);
        }
        
        return $result;
    }
    
    public function getMethodCallsCount($dateFrom, $dateTo, $timePeriod, $method) 
    {
        $xmlParams = new SimpleXMLElement('<CallArchivesSearchParameters></CallArchivesSearchParameters>');
        $xmlParams->addChild('TimePeriod', $timePeriod);
        $xmlParams->addChild('DateFrom', $dateFrom);
        $xmlParams->addChild('DateTo', $dateTo);
        $xmlParams->addChild('IncludeArchiveByMethods', 'true');
        $xmlParams->addChild('MethodName', $method);
        $xmlParams = str_replace('<?xml version="1.0"?>', '', $xmlParams->asXML());
        $data = $this->otapilib->GetCallArchives($xmlParams);
        $result = $this->preparePeriodsStatistic($data);
        return $result;        
    }
    
    private function prepareMethodsStatistic($data) {
        $methodsCount = array();
        foreach ($data['totalcallsarchive']['methods'] as $key => $method) {
            $name = (string)$method['Name'];
            $count = (int)$method;
            if (! isset($methodsCount[$name])) {
                $methodsCount[$name] = array('totalCalls' => $count);
            } else {
                $methodsCount[$name]['totalCalls'] = $count;
            }
        }
        foreach ($data['paidcallsarchive']['methods'] as $key => $method) {
            $name = (string)$method['Name'];
            $count = (int)$method;
            if (! isset($methodsCount[$name])) {
                $methodsCount[$name] = array('payedCalls' => $count);
            } else {
                $methodsCount[$name]['payedCalls'] = $count;
            }
        }
        foreach ($data['errorcallsarchive']['methods'] as $key => $method) {
            $name = (string)$method['Name'];
            $count = (int)$method;
            if (! isset($methodsCount[$name])) {
                $methodsCount[$name] = array('errorCalls' => $count);
            } else {
                $methodsCount[$name]['errorCalls'] = $count;
            }
        }
        return array('callsCount' => $methodsCount, 'totalCount' => $data['totalcallsarchive']['sumcall'], 'payedCount' => $data['paidcallsarchive']['sumcall'], 'errorCount' => $data['errorcallsarchive']['sumcall']);
    }
    
    private function preparePeriodsStatistic($data) {
        $callsCount = array();
        foreach ($data['totalcallsarchive']['records'] as $key => $day) {
            $callsCount[$day['timemeasure']] = array('totalCalls' => $day['count']);
        }
        foreach ($data['paidcallsarchive']['records'] as $key => $day) {
            if (! isset($callsCount[$day['timemeasure']])) {
                $callsCount[$day['timemeasure']] = array('payedCalls' => $day['count']);
            } else {
                $callsCount[$day['timemeasure']]['payedCalls'] = $day['count'];
            }
        }
        foreach ($data['errorcallsarchive']['records'] as $key => $day) {
            if (! isset($callsCount[$day['timemeasure']])) {
                $callsCount[$day['timemeasure']] = array('errorCalls' => $day['count']);
            } else {
                $callsCount[$day['timemeasure']]['errorCalls'] = $day['count'];
            }
        }
        return array('callsCount' => $callsCount, 'totalCount' => $data['totalcallsarchive']['sumcall'], 'payedCount' => $data['paidcallsarchive']['sumcall'], 'errorCount' => $data['errorcallsarchive']['sumcall']);
    } 
    
}
