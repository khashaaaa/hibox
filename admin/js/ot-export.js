var Sets = new Backbone.Collection();
var SetsPage = Backbone.View.extend({
    "el": ".sets_wrapper",
    "events": {
        "click #start-export": "startExport",
    },

    initialize: function()
    {
    },
    startExport: function(e)
    {
        var self = this;
        var exportingTarget = $('#exportingTarget').val();

        $('button#start-export').button('loading');

        $.post(
            '?cmd=sets&do=startExportScan',
            {
                'exportingTarget':exportingTarget
            },
            function (data) {
                if (!data.error) {
                    showMessage(data.message ? data.message : trans.get('Notify_success'));
                    var activityId = data.activityId;
                    var activityType = data.activityType;
                    openActivity(activityId, activityType);
                } else {
                    showError(data);
                }
                $('button#start-export').button('reset');
            }, 'json'
        );
    },
});

$(function() {
    var U = new SetsPage();
});
