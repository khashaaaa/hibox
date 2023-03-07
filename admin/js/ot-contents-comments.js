var Contents = new Backbone.Collection();
var CommentsPage = Backbone.View.extend(
{
    "el": ".contents-wrapper",
    "events": {
		"click .activate-comment": "activateComment",
        "click .remove-comment": "removeComment",
        "click .answer-comment": "answerComment",
        "click .ot-bulkRemoveComment": "bulkRemoveComment",
        "click .ot-bulkActivateComment": "bulkActivateComment",         
		"click #checkAll input": "toggleCheckAll"
    },
    answerComment: function(e)
    {           
        var target = this.$(e.target);
    	var id = target.attr('data-id');
        var action = target.attr('data-action');
        var button = target.parents('ul:first').prev();
        $('.ot_answer_comment_window .modal-body #toText').html($('.' + id + '_commentText').html());
        $('.ot_answer_comment_window .modal-body #answerText_tmp').html($('.' + id + '_answerText').html());
        var content = $('.ot_answer_comment_window .modal-body').html();
        content = content.replace(new RegExp("answerText_tmp",'g'),"answerText");
        modalDialog(trans.get('Wright_answer'), content, function(body){
            button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
            button.parent().removeClass('open');            
            $.post(
                action,
                {
                    "id" : id,
                    "text" : $('#answerText', body).val()
                },
                function (data) {
                    if (! data.error) {
                        showMessage(trans.get('Success_answer'));   
                        location.reload();
                    } else {
                        showError(data);                        
                        button.removeClass('disabled').find('i').attr('class', 'icon-cog');
                    }
                }, 'json'
            );
        },{confirm: trans.get('Save'), cancel: trans.get('Cancel') });
    	
    },
    removeComment: function(e)
    {   
        var target = this.$(e.target);
    	var id = target.attr('data-id');
        var action = target.attr('data-action');
        var button = target.parents('ul:first').prev();
        modalDialog(trans.get('Confirm_needed'), trans.get('Really_remove_these_comment'), function(){
            button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
            button.parent().removeClass('open');            
            $.post(
                action,
                {"id" : id},
                function (data) {
                    button.removeClass('disabled').find('i').attr('class', 'icon-cog');
                    if (! data.error) {
                        showMessage(trans.get('Comment_removed')); 
                        $('#' + id + '_comment').remove();
                    } else {
                        showError(data);
                    }
                }, 'json'
            );
        });
    	
    },
    bulkRemoveComment: function(e)
    {
    	e.preventDefault();
        var target = this.$(e.target);
        var action = target.attr('data-action');
        var button = target.parents('ul:first').prev();
        var self = this;
        var ids = [];
        $('input[name=ids]:checked').each(function() {
            ids.push(this.value);
        })
        if (ids.length == 0) {
            showError(trans.get('No_comments_selected'));
            button.removeClass('disabled').find('i').attr('class', 'icon-cog');
            return false;
        }      
        modalDialog(trans.get('Confirm_needed'), trans.get('Really_remove_these_comments'), function(){
            button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
            button.parent().removeClass('open');
            $.post(
                action,
                {"ids" : ids},
                function (data) {
                    if (! data.error) {
                        showMessage(trans.get('Comments_removed')); 
                        location.reload();
                    } else {
                        showError(data);                        
                        button.removeClass('disabled').find('i').attr('class', 'icon-cog');
                    }
                }, 'json'
            );
        });
    	
    },
    activateComment: function(e)
    {
    	var target = this.$(e.target);
    	var id = target.attr('data-id');
        var action = target.attr('data-action');        
        var button = target.parents('ul:first').prev();
        button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
        button.parent().removeClass('open');
        $.post(
            action,
            {"id" : id},
            function (data) {
                button.removeClass('disabled').find('i').attr('class', 'icon-cog');
                if (! data.error) {
                    showMessage(trans.get('Comment_activated'));
                    $('#' + id + '_comment').attr('style', '');
                } else {
                    showError(data);
                }
            }, 'json'
        );    	
    },
    bulkActivateComment: function(e)
    {
    	e.preventDefault();        
        var target = this.$(e.target);
        var action = target.attr('data-action');
        var button = target.parents('ul:first').prev();
        var self = this;
        var ids = [];
        $('input[name=ids]:checked').each(function() {
            ids.push(this.value);
        })
        if (ids.length == 0) {
            showError(trans.get('No_comments_selected'));
            button.removeClass('disabled').find('i').attr('class', 'icon-cog');
            return false;
        }      
        button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');
        button.parent().removeClass('open');
        $.post(
            action,
            {"ids" : ids},
            function (data) {
                if (! data.error) {
                    showMessage(trans.get('Comments_activated'));
                    location.reload();
                } else {
                    showError(data);                        
                    button.removeClass('disabled').find('i').attr('class', 'icon-cog');
                }
            }, 'json'
        );    	
    },
    toggleCheckAll: function(ev){
        var self = this.$(ev.target);
        self.parents('thead').next().find('input[type=checkbox]').prop('checked', self.is(':checked'));
    },
    render: function()
    {
    	return this;
    },
    initialize: function()
    {
        this.render();        
    }
});

$(function()
{
    var CP = new CommentsPage();
});
