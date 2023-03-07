var autoUpdateActivityInfo = true; // Разрешение обновления содержимого окна с информацией об активности. Запрет в момент выполнения action
var alreadyOpen = false; // Признак уже открытого окна с информацией об активности
var flag = false; // Используется если активность завершённая чтобы загрузить информацию только один раз
var loadActivityInfoRun = false; // запрещаем повторный вызов loadActivityInfo
setInterval(updateActivityInfo, 5000); // Таймер на 5 секунд, по которому вызывается обновление информации об активности при открытом окне

function openActivity(id, type, closeCallback, finished)
{
    if (alreadyOpen) {
        return false;
    }
    if(finished) {
        flag = true;
    }
    alreadyOpen = true;
    autoUpdateActivityInfo = true;
    var content = '<div class="ot-preloader-mini" id="activity-info-container" activity-id="' + id +'" activity-type="' + type + '"></div>';
    modalDialog(trans.get('Activity_info'), content, function(){autoUpdateActivityInfo = false; alreadyOpen=false;}, {confirm: false, 'cancel': trans.get('Close')}, function(body) {
    	var dialog = $(body).closest('.confirmDialog');
    	if ('function' === typeof closeCallback) { 
	    	dialog.on('hidden.restoreDefaults', function(){
	            $.post('?cmd=ActivitiesUtil&do=getActivityInfo',
	                    {
	                        'id': id,
	                        'type': type
	                    },
	                    function (data) {
	                        if (!data.error) {
	                        	closeCallback(data.isFinished);
	                        }
	                    }, 'json'
	                );
	            
	        });
    	}
        setTimeout(function(){
            loadActivityInfo();
        }, 1000);
        $(body).delegate('button#start-stop','click', function(){
        	if ($('button#start-stop i').hasClass('icon-pause')) {
        		$('button#start-stop i').removeClass('icon-pause');
        		$('button#start-stop i').addClass('icon-play');
                autoUpdateActivityInfo = false;
        	} else {
        		$('button#start-stop i').addClass('icon-pause');
        		$('button#start-stop i').removeClass('icon-play');
                autoUpdateActivityInfo = true;
        	}
        })
    }, 1);
}

function doActivityAction(ev) {
    var activityId = $(ev.target).attr('activity-id');
    var actionId = $(ev.target).attr('action-id');
    var activityType = $(ev.target).attr('activity-type');

    autoUpdateActivityInfo = false;
    $('#activity-info-container button').attr('disabled', 'disabled');
    $('#activity-info-container button').addClass('disabled');

    $.post('?cmd=ActivitiesUtil&do=doActivityAction',
        {
            'activityId': activityId,
            'activityType': activityType,
            'actionId': actionId 
        },
        function (data) {
            autoUpdateActivityInfo = true;
            loadActivityInfo();
        }, 'json'
    );
}

function updateActivityInfo() {
    if ($('#activity-info-container').length > 0) {
        if (autoUpdateActivityInfo) {
            loadActivityInfo();
        }
    } else {
        alreadyOpen = false;
    }
}

function loadActivityInfo() {
    // предотвращаем повторный вызов
    if (loadActivityInfoRun) return false;
    loadActivityInfoRun = true;

    if (!autoUpdateActivityInfo) {
        return false;
    }
    var id = $('#activity-info-container').attr('activity-id');
    var type = $('#activity-info-container').attr('activity-type');
    $.post('?cmd=ActivitiesUtil&do=getActivityInfo',
        {
            'id': id,
            'type': type
        },
        function (data) {
        	if (! autoUpdateActivityInfo) {
                return false;
            }
            if (!data.error) {
                // проверяем - активность завершена или нет
                if (data.isFinished) {
                    flag = true;
                }

                $('#activity-info-container').html(data.content);
                $('#activity-info-container').removeClass('ot-preloader-mini');
                if ($('#activity-steps a#end').length > 0) {
	                var destination = $('#activity-steps a#end').offset().top;
	                $('#activity-info-container #activity-steps').scrollTop(destination);
                }
                if (flag) {
                    if ($('button#start-stop i').hasClass('icon-pause')) {
                        $('button#start-stop i').removeClass('icon-pause');
                        $('button#start-stop i').addClass('icon-play');
                    }
                    autoUpdateActivityInfo = false;
                    flag = false;
                }
                $('button.activity-action').unbind('click').bind('click', doActivityAction);
            } else {
                showError(data);
            }
        }, 'json'
    ).complete(function() {
        loadActivityInfoRun = false;
    });
}

$('body').on('click', '.activityStepActionBtn.DoStepAction', function (){
    var dropdownToggle = $(this).closest('.btn-group').find('button[data-toggle="dropdown"]');
    dropdownToggle.click();
    dropdownToggle.button('loading');

    $(this).closest('[data-toggle="dropdown"]').click();
    var activityId = $('#activity-info-container').attr('activity-id');
    var activityType = $('#activity-info-container').attr('activity-type');

    autoUpdateActivityInfo = false;
    $('#activity-info-container button').attr('disabled', 'disabled');
    $('#activity-info-container button').addClass('disabled');

    $.post('?cmd=ActivitiesUtil&do=doStepActivity',
        {
            'activityId': activityId,
            'activityType': activityType,
            'params': $(this).data('params')
        },
        function (data) {
            if (data.error) {
                showError(data);
            }
            autoUpdateActivityInfo = true;
            loadActivityInfo();
        }, 'json'
    );

    return false;
});

$('body').on('click', '.activityStepActionBtn.Link', function (){
    var url =  $(this).data('url');

    window.open(url);
    return false;
});
