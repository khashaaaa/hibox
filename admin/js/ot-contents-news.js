var Contents = new Backbone.Collection();
var ContentsPage = Backbone.View.extend(
{
    "el": ".contents-wrapper",
    "events": {
		"click a.ot_show_deletion_dialog_modal": "removeNews",
		"click a.edit_news": "editNews",
		"click form.ot_edit_news_form button.btn-primary": "saveNews",
		"click form.ot_edit_news_form .save-and-continue": "saveNewsAndContinue",
    },
    render: function()
    {
        return this;
    },
    initialize: function()
    {
        this.render();
        initializeTinyMCE('#news-content, #news-preview');
    },
    saveNewsAndContinue:  function (e) {
        this.saveNews(e, false);
    },
    saveNews: function(e, reload)
    {
        e.preventDefault();
        if (reload === undefined) {
            reload = true;
        }
        var target = this.$(e.target);        
    	var form = $(e.currentTarget).closest('form');    	
    	var content = tinyMCE.editors[0].getContent(); 
    	$('#news-content', form).val(content);
    	var preview = tinyMCE.editors[1].getContent(); 
    	$('#news-preview', form).val(preview);    	
        var $button = target.button('loading');
        $(form).ajaxSubmit({
            url     :   $(this).attr('action'),
            type    :   'POST',
            dataType:   'json',
            success :   function(data) {
                if(data && data.result && data.result == 'ok') {
                    showMessage(trans.get('contents::Page_saved_successfully'));
                    if (reload) {
                        document.location.href = '?cmd=contents&do=news';
                    } else {
                        $button.button('reset');
                    }
                } else {
                    showError(data);
                    $button.button('reset');
                }
            }
        });
        
    	return false;
    },
    removeNews: function(e)
    {
    	var tr = $(e.currentTarget).closest('tr');
    	var newsId = $(tr).attr('id');
    	
    	modalDialog(trans.get('Confirm_needed'), trans.get('contents::Really_remove_this_news'), function() {
    		$.post('?cmd=contents&do=deleteNews', { 'id' : newsId}, function (data) {
                if (data.result == 'ok') {
                	$(tr).remove();
                }
            }, 'json');    		
        }, {'confirm':trans.get('Delete'), 'cancel':trans.get('Cancel')});
    },
    editNews: function(e) 
    {
    	var tr = $(e.currentTarget).closest('tr');
    	var newsId = $(tr).attr('id');
    	document.location.href = 'index.php?cmd=contents&do=editNews&id='+newsId;
    },
});

$(function()
{
    var P = new ContentsPage();
});
