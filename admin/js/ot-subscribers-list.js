var Subscribers = Backbone.View.extend({
    "el": "#content",
    "events": {
        "click .ot_show_deletion_dialog_modal": "removeSubscriber",
        "change #per-page": "setPerPage",
        "submit #import-subscribers": "importSubscribers",
        "click .ot-add-subscribers-custom": "showAddSubscriberForm"
    },
    isTypeAheadInitialized: false,
    progressButton: null,
    render: function() {
        var that = this;

        this.$('#per-page').val(this.$('#per-page').attr('data-value'));

        this.$('.ot-add-subscribers').on('shown', function(e, editable) {
            setTimeout(that.initTypeahead, 100);
        });
        this.$('.ot-add-subscribers').on('save', function(e, editable) {
            if (editable.response.error) {
                showError(editable.response.message);
            } else {
                showMessage(trans.get('Subscriber_added'));
                setTimeout('window.location.reload();', 2000);
            }
            setTimeout(function() {
                $('.ot-add-subscribers').html('<i class="icon-plus font-14 color-blue"></i> '+trans.get('Add_user')+'');
            }, 500);
        });
        this.$('.ot-add-subscribers').editable({
            mode: 'popup',
            url: '?cmd=Subscribers&do=add',
            title: trans.get('Start_typing_an_user_login'),
            placeholder: trans.get('Start_typing_an_user_login'),
            emptytext : '',
            inputclass: 'input-medium ot-typehead-subscribers'
        });
    },
    initTypeahead: function() {
        var that = this;
        if (!this.isTypeAheadInitialized) {
            $('.ot-typehead-subscribers').typeahead({
                ajax: {
                    url: '?cmd=Subscribers&do=searchUserInOtapi',
                    method: 'post'
                },
                onSelect: function(data) {
                    that.$('.ot-add-subscribers').editable('option', 'pk', data.value);
                }
            });
        }
    },
    removeSubscriber: function(ev) {
        var url = $(ev.target).closest('a').attr('href');
        modalDialog(trans.get('Confirm_needed'), trans.get('Confirm_delete_subscriber'), function () {
            window.location.href = url;
        });
        return false;
    },
    showAddSubscriberForm: function(ev) {
        var content = $('.ot_subscriber_add_modal .modal-body').html();
        modalDialog(trans.get('Add_custom_subscriber'), content, function(body) {
            var subscriberName = $('#subscriberName', body).val();
            var subscriberMail = $('#subscriberMail', body).val();            
            $.post(
                '?cmd=Subscribers&do=addSubscriber',
                { 
                    subscriberName : subscriberName,
                    subscriberMail : subscriberMail
                },
                function (data) {
                    if (! data.error) {			  
				        showMessage(trans.get('Subscriber_is_added'));
                        location.reload();
				    } else {				        
                        showError(data.message);
				    }
                }, 'json'
            );
            
        }, {confirm: trans.get('Save'), cancel: trans.get('Cancel') });
        return false;
    },
    setPerPage: function(ev) {
        window.location.href = $(ev.target).attr('data-url') + '&perPage=' + $(ev.target).val();
    },
    importSubscribers: function(ev) {
        var that = this;

        var form = $(ev.target);
        var submitButton = form.find('[type="submit"]');
        var formData = new FormData(form[0]);
        var file = $('#import-subscribers input[type="file"]').val();
        
        if (! file) {
        	showError(trans.get('No_files_to_upload'));
        	return false;
        }

        this.progressButton = Ladda.create(submitButton[0]);
        this.progressButton.start();
        submitButton.find('.ladda-label').text(trans.get('loading'));

        $.ajax({
            url: '?cmd=Subscribers&do=import',
            type: 'POST',
            data: formData,
            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                if(myXhr.upload){
                    myXhr.upload.addEventListener('progress',that.setFileUploadProgress, false);
                }
                return myXhr;
            },
            success: function(data) {
                that.progressButton.stop();
            	if ( ! data.error) {
	                showMessage(trans.get('Users_imported'));
	                setTimeout('window.location.reload();', 1000);
            	} else {
            		showError(data.message);
            	}	
            },
            cache: false,
            contentType: false,
            processData: false
        });

        return false;
    },
    setFileUploadProgress: function(e) {
        //this.progressButton.setProgress(e.loaded / e.total);
    }
});

$(function(){
    var N = new Subscribers();
    N.render();
});