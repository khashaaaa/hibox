var BillingPage = Backbone.View.extend({
    "el": $("#billing-view-wrapper")[0],
    render: function()
    {
        return this;
    },
    initialize: function()
    {
        var self = this;
        this.render();

        $('#apply_button').click(this.getBillingPeriodInfo);
    },
    getBillingPeriodInfo: function(){
    	var dateFrom = $('#date-start-display').val();
    	var dateTo = $('#date-end-display').val();
    	if (dateFrom == '' || dateTo == '') {
            showError(trans.get('Must_input_dates_to_get_turnover'));
            return;
        }
    	$('#apply_button').button('loading');
    	
    	$.ajax({
            async : true,
            type: 'POST',
            dataType: 'json',
            url: "?cmd=Reports&do=billingForPeriod",
            data : {
                "dateFrom" : dateFrom,
                "dateTo" : dateTo,
            },
            success : function (data) {
                if (data.error) {
                    showError(data);
                } else {
                	$('#billing_period_info').html(data.info);
                }
                $('#apply_button').button('reset');
            }
        });
    }
});

$(function(){
    new BillingPage();	
});
