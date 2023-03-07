function createUploader() {
	var uploader = new qq.FileUploader({
		element:document.getElementById('file-uploader'),
		action:'utils/Upload.php',
		debug:true,
		template:'<div class="qq-uploader">' +
			'<div class="qq-upload-drop-area"><span></span></div>' +
			'<div class="qq-upload-button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"'+
			' style="height:23px;padding-top:7px">&nbsp;&nbsp;&nbsp;'+
			langs.select_picture+'&nbsp;&nbsp;&nbsp;</div>' +
			'<ul class="qq-upload-list"></ul>' +
			'</div>',
		onComplete:function (id, fileName, responseJSON) {
			$('.qq-upload-list-new').remove();
			$('.qq-upload-list').empty().append($('<li></li>').append($('<img />').attr('src', responseJSON.url +
				'?' + Math.random())));
			$('#logo').val(responseJSON.url);
		}
	});
}

function check_value_limit (obj, max) {
	if (obj.value > max) obj.value = max; 
}

$(function () {
	createUploader();
	$('#file-uploader .qq-uploader').append(
		$('<ul></ul>')
			.addClass('qq-upload-list-new')
			.append(
			$('<li></li>')
				.append(
				$('<img />').attr('src', logo)
			)
		)
	);
});