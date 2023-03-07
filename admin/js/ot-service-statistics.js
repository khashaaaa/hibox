function getStatistic() {
    $.post(
        '?cmd=Reports&do=getStatistic',
        {},
        function (data) {
            if (! data.error) {
                $('#OtapiAllCallStatistics .DailyCallCount'  ).html(sdf_FTS(data.OtapiAllCallStatistics.DailyCallCount, 0, ' '));
                $('#OtapiAllCallStatistics .MonthlyCallCount').html(sdf_FTS(data.OtapiAllCallStatistics.MonthlyCallCount, 0, ' '));
                $('#OtapiAllCallStatistics .TotalCount'      ).html(sdf_FTS(data.OtapiAllCallStatistics.TotalCount, 0, ' '));

                $('#OtapiCallStatistics .DailyCallCount'  ).html(sdf_FTS(data.OtapiCallStatistics.DailyCallCount, 0, ' '));
                $('#OtapiCallStatistics .MonthlyCallCount').html(sdf_FTS(data.OtapiCallStatistics.MonthlyCallCount, 0, ' '));
                $('#OtapiCallStatistics .TotalCount'      ).html(sdf_FTS(data.OtapiCallStatistics.TotalCount, 0, ' '));

                $('#TotalLengthTranslatedTexts .DailyCallCount'  ).html(sdf_FTS(data.TotalLengthTranslatedTexts.DailyCallCount, 0, ' '));
                $('#TotalLengthTranslatedTexts .MonthlyCallCount').html(sdf_FTS(data.TotalLengthTranslatedTexts.MonthlyCallCount, 0, ' '));
                $('#TotalLengthTranslatedTexts .TotalCount'      ).html(sdf_FTS(data.TotalLengthTranslatedTexts.TotalCount, 0, ' '));

                $('#LengthExternalTranslatedTexts .DailyCallCount'  ).html(sdf_FTS(data.LengthExternalTranslatedTexts.DailyCallCount, 0, ' '));
                $('#LengthExternalTranslatedTexts .MonthlyCallCount').html(sdf_FTS(data.LengthExternalTranslatedTexts.MonthlyCallCount, 0, ' '));
                $('#LengthExternalTranslatedTexts .TotalCount'      ).html(sdf_FTS(data.LengthExternalTranslatedTexts.TotalCount, 0, ' '));

                $('#CachedDailyCallCount').html(data.CachedDailyCallCount.toFixed(2) + '%');
                $('#CachedMonthlyCallCount').html(data.CachedMonthlyCallCount.toFixed(2) + '%');
                $('#CachedTotalCount').html(data.CachedTotalCount.toFixed(2) + '%');
            } else {
                showError(data);
            }
        }, 'json'
    );
}

setInterval(getStatistic, 5000);
