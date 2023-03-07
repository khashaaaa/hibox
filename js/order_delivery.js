function checkinputstep3()
    {
        var f = true;

        if ($('#LastName:visible').length && $('#LastName').val().trim() == '') f = false;
        if ($('#FirstName:visible').length && $('#FirstName').val().trim() == '') f = false;
        if ($('#MiddleName:visible').length && $('#MiddleName').val().trim() == '') f = false;
        if ($('#City:visible').length && $('#City').val().trim() == '') f = false;
        if ($('#Address:visible').length && $('#Address').val().trim() == '') f = false;
        if ($('#Phone:visible').length && $('#Phone').val().trim() == '') f = false;
        if ($('#deliveries-list tbody:visible').length && $('#deliveries-list tbody').html() == '') f = false;
        if ($('#Country:visible').length && $('#Country').val().trim() == '') f = false;
        $('input[name=selected_profile_id]').val($('#profiles_select').val());

        if (!f) {
            showMessage(trans.get('not_filled_required_field'));
        } else {
            DisableSubmit('nextstep','address_form2');
        }
}
 
 

$(function(){
    $('#Country').change(function(){
        $('#deliveries-list tbody').empty();
        var code = $(this).find('option:selected').attr('code');
        if (code) { 
            $('#empty_deliveries').hide();
            if ((typeof countryPrices[code] !== 'undefined') && (countryPrices[code].length)) {
                $('.toast-container').remove();
            } else {
                $().toastmessage('showToast', {'text': trans.get('empty_deliveries'), 'sticky' : true, 'type': 'error'});
            }
            var deliveries = [];
            for (var i in countryPrices[code]) {
                var del = countryPrices[code][i];
                if (deliveryId && deliveryId == del.id) {
                	deliveries.push(del);
                }
            }
            if (deliveries.length == 0) {
            	deliveries = countryPrices[code];
            	if (deliveryId) {
                    if (typeof showError === 'function') {
                        showError(trans.get('Defined_delivery_mode_not_found_Please_select_other'));
                    } else {
                        $().toastmessage('showToast', {'text': trans.get('Defined_delivery_mode_not_found_Please_select_other'), 'sticky' : true, 'type': 'error'});
                    }
            	}
            }
            
            for (var i in deliveries) {
                var del = deliveries[i];
                if (typeof del !== 'object') {
                	continue;
                }
                $('#deliveries-list tbody').append(
                    $('<tr></tr>')
                    .append(
                        $('<td width="5%"></td>')
                        .append(
                            $('<input type="radio" name="model" />').val(del.Id)
                        )
                    )
                    .append(
                        $('<td></td>')
                        .append(
                            $('<strong></strong>').text(del.Name)
                        )
                        .append($('<br />'))
                        .append(
                            $('<span></span>').text(del.Description)
                        )
                    )
                    .append(
                        $('<td></td>').append(
                            $('<b></b>').text(sdf_FTS(parseFloat(del.Price), price_round_decimals, ' ') + del.CurrencySign)
                        )
                    )
                );
            }
            if (defaultDelivery && $('#deliveries-list tbody input[value="' + defaultDelivery + '"]').length != 0) {
                $('#deliveries-list tbody input[value="' + defaultDelivery + '"]').attr('checked', 'checked');
            } else {
                $('#deliveries-list tbody input[type="radio"]:first').attr('checked', 'checked');
            }
        }
    });
    
    init_delivery_profile();

    $('input#PassportNumber').on('keyup', function() {
        limitText(this, 255);
    });
});