
function ValidateForm() {
	var valid = true;
	
	if ($('#min_weight').val()!='') {
		if (isNaN($('#min_weight').val())) {
  			$('#min_weight').css({'border-color' : 'red'});
			valid = false;
		} else {  			
			$('#min_weight').css({'border-color' : ''});
		}
	} else {
		$('#min_weight').css({'border-color' : ''});
	}
	
	if ($('#max_weight').val()!='') {
		if (isNaN($('#max_weight').val())) {
  			$('#max_weight').css({'border-color' : 'red'});
			valid = false;
		} else {  			
			$('#max_weight').css({'border-color' : ''});
		}
	}
	else {
		$('#max_weight').css({'border-color' : ''});
	}
	
	if ($('#step_weight').val()!='') {
		if (isNaN($('#step_weight').val())) {
  			$('#step_weight').css({'border-color' : 'red'}); 
			valid = false;
		} else {  			
			$('#step_weight').css({'border-color' : ''});
		}
	} else {
		$('#step_weight').css({'border-color' : ''});
	}
	
	if ( ($("#min_price_delivery").prop("checked")==false) && ($("#step_price").prop("checked")==false)) {
		alert('Должен быть отмечен хотя бы один из пунктов: минимальная стоимость доставки или стоимость шага по весу.');
		$('#min_price_delivery_txt').css({'color' : 'red'}); 
		$('#step_price_txt').css({'color' : 'red'}); 
		valid = false;
	} else {
		$('#min_price_delivery_txt').css({'color' : ''}); 
		$('#step_price_txt').css({'color' : ''});
	}
	
	if (valid==true) {
		$('#formul_gen').submit();
	} else {
		return false;
	}
	
	

	
}