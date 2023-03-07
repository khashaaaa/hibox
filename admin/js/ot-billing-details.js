var BillingPage = Backbone.View.extend({
    "el": $("#billing-view-wrapper")[0],
    "events": {
        "click .export-details": "exportFinanceDetails"
    },
    render: function()
    {
        return this;
    },
    initialize: function()
    {
        var self = this;
        this.render();
    },
    exportFinanceDetails: function (e) {
        e.preventDefault();
        var button = this.$('.export-details');
        button.addClass('disabled').find('i').attr('class', 'ot-preloader-micro');

        this.exportData(0, button);

        return false;
    },
    exportData: function(position, button){
        var self = this;
        var form = $("#apply-filter-finance-details");
        var filter = {
            'fromdate': form.find('#date-start-display').val(),
            'todate': form.find('#date-end-display').val()
        }
        $.post(
            '?cmd=Reports&do=exportFinanceDetails',
            {
                'position': position,
                'filter': filter,
                
            }, function (data) {
            if (! data.error) {
                if (data.isEnd) {
                    button.removeClass('disabled').find('i').attr('class', 'icon-cog');
                    window.location = '?cmd=Reports&do=dowmloadExportData';
                } else {
                    position = position + 100;
                    self.exportData(position, button);
                }
            } else {
                showError(data);
            }
        }, 'json');
        return false;
    }
});

$(function(){
    var isLoading = false;
    new BillingPage();	
});
