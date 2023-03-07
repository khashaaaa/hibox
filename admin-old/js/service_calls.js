//// Преобразует число в строку формата 1_separator000_separator000._decimal
// _number - число любое, целое или дробное не важно
// _decimal - число знаков после запятой
// _separator - разделитель разрядов
function sdf_FTS(_number, _decimal, _separator) {
    // определяем, количество знаков после точки, по умолчанию выставляется 2 знака
    var decimal=(typeof(_decimal)!='undefined')?_decimal:2;

    // определяем, какой будет сепаратор [он же разделитель] между разрядами
    var separator=(typeof(_separator)!='undefined')?_separator:'';

    // преобразовываем входящий параметр к дробному числу, на всяк случай, если вдруг
    // входящий параметр будет не корректным
    var r=parseFloat(_number)

    // так как в javascript нет функции для фиксации дробной части после точки
    // то выполняем своеобразный fix
    var exp10=Math.pow(10,decimal);// приводим к правильному множителю
    r=Math.round(r*exp10)/exp10;// округляем до необходимого числа знаков после запятой

    // преобразуем к строгому, фиксированному формату, так как в случае вывода целого числа
    // нули отбрасываются не корректно, то есть целое число должно
    // отображаться 1.00, а не 1
    rr=Number(r).toFixed(decimal).toString().split('.');

    // разделяем разряды в больших числах, если это необходимо
    // то есть, 1000 превращаем 1 000
    b=rr[0].replace(/(\d{1,3}(?=(\d{3})+(?:\.\d|\b)))/g,"\$1"+separator);
    if (_decimal != 0) {
        r = b + '.' + rr[1];
    } else {
        r = b;
    }
    return r;// возвращаем результат
}


function getCount(){
    $.get('index.php', {cmd: 'ServiceCallCounter', do: 'getCount'},function(data){		
		
        $('#CallCount').html(sdf_FTS(data.CallCount, 0, ' '));
        $('#DailyCallCount').html(sdf_FTS(data.DailyCallCount, 0, ' '));
        $('#MonthlyCallCount').html(sdf_FTS(data.MonthlyCallCount, 0, ' '));

        $('#TotalLengthTranslatedTextsDailyCallCount').html(sdf_FTS(data.TotalLengthTranslatedTextsDailyCallCount, 0, ' '));
        $('#TotalLengthTranslatedTextsMonthlyCallCount').html(sdf_FTS(data.TotalLengthTranslatedTextsMonthlyCallCount, 0, ' '));
        $('#TotalLengthTranslatedTextsTotalCallCount').html(sdf_FTS(data.TotalLengthTranslatedTextsTotalCallCount, 0, ' '));

        $('#LengthExternalTranslatedTextsDailyCallCount').html(sdf_FTS(data.LengthExternalTranslatedTextsDailyCallCount, 0, ' '));
        $('#LengthExternalTranslatedTextsMonthlyCallCount').html(sdf_FTS(data.LengthExternalTranslatedTextsMonthlyCallCount, 0, ' '));
        $('#LengthExternalTranslatedTextsTotalCallCount').html(sdf_FTS(data.LengthExternalTranslatedTextsTotalCallCount, 0, ' '));

        $('#CachedDailyCallCount').html(data.CachedDailyCallCount.toFixed(2) + '%');
        $('#CachedMonthlyCallCount').html(data.CachedMonthlyCallCount.toFixed(2) + '%');
        $('#CachedTotalCallCount').html(data.CachedTotalCount.toFixed(2) + '%');

        setTimeout(getCount, 5000)
    }, 'json');
}

function getTarif(){
    $.get('index.php', {cmd: 'ServiceCallCounter', do: 'getTarif'},function(data){
		
        $('#stat_ano').show();
		$('.t_name').html(data.Tariff.name);
		$('.t_limit').html(data.Tariff.calllimit);
		$('.t_price').html(data.Tariff.callprice);
		$('.t_procent').html(data.Tariff.TurnoverPercent);
    }, 'json')
        .error(function(xhr, ajaxOptions, thrownError){
            handleAjaxError(xhr, ajaxOptions, thrownError);
        });
}

$(function(){
    getCount();
	getTarif();
});