var Sets = new Backbone.Collection();
var SetsPage = Backbone.View.extend({
    "el": ".sets_wrapper",
    "events": {
        "click #start-scan": "startScan",
    },

    initialize: function()
    {
    },
    startScan: function(e)
    {
        var self = this;

        $('button#start-scan').button('loading');

        $.post(
            '?cmd=sets&do=startAutosetsScan',
            {
            },
            function (data) {
                if (!data.error) {
                    showMessage(data.message ? data.message : trans.get('Notify_success'));
                    var activityId = data.activityId;
                    var activityType = data.activityType;
                    openActivity(activityId, activityType, self.refreshOrders);
                } else {
                    showError(data);
                }
                $('button#start-scan').button('reset');
            }, 'json'
        );        
    },
});

$(function() {
    var U = new SetsPage();
});
