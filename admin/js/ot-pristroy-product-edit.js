var PristroyEditPage = Backbone.View.extend({
    "el": ".pristroy-form-wrapper",
    "events": {
        "click #submit_btn": "saveProduct",
        "click .thumbnails button.ot_show_deletion_dialog_modal": "removeImage"
    },
    removeImage: function(ev) 
    {
    	var target = ev.target;
    	var self = this;
    	var li = $(target).closest('li');
    	if ($('.file_name', li).val() != '' || $( '.file', li).val() != '' ) {
    		modalDialog('', trans.get('Removing_item_images_confirmation'), function(){
    			$( '.file_name', li).val('');
    			$( '.file', li).val('');
        			self.saveProduct(ev, 1);    		
        		});
    	}
    	return false;
    },
    render: function()
    {
        var self = this;

        initializeTinyMCE('#description');

        return this;
    },
    initialize: function()
    {
        this.render();
    },
    updateProductStatus: function(ev)
    {
        ev.preventDefault();
        if (! $(ev.target).hasClass('disabled')) {
            this.$('input[name=status]').val($(ev.target).data('status'));
            this.saveProduct(ev, 1);
        }
        return false;
    },
    saveProduct: function(ev, reloadPage) 
    {
        ev.preventDefault();
        var btn = this.$('#submit_btn');
        btn.button('loading').siblings('button').addClass('disabled');
        if (! $('#uploaded_image').is('img')) {
            $('input[name=existing_uploaded_image]').val('');
        }
        btn.closest('form').ajaxSubmit({
            url     :   $(this).attr('action'),
            type    :   'POST',
            dataType:   'json',
            success :   function(data) {
                btn.button('reset').siblings('button').removeClass('disabled');
                if (! data.error) {
                    showMessage(trans.get('Data_updated_successfully'));
                    if ('undefined' !== typeof reloadPage) {
                        window.location.reload();
                    }
                    else {
                    	window.location.href = $('a#cancel_btn').attr('href');
                    }
                } else {
                    showError(data);
                }
             }
        });
        return false;
    }
});

$(function(){
    var PE = new PristroyEditPage();
});
