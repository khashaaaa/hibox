var Newsletters = Backbone.View.extend({
    "el": "#content",
    "events": {
        "click .remove-newsletter": "removeNewsletter",
        "click .ot_show_deletion_dialog_modal": "removeSubscriber",
        "change #per-page": "setPerPage"
    },
    render: function() {
        this.$('#per-page').val(this.$('#per-page').attr('data-value'));
    },
    removeNewsletter: function(ev) {
        var url = $(ev.target).attr('href');
        modalDialog(trans.get('Confirmation'), trans.get('Mailing_remove_confirmation'), function () {
            window.location.href = url;
        });
        return false;
    },
    removeSubscriber: function(ev) {
        var url = $(ev.target).closest('a').attr('href');
        modalDialog(trans.get('Confirm_needed'), trans.get('Confirm_delete_subscriber_from_newsl'), function () {
            window.location.href = url;
        });
        return false;
    },
    setPerPage: function(ev) {
        window.location.href = $(ev.target).attr('data-url') + '&perPage=' + $(ev.target).val();
    }
});

$(function(){
    var N = new Newsletters();
    N.render();
});