	
	

$(document).ready(function(){
	
	var camera = $('#camera'),
		photos = $('#photos'),
		screen =  $('#screen');

	var template = '<div id="onephoto" class="{col}"><a href="../files/ItemCam/{src}" target="_blank" rel="cam" '+'style="background-image:url(../files/ItemCam/thumbs/{src});" >    </a>  <div align="center" id="photodel" onclick="DelPhoto(\'{src}\',\'{col}\');">Х</div>  </div> ';

	/*----------------------------------
		Установки веб камеры
	----------------------------------*/


	webcam.set_swf_url('js/ItemPhoto/webcam/webcam.swf');
	//webcam.set_api_url('js/ItemPhoto/upload.php');	// Скрипт загрузки
	webcam.set_quality(80);				// Качество фотографий JPEG
	webcam.set_shutter_sound(true, 'js/ItemPhoto/webcam/shutter.mp3');

	// Генерируем код HTML для камеры и добавляем его на страницу:	
	screen.html(
		webcam.get_html(screen.width(), screen.height())
	);


	/*------------------------------
		Обработчики событий
	-------------------------------*/


	var shootEnabled = false;
		
	$('#shootButton').click(function(){
		
		if(!shootEnabled){
			return false;
		}
		
		webcam.freeze();
		togglePane();
		return false;
	});
	
	$('#cancelButton').click(function(){
		webcam.reset();
		togglePane();
		return false;
	});
	
	$('#uploadButton').click(function(){
		webcam.set_api_url('js/ItemPhoto/upload.php?nme='+OrederID+'-'+ItemID);	// Скрипт загрузки Устанавливаем по факты чтобы знать куды записывать							    
		webcam.upload();
		webcam.reset();
		togglePane();
		return false;
	});

	camera.find('.settings').click(function(){
		if(!shootEnabled){
			return false;
		}
		
		webcam.configure('camera');
	});

	// Показываем и скрываем панель камеры:
	
	$('.ShowCamera').click(function(){
		ItemID = $(this).val();		
		$('#camera').fadeIn('fast');	
		
	});

	$('#HideCamera').click(function(){
		
		$('#camera').fadeOut('fast');
		$('#PhotosBlock').fadeOut('fast');
		
	});
	
	$('.ShowPhotos').click(function(){
		ItemID = $(this).val();
		loadPics(); //Вот тут наполняем по шаблону		
		$('#PhotosBlock').fadeIn('fast');	
		
	});
	$('#HidePhotos').click(function(){		
		
		$('#PhotosBlock').fadeOut('fast');
		
	});
	
	
	
	


	/*---------------------- 
		Возвратные вызовы
	----------------------*/
	
	
	webcam.set_hook('onLoad',function(){
		// Когда FLASH загружен, разрешаем доступ 
		// к кнопкам "Снимаю" и "Установка"
		shootEnabled = true;
	});
	
	webcam.set_hook('onComplete', function(msg){
		
		// Данный ответ возвращается upload.php
		// и содержит имя изображения в формате объекта JSON
		
		
		msg = $.parseJSON(msg);
		
		if(msg.error){
			alert(msg.message);
		}
		else {
			// Добавляем его на страницу
			photos.prepend(templateReplace(template,{src:msg.filename}));
			initFancyBox();
		}
	});
	
	webcam.set_hook('onError',function(e){
		screen.html(e);
	});
	
	
	
	
	
	/*-------------------------------------
		Наполняем страницу изображениями
	-------------------------------------*/
	
	var start = '';
	
	function loadPics(){
		
		
		// This имеет значение true, когда loadPics вызывается
		// для события, сгенерированного кнопкой "Загрузить еще":
		
		photos.html(''); // Обнуляем предыдущий просмотр
		
		if(this != window){
			if($(this).html() == transAdmin.loading){
				// Дублирующие нажатия кнопки не обрабатываем
				return false;
			}
			$(this).html(transAdmin.loading);
		}
				
		// Запускаем запрос AJAX. Параметр start либо пустой
		// либо содержит имя первого изображения, которое
		// надо выводить. Используется для постраничного вывода:
		
		$.getJSON('js/ItemPhoto/browse.php?nme='+OrederID+'-'+ItemID,{},function(r){
				
			photos.find('a').show();
			var loadMore = $('#loadMore').detach();
			
			if(!loadMore.length){
				loadMore = $('<span>',{
					id			: 'loadMore',
					html		: transAdmin.load_more,
					click		: loadPics
				});
			}
			
			$.each(r.files,function(i,filename){
				photos.append(templateReplace(template,{src:filename,col:i}));
			});
			
				
			// Если это следующая страница с изображениями:			
			if(r.nextStart){
				
				// r.nextStart содержит имя изображения,
				// которое следует за последним выведенным.
				
				start = r.nextStart;
				photos.find('a:last').hide();
				photos.append(loadMore.html(transAdmin.load_more));
			}
			
			// Нужно повторно инициализировать fancybox каждый раз,
			// когда добавляется новое изображение на страницу:
			
			initFancyBox();
		});
		
		return false;
	}

	// Автоматичсеки вызываем loadPics для
	// наполнения страницы при загрузке: - нет не будем ыыыы...
	
	//loadPics();
	

	/*----------------------------
		Вспомогательные функции
	------------------------------*/

	
	// Данная функция инициализирует скрипт 
	// fancybox.
	
	function initFancyBox(filename){
		photos.find('a').fancybox({
			'transitionIn'	: 'elastic',
			'transitionOut'	: 'elastic',
			'overlayColor'	: '#111'
		});
	}


	// Данная функция разрешает доступ к двум 
	// элментам div .buttonPane:
	
	function togglePane(){
		var visible = $('#camera .buttonPane:visible:first');
		var hidden = $('#camera .buttonPane:hidden:first');
		
		visible.fadeOut('fast',function(){
			hidden.show();
		});
	}
	
	
	// Данная функция замещает "{ключевое_слово}"
	// соотвествующим значением объекта:
	
	function templateReplace(template,data){
		return template.replace(/{([^}]+)}/g,function(match,group){
			return data[group.toLowerCase()];
		});
	}
});

function DelPhoto(nme,col){
		
		/*-------------------------------------
		Удаляем картинку	-------------------------------------*/	
		
		
		
		$('#loading').show();
		
		
		$.ajax({url: 'js/ItemPhoto/del.php?nme='+nme,        
        success: function(){
			
            $("."+col).remove();
			
        },
		error: function() { 
			alert(transAdmin.exe_error); 
		}
    	});
		
				
		$('#loading').hide();
		
		
}
