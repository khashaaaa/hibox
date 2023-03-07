var NewsletterEdit = Backbone.View.extend({
    "el": "#content",
    "events": {
        "click button.btn_preloader": "enablePreLoader",
        "click #test-newsletter": "testNewsletter"
    },
    initialize: function() {
        var text = $('#text').val();
        if (text.length == 0) {
            $('#text').val($('.defaultTemplate').html());
        }
    },
    enablePreLoader: function(ev) {
        var form = $(ev.target).closest('form');
        var title = form.find('input[name=title]').val();
        var text = tinyMCE.editors[0].getContent();

        if (! title) {
            showError(trans.get('no_subject_set'));
            return false;
        }

        if (! text) {
            showError(trans.get('no_text_set'));
            return false;
        }

        $(ev.target).attr('disabled', 'disabled');
        var form = $(ev.target).closest('form');
        form.append($('<input>').attr({'name': $(ev.target).attr('name'), 'type': 'hidden'}).val($(ev.target).val()));
        form.submit();
    },
    testNewsletter: function() {
        var that = this;
        this.$('#test-newsletter').attr('disabled', 'disabled');
        var text = tinyMCE.editors[0].getContent();
        $('#text').val(text);
        $.post('?cmd=Newsletters&do=test', this.$('#edit-form').serializeArray(), function(data) {
            if (data.error != 'Ok') {
                showError(data.message);
            } else {
                showMessage(''+trans.get('Test_letter_successfuly_sent')+'');
            }
            that.$('#test-newsletter').removeAttr('disabled');
        }, 'json');
        return false;
    }
});

$(function(){
    new NewsletterEdit();

    initializeTinyMCE('#text', {
        height: 800,
        width: '100%',
        content_css : "/css/style_editor.css",
        relative_urls: false,
        remove_script_host : false,
    });
});