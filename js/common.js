/*
 * Преобразует число в строку
 * @param number _number
 * @param string _decimal - число знаков после запятой
 * @param string _separator - разделитель разрядов
 */
var priceRounding = (typeof PRICE_ROUNDING !== 'undefined') ? PRICE_ROUNDING : 2;

function number_format(_number, _decimal, _separator) {
    // определяем, количество знаков после точки, по умолчанию выставляется 2 знака
    var decimal = (typeof(_decimal) !== 'undefined') ? _decimal : 2;
    if (decimal < 0) {
        decimal = 0;
    }
    // определяем, какой будет сепаратор [он же разделитель] между разрядами
    var separator = (typeof(_separator) !== 'undefined') ? _separator : '';

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
    rr = Number(r).toFixed(decimal).toString().split('.');

    // разделяем разряды в больших числах, если это необходимо, то есть, 1000 превращаем 1 000
    b = rr[0].replace(/(\d{1,3}(?=(\d{3})+(?:\.\d|\b)))/g,"\$1"+separator);
    if (_decimal > 0) {
        r = b + '.' + rr[1];
    } else {
        r = b;
    }
    return r;
}

function getCurrencyPrice(price, currency) {
    if (typeof price.Value !== 'undefined') {
        // если это диапазон
        if (typeof price.Min !== 'undefined' && typeof price.Max !== 'undefined') {
            return '<span dir="ltr" itemprop="price" content="' + number_format(price.Min, priceRounding, '') + '">' + number_format(price.Min, priceRounding, '&nbsp;') + '</span>' + '-' + '<span dir="ltr" itemprop="price" content="' + number_format(price.Min, priceRounding, '') + '">' + number_format(price.Max, priceRounding, '&nbsp;') + '</span>' + '&nbsp;' + currency;
        }

        price = price.Value;
    }

    return '<span dir="ltr" itemprop="price" content="' + number_format(price, priceRounding, '') + '">' + number_format(price, priceRounding, '&nbsp;') + '</span>' + '&nbsp;' + '<span itemprop="priceCurrency" content="' + currency + '">' + currency + '</span>';
}

$('img').on('error', function() {
    $(this).attr('src', '/i/noimg.png');
});

$(function(){
    if (typeof($.fn.numeric) === 'function') {
        $('body').on('input', 'input[data-type]', function () {
            if ($(this).data('min') !== "undefined") {
                var minValue = $(this).data('min');
                if (($(this).val() !== '') && ($(this).val() < minValue)) {
                    $(this).val(minValue);
                    return false;
                }
            }
            if ($(this).data('max') !== "undefined") {
                var maxValue = $(this).data('max');
                if (($(this).val() !== '') && ($(this).val() > maxValue)) {
                    $(this).val(maxValue);
                    return false;
                }
            }
        });
        $('body').on('input', 'input[data-type="integer"]', function () {
            $(this).numeric();
        });
        $('body').on('input', 'input[data-type="weight"]', function () {
            $(this).numeric({allow:".", altDecimal : ",", decimalPlaces: 3});
        });
        $('body').on('input', 'input[data-type="price"]', function () {
            $(this).numeric({allow:".", altDecimal : ",", decimalPlaces: priceRounding});
        });
    }

    if (typeof($.fn.button) === 'undefined') {
        $.fn.button = function(action) {
            if (action === 'loading') {
                this.prop('disabled', true)
                    .data('original-text', this.html())
                    .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;');
                if (this.data('loading-text')) {
                    this.html(this.html() + this.data('loading-text'));
                } else {
                    this.html(this.html() + this.data('original-text'));
                }
            }
            if (action === 'reset' && this.data('original-text')) {
                this.prop('disabled', false);
                if (this.data('original-text')) {
                    this.html(this.data('original-text'));
                }
            }
        };
    }
});